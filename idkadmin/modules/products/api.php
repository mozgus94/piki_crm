<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../includes/connect.php");

$action = $_GET['action'] ?? '';

if ($action === 'fetch') {
    try {
        $query = "SELECT product_id, product_sku, product_name, product_price, mpc_price, product_image FROM idk_product WHERE product_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($products);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

if ($action === 'sync') {
    // Sync logic here
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Invalid action']);
