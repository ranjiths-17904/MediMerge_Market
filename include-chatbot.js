// Simple script to include chatbot in all pages
(function() {
    // Check if chatbot is already loaded
    if (window.medimergeChatbot) return;
    
    // Load chatbot component
    const script = document.createElement('script');
    script.src = './chatbot-component.js';
    script.onload = function() {
        console.log('MediMerge Chatbot loaded successfully!');
    };
    script.onerror = function() {
        console.error('Failed to load MediMerge Chatbot');
    };
    document.head.appendChild(script);
})();
