<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

class MediMergeChatbot {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function processMessage($message, $userId = null, $sessionId = null) {
        $message = strtolower(trim($message));
        
        // Store user message
        if ($userId || $sessionId) {
            $this->storeMessage($userId, $sessionId, $message, '');
        }
        
        // Process message and generate response
        $response = $this->generateResponse($message);
        
        // Store bot response
        if ($userId || $sessionId) {
            $this->updateResponse($userId, $sessionId, $message, $response);
        }
        
        return $response;
    }
    
    private function generateResponse($message) {
        // Product-related queries
        if (strpos($message, 'product') !== false || strpos($message, 'medicine') !== false) {
            if (strpos($message, 'pain') !== false || strpos($message, 'headache') !== false) {
                return "For pain relief, we recommend Paracetamol 500mg or Aspirin 100mg. These are effective for headaches and general pain. Would you like me to show you our pain relief products?";
            }
            if (strpos($message, 'cold') !== false || strpos($message, 'cough') !== false) {
                return "For cold and cough symptoms, we have Equate Cold & Flu Relief and Vicks VapoRub. These provide multi-symptom relief. Should I show you our cold & cough products?";
            }
            if (strpos($message, 'vitamin') !== false || strpos($message, 'supplement') !== false) {
                return "We offer Vitamin C 1000mg and Creatine Monohydrate for your health needs. Vitamins boost immunity while supplements support fitness goals. Would you like to see our vitamin collection?";
            }
            if (strpos($message, 'diabetes') !== false || strpos($message, 'blood sugar') !== false) {
                return "For diabetes management, we have Diabetes Management tablets and Insulin Injection. These help control blood sugar levels. Should I show you our diabetes care products?";
            }
            return "We have a comprehensive range of products including pain relief, cold & cough medicines, vitamins, diabetes care, and first aid supplies. What specific category are you looking for?";
        }
        
        // Order and delivery queries
        if (strpos($message, 'order') !== false || strpos($message, 'delivery') !== false) {
            if (strpos($message, 'track') !== false) {
                return "To track your order, please provide your order ID. You can find it in your order confirmation email or in your dashboard.";
            }
            if (strpos($message, 'delivery time') !== false || strpos($message, 'shipping') !== false) {
                return "Standard delivery takes 3-5 business days. Express delivery (1-2 days) is available for an additional fee. We deliver to all major cities and towns.";
            }
            return "Orders are typically processed within 24 hours and delivered in 3-5 business days. You can track your order status in your dashboard. Need help with anything specific?";
        }
        
        // Payment queries
        if (strpos($message, 'payment') !== false || strpos($message, 'pay') !== false) {
            if (strpos($message, 'method') !== false || strpos($message, 'card') !== false) {
                return "We accept all major credit/debit cards, UPI, net banking, and digital wallets. All payments are secure and encrypted. You can also pay on delivery for orders under ₹1000.";
            }
            if (strpos($message, 'refund') !== false || strpos($message, 'return') !== false) {
                return "We offer easy returns and refunds within 7 days of delivery. Damaged or incorrect items are replaced immediately. Contact our support team for assistance.";
            }
            return "We offer multiple secure payment options including cards, UPI, net banking, and digital wallets. All transactions are protected with bank-level security.";
        }
        
        // General health queries
        if (strpos($message, 'health') !== false || strpos($message, 'medical') !== false) {
            if (strpos($message, 'emergency') !== false) {
                return "For medical emergencies, please call emergency services immediately (112 in India). Our products are for general health and wellness, not emergency medical care.";
            }
            if (strpos($message, 'consultation') !== false || strpos($message, 'doctor') !== false) {
                return "While we provide quality health products, we recommend consulting a healthcare professional for medical advice. Our team can help you find the right products based on your needs.";
            }
            return "We're here to help with your health and wellness needs. We offer quality products, but always consult healthcare professionals for medical advice. How can I assist you today?";
        }
        
        // Contact and support
        if (strpos($message, 'contact') !== false || strpos($message, 'help') !== false || strpos($message, 'support') !== false) {
            return "Our customer support team is available 24/7. You can reach us at support@medimerge.com or call +91-1800-123-4567. We're here to help with any questions or concerns.";
        }
        
        // Greetings
        if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false || strpos($message, 'hey') !== false) {
            return "Hello! Welcome to MediMerge. I'm your health assistant. I can help you find products, track orders, answer questions about payments, and provide general health information. How can I help you today?";
        }
        
        // Thanks
        if (strpos($message, 'thank') !== false || strpos($message, 'thanks') !== false) {
            return "You're welcome! I'm here to help. Is there anything else you'd like to know about our products or services?";
        }
        
        // Default response
        return "I'm here to help with your MediMerge experience! I can assist with product information, orders, payments, delivery, and general health queries. What would you like to know?";
    }
    
    private function storeMessage($userId, $sessionId, $message, $response) {
        $sql = "INSERT INTO chat_messages (user_id, session_id, message, response) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isss", $userId, $sessionId, $message, $response);
        $stmt->execute();
        $stmt->close();
    }
    
    private function updateResponse($userId, $sessionId, $message, $response) {
        $sql = "UPDATE chat_messages SET response = ? WHERE user_id = ? AND session_id = ? AND message = ? ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $response, $userId, $sessionId, $message);
        $stmt->execute();
        $stmt->close();
    }
    
    public function getChatHistory($userId = null, $sessionId = null, $limit = 10) {
        $sql = "SELECT * FROM chat_messages WHERE ";
        $params = [];
        $types = "";
        
        if ($userId) {
            $sql .= "user_id = ? ";
            $params[] = $userId;
            $types .= "i";
        } elseif ($sessionId) {
            $sql .= "session_id = ? ";
            $params[] = $sessionId;
            $types .= "s";
        } else {
            return [];
        }
        
        $sql .= "ORDER BY timestamp DESC LIMIT ?";
        $params[] = $limit;
        $types .= "i";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        
        $stmt->close();
        return array_reverse($history);
    }
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $action = $input['action'] ?? '';
    $message = $input['message'] ?? '';
    $userId = $input['user_id'] ?? null;
    $sessionId = $input['session_id'] ?? uniqid();
    
    if (empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Message is required'
        ]);
        exit;
    }
    
    $chatbot = new MediMergeChatbot($conn);
    
    switch ($action) {
        case 'send_message':
            $response = $chatbot->processMessage($message, $userId, $sessionId);
            echo json_encode([
                'success' => true,
                'response' => $response,
                'session_id' => $sessionId
            ]);
            break;
            
        case 'get_history':
            $history = $chatbot->getChatHistory($userId, $sessionId);
            echo json_encode([
                'success' => true,
                'history' => $history
            ]);
            break;
            
        default:
            $response = $chatbot->processMessage($message, $userId, $sessionId);
            echo json_encode([
                'success' => true,
                'response' => $response,
                'session_id' => $sessionId
            ]);
            break;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests are allowed'
    ]);
}

$conn->close();
?>
