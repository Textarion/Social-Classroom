<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se la variabile user_id non esiste nella sessione, l'utente NON è loggato
if (!isset($_SESSION['user_id'])) {
    // Lo buttiamo fuori rimandandolo al login
    header("Location: /modules/auth/login.php");
    exit;
}
?>