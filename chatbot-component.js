// MediMerge Chatbot Component - Enhanced Version with API Integration
class MediMergeChatbot {
    constructor(options = {}) {
        this.options = {
            position: options.position || 'bottom-right',
            theme: options.theme || 'default',
            apiUrl: options.apiUrl || './api/chatbot_api.php',
            ...options
        };
        this.isOpen = false;
        this.messages = [];
        this.sessionId = this.generateSessionId();
        this.userId = this.getUserId();
        this.isTyping = false;
        this.init();
    }

    init() {
        // Check if chatbot already exists to prevent duplicates
        if (document.querySelector('.medimerge-chatbot')) {
            return;
        }
        
        this.createChatbot();
        this.setupEventListeners();
        this.loadInitialMessage();
        this.loadChatHistory();
    }

    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    getUserId() {
        // Try to get user ID from session or localStorage
        const userId = localStorage.getItem('medimerge_user_id') || 
                      sessionStorage.getItem('medimerge_user_id') || null;
        return userId;
    }

    createChatbot() {
        const chatbotHTML = `
            <div class="medimerge-chatbot" id="medimerge-chatbot">
                <div class="chatbot-toggle" id="chatbot-toggle">
                    <i class="fas fa-comments"></i>
                    <span class="notification-dot"></span>
                </div>
                <div class="chatbot-widget" id="chatbot-widget">
                    <div class="chatbot-header">
                        <div class="header-content">
                            <div class="chatbot-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="header-text">
                                <h3>MediMerge Assistant</h3>
                                <span class="status">Online</span>
                            </div>
                        </div>
                        <button class="chatbot-close" id="chatbot-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="chatbot-messages" id="chatbot-messages">
                        <!-- Messages will be added here -->
                    </div>
                    <div class="chatbot-typing" id="chatbot-typing" style="display: none;">
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="chatbot-input">
                        <input type="text" id="chatbot-input" placeholder="Type your message..." maxlength="500">
                        <button id="chatbot-send">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
        this.addStyles();
    }

    addStyles() {
        const styles = `
            <style>
                .medimerge-chatbot {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    z-index: 10000;
                    font-family: 'Inter', sans-serif;
                }

                .chatbot-toggle {
                    width: 42px;
                    height: 42px;
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    border: none;
                    border-radius: 50%;
                    color: white;
                    font-size: 20px;
                    cursor: pointer;
                    box-shadow: 0 5px 15px rgba(17, 182, 113, 0.3);
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    position: relative;
                }

                .chatbot-toggle:hover {
                    transform: scale(1.1);
                    box-shadow: 0 8px 20px rgba(17, 182, 113, 0.4);
                }

                .notification-dot {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                    width: 12px;
                    height: 12px;
                    background: #ef4444;
                    border-radius: 50%;
                    animation: pulse 2s infinite;
                }

                @keyframes pulse {
                    0% { transform: scale(1); opacity: 1; }
                    50% { transform: scale(1.2); opacity: 0.7; }
                    100% { transform: scale(1); opacity: 1; }
                }

                .chatbot-widget {
                    position: absolute;
                    bottom: 80px;
                    right: 0;
                    width: 300px;
                    max-width: 85vw;
                    height: 400px;
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
                    display: none;
                    flex-direction: column;
                    overflow: hidden;
                }

                .chatbot-widget.show {
                    display: flex;
                    animation: slideUp 0.3s ease-out;
                }

                @keyframes slideUp {
                    from { transform: translateY(20px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }

                .chatbot-header {
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    color: white;
                    padding: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }

                .header-content {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }

                .chatbot-avatar {
                    width: 40px;
                    height: 40px;
                    background: rgba(255, 255, 255, 0.2);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                }

                .header-text h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                }

                .header-text .status {
                    font-size: 12px;
                    opacity: 0.8;
                }

                .chatbot-close {
                    background: none;
                    border: none;
                    color: white;
                    font-size: 18px;
                    cursor: pointer;
                    padding: 5px;
                    border-radius: 50%;
                    transition: background 0.3s ease;
                }

                .chatbot-close:hover {
                    background: rgba(255, 255, 255, 0.2);
                }

                .chatbot-messages {
                    flex: 1;
                    padding: 20px;
                    overflow-y: auto;
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                }

                .message {
                    max-width: 80%;
                    padding: 12px 16px;
                    border-radius: 18px;
                    font-size: 14px;
                    line-height: 1.4;
                    word-wrap: break-word;
                }

                .message.user {
                    background: #e3f2fd;
                    color: #1565c0;
                    align-self: flex-end;
                    border-bottom-right-radius: 6px;
                }

                .message.bot {
                    background: #f5f5f5;
                    color: #333;
                    align-self: flex-start;
                    border-bottom-left-radius: 6px;
                }

                .chatbot-typing {
                    padding: 0 20px 20px;
                }

                .typing-indicator {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    padding: 12px 16px;
                    background: #f5f5f5;
                    border-radius: 18px;
                    width: fit-content;
                    border-bottom-left-radius: 6px;
                }

                .typing-indicator span {
                    width: 8px;
                    height: 8px;
                    background: #999;
                    border-radius: 50%;
                    animation: typing 1.4s infinite ease-in-out;
                }

                .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
                .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

                @keyframes typing {
                    0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
                    40% { transform: scale(1); opacity: 1; }
                }

                .chatbot-input {
                    padding: 20px;
                    border-top: 1px solid #eee;
                    display: flex;
                    gap: 10px;
                    align-items: center;
                }

                .chatbot-input input {
                    flex: 1;
                    padding: 12px 16px;
                    border: 2px solid #e0e0e0;
                    border-radius: 25px;
                    font-size: 14px;
                    outline: none;
                    transition: border-color 0.3s ease;
                }

                .chatbot-input input:focus {
                    border-color: #11b671;
                }

                .chatbot-input button {
                    width: 40px;
                    height: 40px;
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    border: none;
                    border-radius: 50%;
                    color: white;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: transform 0.3s ease;
                }

                .chatbot-input button:hover {
                    transform: scale(1.1);
                }

                .chatbot-input button:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                    transform: none;
                }

                /* Responsive Design */
                @media (max-width: 480px) {
                    .chatbot-widget {
                        width: calc(100vw - 32px);
                        right: -6px;
                        height: 55vh;
                        bottom: 70px;
                    }
                    .chatbot-toggle {
                        width: 46px;
                        height: 46px;
                        font-size: 18px;
                    }
                }

                @media (max-width: 768px) {
                    .chatbot-widget {
                        width: 280px;
                        height: 380px;
                    }
                }

                /* Quick Actions */
                .quick-actions {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                    margin-top: 10px;
                }

                .quick-action-btn {
                    background: #f0f9ff;
                    border: 1px solid #0ea5e9;
                    color: #0ea5e9;
                    padding: 6px 12px;
                    border-radius: 15px;
                    font-size: 12px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .quick-action-btn:hover {
                    background: #0ea5e9;
                    color: white;
                }

                /* Message Timestamp */
                .message-time {
                    font-size: 11px;
                    opacity: 0.6;
                    margin-top: 4px;
                }

                /* Scrollbar Styling */
                .chatbot-messages::-webkit-scrollbar {
                    width: 6px;
                }

                .chatbot-messages::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 3px;
                }

                .chatbot-messages::-webkit-scrollbar-thumb {
                    background: #c1c1c1;
                    border-radius: 3px;
                }

                .chatbot-messages::-webkit-scrollbar-thumb:hover {
                    background: #a8a8a8;
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    setupEventListeners() {
        const toggle = document.getElementById('chatbot-toggle');
        const close = document.getElementById('chatbot-close');
        const input = document.getElementById('chatbot-input');
        const send = document.getElementById('chatbot-send');

        toggle.addEventListener('click', () => this.toggleChatbot());
        close.addEventListener('click', () => this.closeChatbot());
        
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        send.addEventListener('click', () => this.sendMessage());

        // Close chatbot when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.medimerge-chatbot')) {
                this.closeChatbot();
            }
        });
    }

    toggleChatbot() {
        if (this.isOpen) {
            this.closeChatbot();
        } else {
            this.openChatbot();
        }
    }

    openChatbot() {
        this.isOpen = true;
        document.getElementById('chatbot-widget').classList.add('show');
        document.getElementById('chatbot-input').focus();
        this.hideNotification();
    }

    closeChatbot() {
        this.isOpen = false;
        document.getElementById('chatbot-widget').classList.remove('show');
    }

    showNotification() {
        const dot = document.querySelector('.notification-dot');
        if (dot) dot.style.display = 'block';
    }

    hideNotification() {
        const dot = document.querySelector('.notification-dot');
        if (dot) dot.style.display = 'none';
    }

    loadInitialMessage() {
        const welcomeMessage = "Hello! Welcome to MediMerge. I'm your health assistant. I can help you find products, track orders, answer questions about payments, and provide general health information. How can I help you today?";
        this.addMessage('bot', welcomeMessage);
    }

    async loadChatHistory() {
        try {
            const response = await fetch(this.options.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'get_history',
                    user_id: this.userId,
                    session_id: this.sessionId
                })
            });

            const data = await response.json();
            if (data.success && data.history.length > 0) {
                // Don't show initial message if we have history
                const messagesContainer = document.getElementById('chatbot-messages');
                messagesContainer.innerHTML = '';
                
                data.history.forEach(msg => {
                    this.addMessage('user', msg.message, false);
                    this.addMessage('bot', msg.response, false);
                });
            }
        } catch (error) {
            console.error('Error loading chat history:', error);
        }
    }

    async sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message || this.isTyping) return;

        // Add user message
        this.addMessage('user', message);
        input.value = '';

        // Show typing indicator
        this.showTyping();

        try {
            const response = await fetch(this.options.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'send_message',
                    message: message,
                    user_id: this.userId,
                    session_id: this.sessionId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Hide typing indicator
                this.hideTyping();
                
                // Add bot response
                this.addMessage('bot', data.response);
                
                // Update session ID if provided
                if (data.session_id) {
                    this.sessionId = data.session_id;
                }
            } else {
                this.hideTyping();
                this.addMessage('bot', 'Sorry, I encountered an error. Please try again.');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.hideTyping();
            this.addMessage('bot', 'Sorry, I\'m having trouble connecting. Please check your internet connection and try again.');
        }
    }

    showTyping() {
        this.isTyping = true;
        document.getElementById('chatbot-typing').style.display = 'block';
        document.getElementById('chatbot-send').disabled = true;
        this.scrollToBottom();
    }

    hideTyping() {
        this.isTyping = false;
        document.getElementById('chatbot-typing').style.display = 'none';
        document.getElementById('chatbot-send').disabled = false;
    }

    addMessage(type, content, scroll = true) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        
        const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        messageDiv.innerHTML = `
            ${content}
            <div class="message-time">${timestamp}</div>
        `;

        // Add quick action buttons for bot messages
        if (type === 'bot') {
            const quickActions = this.getQuickActions(content);
            if (quickActions.length > 0) {
                const actionsDiv = document.createElement('div');
                actionsDiv.className = 'quick-actions';
                actionsDiv.innerHTML = quickActions.map(action => 
                    `<button class="quick-action-btn" onclick="window.medimergeChatbot.handleQuickAction('${action}')">${action}</button>`
                ).join('');
                messageDiv.appendChild(actionsDiv);
            }
        }

        messagesContainer.appendChild(messageDiv);
        
        if (scroll) {
            this.scrollToBottom();
        }

        // Show notification if chatbot is closed
        if (!this.isOpen && type === 'bot') {
            this.showNotification();
        }
    }

    getQuickActions(content) {
        const actions = [];
        
        if (content.includes('pain relief')) {
            actions.push('Show Pain Relief', 'Show All Products');
        } else if (content.includes('cold') || content.includes('cough')) {
            actions.push('Show Cold & Cough', 'Show All Products');
        } else if (content.includes('vitamin') || content.includes('supplement')) {
            actions.push('Show Vitamins', 'Show All Products');
        } else if (content.includes('diabetes')) {
            actions.push('Show Diabetes Care', 'Show All Products');
        } else if (content.includes('order') || content.includes('delivery')) {
            actions.push('Track Order', 'View Orders');
        } else if (content.includes('payment')) {
            actions.push('Payment Methods', 'Contact Support');
        }
        
        return actions;
    }

    handleQuickAction(action) {
        let message = '';
        
        switch (action) {
            case 'Show Pain Relief':
                message = 'Show me pain relief products';
                break;
            case 'Show Cold & Cough':
                message = 'Show me cold and cough medicines';
                break;
            case 'Show Vitamins':
                message = 'Show me vitamin supplements';
                break;
            case 'Show Diabetes Care':
                message = 'Show me diabetes management products';
                break;
            case 'Show All Products':
                message = 'Show me all products';
                break;
            case 'Track Order':
                message = 'How do I track my order?';
                break;
            case 'View Orders':
                message = 'How can I view my orders?';
                break;
            case 'Payment Methods':
                message = 'What payment methods do you accept?';
                break;
            case 'Contact Support':
                message = 'I need to contact support';
                break;
            default:
                message = action;
        }
        
        // Set the input value and send
        const input = document.getElementById('chatbot-input');
        input.value = message;
        this.sendMessage();
    }

    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbot-messages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Public method to programmatically send a message
    sendProgrammaticMessage(message) {
        const input = document.getElementById('chatbot-input');
        input.value = message;
        this.sendMessage();
    }

    // Public method to get current session ID
    getSessionId() {
        return this.sessionId;
    }

    // Public method to set user ID
    setUserId(userId) {
        this.userId = userId;
        localStorage.setItem('medimerge_user_id', userId);
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.medimergeChatbot = new MediMergeChatbot({
        apiUrl: './api/chatbot_api.php'
    });
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MediMergeChatbot;
}
