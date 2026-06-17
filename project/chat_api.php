<?php
include 'includes/session.php';
include 'includes/db.php';

header('Content-Type: application/json');

$my_id = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// 1. RECUPERA MESSAGGI
if ($action === 'fetch' && isset($_GET['friend_id'])) {
    $friend_id = (int) $_GET['friend_id'];

    // Segna i messaggi ricevuti da questo amico come letti
    $update = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
    $update->execute([$friend_id, $my_id]);

    // Estrae la cronologia della conversazione
    $stmt = $pdo->prepare("SELECT * FROM messages 
                           WHERE (sender_id = ? AND receiver_id = ?) 
                              OR (sender_id = ? AND receiver_id = ?) 
                           ORDER BY created_at ASC");
    $stmt->execute([$my_id, $friend_id, $friend_id, $my_id]);
    $messages = $stmt->fetchAll();

    echo json_encode(['success' => true, 'messages' => $messages, 'my_id' => $my_id]);
    exit;
}

// 2. INVIA MESSAGGIO
if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $friend_id = isset($input['friend_id']) ? (int) $input['friend_id'] : 0;
    $message = isset($input['message']) ? trim($input['message']) : '';

    if ($friend_id > 0 && $message !== '') {
        // Verifica prima se siete amici accettati
        $check = $pdo->prepare("SELECT id FROM friends 
                                WHERE ((user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)) 
                                AND status = 'accepted'");
        $check->execute([$my_id, $friend_id, $friend_id, $my_id]);

        if ($check->rowCount() > 0) {
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$my_id, $friend_id, $message]);
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

echo json_encode(['success' => false]);
exit;