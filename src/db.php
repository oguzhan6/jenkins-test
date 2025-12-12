<?php
header('Content-Type: application/json');

function getDbConnection() {
    $host = 'mysql';
    $dbname = 'mydatabase';
    $user = 'myuser';
    $pass = 'mypassword';

    $attempts = 5;
    $delay = 2; // seconds

    for ($i = 0; $i < $attempts; $i++) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // If this is the last attempt, re-throw the exception.
            if ($i === $attempts - 1) {
                // In a real app, you'd log this error and show a generic message
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Database connection failed after multiple attempts.']);
                exit;
            }
            sleep($delay);
        }
    }
}
