<?php
require_once '../config/session.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$userId = getCurrentUserId();

// ✅ Fix-1: Dynamic Role Check (Replace requireRole)
$role = $_SESSION['role'] ?? null;

if (!$role || !in_array($role, ['user', 'dietitian'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// ✅ Check Input Name 'avatar'
if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$uploadDir = '../../uploads/profile/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type']);
    exit;
}

// ✅ Fix-3: Dynamic Filename Prefix
$prefix = ($role === 'dietitian') ? 'dietitian' : 'user';
$filename = $prefix . '_' . $userId . '_' . time() . '.' . $ext;

$targetFile = $uploadDir . $filename;

if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
    $conn = getDBConnection();

    // ✅ Fix-2: Dynamic Table Selection
    $table = ($role === 'dietitian') ? 'dietitians' : 'users';

    // Safe Dynamic SQL (Table name is whitelisted via logic above)
    $stmt = $conn->prepare("UPDATE $table SET profile_picture=? WHERE id=?");
    $stmt->bind_param("si", $filename, $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'file' => $filename]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }

    closeDBConnection($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Upload failed']);
}
?>