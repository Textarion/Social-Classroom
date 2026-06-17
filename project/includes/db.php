<?php
// Parametri di connessione (default di XAMPP)
$host = 'localhost';
$db   = 'my_dsferrazza';
$user = 'root';
$pass = ''; // Di default su XAMPP è vuota
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Gestione errori
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Restituisce array associativi
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Sicurezza extra
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     // Decommenta la riga sotto solo per testare se funziona, poi eliminala
     // echo "Connessione al database riuscita!";
} catch (\PDOException $e) {
     // In produzione non mostrare l'errore dettagliato, usa un messaggio generico
     die("Errore di connessione al database: " . $e->getMessage());
}
?>