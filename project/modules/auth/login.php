<?php
require_once '../../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errore = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    usleep(600000); // 0.6 secondi per lo spinner
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && hash('sha256', $password) === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['ruolo'] = $user['ruolo'];
        $_SESSION['foto_profilo'] = $user['foto_profilo'];
        header("Location: /index.php");
        exit;
    } else {
        $errore = "Email o Password errate!";
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi - Social Classroom</title>
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
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
        }

        [data-bs-theme="dark"] .form-control {
            background-color: #161c2a;
            border-color: #242f47;
            color: white;
        }

        .btn-primary {
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            transition: all 0.3s;
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 100;
        }

        /* Schermata e animazione di Caricamento login (Overlay) */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(var(--bs-body-bg-rgb), 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 1050;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        body.is-loading #loadingOverlay {
            opacity: 1;
            pointer-events: auto;
        }

        body.is-loading .auth-container {
            transform: scale(0.95);
            filter: blur(4px);
            opacity: 0.5;
        }

        .custom-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(13, 110, 253, 0.1);
            border-left-color: #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <link rel="icon" type="image/png" href="/favicon.png">
</head>

<body class="d-flex align-items-center min-vh-100">

    <div id="loadingOverlay">
        <div class="custom-spinner mb-3"></div>
        <h5 class="fw-bold text-primary mb-1">Verifica credenziali...</h5>
        <p class="text-muted small mb-0">Preparazione della tua area di studio</p>
    </div>

    <div class="theme-toggle">
        <button id="themeToggle" class="btn btn-outline-secondary rounded-circle p-2">
            <i id="themeIcon" class="bi bi-moon-stars"></i>
        </button>
    </div>

    <div class="container auth-container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="auth-card card">
                    <div class="auth-header">
                        <img src="/assets/img/SC.png" alt="Logo" style="height: 50px;" class="mb-3">
                        <h4 class="fw-bold mb-0">Bentornato!</h4>
                        <p class="small opacity-75 mb-0">Accedi alla tua classe digitale</p>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <?php if ($errore): ?>
                            <div class="alert alert-danger py-2 small border-0 shadow-sm mb-4"><?php echo $errore; ?></div>
                        <?php endif; ?>

                        <form action="login.php" method="POST" id="loginForm">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="nome@esempio.it"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-bold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 shadow-sm mb-3">Accedi</button>
                            <div class="text-center">
                                <span class="small text-muted">Nuovo qui? <a href="register.php"
                                        class="text-decoration-none fw-bold page-link-transition">Crea un
                                        account</a></span>
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

        // Spinner sul submit del Form di Login
        document.getElementById('loginForm').addEventListener('submit', () => {
            document.body.classList.add('is-loading');
        });

        // Animazione Fade-Out al click sul link "Crea un account"
        document.querySelector('.page-link-transition').addEventListener('click', function (e) {
            e.preventDefault(); // Blocca il cambio pagina istantaneo
            const targetUrl = this.getAttribute('href');
            document.body.classList.add('page-leave'); // Attiva l'effetto dissolvenza in uscita

            setTimeout(() => {
                window.location.href = targetUrl; // Cambia pagina dopo 400ms (durata del CSS transition)
            }, 400);
        });
    </script>
</body>

</html>