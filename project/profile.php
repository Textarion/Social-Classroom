<?php
include 'includes/session.php';
include 'includes/db.php';

$my_id = $_SESSION['user_id'];

// Determina se stiamo guardando il nostro profilo o quello di un altro
$user_id = isset($_GET['id']) ? (int) $_GET['id'] : $my_id;
$is_own_profile = ($user_id === $my_id);

// Recupera i dati dell'utente del profilo corrente
$stmt = $pdo->prepare("SELECT username, email, ruolo, bio, foto_profilo FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$profile_user = $stmt->fetch();

if (!$profile_user) {
    die("Utente non trovato.");
}

// Recupera i post caricati da questo utente
$stmt = $pdo->prepare("SELECT id, titolo, materia, prezzo FROM contents WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$user_posts = $stmt->fetchAll();

// Gestione dello stato amicizia (se stiamo guardando il profilo di qualcun altro)
$friend_status = null;
$is_sender = false;

if (!$is_own_profile) {
    $stmt = $pdo->prepare("SELECT * FROM friends WHERE (user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?)");
    $stmt->execute([$my_id, $user_id, $user_id, $my_id]);
    $friendship = $stmt->fetch();

    if ($friendship) {
        $friend_status = $friendship['status'];
        $is_sender = ($friendship['user_id'] == $my_id);
    }
}

// Se è il proprio profilo, recupera la lista degli amici accettati e le richieste pendenti
$friends_list = [];
$pending_requests = [];

if ($is_own_profile) {
    // Amici accettati
    $stmt = $pdo->prepare("SELECT u.id, u.username, u.foto_profilo FROM friends f 
                           JOIN users u ON (f.user_id = u.id AND f.friend_id = ?) OR (f.friend_id = u.id AND f.user_id = ?)
                           WHERE f.status = 'accepted' AND u.id != ?");
    $stmt->execute([$my_id, $my_id, $my_id]);
    $friends_list = $stmt->fetchAll();

    // Richieste in entrata (da accettare)
    $stmt = $pdo->prepare("SELECT u.id, u.username, u.foto_profilo FROM friends f 
                           JOIN users u ON f.user_id = u.id 
                           WHERE f.friend_id = ? AND f.status = 'pending'");
    $stmt->execute([$my_id]);
    $pending_requests = $stmt->fetchAll();
}

include 'partials/header.php';
?>

<div class="container py-5">
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center"
                style="border-radius: 20px; background-color: var(--bs-card-bg);">
                <img src="/assets/img/<?php echo !empty($profile_user['foto_profilo']) ? $profile_user['foto_profilo'] : 'default.png'; ?>"
                    class="mx-auto mb-3 img-thumbnail rounded-circle shadow-sm"
                    style="width: 120px; height: 120px; object-fit: cover;">

                <h4 class="fw-bold mb-1">@<?php echo htmlspecialchars($profile_user['username']); ?></h4>
                <span
                    class="badge bg-primary-subtle text-primary rounded-pill px-3 mb-3 text-capitalize"><?php echo $profile_user['ruolo']; ?></span>

                <p class="text-muted small px-2">
                    <?php echo !empty($profile_user['bio']) ? htmlspecialchars($profile_user['bio']) : 'Nessuna biografia inserita.'; ?>
                </p>

                <hr class="my-3 opacity-50">

                <?php if ($is_own_profile): ?>
                    <a href="/edit_profile.php" class="btn btn-outline-secondary btn-sm w-100 rounded-3 py-2 fw-semibold">
                        <i class="bi bi-gear me-1"></i> Modifica Profilo
                    </a>
                <?php else: ?>
                    <?php if ($friend_status === null): ?>
                        <a href="/friend_action.php?action=add&id=<?php echo $user_id; ?>"
                            class="btn btn-primary btn-sm w-100 rounded-3 py-2 fw-bold">
                            <i class="bi bi-person-plus-fill me-1"></i> Stringi Amicizia
                        </a>
                    <?php elseif ($friend_status === 'pending' && $is_sender): ?>
                        <button class="btn btn-secondary btn-sm w-100 rounded-3 py-2 fw-semibold" disabled>
                            <i class="bi bi-clock-history me-1"></i> Richiesta Inviata
                        </button>
                        <a href="/friend_action.php?action=remove&id=<?php echo $user_id; ?>"
                            class="text-danger d-block small mt-2 text-decoration-none">Annulla richiesta</a>
                    <?php elseif ($friend_status === 'pending' && !$is_sender): ?>
                        <div class="d-grid gap-2">
                            <a href="/friend_action.php?action=accept&id=<?php echo $user_id; ?>"
                                class="btn btn-success btn-sm rounded-3 py-2 fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Accetta Amicizia
                            </a>
                            <a href="/friend_action.php?action=remove&id=<?php echo $user_id; ?>"
                                class="btn btn-outline-danger btn-sm rounded-3">Rifiuta</a>
                        </div>
                    <?php elseif ($friend_status === 'accepted'): ?>
                        <button class="btn btn-success btn-sm w-100 rounded-3 py-2 fw-bold mb-2" disabled>
                            <i class="bi bi-people-fill me-1"></i> Siete Amici
                        </button>

                        <a href="/chat.php?friend_id=<?php echo $user_id; ?>"
                            class="btn btn-primary btn-sm w-100 rounded-3 py-2 fw-bold mb-3">
                            <i class="bi bi-chat-fill me-1"></i> Invia Messaggio
                        </a>

                        <a href="/friend_action.php?action=remove&id=<?php echo $user_id; ?>"
                            class="text-danger d-block small text-decoration-none"
                            onclick="return confirm('Vuoi rimuovere questo amico?')">Rimuovi amico</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if ($is_own_profile): ?>
                <?php if (!empty($pending_requests)): ?>
                    <div class="card border-0 shadow-sm p-3 mt-4" style="border-radius: 16px;">
                        <h6 class="fw-bold text-warning mb-3"><i class="bi bi-exclamation-circle me-1"></i> Richieste in attesa
                        </h6>
                        <?php foreach ($pending_requests as $req): ?>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-light">
                                <a href="/profile.php?id=<?php echo $req['id']; ?>"
                                    class="d-flex align-items-center text-decoration-none text-body">
                                    <img src="/assets/img/<?php echo $req['foto_profilo']; ?>" class="rounded-circle me-2"
                                        style="width: 32px; height: 32px; object-fit: cover;">
                                    <span class="small fw-semibold">@<?php echo htmlspecialchars($req['username']); ?></span>
                                </a>
                                <div class="btn-group btn-group-sm">
                                    <a href="/friend_action.php?action=accept&id=<?php echo $req['id']; ?>"
                                        class="btn btn-success p-1 px-2"><i class="bi bi-check"></i></a>
                                    <a href="/friend_action.php?action=remove&id=<?php echo $req['id']; ?>"
                                        class="btn btn-outline-danger p-1 px-2"><i class="bi bi-x"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm p-3 mt-4" style="border-radius: 16px;">
                    <h6 class="fw-bold mb-3"><i class="bi bi-people me-1"></i> I tuoi Amici
                        (<?php echo count($friends_list); ?>)</h6>
                    <?php if (empty($friends_list)): ?>
                        <p class="text-muted small mb-0">Non hai ancora aggiunto amici.</p>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach ($friends_list as $friend): ?>
                                <div class="col-4 text-center mb-2">
                                    <a href="/profile.php?id=<?php echo $friend['id']; ?>"
                                        class="text-decoration-none text-body d-block text-truncate">
                                        <img src="/assets/img/<?php echo $friend['foto_profilo']; ?>"
                                            class="rounded-circle shadow-sm mb-1"
                                            style="width: 45px; height: 45px; object-fit: cover; border: 2px solid var(--bs-border-color);">
                                        <div class="text-xs text-muted" style="font-size: 0.75rem;">
                                            @<?php echo htmlspecialchars($friend['username']); ?></div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 20px; background-color: var(--bs-card-bg);">
                <h4 class="fw-bold mb-4 text-body">
                    <?php echo $is_own_profile ? 'Le tue risorse pubblicate' : 'Risorse caricate da ' . htmlspecialchars($profile_user['username']); ?>
                </h4>

                <?php if (empty($user_posts)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-folder2-open display-4 text-muted mb-2"></i>
                        <p class="text-muted mb-0">Nessun materiale condiviso finora.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Titolo Risorsa</th>
                                    <th>Materia</th>
                                    <th>Prezzo</th>
                                    <th class="text-end">Azione</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_posts as $post): ?>
                                    <tr>
                                        <td class="fw-semibold text-body"><?php echo htmlspecialchars($post['titolo']); ?></td>
                                        <td><span
                                                class="badge bg-secondary-subtle text-secondary border"><?php echo htmlspecialchars($post['materia']); ?></span>
                                        </td>
                                        <td class="fw-bold text-primary">
                                            <?php echo $post['prezzo'] == 0 ? 'Gratis' : '€' . number_format($post['prezzo'], 2); ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="/view.php?id=<?php echo $post['id']; ?>"
                                                class="btn btn-sm btn-outline-primary rounded-2">Vedi</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include 'partials/footer.php'; ?>