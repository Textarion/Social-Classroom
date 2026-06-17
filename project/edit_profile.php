<?php
require_once 'includes/db.php';
require_once 'includes/session.php';
include 'partials/header.php';

// Verifichiamo che l'utente sia loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$my_id = $_SESSION['user_id'];

// 1. Recuperiamo i dati attuali dell'utente
$stmt = $pdo->prepare("SELECT username, email, foto_profilo, ruolo, bio FROM users WHERE id = ?");
$stmt->execute([$my_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='container py-5'><div class='alert alert-danger shadow-sm'>Utente non trovato.</div></div>";
    include 'partials/footer.php';
    exit;
}

// 2. Gestione del salvataggio delle modifiche
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['salva_profilo'])) {
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    $foto_corrente = $user['foto_profilo'];
    $nuova_foto = $foto_corrente;

    // Gestione dell'immagine ritagliata inviata in Base64
    if (!empty($_POST['avatar_cropped_base64'])) {
        $base64_data = $_POST['avatar_cropped_base64'];

        if (preg_match('/^data:image\/(\w+);base64,/', $base64_data, $type)) {
            $data = substr($base64_data, strpos($base64_data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, webp, ecc.

            if (in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                $data = base64_decode($data);

                if ($data !== false) {
                    $nuova_foto = 'avatar_' . $my_id . '_' . time() . '.' . $type;
                    $target_dir = 'assets/img/';

                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }

                    if (file_put_contents($target_dir . $nuova_foto, $data)) {
                        if (!empty($foto_corrente) && $foto_corrente != 'default.png' && file_exists($target_dir . $foto_corrente)) {
                            unlink($target_dir . $foto_corrente);
                        }
                    }
                }
            }
        }
    }

    // Aggiornamento sul Database di tutti i campi modificabili
    $update = $pdo->prepare("UPDATE users SET email = ?, bio = ?, foto_profilo = ? WHERE id = ?");
    try {
        $update->execute([$email, $bio, $nuova_foto, $my_id]);

        // --- LA RIGA DA AGGIUNGERE È QUESTA ---
        // Aggiorniamo subito la sessione con il nuovo nome del file, così l'header si allinea all'istante!
        $_SESSION['foto_profilo'] = $nuova_foto;
        // --------------------------------------

        echo "<div class='container mt-3'><div class='alert alert-success border-0 shadow-sm'><i class='bi bi-check-circle-fill me-2'></i>Profilo aggiornato con successo!</div></div>";

        echo "<script>
                setTimeout(function(){
                    window.location.href = 'edit_profile.php';
                }, 1000);
              </script>";
    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger border-0 shadow-sm'>Errore durante l'aggiornamento: " . $e->getMessage() . "</div></div>";
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />

<style>
    .edit-profile-card {
        border: none;
        border-radius: 20px;
        background-color: var(--bs-card-bg);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .avatar-preview-wrapper {
        position: relative;
        display: inline-block;
    }

    .avatar-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid var(--bs-primary);
    }

    /* Fix input in Dark Mode ed elementi disabilitati leggeri */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-control:disabled {
        background-color: #161c2a;
        border-color: #242f47;
        color: #ffffff;
    }

    .profile-wrapper {
        max-width: 700px;
        margin: 0 auto;
    }

    .cropper-container-wrapper {
        max-height: 400px;
        width: 100%;
        overflow: hidden;
        background-color: #000;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #imageToCrop {
        display: block;
        max-width: 100%;
        height: auto;
    }
</style>

<div class="container py-5">
    <div class="profile-wrapper">

        <a href="profile.php?id=<?php echo $my_id; ?>"
            class="text-decoration-none small fw-semibold d-inline-flex align-items-center mb-4">
            <i class="bi bi-arrow-left me-1"></i> Visualizza Profilo
        </a>

        <div class="card edit-profile-card p-4 p-md-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-body mb-1">Modifica Profilo</h3>
                <p class="text-muted small">Gestisci le informazioni pubbliche del tuo account Social Classroom</p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" id="profileForm">
                <input type="hidden" name="salva_profilo" value="1">

                <input type="hidden" name="avatar_cropped_base64" id="avatarCroppedBase64">

                <div class="text-center mb-4">
                    <div class="avatar-preview-wrapper mb-3">
                        <img src="assets/img/<?php echo !empty($user['foto_profilo']) ? $user['foto_profilo'] : 'default.png'; ?>"
                            class="rounded-circle avatar-preview shadow-sm" id="avatarImgPreview">
                    </div>
                    <div>
                        <label for="foto_profilo_upload"
                            class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                            <i class="bi bi-camera me-1"></i> Cambia Avatar
                        </label>
                        <input type="file" id="foto_profilo_upload" class="d-none" accept="image/*">
                        <span class="d-block text-muted x-small mt-1">Formati supportati: PNG, JPG, JPEG, WEBP</span>
                    </div>
                </div>

                <hr class="my-4 opacity-25">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-body">Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary border-end-0 text-muted"
                                style="border-radius: 10px 0 0 10px;">@</span>
                            <input type="text" class="form-control border-start-0"
                                value="<?php echo htmlspecialchars($user['username']); ?>"
                                style="border-radius: 0 10px 10px 0;" readonly disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-body">Ruolo Account</label>
                        <input type="text" class="form-control text-capitalize"
                            value="<?php echo htmlspecialchars($user['ruolo']); ?>" style="border-radius: 10px;"
                            readonly disabled>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-body">Indirizzo Email</label>
                        <input type="email" name="email" class="form-control"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            style="border-radius: 10px; padding: 10px;" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-body">Biografia / Descrizione</label>
                        <textarea name="bio" class="form-control" rows="4"
                            placeholder="Racconta qualcosa di te, della tua scuola o delle tue specializzazioni..."
                            style="border-radius: 10px;"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <a href="profile.php?id=<?php echo $my_id; ?>"
                        class="btn btn-light w-50 py-2 fw-semibold rounded-3">
                        Annulla
                    </a>
                    <button type="submit" class="btn btn-primary w-50 py-2 fw-bold shadow-sm rounded-3">
                        <i class="bi bi-save me-1"></i> Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cropModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; background-color: var(--bs-card-bg);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-body" id="cropModalLabel">Centra e Ritaglia Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="cropper-container-wrapper rounded-3">
                    <img id="imageToCrop" src="">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 px-3 fw-semibold"
                    data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary rounded-3 px-4 fw-bold" id="cropButton">Conferma
                    Ritaglio</button>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    let cropper = null;
    const fileInput = document.getElementById('foto_profilo_upload');
    const imageToCrop = document.getElementById('imageToCrop');
    const cropModalElement = document.getElementById('cropModal');
    const cropModal = new bootstrap.Modal(cropModalElement);
    const cropButton = document.getElementById('cropButton');
    const avatarImgPreview = document.getElementById('avatarImgPreview');
    const avatarCroppedBase64 = document.getElementById('avatarCroppedBase64');

    // 1. Intercettiamo la selezione del file immagine
    fileInput.addEventListener('change', function (e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            const file = files[0];

            if (!file.type.startsWith('image/')) {
                alert('Per favore seleziona un file immagine valido (PNG, JPG, WEBP).');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }

                imageToCrop.src = event.target.result;
                cropModal.show();
            };
            reader.readAsDataURL(file);
        }
    });

    // 2. Inizializziamo Cropper solo quando la modale ha terminato l'animazione di apertura
    cropModalElement.addEventListener('shown.bs.modal', function () {
        setTimeout(() => {
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                background: true,
                autoCropArea: 0.8,
                responsive: true,
                restore: false,
                checkOrientation: false
            });
        }, 50);
    });

    // 3. Reset completo alla chiusura della modale per liberare memoria
    cropModalElement.addEventListener('hidden.bs.modal', function () {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        imageToCrop.src = "";
        fileInput.value = "";
    });

    // 4. Estrazione del ritaglio e invio al form principale
    cropButton.addEventListener('click', function () {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            const croppedBase64 = canvas.toDataURL('image/jpeg', 0.9);

            avatarCroppedBase64.value = croppedBase64;
            avatarImgPreview.src = croppedBase64;

            cropModal.hide();
        }
    });
</script>