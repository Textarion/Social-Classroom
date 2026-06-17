<?php
include '../../includes/session.php';
include '../../includes/db.php';

$messaggio = "";
$tipo_alert = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titolo = trim($_POST['titolo']);
    $descrizione = trim($_POST['descrizione']);
    $materia = trim($_POST['materia']);
    $grado_scolastico = trim($_POST['grado_scolastico']);
    $prezzo = !empty($_POST['prezzo']) ? (float) $_POST['prezzo'] : 0.00;
    $user_id = $_SESSION['user_id'];

    // Gestione Caricamento File
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        $id_univoco = uniqid();
        $nome_file_originale = $_FILES['documento']['name'];
        $estensione = pathinfo($nome_file_originale, PATHINFO_EXTENSION);
        $nuovo_nome_file = $id_univoco . "." . $estensione;

        $cartella_destinazione = '../../uploads/';
        if (!is_dir($cartella_destinazione)) {
            mkdir($cartella_destinazione, 0777, true);
        }

        $percorso_finale = $cartella_destinazione . $nuovo_nome_file;

        if (move_uploaded_file($_FILES['documento']['tmp_name'], $percorso_finale)) {
            $sql = "INSERT INTO contents (user_id, titolo, descrizione, materia, grado_scolastico, file_path, prezzo) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            try {
                $stmt->execute([$user_id, $titolo, $descrizione, $materia, $grado_scolastico, $nuovo_nome_file, $prezzo]);
                $messaggio = "Risorsa pubblicata con successo nella Social Classroom!";
                $tipo_alert = "success";
            } catch (PDOException $e) {
                $messaggio = "Errore nel salvataggio del database: " . $e->getMessage();
                $tipo_alert = "danger";
            }
        } else {
            $messaggio = "Impossibile spostare il file caricato nella cartella dei download.";
            $tipo_alert = "danger";
        }
    } else {
        $messaggio = "Errore nel caricamento del file. Controlla la dimensione.";
        $tipo_alert = "danger";
    }
}

include '../../partials/header.php';
?>

<style>
    /* Regole CSS specifiche per integrare il Form con la Dark Mode */
    .upload-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        background-color: var(--bs-card-bg);
    }

    .form-label {
        color: var(--bs-body-color);
    }

    .form-control,
    .form-select,
    .input-group-text {
        border-radius: 10px;
        padding: 11px 15px;
        border: 1px solid var(--bs-border-color);
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
    }

    /* Fix specifico per evitare che gli input rimangano bianchi in Dark Mode */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .input-group-text {
        background-color: #161c2a;
        border-color: #242f47;
        color: #ffffff;
    }

    [data-bs-theme="dark"] .form-control::placeholder {
        color: #6c757d;
    }

    /* Mantiene i bordi arrotondati corretti sui gruppi di input con icone */
    .input-group .form-control {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    .input-group .input-group-text {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        background-color: var(--bs-tertiary-bg);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <a href="/index.php" class="text-decoration-none small fw-semibold d-inline-flex align-items-center mb-3">
                <i class="bi bi-arrow-left me-1"></i> Torna al Feed
            </a>

            <div class="card upload-card p-4 p-md-5">

                <div class="mb-4">
                    <h3 class="fw-bold text-body mb-1">Condividi una nuova risorsa 🚀</h3>
                    <p class="text-muted small mb-0">I tuoi appunti, riassunti o progetti aiuteranno l'intera classe
                        digitale.</p>
                </div>

                <?php if ($messaggio): ?>
                    <div class="alert alert-<?php echo $tipo_alert; ?> border-0 shadow-sm py-2.5 small mb-4" role="alert">
                        <i
                            class="bi <?php echo $tipo_alert == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
                        <?php echo $messaggio; ?>
                    </div>
                <?php endif; ?>

                <form action="create.php" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Titolo del Materiale</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-type text-muted"></i></span>
                            <input type="text" name="titolo" class="form-control"
                                placeholder="Es: Riassunto capitolo 3 di Sistemi e Reti" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Materia / Disciplina</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-book text-muted"></i></span>
                                <input type="text" name="materia" class="form-control"
                                    placeholder="Es: Sistemi, TIPSIT, Informatica" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Classe / Scuola</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-mortarboard text-muted"></i></span>
                                <input type="text" name="grado_scolastico" class="form-control"
                                    placeholder="Es: 5° Anno Informatica">
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-7 mb-3">
                            <label class="form-label small fw-bold">Seleziona Documento (PDF, ZIP, DOCX)</label>
                            <input type="file" name="documento" class="form-control" required>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label small fw-bold">Prezzo (€) <span class="text-muted fw-normal">(0 =
                                    gratis)</span></label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold">€</span>
                                <input type="number" step="0.01" min="0" name="prezzo" class="form-control"
                                    placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Descrizione / Dettagli del File</label>
                        <textarea name="descrizione" class="form-control" rows="4"
                            placeholder="Spiega brevemente cosa contiene questo file e come può essere utile per studiare..."
                            required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm rounded-3">
                        🚀 Pubblica nella Social Classroom
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../partials/footer.php'; ?>