<?php
// Assicuriamoci che la sessione sia attiva per evitare errori
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$unread_messages_count = 0;

// Se l'utente è loggato, calcoliamo quanti messaggi non letti ha in totale
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../includes/db.php';
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt_count->execute([$_SESSION['user_id']]);
    $unread_messages_count = (int) $stmt_count->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Classroom</title>
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
        }

        /* === Inserisci qui il blocco del contrasto Light Mode === */
        [data-bs-theme="light"] {
            --bs-tertiary-bg: #f4f6f9;
            --bs-body-color: #1a1f2c;
            --bs-muted-color: #505d6e;
            --bs-secondary-color: #4a5568;
            --bs-border-color: #cbd5e1;
        }

        [data-bs-theme="light"] .text-muted {
            color: #556575 !important;
        }

        [data-bs-theme="light"] .price-tag.text-success {
            color: #157347 !important;
        }

        /* Gli altri stili che avevamo già messo (navbar-blur, animate-pulse, ecc.) */
        .navbar-blur {
            background-color: rgba(var(--bs-body-bg-rgb), 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        @media (min-width: 992px) {
            .position-lg-absolute {
                position: absolute !important;
            }

            .top-lg-0 {
                top: 4px !important;
            }

            .start-lg-100 {
                start: 82% !important;
            }

            .translate-middle-lg {
                transform: translate(-50%, -50%) !important;
            }
        }

        .animate-pulse {
            animation: pulseEffect 2s infinite;
        }

        @keyframes pulseEffect {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.5);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }
    </style>
    <link rel="icon" type="image/png" href="/favicon.png">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-blur sticky-top border-bottom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-primary gap-2" href="/index.php">
                <img src="/assets/img/SC.png" alt="Logo" style="height: 32px; object-fit: contain;">
                <span class="d-none d-sm-inline">Social <span class="text-success">Classroom</span></span>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2 pt-2 pt-lg-0">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-semibold text-body" href="/index.php">
                                <i class="bi bi-house-door me-1"></i> Feed
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link px-3 fw-semibold text-body d-flex align-items-center position-relative"
                                href="/chat.php">
                                <i class="bi bi-chat-text me-1"></i> Chat
                                <?php if ($unread_messages_count > 0): ?>
                                    <span
                                        class="badge rounded-pill bg-danger ms-1 ms-lg-0 position-lg-absolute top-lg-0 start-lg-100 translate-middle-lg shadow-sm animate-pulse"
                                        style="font-size: 0.7rem; padding: 4px 7px;">
                                        <?php echo $unread_messages_count; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 border rounded-pill px-3 py-1.5 background-card shadow-sm"
                                href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="/assets/img/<?php echo !empty($_SESSION['foto_profilo']) ? $_SESSION['foto_profilo'] : 'default.png'; ?>"
                                    class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                                <span
                                    class="small fw-bold text-body">@<?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2" style="border-radius: 12px;">
                                <li><a class="dropdown-item py-2 small fw-semibold" href="/profile.php"><i
                                            class="bi bi-person me-2"></i>Il mio Profilo</a></li>
                                <li><a class="dropdown-item py-2 small fw-semibold" href="/modules/posts/create.php"><i
                                            class="bi bi-cloud-upload me-2"></i>Carica Risorsa</a></li>
                                <li>
                                    <hr class="dropdown-divider opacity-50">
                                </li>
                                <li><a class="dropdown-item py-2 small fw-semibold text-danger"
                                        href="/modules/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Esci</a>
                                </li>
                            </ul>
                        </li>

                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-sm btn-outline-primary px-3 fw-bold rounded-pill"
                                href="/modules/auth/login.php">Accedi</a></li>
                        <li class="nav-item"><a class="btn btn-sm btn-success px-3 fw-bold rounded-pill text-white"
                                href="/modules/auth/register.php">Registrati</a></li>
                    <?php endif; ?>

                    <li class="nav-item ms-lg-2 border-start d-none d-lg-block ps-3">
                        <button id="themeToggleGlobal" class="btn btn-link nav-link p-1 text-body shadow-none border-0">
                            <i id="themeIconGlobal" class="bi bi-moon-stars"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        // Gestione e sincronizzazione del Tema Scuro/Chiaro globale
        window.addEventListener('DOMContentLoaded', () => {
            const globalBtn = document.getElementById('themeToggleGlobal');
            const globalIcon = document.getElementById('themeIconGlobal');
            if (globalBtn && globalIcon) {
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                globalIcon.className = currentTheme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';

                globalBtn.onclick = () => {
                    const target = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-bs-theme', target);
                    localStorage.setItem('theme', target);
                    globalIcon.className = target === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';

                    const localIcon = document.getElementById('themeIcon');
                    if (localIcon) localIcon.className = target === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
                };
            }
        });
    </script>