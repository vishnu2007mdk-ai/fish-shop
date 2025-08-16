<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$pdo = getDBConnection();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGetCart($pdo);
            break;
        case 'POST':
            handleAddToCart($pdo);
            break;
        case 'PUT':
            handleUpdateCart($pdo);
            break;
        case 'DELETE':
            handleRemoveFromCart($pdo);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} finally {
    closeConnection($pdo);
}

function handleGetCart($pdo) {
    $sessionId = $_GET['session_id'] ?? '';
    
    if (empty($sessionId)) {
        echo json_encode(['cart' => [], 'total' => 0]);
        return;
    }
    
    $sql = "SELECT gc.*, p.name, p.price, p.image_url, p.in_stock 
            FROM guest_cart gc 
            JOIN products p ON gc.product_id = p.id 
            WHERE gc.session_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sessionId]);
    $cartItems = $stmt->fetchAll();
    
    $total = 0;
    foreach ($cartItems as &$item) {
        $item['total_price'] = $item['price'] * $item['quantity'];
        $total += $item['total_price'];
    }
    
    echo json_encode([
        'cart' => $cartItems,
        'total' => round($total, 2)
    ]);
}

function handleAddToCart($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['session_id']) || !isset($input['product_id']) || !isset($input['quantity'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Session ID, product ID, and quantity are required']);
        return;
    }
    
    $sessionId = $input['session_id'];
    $productId = $input['product_id'];
    $quantity = (int)$input['quantity'];
    
    if ($quantity <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Quantity must be greater than 0']);
        return;
    }
    
    // Check if product exists and is in stock
    $productSql = "SELECT id, name, price, stock_quantity, in_stock FROM products WHERE id = ?";
    $productStmt = $pdo->prepare($productSql);
    $productStmt->execute([$productId]);
    $product = $productStmt->fetch();
    
    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        return;
    }
    
    if (!$product['in_stock'] || $product['stock_quantity'] < $quantity) {
        http_response_code(400);
        echo json_encode(['error' => 'Product not available in requested quantity']);
        return;
    }
    
    // Check if item already exists in cart
    $existingSql = "SELECT id, quantity FROM guest_cart WHERE session_id = ? AND product_id = ?";
    $existingStmt = $pdo->prepare($existingSql);
    $existingStmt->execute([$sessionId, $productId]);
    $existing = $existingStmt->fetch();
    
    if ($existing) {
        // Update quantity
        $newQuantity = $existing['quantity'] + $quantity;
        if ($newQuantity > $product['stock_quantity']) {
            http_response_code(400);
            echo json_encode(['error' => 'Requested quantity exceeds available stock']);
            return;
        }
        
        $updateSql = "UPDATE guest_cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$newQuantity, $existing['id']]);
        
        echo json_encode([
            'message' => 'Cart updated successfully',
            'quantity' => $newQuantity
        ]);
    } else {
        // Add new item
        $insertSql = "INSERT INTO guest_cart (session_id, product_id, quantity) VALUES (?, ?, ?)";
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([$sessionId, $productId, $quantity]);
        
        http_response_code(201);
        echo json_encode([
            'message' => 'Item added to cart successfully',
            'quantity' => $quantity
        ]);
    }
}

function handleUpdateCart($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['session_id']) || !isset($input['product_id']) || !isset($input['quantity'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Session ID, product ID, and quantity are required']);
        return;
    }
    
    $sessionId = $input['session_id'];
    $productId = $input['product_id'];
    $quantity = (int)$input['quantity'];
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0 or negative
        handleRemoveFromCart($pdo);
        return;
    }
    
    // Check if product is available
    $productSql = "SELECT stock_quantity, in_stock FROM products WHERE id = ?";
    $productStmt = $pdo->prepare($productSql);
    $productStmt->execute([$productId]);
    $product = $productStmt->fetch();
    
    if (!$product || !$product['in_stock'] || $product['stock_quantity'] < $quantity) {
        http_response_code(400);
        echo json_encode(['error' => 'Product not available in requested quantity']);
        return;
    }
    
    // Update cart item
    $updateSql = "UPDATE guest_cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE session_id = ? AND product_id = ?";
    $updateStmt = $pdo->prepare($updateSql);
    $result = $updateStmt->execute([$quantity, $sessionId, $productId]);
    
    if ($result && $updateStmt->rowCount() > 0) {
        echo json_encode([
            'message' => 'Cart updated successfully',
            'quantity' => $quantity
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Cart item not found']);
    }
}

function handleRemoveFromCart($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['session_id']) || !isset($input['product_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Session ID and product ID are required']);
        return;
    }
    
    $sessionId = $input['session_id'];
    $productId = $input['product_id'];
    
    $deleteSql = "DELETE FROM guest_cart WHERE session_id = ? AND product_id = ?";
    $deleteStmt = $pdo->prepare($deleteSql);
    $result = $deleteStmt->execute([$sessionId, $productId]);
    
    if ($result && $deleteStmt->rowCount() > 0) {
        echo json_encode(['message' => 'Item removed from cart successfully']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Cart item not found']);
    }
}
?>

