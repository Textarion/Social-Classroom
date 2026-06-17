<?php
include 'includes/session.php';
include 'includes/db.php';

// Query corretta e sicura: recupera i contenuti e le info dell'autore
$query = "SELECT c.*, u.username, u.foto_profilo, u.ruolo
          FROM contents c 
          JOIN users u ON c.user_id = u.id 
          ORDER BY c.created_at DESC";
$stmt = $pdo->query($query);
$contents = $stmt->fetchAll();

include 'partials/header.php';
?>

<style>
    /* Stili specifici per il Feed in stile Social Media */
    .hero-banner {
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.08), rgba(25, 135, 84, 0.08));
        border-radius: 24px;
        border: 1px solid rgba(13, 110, 253, 0.1);
    }

    .social-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: var(--bs-card-bg);
    }

    .social-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.07);
    }

    .avatar-sm {
        width: 42px;
        height: 42px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid var(--bs-primary);
    }

    .sidebar-sticky {
        position: sticky;
        top: 90px;
    }

    .badge-materia {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        font-weight: 600;
        border-radius: 8px;
        padding: 6px 12px;
    }

    [data-bs-theme="dark"] .badge-materia {
        background-color: rgba(13, 110, 253, 0.2);
        color: #6ea8fe;
    }

    .price-tag {
        font-size: 1.1rem;
        font-weight: 700;
    }
</style>

<div class="container py-4">

    <div class="hero-banner p-4 p-md-5 mb-4 d-flex align-items-center justify-content-between row mx-0">
        <div class="col-lg-8 px-0">
            <h1 class="display-6 fw-bold text-body mb-2">Ciao, @<?php echo htmlspecialchars($_SESSION['username']); ?>!
                👋</h1>
            <p class="lead text-muted mb-0">Esplora i materiali didattici più recenti o condividi i tuoi appunti con la
                community di Social Classroom.</p>
        </div>
        <div class="col-lg-4 d-none d-lg-flex justify-content-end px-0">
            <a href="/modules/posts/create.php" class="btn btn-primary btn-lg px-4 py-3 fw-bold shadow-sm"
                style="border-radius: 12px;">
                🚀 Condividi una risorsa
            </a>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-3 order-lg-1 order-2">
            <div class="sidebar-sticky">

                <div class="card social-card p-3 mb-4 text-center">
                    <img src="/assets/img/<?php echo !empty($_SESSION['foto_profilo']) ? $_SESSION['foto_profilo'] : 'default.png'; ?>"
                        class="mx-auto mb-3 img-thumbnail rounded-circle shadow-sm"
                        style="width: 80px; height: 80px; object-fit: cover;">
                    <h5 class="fw-bold mb-1">@<?php echo htmlspecialchars($_SESSION['username']); ?></h5>
                    <span
                        class="badge bg-secondary-subtle text-secondary border rounded-pill px-3 mb-2 text-capitalize"><?php echo $_SESSION['ruolo']; ?></span>
                    <hr class="my-3 opacity-50">
                    <div class="row text-center g-0">
                        <div class="col-6 border-end">
                            <div class="small text-muted">Stato</div>
                            <div class="fw-bold text-success">Attivo</div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted">Ambiente</div>
                            <div class="fw-bold text-primary">Root</div>
                        </div>
                    </div>
                </div>

                <div class="card social-card p-3">
                    <h6 class="fw-bold text-uppercase tracking-wider small mb-3 text-primary">💡 Consiglio Smart</h6>
                    <p class="small text-muted mb-0">Lascia sempre una recensione con un voto a stella dopo aver
                        scaricato un file per aiutare i tuoi compagni nella scelta delle risorse migliori!</p>
                </div>

            </div>
        </div>

        <div class="col-lg-9 order-lg-2 order-1">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0 text-body">Ultimi Aggiornamenti</h4>
                <span class="badge bg-primary rounded-pill px-3 py-2"><?php echo count($contents); ?> Risorse</span>
            </div>

            <?php if (empty($contents)): ?>
                <div class="card social-card p-5 text-center">
                    <i class="bi bi-folder-x display-1 text-muted mb-3"></i>
                    <h5 class="fw-bold text-muted">Nessun contenuto disponibile</h5>
                    <p class="text-muted small mb-0">Sii il primo a caricare del materiale per questa classe!</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($contents as $row): ?>
                        <div class="col-md-6">
                            <div class="card social-card h-100 d-flex flex-column justify-content-between p-3">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <a href="/profile.php?id=<?php echo $row['user_id']; ?>"
                                                class="d-flex align-items-center text-decoration-none">
                                                <img src="/assets/img/<?php echo !empty($row['foto_profilo']) ? $row['foto_profilo'] : 'default.png'; ?>"
                                                    class="avatar-sm me-2">
                                                <div>
                                                    <h6 class="fw-bold mb-0 small text-body">
                                                        @<?php echo htmlspecialchars($row['username']); ?></h6>
                                                    <span class="text-muted"
                                                        style="font-size: 0.75rem;"><?php echo date('d M Y', strtotime($row['created_at'])); ?></span>
                                                </div>
                                            </a>
                                        </div>
                                        <span
                                            class="badge <?php echo $row['ruolo'] == 'docente' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info'; ?> border rounded-pill px-2.5 py-1 small text-capitalize">
                                            <?php echo $row['ruolo']; ?>
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge badge-materia small">
                                            <i class="bi bi-book me-1"></i><?php echo htmlspecialchars($row['materia']); ?>
                                        </span>
                                        <?php if (!empty($row['grado_scolastico'])): ?>
                                            <span class="badge bg-light text-dark border small rounded-8 d-flex align-items-center">
                                                <?php echo htmlspecialchars($row['grado_scolastico']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <h5 class="fw-bold text-body mb-2 text-truncate-2" style="line-height: 1.4;">
                                        <?php echo htmlspecialchars($row['titolo']); ?>
                                    </h5>
                                    <p class="text-muted small mb-3 text-truncate-3"
                                        style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        <?php echo htmlspecialchars($row['descrizione']); ?>
                                    </p>
                                </div>

                                <div class="border-top pt-3 mt-2 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div>
                                            <?php if ($row['prezzo'] == 0): ?>
                                                <span class="text-success fw-bold price-tag">Gratis</span>
                                            <?php else: ?>
                                                <span
                                                    class="text-primary fw-bold price-tag">€<?php echo number_format($row['prezzo'], 2); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="small">
                                            <span class="text-muted text-xs"><i class="bi bi-chat-square-text me-1"></i>Social
                                                Classroom</span>
                                        </div>
                                    </div>

                                    <a href="/view.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-outline-primary btn-sm px-3 fw-semibold" style="border-radius: 8px;">
                                        Apri <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include 'partials/footer.php'; ?>