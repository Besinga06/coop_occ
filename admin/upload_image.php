<?php
session_start();
require('db_connect.php'); // must create $db as mysqli

// Validate inputs
if (!isset($_POST['product_id']) || !isset($_FILES['fileToUpload'])) {
    exit('Invalid request');
}

$product_id = (int) $_POST['product_id'];

// 🔹 Fetch existing product image
$sql = "SELECT image FROM tbl_products WHERE product_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// 🔹 Delete old image if exists
if (!empty($product['image'])) {
    $oldPath = "../uploads/" . $product['image'];
    if (file_exists($oldPath)) {
        unlink($oldPath);
    }
}

// 🔹 Create new filename
$temp = explode(".", $_FILES["fileToUpload"]["name"]);
$extension = strtolower(end($temp));
$newfilename = md5($product_id) . "." . $extension;

// 🔹 Upload new file
if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], "../uploads/" . $newfilename)) {

    // 🔹 Update product image
    $update = "UPDATE tbl_products SET image = ? WHERE product_id = ?";
    $stmt = $db->prepare($update);
    $stmt->bind_param("si", $newfilename, $product_id);
    $stmt->execute();
    $stmt->close();

    // 🔹 Insert history log
    $historyData = json_encode([
        'product_id' => $product_id,
        'user_id'    => $_SESSION['user_id'] ?? 0
    ]);

    $history = "INSERT INTO tbl_history (details, history_type, date_history)
                VALUES (?, '14', NOW())";
    $stmt = $db->prepare($history);
    $stmt->bind_param("s", $historyData);
    $stmt->execute();
    $stmt->close();

    echo "1"; // success
} else {
    echo "0"; // upload failed
}
