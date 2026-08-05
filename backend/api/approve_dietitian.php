<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
requireRole('admin');
$id = $_POST['id'] ?? 0;
$conn = getDBConnection();
$result = $conn->query("SELECT * FROM pending_dietitians WHERE id = $id");
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $stmt = $conn->prepare("INSERT INTO dietitians (full_name, email, phone, password, passing_institute, degrees, experience, specialization, profile_picture, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssssssss", $data['full_name'], $data['email'], $data['phone'], $data['password'], $data['passing_institute'], $data['degrees'], $data['experience'], $data['specialization'], $data['profile_picture']);
    if ($stmt->execute()) {
        $conn->query("DELETE FROM pending_dietitians WHERE id = $id");
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => true]);
    } else {
        $stmt->close();
        closeDBConnection($conn);
        echo json_encode(['success' => false]);
    }
} else {
    closeDBConnection($conn);
    echo json_encode(['success' => false]);
}
?>
