<?php
header('Content-Type: application/json');
$conn = require_once('db.php');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($action === 'register') {
        $username = $conn->real_escape_string($data['username']);
        $password = $conn->real_escape_string($data['password']);

        // Check if user exists
        $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($check->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username sudah dipakai.']);
            exit;
        }

        $sql = "INSERT INTO users (username, password, role, points) VALUES ('$username', '$password', 'customer', 0)";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true, 'message' => 'Registrasi berhasil!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mendaftar: ' . $conn->error]);
        }
    }

    if ($action === 'login') {
        $username = $conn->real_escape_string($data['username']);
        $password = $conn->real_escape_string($data['password']);

        $sql = "SELECT username, role, points FROM users WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Username atau password salah.']);
        }
    }

    if ($action === 'update_points') {
        $username = $conn->real_escape_string($data['username']);
        $points = (int)$data['points'];

        $sql = "UPDATE users SET points = $points WHERE username = '$username'";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
} else {
    if ($action === 'get_users') {
        $result = $conn->query("SELECT username, role, points FROM users WHERE role = 'customer' ORDER BY created_at DESC");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode(['success' => true, 'users' => $users]);
    }
}
