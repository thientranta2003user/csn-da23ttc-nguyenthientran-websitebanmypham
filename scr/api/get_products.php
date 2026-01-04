<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

// Xử lý lấy danh mục nếu có tham số action=categories
if (isset($_GET['action']) && $_GET['action'] === 'categories') {
    $sql = "SELECT * FROM categories ORDER BY id";
    $result = $conn->query($sql);
    
    $categories = array();
    while ($row = $result->fetch_assoc()) {
        $categories[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'slug' => $row['slug'],
            'icon' => $row['icon']
        );
    }
    
    echo json_encode($categories, JSON_UNESCAPED_UNICODE);
    $conn->close();
    exit;
}

$category = isset($_GET['category']) ? $_GET['category'] : 'all';

if ($category === 'all') {
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE c.slug = ? 
            ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

$products = array();
while ($row = $result->fetch_assoc()) {
    $products[] = array(
        'id' => $row['id'],
        'name' => $row['name'],
        'category' => $row['category_name'],
        'categorySlug' => $row['category_slug'],
        'description' => $row['description'],
        'price' => number_format($row['price'], 0, ',', '.') . 'đ',
        'priceRaw' => $row['price'],
        'image' => $row['image'],
        'stock' => $row['stock']
    );
}

echo json_encode($products, JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>
