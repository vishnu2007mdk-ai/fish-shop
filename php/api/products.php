<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
            handleGetProducts($pdo);
            break;
        case 'POST':
            handleCreateProduct($pdo);
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

function handleGetProducts($pdo) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $priceMin = isset($_GET['price_min']) ? (float)$_GET['price_min'] : null;
    $priceMax = isset($_GET['price_max']) ? (float)$_GET['price_max'] : null;
    $featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : null;
    
    $offset = ($page - 1) * $limit;
    
    // Build the base query
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1=1";
    $params = [];
    
    // Add search filter
    if (!empty($search)) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    // Add category filter
    if (!empty($category)) {
        $sql .= " AND c.name = ?";
        $params[] = $category;
    }
    
    // Add price filters
    if ($priceMin !== null) {
        $sql .= " AND p.price >= ?";
        $params[] = $priceMin;
    }
    if ($priceMax !== null) {
        $sql .= " AND p.price <= ?";
        $params[] = $priceMax;
    }
    
    // Add featured filter
    if ($featured !== null) {
        $sql .= " AND p.featured = ?";
        $params[] = $featured ? 1 : 0;
    }
    
    // Add ordering and pagination
    $sql .= " ORDER BY p.featured DESC, p.name ASC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    // Execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE 1=1";
    
    if (!empty($search)) {
        $countSql .= " AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
    }
    if (!empty($category)) {
        $countSql .= " AND c.name = ?";
    }
    if ($priceMin !== null) {
        $countSql .= " AND p.price >= ?";
    }
    if ($priceMax !== null) {
        $countSql .= " AND p.price <= ?";
    }
    if ($featured !== null) {
        $countSql .= " AND p.featured = ?";
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(array_slice($params, 0, -2)); // Remove limit and offset
    $totalCount = $countStmt->fetch()['total'];
    
    // Format response
    $response = [
        'products' => $products,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => (int)$totalCount,
            'pages' => ceil($totalCount / $limit)
        ]
    ];
    
    echo json_encode($response);
}

function handleCreateProduct($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON input']);
        return;
    }
    
    // Validate required fields
    $required = ['name', 'price', 'category_id'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            return;
        }
    }
    
    // Insert new product
    $sql = "INSERT INTO products (name, description, price, category_id, stock_quantity, featured, image_url) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        $input['name'],
        $input['description'] ?? '',
        $input['price'],
        $input['category_id'],
        $input['stock_quantity'] ?? 0,
        $input['featured'] ?? false,
        $input['image_url'] ?? null
    ]);
    
    if ($success) {
        $productId = $pdo->lastInsertId();
        http_response_code(201);
        echo json_encode([
            'message' => 'Product created successfully',
            'product_id' => $productId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create product']);
    }
}
?>

