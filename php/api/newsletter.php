<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$pdo = getDBConnection();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
        exit();
    }
    
    $email = trim($input['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit();
    }
    
    // Check if email already exists
    $checkSql = "SELECT id, subscribed FROM newsletter_subscriptions WHERE email = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$email]);
    $existing = $checkStmt->fetch();
    
    if ($existing) {
        if ($existing['subscribed']) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already subscribed']);
        } else {
            // Resubscribe
            $updateSql = "UPDATE newsletter_subscriptions SET subscribed = TRUE, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$existing['id']]);
            
            echo json_encode([
                'message' => 'Successfully resubscribed to newsletter',
                'email' => $email
            ]);
        }
    } else {
        // New subscription
        $insertSql = "INSERT INTO newsletter_subscriptions (email) VALUES (?)";
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([$email]);
        
        http_response_code(201);
        echo json_encode([
            'message' => 'Successfully subscribed to newsletter',
            'email' => $email
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} finally {
    closeConnection($pdo);
}
?>

