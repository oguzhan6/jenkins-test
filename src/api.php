<?php
require_once 'db.php';

$pdo = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $pdo->query("SELECT id, username, message, created_at FROM comments ORDER BY created_at DESC");
        $comments = $stmt->fetchAll();
        echo json_encode($comments);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve comments.']);
    }
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['username']) || empty($data['message'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Username and message are required.']);
        exit;
    }

    $username = htmlspecialchars(strip_tags($data['username']));
    $message = htmlspecialchars(strip_tags($data['message']));

    try {
        $sql = "INSERT INTO comments (username, message) VALUES (:username, :message)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username, 'message' => $message]);

        http_response_code(201);
        echo json_encode(['status' => 'success', 'message' => 'Comment added successfully.']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to add comment.']);
    }
    exit;
}

// If not GET or POST, method not allowed
http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
