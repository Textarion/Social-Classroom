<?php
require_once '../../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$messaggio = "";
$tipo_alert = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $ruolo = $_POST['ruolo'];
    $bio = trim($_POST['bio']);

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->execute([$email, $username]);

    if ($check->rowCount() > 0) {
        $messaggio = "Username o Email già utilizzati!";
        $tipo_alert = "danger";
    } else {
        $password_hash = hash('sha256', $password);
        $sql = "INSERT INTO users (username, email, password, ruolo, bio, foto_profilo) VALUES (?, ?, ?, ?, ?, 'default.png')";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([$username, $email, $password_hash, $ruolo, $bio]);
            $messaggio = "Registrazione completata! Verrai reindirizzato...";
            $tipo_alert = "success";
            header("Refresh:2; url=login.php");
        } catch (PDOException $e) {
            $messaggio = "Errore: " . $e->getMessage();
            $tipo_alert = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrati - Social Classroom</title>
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bs-tertiary-bg);
            transition: background-color 0.3s ease;
            overflow-x: hidden;
        }

        /* Animazioni di transizione pagina */
        .auth-container {
            opacity: 0;
            transform: scale(0.98);
            transition: opacity 0.4s ease, transform 0.4s ease, filter 0.4s ease;
        }

        body.page-ready .auth-container {
            opacity: 1;
            transform: scale(1);
        }

        body.page-leave .auth-container {
            opacity: 0;
            transform: scale(0.96);
            filter: blur(5px);
        }

        .auth-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, #198754, #157347);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #dee2e6;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #161c2a;
            border-color: #242f47;
            color: white;
        }

        .btn-success {
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 100;
        }
    </style>
    <link rel="icon" type="image/png" href="/favicon.png">
</head>

<body class="py-5">

    <div class="theme-toggle">
        <button id="themeToggle" class="btn btn-outline-secondary rounded-circle p-2">
            <i id="themeIcon" class="bi bi-moon-stars"></i>
        </button>
    </div>

    <div class="container auth-container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="auth-card card">
                    <div class="auth-header">
                        <h4 class="fw-bold mb-0">Unisciti a noi</h4>
                        <p class="small opacity-75 mb-0">Condividi e impara con la community</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($messaggio): ?>
                            <div class="alert alert-<?php echo $tipo_alert; ?> py-2 small border-0 shadow-sm mb-4">
                                <?php echo $messaggio; ?>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="LucaV"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">Chi sei?</label>
                                    <select name="ruolo" class="form-select" required>
                                        <option value="studente">Studente</option>
                                        <option value="docente">Docente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="email@scuola.it"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Password</label>
                                <input type="password" name="password" class="form-control" minlength="6"
                                    placeholder="Minimo 6 caratteri" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Bio</label>
                                <textarea name="bio" class="form-control" rows="2"
                                    placeholder="Qualcosa su di te..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100 shadow-sm mb-3">Registrati</button>
                            <div class="text-center">
                                <span class="small text-muted">Hai già un account? <a href="login.php"
                                        class="text-decoration-none fw-bold text-success page-link-transition">Accedi</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Effetto Fade-In all'avvio della pagina
        window.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('page-ready');
        });

        // Gestione Tema
        const btn = document.getElementById('themeToggle');
        const icon = document.getElementById('themeIcon');
        btn.onclick = () => {
            const current = document.documentElement.getAttribute('data-bs-theme');
            const target = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', target);
            localStorage.setItem('theme', target);
            icon.className = target === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        };
        if (document.documentElement.getAttribute('data-bs-theme') === 'dark') icon.className = 'bi bi-sun';

        // Animazione Fade-Out al click sul link "Accedi"
        document.querySelector('.page-link-transition').addEventListener('click', function (e) {
            e.preventDefault();
            const targetUrl = this.getAttribute('href');
            document.body.classList.add('page-leave');

            setTimeout(() => {
                window.location.href = targetUrl;
            }, 400);
        });
    </script>
</body>

</html>