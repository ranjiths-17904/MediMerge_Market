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
        // Check if chatbot already exists to prevent duplicates
        if (document.querySelector('.medimerge-chatbot')) {
            return;
        }
        
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
                    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    display: none;
                    flex-direction: column;
                    overflow: hidden;
                }

                .chatbot-widget.open {
                    display: flex;
                    animation: slideUp 0.3s ease;
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
                    justify-content: space-between;
                    align-items: center;
                }

                .header-content {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }

                .chatbot-logo {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                }

                .header-text h3 {
                    margin: 0;
                    font-size: 18px;
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
                    font-size: 20px;
                    cursor: pointer;
                    padding: 5px;
                    border-radius: 50%;
                    transition: all 0.3s ease;
                }

                .chatbot-close:hover {
                    background: rgba(255,255,255,0.2);
                    transform: scale(1.1);
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
                }

                .message.user {
                    background: #f0f0f0;
                    color: #333;
                    align-self: flex-end;
                    border-bottom-right-radius: 6px;
                }

                .message.bot {
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    color: white;
                    align-self: flex-start;
                    border-bottom-left-radius: 6px;
                }

                .chatbot-input {
                    padding: 20px;
                    border-top: 1px solid #eee;
                    display: flex;
                    gap: 10px;
                }

                .chatbot-input input {
                    flex: 1;
                    padding: 12px 16px;
                    border: 2px solid #e8e8e8;
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
                    background: linear-gradient(135deg, #11b671, #0ea55d);
                    border: none;
                    border-radius: 50%;
                    width: 44px;
                    height: 44px;
                    color: white;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .chatbot-input button:hover {
                    transform: scale(1.1);
                    box-shadow: 0 4px 12px rgba(17, 182, 113, 0.3);
                }

                .chatbot-input button:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                    transform: none;
                }

                /* Responsive Design */
                @media (max-width: 480px) {
                    .chatbot-widget {
                        width: 320px;
                        height: 450px;
                        right: -10px;
                    }
                    
                    .chatbot-toggle {
                        width: 55px;
                        height: 55px;
                        font-size: 22px;
                    }
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    setupEventListeners() {
        const toggle = document.getElementById('chatbot-toggle');
        const widget = document.getElementById('chatbot-widget');
        const close = document.getElementById('chatbot-close');
        const input = document.getElementById('chatbot-input');
        const send = document.getElementById('chatbot-send');

        toggle.addEventListener('click', () => {
            this.toggleChatbot();
        });

        close.addEventListener('click', () => {
            this.closeChatbot();
        });

        send.addEventListener('click', () => {
            this.sendMessage();
        });

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });

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
        const widget = document.getElementById('chatbot-widget');
        widget.classList.add('open');
        this.isOpen = true;
        
        // Focus on input
        setTimeout(() => {
            document.getElementById('chatbot-input').focus();
        }, 300);
    }

    closeChatbot() {
        const widget = document.getElementById('chatbot-widget');
        widget.classList.remove('open');
        this.isOpen = false;
    }

    loadInitialMessage() {
        const messages = document.getElementById('chatbot-messages');
        const welcomeMessage = `
            <div class="message bot">
                Hello! 👋 I'm your MediMerge assistant. How can I help you today? You can ask me about:
                <br>• Product information
                <br>• Order status
                <br>• Payment methods
                <br>• Shipping details
                <br>• General questions
            </div>
        `;
        messages.innerHTML = welcomeMessage;
    }

    sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        input.value = '';

        // Disable send button while processing
        const sendBtn = document.getElementById('chatbot-send');
        sendBtn.disabled = true;

        // Simulate bot response
        setTimeout(() => {
            const response = this.generateResponse(message);
            this.addMessage(response, 'bot');
            sendBtn.disabled = false;
        }, 1000);
    }

    addMessage(text, sender) {
        const messages = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        messageDiv.textContent = text;
        
        messages.appendChild(messageDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    generateResponse(userMessage) {
        const message = userMessage.toLowerCase();
        
        if (message.includes('product') || message.includes('medicine')) {
            return "We offer a wide range of healthcare products including medicines, supplements, and medical devices. You can browse our products on the Products page or use the search function to find specific items.";
        } else if (message.includes('order') || message.includes('track')) {
            return "To track your order, please check your order confirmation email or contact our customer support. You can also view your order history in your dashboard.";
        } else if (message.includes('payment') || message.includes('pay')) {
            return "We accept various payment methods including credit/debit cards, net banking, UPI, and cash on delivery. All online payments are secure and encrypted.";
        } else if (message.includes('shipping') || message.includes('delivery')) {
            return "We offer fast and reliable delivery across India. Standard delivery takes 2-3 business days. Express delivery is available for select locations.";
        } else if (message.includes('return') || message.includes('refund')) {
            return "We have a hassle-free return policy. If you're not satisfied with your purchase, you can return it within 7 days of delivery. Contact our support team for assistance.";
        } else if (message.includes('contact') || message.includes('support')) {
            return "You can reach our customer support team at support@medimerge.com or call us at +91-XXXXXXXXXX. We're available 24/7 to help you.";
        } else if (message.includes('hello') || message.includes('hi') || message.includes('hey')) {
            return "Hello! How can I assist you today? Feel free to ask any questions about our products or services.";
        } else {
            return "Thank you for your message. I'm here to help with any questions about MediMerge. You can ask about products, orders, payments, shipping, or general inquiries.";
        }
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector('.medimerge-chatbot')) {
        new MediMergeChatbot();
    }
});
