<?php
include 'includes/session.php';
include 'includes/db.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $my_id = $_SESSION['user_id'];
    $target_id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($my_id !== $target_id) {
        if ($action === 'add') {
            // Invia richiesta di amicizia
            $stmt = $pdo->prepare("INSERT IGNORE INTO friends (user_id, friend_id, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$my_id, $target_id]);
        } elseif ($action === 'accept') {
            // Accetta richiesta ricevuta
            $stmt = $pdo->prepare("UPDATE friends SET status = 'accepted' WHERE user_id = ? AND friend_id = ?");
            $stmt->execute([$target_id, $my_id]);
        } elseif ($action === 'remove') {
            // Rimuove o rifiuta l'amicizia (in entrambe le direzioni)
            $stmt = $pdo->prepare("DELETE FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
            $stmt->execute([$my_id, $target_id, $target_id, $my_id]);
        }
    }
    
    // Torna al profilo dell'utente visitato
    header("Location: /profile.php?id=" . $target_id);
    exit;
}

header("Location: /index.php");
exit;