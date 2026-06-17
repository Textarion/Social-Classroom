<?php
include 'includes/session.php';
include 'includes/db.php';

$my_id = $_SESSION['user_id'];

// Recupera tutti gli amici accettati e conta quanti messaggi non letti ci sono per ognuno
$stmt = $pdo->prepare("
    SELECT u.id, u.username, u.foto_profilo,
    (SELECT COUNT(*) FROM messages m WHERE m.sender_id = u.id AND m.receiver_id = ? AND m.is_read = 0) as unread_count
    FROM friends f 
    JOIN users u ON (f.user_id = u.id AND f.friend_id = ?) OR (f.friend_id = u.id AND f.user_id = ?)
    WHERE f.status = 'accepted' AND u.id != ?
    ORDER BY unread_count DESC, u.username ASC
");
$stmt->execute([$my_id, $my_id, $my_id, $my_id]);
$friends = $stmt->fetchAll();

// Se l'utente ha aperto la chat direttamente dal profilo di un amico specifico
$active_friend_id = isset($_GET['friend_id']) ? (int) $_GET['friend_id'] : (count($friends) > 0 ? $friends[0]['id'] : 0);

$active_friend = null;
if ($active_friend_id > 0) {
    $stmt = $pdo->prepare("SELECT id, username, foto_profilo, ruolo FROM users WHERE id = ?");
    $stmt->execute([$active_friend_id]);
    $active_friend = $stmt->fetch();
}

include 'partials/header.php';
?>

<style>
    .chat-box {
        height: 500px;
        overflow-y: auto;
        background-color: var(--bs-tertiary-bg);
        border-radius: 12px;
        padding: 15px;
    }

    .msg-bubble {
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 16px;
        margin-bottom: 10px;
        word-wrap: break-word;
        font-size: 0.95rem;
    }

    .msg-sent {
        background-color: #0d6efd;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }

    .msg-received {
        background-color: var(--bs-card-bg);
        color: var(--bs-body-color);
        border: 1px solid var(--bs-border-color);
        border-bottom-left-radius: 4px;
    }

    .friend-list-item {
        transition: background-color 0.2s;
        border-radius: 10px;
        cursor: pointer;
    }

    .friend-list-item.active {
        background-color: rgba(13, 110, 253, 0.15);
        font-weight: 600;
    }
</style>

<div class="container py-4">
    <div class="row g-4">

        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="border-radius: 16px;">
                <h5 class="fw-bold mb-3 text-body"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Messaggi</h5>

                <?php if (empty($friends)): ?>
                    <p class="text-muted small text-center my-4">Aggiungi degli amici dal loro profilo per iniziare a
                        chattare!</p>
                <?php else: ?>
                    <div class="nav flex-column nav-pills gap-1">
                        <?php foreach ($friends as $f): ?>
                            <a href="/chat.php?friend_id=<?php echo $f['id']; ?>"
                                class="nav-link p-2.5 text-body d-flex align-items-center justify-content-between friend-list-item <?php echo $f['id'] == $active_friend_id ? 'active' : ''; ?>">
                                <div class="d-flex align-items-center">
                                    <img src="/assets/img/<?php echo $f['foto_profilo']; ?>" class="rounded-circle me-2.5"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="small text-truncate" style="max-width: 140px;">@
                                        <?php echo htmlspecialchars($f['username']); ?>
                                    </span>
                                </div>
                                <?php if ($f['unread_count'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill small">
                                        <?php echo $f['unread_count']; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-8 col-lg-9">
            <?php if ($active_friend): ?>
                <div class="card border-0 shadow-sm p-3" style="border-radius: 16px;">

                    <div class="d-flex align-items-center pb-3 border-bottom mb-3">
                        <img src="/assets/img/<?php echo $active_friend['foto_profilo']; ?>" class="rounded-circle me-2.5"
                            style="width: 45px; height: 45px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold mb-0 text-body">@
                                <?php echo htmlspecialchars($active_friend['username']); ?>
                            </h6>
                            <span class="badge bg-light text-muted border text-capitalize px-2 py-0.5"
                                style="font-size: 0.7rem;">
                                <?php echo $active_friend['ruolo']; ?>
                            </span>
                        </div>
                    </div>

                    <div class="chat-box d-flex flex-column" id="chatWindow">
                    </div>

                    <form id="messageForm" class="mt-3">
                        <div class="input-group">
                            <input type="text" id="messageInput" class="form-control px-3"
                                placeholder="Scrivi un messaggio..." style="border-radius: 10px 0 0 10px;"
                                autocomplete="off" required>
                            <button class="btn btn-primary px-4 fw-bold" type="submit"
                                style="border-radius: 0 10px 10px 0;">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>

                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm p-5 text-center h-100 d-flex flex-column align-items-center justify-content-center"
                    style="border-radius: 16px;">
                    <i class="bi bi-chat-quote display-2 text-muted mb-3"></i>
                    <h5 class="fw-bold text-muted">Nessuna conversazione selezionata</h5>
                    <p class="text-muted small">Seleziona un amico a sinistra per iniziare a scambiarvi appunti e pareri!
                    </p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
<?php if ($active_friend): ?>
        const friendId = <?php echo $active_friend_id; ?>;
        const chatWindow = document.getElementById('chatWindow');
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');

        // Funzione per recuperare i messaggi in background
        async function fetchMessages() {
            try {
                const response = await fetch(`/chat_api.php?action=fetch&friend_id=${friendId}`);
                const data = await response.json();

                if (data.success) {
                    const wasAtBottom = chatWindow.scrollHeight - chatWindow.scrollTop <= chatWindow.clientHeight + 50;
                    let html = '';

                    if (data.messages.length === 0) {
                        html = '<div class="text-center text-muted small my-auto">L\'inizio della vostra storia. Invia un messaggio!</div>';
                    } else {
                        data.messages.forEach(msg => {
                            const isMe = (msg.sender_id == data.my_id);
                            const cName = isMe ? 'msg-sent' : 'msg-received';
                            html += `<div class="msg-bubble ${cName}">${escapeHtml(msg.message)}</div>`;
                        });
                    }

                    chatWindow.innerHTML = html;

                    // Forza lo scroll automatico in basso solo al primo caricamento o se l'utente era già giù
                    if (wasAtBottom) {
                        chatWindow.scrollTop = chatWindow.scrollHeight;
                    }
                }
            } catch (err) { console.error("Errore recupero messaggi:", err); }
        }

        // Funzione per inviare i messaggi
        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = messageInput.value.trim();
            if (!text) return;

            messageInput.value = '';

            try {
                const response = await fetch('/chat_api.php?action=send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ friend_id: friendId, message: text })
                });
                const data = await response.json();
                if (data.success) {
                    fetchMessages(); // Aggiorna subito la chat
                }
            } catch (err) { console.error("Errore invio messaggio:", err); }
        });

        // Helper per pulire l'input da tag HTML dannosi (XSS protection)
        function escapeHtml(text) {
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        // Esegui la sincronizzazione della chat
        fetchMessages(); // Caricamento immediato
        setInterval(fetchMessages, 3000); // Controlla nuovi messaggi ogni 3 secondi
<?php endif; ?>
</script>

<?php include 'partials/footer.php'; ?>