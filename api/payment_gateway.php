<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

class PaymentGateway {
    private $conn;
    private $apiKey = 'test_key_12345'; // Replace with actual payment gateway API key
    private $merchantId = 'MEDIMERGE001';
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function processPayment($paymentData) {
        try {
            $amount = floatval($paymentData['amount']);
            $paymentMethod = $paymentData['paymentMethod'];
            $orderId = $paymentData['orderId'];
            $customerData = $paymentData['customer'];
            
            // Validate payment data
            if (!$this->validatePaymentData($paymentData)) {
                throw new Exception('Invalid payment data');
            }
            
            // Process payment based on method
            $paymentResult = $this->processPaymentMethod($paymentMethod, $paymentData);
            
            if ($paymentResult['success']) {
                // Update order payment status
                $this->updateOrderPaymentStatus($orderId, 'completed', $paymentResult['transaction_id']);
                
                // Send receipt via SMS
                $this->sendReceiptSMS($customerData['phone'], $orderId, $amount, $paymentResult['transaction_id']);
                
                return [
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'transaction_id' => $paymentResult['transaction_id'],
                    'order_id' => $orderId
                ];
            } else {
                throw new Exception($paymentResult['message']);
            }
            
        } catch (Exception $e) {
            // Update order payment status to failed
            if (isset($orderId)) {
                $this->updateOrderPaymentStatus($orderId, 'failed');
            }
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function validatePaymentData($paymentData) {
        $required = ['amount', 'paymentMethod', 'orderId', 'customer'];
        
        foreach ($required as $field) {
            if (!isset($paymentData[$field])) {
                return false;
            }
        }
        
        if (empty($paymentData['amount']) || $paymentData['amount'] <= 0) {
            return false;
        }
        
        if (empty($paymentData['customer']['phone'])) {
            return false;
        }
        
        return true;
    }
    
    private function processPaymentMethod($method, $paymentData) {
        switch ($method) {
            case 'card':
                return $this->processCardPayment($paymentData);
            case 'upi':
                return $this->processUPIPayment($paymentData);
            case 'netbanking':
                return $this->processNetBankingPayment($paymentData);
            case 'wallet':
                return $this->processWalletPayment($paymentData);
            case 'cod':
                return $this->processCODPayment($paymentData);
            default:
                throw new Exception('Unsupported payment method');
        }
    }
    
    private function processCardPayment($paymentData) {
        // Simulate card payment processing
        $cardData = $paymentData['cardData'] ?? [];
        
        if (empty($cardData['cardNumber']) || empty($cardData['expiry']) || empty($cardData['cvv'])) {
            throw new Exception('Invalid card details');
        }
        
        // Simulate payment gateway API call
        $transactionId = 'TXN_' . time() . '_' . rand(1000, 9999);
        
        // Simulate processing delay
        usleep(500000); // 0.5 seconds
        
        // Simulate success (90% success rate for demo)
        if (rand(1, 10) <= 9) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Card payment processed successfully'
            ];
        } else {
            throw new Exception('Card payment failed. Please try again.');
        }
    }
    
    private function processUPIPayment($paymentData) {
        $upiData = $paymentData['upiData'] ?? [];
        
        if (empty($upiData['upiId'])) {
            throw new Exception('Invalid UPI ID');
        }
        
        $transactionId = 'UPI_' . time() . '_' . rand(1000, 9999);
        
        // Simulate UPI processing
        usleep(300000); // 0.3 seconds
        
        if (rand(1, 10) <= 9) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'UPI payment processed successfully'
            ];
        } else {
            throw new Exception('UPI payment failed. Please try again.');
        }
    }
    
    private function processNetBankingPayment($paymentData) {
        $bankData = $paymentData['bankData'] ?? [];
        
        if (empty($bankData['bankCode'])) {
            throw new Exception('Invalid bank selection');
        }
        
        $transactionId = 'NB_' . time() . '_' . rand(1000, 9999);
        
        // Simulate net banking processing
        usleep(400000); // 0.4 seconds
        
        if (rand(1, 10) <= 9) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Net banking payment processed successfully'
            ];
        } else {
            throw new Exception('Net banking payment failed. Please try again.');
        }
    }
    
    private function processWalletPayment($paymentData) {
        $walletData = $paymentData['walletData'] ?? [];
        
        if (empty($walletData['walletType'])) {
            throw new Exception('Invalid wallet selection');
        }
        
        $transactionId = 'WLT_' . time() . '_' . rand(1000, 9999);
        
        // Simulate wallet processing
        usleep(200000); // 0.2 seconds
        
        if (rand(1, 10) <= 9) {
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Wallet payment processed successfully'
            ];
        } else {
            throw new Exception('Wallet payment failed. Please try again.');
        }
    }
    
    private function processCODPayment($paymentData) {
        // Cash on Delivery - no immediate payment processing needed
        $transactionId = 'COD_' . time() . '_' . rand(1000, 9999);
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'message' => 'Cash on Delivery order confirmed'
        ];
    }
    
    private function updateOrderPaymentStatus($orderId, $status, $transactionId = null) {
        $sql = "UPDATE orders SET payment_status = ?, transaction_id = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $status, $transactionId, $orderId);
        $stmt->execute();
        $stmt->close();
    }
    
    private function sendReceiptSMS($phone, $orderId, $amount, $transactionId) {
        // Simulate SMS sending
        $message = "Thank you for your order! Order ID: $orderId, Amount: ₹$amount, Transaction ID: $transactionId. Your order will be delivered soon. - MediMerge";
        
        // In production, integrate with actual SMS gateway like Twilio, MSG91, etc.
        // For now, we'll just log the message
        error_log("SMS sent to $phone: $message");
        
        return true;
    }
    
    public function getPaymentMethods() {
        return [
            [
                'id' => 'card',
                'name' => 'Credit/Debit Card',
                'icon' => 'fas fa-credit-card',
                'description' => 'Pay with Visa, MasterCard, American Express, RuPay',
                'processing_fee' => 0,
                'min_amount' => 1,
                'max_amount' => 100000
            ],
            [
                'id' => 'upi',
                'name' => 'UPI',
                'icon' => 'fas fa-mobile-alt',
                'description' => 'Pay with any UPI app (GPay, PhonePe, Paytm, etc.)',
                'processing_fee' => 0,
                'min_amount' => 1,
                'max_amount' => 100000
            ],
            [
                'id' => 'netbanking',
                'name' => 'Net Banking',
                'icon' => 'fas fa-university',
                'description' => 'Pay with your bank account',
                'processing_fee' => 0,
                'min_amount' => 1,
                'max_amount' => 100000
            ],
            [
                'id' => 'wallet',
                'name' => 'Digital Wallet',
                'icon' => 'fas fa-wallet',
                'description' => 'Pay with Paytm, PhonePe, Amazon Pay, etc.',
                'processing_fee' => 0,
                'min_amount' => 1,
                'max_amount' => 10000
            ],
            [
                'id' => 'cod',
                'name' => 'Cash on Delivery',
                'icon' => 'fas fa-money-bill-wave',
                'description' => 'Pay when you receive your order',
                'processing_fee' => 50,
                'min_amount' => 1,
                'max_amount' => 2000
            ]
        ];
    }
    
    public function validateCard($cardNumber, $expiry, $cvv) {
        // Basic card validation
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return ['valid' => false, 'message' => 'Invalid card number length'];
        }
        
        if (!preg_match('/^\d{2}\/\d{2}$/', $expiry)) {
            return ['valid' => false, 'message' => 'Invalid expiry format (MM/YY)'];
        }
        
        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            return ['valid' => false, 'message' => 'Invalid CVV'];
        }
        
        // Check expiry date
        $expiryParts = explode('/', $expiry);
        $month = intval($expiryParts[0]);
        $year = intval($expiryParts[1]);
        
        if ($month < 1 || $month > 12) {
            return ['valid' => false, 'message' => 'Invalid month'];
        }
        
        $currentYear = intval(date('y'));
        if ($year < $currentYear) {
            return ['valid' => false, 'message' => 'Card has expired'];
        }
        
        return ['valid' => true, 'message' => 'Card details are valid'];
    }
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $action = $input['action'] ?? '';
    $paymentGateway = new PaymentGateway($conn);
    
    switch ($action) {
        case 'process_payment':
            $result = $paymentGateway->processPayment($input);
            echo json_encode($result);
            break;
            
        case 'get_payment_methods':
            $methods = $paymentGateway->getPaymentMethods();
            echo json_encode([
                'success' => true,
                'methods' => $methods
            ]);
            break;
            
        case 'validate_card':
            $cardNumber = $input['cardNumber'] ?? '';
            $expiry = $input['expiry'] ?? '';
            $cvv = $input['cvv'] ?? '';
            
            $validation = $paymentGateway->validateCard($cardNumber, $expiry, $cvv);
            echo json_encode($validation);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
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
