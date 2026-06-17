# 🎓 Social Classroom

Social Classroom è una piattaforma web dinamica pensata per studenti e docenti, progettata per facilitare la condivisione di risorse didattiche, la comunicazione in tempo reale e l'interazione all'interno della comunità scolastica o universitaria.

Il progetto è sviluppato in **PHP nativo**, con un'architettura leggera e un'interfaccia utente moderna e responsiva basata su **Bootstrap 5**.

---

## 🚀 Funzionalità Principali

- **Autenticazione Sicura:** Sistema di registrazione e login degli utenti con hashing sicuro delle password.
- **Feed delle Risorse:** Una bacheca interattiva dove gli utenti possono pubblicare, consultare e scaricare materiali, dispense e annunci.
- **Sistema di Chat & Messaggistica:** Chat interna con contatore in tempo reale dei messaggi non letti integrato direttamente nella barra di navigazione.
- **Gestione del Profilo:** Pagina personale utente per personalizzare i dettagli e caricare la propria foto profilo.
- **Avatar di Fallback Globali:** Sistema intelligente che verifica l'esistenza reale delle immagini sul server. Se un utente non ha impostato una foto (o il file è mancante), il sistema genera automaticamente un avatar vettoriale standard uniforme in tutta l'applicazione.
- **Interfaccia ad Alto Contrasto (Light/Dark Mode):** Gestione del tema chiaro/scuro nativa con memorizzazione della preferenza in `localStorage`. La modalità chiara è ottimizzata con contrasti aumentati e colori accessibili (`#1a1f2c`, `#556575`) per ridurre l'affaticamento visivo.
- **Navbar con Effetto Blur:** Barra di navigazione moderna ed elegante con effetto di sfocatura dello sfondo (`backdrop-filter`) stile iOS/Glassmorphism, ottimizzata per la fluidità dello scorrimento.

---

## 🛠️ Tecnologie Utilizzate

- **Backend:** PHP 8.x (Nativo)
- **Database:** MySQL / MariaDB con interfaccia di connessione sicura **PDO** (Prepared Statements contro SQL Injection)
- **Frontend:** HTML5, CSS3, JavaScript (ES6)
- **Framework CSS:** Bootstrap 5.3.2
- **Icone:** Bootstrap Icons v1.11.0
- **Font:** Inter (Google Fonts)

---

## 📂 Struttura delle Cartelle Principali

```text
project/
│
├── admin/            # Pannello di amministrazione e gestione della piattaforma
├── assets/           # Risorse statiche: CSS, JS e immagini di sistema (es. SC.png)
├── includes/         # Logica di backend riutilizzabile (connessione DB via PDO)
├── modules/          # Moduli specifici dell'applicazione (es. autenticazione)
├── partials/         # Componenti riutilizzabili dell'interfaccia (header, footer)
├── uploads/          # Cartella dedicata ai file caricati dagli utenti (es. foto profilo)
│
├── chat.php          # Pagina principale della messaggistica privata
├── chat_api.php      # API o script di gestione asincrona delle richieste della chat
├── config.php        # File di configurazione globale delle costanti e parametri di sistema
├── delete_post.php   # Script per la rimozione dei post dal feed
├── edit_profile.php  # Pagina per la modifica dei dati personali e dell'avatar
├── favicon.png       # Icona del sito visualizzata nella scheda del browser
├── friend_action.php # Gestione delle interazioni tra gli utenti (es. richieste di amicizia)
├── index.php         # Feed principale e bacheca dell'applicazione
├── profile.php       # Visualizzazione del profilo pubblico degli utenti
└── view.php          # Visualizzazione dettagliata di una specifica risorsa o post
