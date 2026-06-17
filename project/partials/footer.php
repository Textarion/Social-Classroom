<style>
    .main-footer {
        background-color: var(--bs-card-bg);
        border-top: 1px solid var(--bs-border-color);
        padding: 25px 0;
        margin-top: auto;
        /* Spinge il footer sempre in fondo alla pagina */
    }

    .footer-link {
        color: var(--bs-muted-color);
        text-decoration: none;
        transition: color 0.2s ease;
        font-size: 0.9rem;
    }

    .footer-link:hover {
        color: var(--bs-primary);
    }

    /* Risolve il problema del footer volante su pagine con pochi contenuti */
    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
    }
</style>

<footer class="main-footer mt-5">
    <div class="container">
        <div class="row align-items-center justify-content-between g-3">

            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted small">
                    &copy; <?php echo date('Y'); ?> <span class="fw-bold text-primary">Social <span
                            class="text-success">Classroom</span></span>.
                    Tutti i diritti riservati.
                </p>
            </div>

            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex justify-content-center justify-content-md-end gap-4">
                    <a href="/index.php" class="footer-link"><i class="bi bi-house-door me-1"></i>Feed</a>
                    <a href="/chat.php" class="footer-link"><i class="bi bi-chat-text me-1"></i>Chat</a>
                    <a href="/profile.php" class="footer-link"><i class="bi bi-person me-1"></i>Profilo</a>
                </div>
            </div>

        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>