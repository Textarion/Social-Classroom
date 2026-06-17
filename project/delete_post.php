<?php
require_once 'includes/db.php';
require_once 'includes/session.php';

// 1. Controllo sicurezza: l'utente deve essere loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: modules/auth/login.php");
    exit;
}

// 2. Recupero e valido l'ID del post da eliminare
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: profile.php");
    exit;
}

$post_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// 3. Verifico se il post esiste e appartiene VERAMENTE all'utente loggato
$stmt = $pdo->prepare("SELECT user_id, file_path FROM contents WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    // Post non trovato
    header("Location: profile.php?error=notfound");
    exit;
}

if ($post['user_id'] !== $user_id) {
    // Tentativo di hackerare l'URL: l'utente prova a cancellare il post di un altro
    header("Location: profile.php?error=unauthorized");
    exit;
}

// 4. PULIZIA SERVER: Elimino il file fisico (es. il PDF o la dispensa) associato al post
if (!empty($post['file_path'])) {
    $file_to_delete = 'uploads/' . $post['file_path']; // Sostituisci con la tua cartella di upload materiali
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete);
    }
}

// 5. CANCELLAZIONE DAL DATABASE
// Nota: Se hai una tabella 'feedback' collegata, le recensioni di questo post vanno eliminate prima (o usa il CASCADE nel DB)
$delete_feedback = $pdo->prepare("DELETE FROM feedback WHERE content_id = ?");
$delete_feedback->execute([$post_id]);

$delete_post = $pdo->prepare("DELETE FROM contents WHERE id = ?");
$delete_post->execute([$post_id]);

// Reindirizzo al profilo con un messaggio di successo
header("Location: profile.php?success=deleted");
exit;