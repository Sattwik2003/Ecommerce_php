<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

// Get the customer ID from the request
$data = json_decode(file_get_contents('php://input'), true);
$customer_id = $data['customer_id'] ?? null;

if ($customer_id) {
    $result = $conn->query("DELETE FROM cart WHERE customer_id=$customer_id");
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid customer ID']);
}
?>