<?php
require_once 'includes/db.php';
require_once 'includes/session.php';
include 'partials/header.php';

// 1. Recupero l'ID dall'URL in modo sicuro
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$my_id = $_SESSION['user_id'];

// 2. GESTIONE CANCELLAZIONE RECENSIONE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['elimina_recensione'])) {
    $feedback_id = intval($_POST['feedback_id']);

    $check_ownership = $pdo->prepare("SELECT id FROM feedback WHERE id = ? AND user_id = ?");
    $check_ownership->execute([$feedback_id, $my_id]);

    if ($check_ownership->fetch()) {
        $del = $pdo->prepare("DELETE FROM feedback WHERE id = ?");
        try {
            $del->execute([$feedback_id]);
            echo "<div class='container mt-3'><div class='alert alert-warning border-0 shadow-sm'><i class='bi bi-trash-fill me-2'></i>Recensione eliminata.</div></div>";
            echo "<script>
                    setTimeout(function(){
                        window.location.href = 'view.php?id=" . $id . "';
                    }, 1000);
                  </script>";
        } catch (PDOException $e) {
            echo "<div class='container mt-3'><div class='alert alert-danger border-0 shadow-sm'>Errore nell'eliminazione: " . $e->getMessage() . "</div></div>";
        }
    } else {
        echo "<div class='container mt-3'><div class='alert alert-danger border-0 shadow-sm'>Azione non autorizzata.</div></div>";
    }
}

// 3. Query per il contenuto singolo + dati autore
$stmt = $pdo->prepare("SELECT c.*, u.username, u.foto_profilo, u.ruolo FROM contents c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
$stmt->execute([$id]);
$content = $stmt->fetch();

if (!$content) {
    echo "<div class='container py-5'><div class='alert alert-danger shadow-sm'>Contenuto non trovato.</div></div>";
    include 'partials/footer.php';
    exit;
}

// 4. Gestione inserimento Feedback
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['inserisci_voto'])) {
    $voto = intval($_POST['valutazione']);
    $commento = trim($_POST['commento']);

    $ins = $pdo->prepare("INSERT INTO feedback (content_id, user_id, valutazione, commento) VALUES (?, ?, ?, ?)");
    try {
        $ins->execute([$id, $my_id, $voto, $commento]);
        echo "<div class='container mt-3'><div class='alert alert-success border-0 shadow-sm'><i class='bi bi-check-circle-fill me-2'></i>Recensione aggiunta con successo!</div></div>";
        echo "<script>
                setTimeout(function(){
                    window.location.href = 'view.php?id=" . $id . "';
                }, 1000);
              </script>";
    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger border-0 shadow-sm'>Errore: " . $e->getMessage() . "</div></div>";
    }
}

// 5. Calcolo statistiche in tempo reale (Conteggio e Media)
$stmt_stats = $pdo->prepare("SELECT COUNT(*) as totale, AVG(valutazione) as media FROM feedback WHERE content_id = ?");
$stmt_stats->execute([$id]);
$stats = $stmt_stats->fetch();
$totale_recensioni = $stats['totale'];
$media_voto = $stats['media'] ? round($stats['media'], 1) : 0;

// 6. Recupero i feedback esistenti
$stmt_f = $pdo->prepare("SELECT f.*, u.username, u.foto_profilo FROM feedback f JOIN users u ON f.user_id = u.id WHERE f.content_id = ? ORDER BY f.created_at DESC");
$stmt_f->execute([$id]);
$feedbacks = $stmt_f->fetchAll();
?>

<style>
    .view-card {
        border: none;
        border-radius: 20px;
        background-color: var(--bs-card-bg);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .custom-inner-box {
        background-color: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 14px;
    }

    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .form-control {
        background-color: #161c2a;
        border-color: #242f47;
        color: #ffffff;
    }

    .main-content-wrapper {
        max-width: 1140px;
        margin: 0 auto;
    }

    .btn-delete-feedback {
        color: var(--bs-danger);
        background: none;
        border: none;
        padding: 0 5px;
        font-size: 0.95rem;
        transition: transform 0.2s ease;
    }

    .btn-delete-feedback:hover {
        transform: scale(1.15);
    }

    /* Hover sui link del profilo per coerenza social */
    .profile-hover-link:hover h6 {
        color: var(--bs-primary) !important;
    }

    .profile-hover-link:hover strong {
        color: var(--bs-primary) !important;
    }
</style>

<div class="container py-5">
    <div class="main-content-wrapper">

        <a href="/index.php" class="text-decoration-none small fw-semibold d-inline-flex align-items-center mb-4">
            <i class="bi bi-arrow-left me-1"></i> Torna al Feed
        </a>

        <div class="row g-4 justify-content-center">

            <div class="col-xl-7 col-lg-7">

                <div class="card view-card p-4 p-md-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center">
                            <a href="profile.php?id=<?php echo $content['user_id']; ?>"
                                class="d-flex align-items-center text-decoration-none profile-hover-link">
                                <img src="/assets/img/<?php echo !empty($content['foto_profilo']) ? $content['foto_profilo'] : 'default.png'; ?>"
                                    class="rounded-circle me-3"
                                    style="width: 48px; height: 48px; object-fit: cover; border: 2px solid var(--bs-primary);">
                                <div>
                                    <h6 class="fw-bold text-body mb-0 transition-colors">
                                        @<?php echo htmlspecialchars($content['username']); ?></h6>
                                    <span
                                        class="text-muted small text-capitalize"><?php echo isset($content['ruolo']) ? $content['ruolo'] : 'Studente'; ?></span>
                                </div>
                            </a>
                        </div>
                        <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i>
                            <?php echo date('d M Y', strtotime($content['created_at'])); ?></span>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold"
                            style="font-size: 0.8rem;">
                            <i class="bi bi-book me-1"></i><?php echo htmlspecialchars($content['materia']); ?>
                        </span>
                        <?php if (!empty($content['grado_scolastico'])): ?>
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill"
                                style="font-size: 0.8rem;">
                                <i
                                    class="bi bi-mortarboard me-1"></i><?php echo htmlspecialchars($content['grado_scolastico']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <h3 class="fw-bold text-body mb-3"><?php echo htmlspecialchars($content['titolo']); ?></h3>
                    <div class="text-body mb-4" style="line-height: 1.6; white-space: pre-line; font-size: 1rem;">
                        <?php echo nl2br(htmlspecialchars($content['descrizione'])); ?>
                    </div>

                    <div
                        class="p-3 custom-inner-box d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary px-3 py-2.5 rounded-3">
                                <i class="bi bi-file-earmark-arrow-down fs-3"></i>
                            </div>
                            <div>
                                <span class="small text-muted d-block" style="font-size: 0.8rem;">Prezzo
                                    materiale</span>
                                <span class="fs-5 fw-bold text-body price-tag">
                                    <?php echo $content['prezzo'] > 0 ? '€' . number_format($content['prezzo'], 2) : '<span class="text-success fw-bold">Gratis</span>'; ?>
                                </span>
                            </div>
                        </div>
                        <a href="uploads/materials/<?php echo $content['file_path']; ?>"
                            class="btn btn-primary px-4 fw-bold rounded-3 shadow-sm" download>
                            <i class="bi bi-download me-2"></i> Scarica Risorsa
                        </a>
                    </div>
                </div>

                <div class="card view-card p-4">
                    <h5 class="fw-bold text-body mb-4"><i class="bi bi-chat-left-text me-2 text-primary"></i>Recensioni
                        della Community (<?php echo $totale_recensioni; ?>)</h5>

                    <?php if (empty($feedbacks)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted small mb-0">Nessuna recensione per questo materiale. Sii il primo!</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($feedbacks as $f): ?>
                                <div class="p-3 custom-inner-box">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <a href="profile.php?id=<?php echo $f['user_id']; ?>"
                                                class="d-flex align-items-center text-decoration-none profile-hover-link">
                                                <img src="/assets/img/<?php echo !empty($f['foto_profilo']) ? $f['foto_profilo'] : 'default.png'; ?>"
                                                    class="rounded-circle me-2"
                                                    style="width: 28px; height: 28px; object-fit: cover;">
                                                <strong
                                                    class="text-body small transition-colors">@<?php echo htmlspecialchars($f['username']); ?></strong>
                                            </a>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-warning" style="font-size: 0.85rem;">
                                                <?php echo str_repeat('★', $f['valutazione']) . str_repeat('☆', 5 - $f['valutazione']); ?>
                                            </span>

                                            <?php if ($f['user_id'] == $my_id): ?>
                                                <form action="" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Vuoi davvero eliminare questa recensione?');">
                                                    <input type="hidden" name="elimina_recensione" value="1">
                                                    <input type="hidden" name="feedback_id" value="<?php echo $f['id']; ?>">
                                                    <button type="submit" class="btn-delete-feedback" title="Elimina recensione">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <p class="text-secondary small mb-1" style="word-wrap: break-word; line-height: 1.4;">
                                        <?php echo htmlspecialchars($f['commento']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="card view-card p-4 text-center mb-4">
                    <h6 class="fw-bold text-body mb-2">Media Valutazioni</h6>
                    <?php if ($totale_recensioni > 0): ?>
                        <h1 class="display-4 fw-bold text-warning mb-0"><?php echo $media_voto; ?></h1>
                        <div class="text-warning mb-1" style="font-size: 1.1rem;">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= round($media_voto) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                            }
                            ?>
                        </div>
                        <p class="text-muted small mb-0">Basato su <?php echo $totale_recensioni; ?> feedback</p>
                    <?php else: ?>
                        <i class="bi bi-star display-6 text-muted mb-2"></i>
                        <p class="text-muted small mb-0">Nessun voto assegnato.</p>
                    <?php endif; ?>
                </div>

                <div class="card view-card p-4 sticky-top" style="top: 90px; z-index: 10;">
                    <h5 class="fw-bold text-body mb-3">Lascia un Feedback</h5>
                    <form action="" method="POST">
                        <input type="hidden" name="inserisci_voto" value="1">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-body">Valutazione</label>
                            <select name="valutazione" class="form-select" style="border-radius: 8px;" required>
                                <option value="5">⭐⭐⭐⭐⭐ (Eccellente)</option>
                                <option value="4">⭐⭐⭐⭐ (Molto Buono)</option>
                                <option value="3">⭐⭐⭐ (Sufficiente)</option>
                                <option value="2">⭐⭐ (Scarso)</option>
                                <option value="1">⭐ (Pessimo)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-body">Commento</label>
                            <textarea name="commento" class="form-control" rows="3"
                                placeholder="Cosa ne pensi di questa risorsa? Spiega qui..." style="border-radius: 8px;"
                                required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-3">
                            <i class="bi bi-send me-1"></i> Invia Recensione
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div> <?php include 'partials/footer.php'; ?>