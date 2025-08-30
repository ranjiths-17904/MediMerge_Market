// MediMerge Chatbot Component
class MediMergeChatbot {
    constructor(options = {}) {
        this.options = {
            position: options.position || 'bottom-right',
            theme: options.theme || 'default',
            ...options
        };
        this.isOpen = false;
        this.init();
    }

    init() {
        this.createChatbot();
        this.setupEventListeners();
        this.loadInitialMessage();
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
                            <img src="./Images/MEDI_MERGE_LOGO.png" alt="MediMerge" class="chatbot-logo">
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
                    <div class="chatbot-input">
                        <input type="text" id="chatbot-input" placeholder="Type your message...">
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
                    width: 60px;
                    height: 60px;
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    border: none;
                    border-radius: 50%;
                    color: white;
                    font-size: 24px;
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
                    background: #ff4757;
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
                    width: 350px;
                    height: 500px;
                    background: white;
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                    display: none;
                    overflow: hidden;
                    animation: slideUp 0.3s ease;
                }

                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .chatbot-widget.active {
                    display: block;
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
                    gap: 12px;
                }

                .chatbot-logo {
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                }

                .header-text h3 {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                }

                .status {
                    font-size: 12px;
                    opacity: 0.9;
                }

                .chatbot-close {
                    background: none;
                    border: none;
                    color: white;
                    font-size: 18px;
                    cursor: pointer;
                    padding: 5px;
                    border-radius: 50%;
                    transition: all 0.3s ease;
                }

                .chatbot-close:hover {
                    background: rgba(255, 255, 255, 0.2);
                }

                .chatbot-messages {
                    height: 350px;
                    overflow-y: auto;
                    padding: 20px;
                    background: #f8f9fa;
                }

                .chatbot-message {
                    margin: 10px 0;
                    max-width: 80%;
                    animation: fadeIn 0.3s ease;
                }

                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .message-user {
                    margin-left: auto;
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    color: white;
                    padding: 12px 16px;
                    border-radius: 18px 18px 4px 18px;
                }

                .message-bot {
                    background: white;
                    color: #333;
                    padding: 12px 16px;
                    border-radius: 18px 18px 18px 4px;
                    border: 1px solid #e1e1e1;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }

                .message-time {
                    font-size: 11px;
                    opacity: 0.7;
                    margin-top: 4px;
                    text-align: right;
                }

                .chatbot-input {
                    padding: 20px;
                    border-top: 1px solid #eee;
                    background: white;
                    display: flex;
                    gap: 10px;
                    align-items: center;
                }

                .chatbot-input input {
                    flex: 1;
                    padding: 12px 16px;
                    border: 2px solid #e1e1e1;
                    border-radius: 25px;
                    outline: none;
                    font-size: 14px;
                    transition: all 0.3s ease;
                }

                .chatbot-input input:focus {
                    border-color: #11b671;
                    box-shadow: 0 0 0 3px rgba(17, 182, 113, 0.1);
                }

                .chatbot-input button {
                    background: #11b671;
                    color: white;
                    border: none;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: all 0.3s ease;
                }

                .chatbot-input button:hover {
                    background: #0ea55d;
                    transform: scale(1.1);
                }

                .chatbot-input button:disabled {
                    background: #ccc;
                    cursor: not-allowed;
                }

                .typing-indicator {
                    display: flex;
                    gap: 4px;
                    padding: 12px 16px;
                    background: white;
                    border-radius: 18px 18px 18px 4px;
                    border: 1px solid #e1e1e1;
                    max-width: 60px;
                }

                .typing-dot {
                    width: 8px;
                    height: 8px;
                    background: #11b671;
                    border-radius: 50%;
                    animation: typing 1.4s infinite ease-in-out;
                }

                .typing-dot:nth-child(1) { animation-delay: -0.32s; }
                .typing-dot:nth-child(2) { animation-delay: -0.16s; }

                @keyframes typing {
                    0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
                    40% { transform: scale(1); opacity: 1; }
                }

                @media (max-width: 768px) {
                    .chatbot-widget {
                        width: 300px;
                        right: -50px;
                    }
                }

                @media (max-width: 480px) {
                    .chatbot-widget {
                        width: 280px;
                        right: -80px;
                    }
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
        close.addEventListener('click', () => this.toggleChatbot());
        
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });

        send.addEventListener('click', () => this.sendMessage());

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            const chatbot = document.getElementById('medimerge-chatbot');
            if (!chatbot.contains(e.target)) {
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
        const widget = document.getElementById('chatbot-widget');
        widget.classList.add('active');
        this.isOpen = true;
        
        // Focus input
        setTimeout(() => {
            document.getElementById('chatbot-input').focus();
        }, 300);

        // Hide notification dot
        const dot = document.querySelector('.notification-dot');
        if (dot) dot.style.display = 'none';
    }

    closeChatbot() {
        const widget = document.getElementById('chatbot-widget');
        widget.classList.remove('active');
        this.isOpen = false;
    }

    loadInitialMessage() {
        this.addMessage('Hi! I\'m your MediMerge assistant. How can I help you today?', 'bot');
    }

    sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        input.value = '';

        // Show typing indicator
        this.showTypingIndicator();

        // Simulate bot response
        setTimeout(() => {
            this.hideTypingIndicator();
            const response = this.getBotResponse(message);
            this.addMessage(response, 'bot');
        }, 1000 + Math.random() * 1000);
    }

    addMessage(text, sender) {
        const messages = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message message-${sender}`;
        
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        messageDiv.innerHTML = `
            ${text}
            <div class="message-time">${time}</div>
        `;
        
        messages.appendChild(messageDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    showTypingIndicator() {
        const messages = document.getElementById('chatbot-messages');
        const typing = document.createElement('div');
        typing.className = 'chatbot-message typing-indicator';
        typing.id = 'typing-indicator';
        typing.innerHTML = `
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        `;
        messages.appendChild(typing);
        messages.scrollTop = messages.scrollHeight;
    }

    hideTypingIndicator() {
        const typing = document.getElementById('typing-indicator');
        if (typing) {
            typing.remove();
        }
    }

    getBotResponse(message) {
        const lowerMessage = message.toLowerCase();
        
        // Product related queries
        if (lowerMessage.includes('product') || lowerMessage.includes('medicine')) {
            return 'We have a wide range of medicines and healthcare products. You can browse our products page or use the search function to find specific items!';
        }
        
        // Order related queries
        if (lowerMessage.includes('order') || lowerMessage.includes('track')) {
            return 'You can track your orders in your Dashboard. All orders are processed within 24 hours and you\'ll receive email updates!';
        }
        
        // Payment related queries
        if (lowerMessage.includes('payment') || lowerMessage.includes('upi') || lowerMessage.includes('card') || lowerMessage.includes('cod')) {
            return 'We accept all major credit/debit cards, UPI (Google Pay, PhonePe, Paytm), and Cash on Delivery. All online payments are secure and encrypted!';
        }
        
        // Delivery related queries
        if (lowerMessage.includes('delivery') || lowerMessage.includes('shipping')) {
            return 'We offer free delivery on all orders above ₹500. Standard delivery takes 3-5 business days. Express delivery is available for urgent orders!';
        }
        
        // Return/Refund queries
        if (lowerMessage.includes('return') || lowerMessage.includes('refund')) {
            return 'We have a 7-day return policy for unopened items. Contact our support team for assistance with returns and refunds.';
        }
        
        // Support queries
        if (lowerMessage.includes('help') || lowerMessage.includes('support') || lowerMessage.includes('contact')) {
            return 'I\'m here to help! For specific inquiries, contact our support team at support@medimerge.com or call +91-1234567890.';
        }
        
        // Price related queries
        if (lowerMessage.includes('price') || lowerMessage.includes('cost') || lowerMessage.includes('expensive')) {
            return 'Our prices are competitive and we often have special offers and discounts. Check our products page for current pricing and deals!';
        }
        
        // Health related queries
        if (lowerMessage.includes('health') || lowerMessage.includes('sick') || lowerMessage.includes('doctor')) {
            return 'I can help you find products, but for medical advice, please consult a healthcare professional. We recommend speaking with your doctor before starting any new medication.';
        }
        
        // Default response
        return 'Thank you for your question! I\'m here to help with products, orders, payments, delivery, and any other concerns. Feel free to ask anything specific!';
    }

    // Public method to add custom responses
    addCustomResponse(pattern, response) {
        if (!this.customResponses) {
            this.customResponses = [];
        }
        this.customResponses.push({ pattern, response });
    }

    // Method to show notification
    showNotification() {
        const dot = document.querySelector('.notification-dot');
        if (dot) {
            dot.style.display = 'block';
        }
    }
}

// Auto-initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.medimergeChatbot = new MediMergeChatbot();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MediMergeChatbot;
}
