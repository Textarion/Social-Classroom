-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Giu 17, 2026 alle 14:01
-- Versione del server: 8.0.45
-- Versione PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_dsferrazza`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `contents`
--

CREATE TABLE `contents` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `titolo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descrizione` text COLLATE utf8mb4_unicode_ci,
  `materia` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grado_scolastico` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prezzo` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `contents`
--

INSERT INTO `contents` (`id`, `user_id`, `titolo`, `descrizione`, `materia`, `grado_scolastico`, `file_path`, `prezzo`, `created_at`) VALUES
(1, 2, 'Appunti Completi Analisi Matematica 1', 'Raccolta di teoremi, dimostrazioni ed esercizi svolti passo passo di limiti, derivate e integrali.', 'Matematica', NULL, 'analisi1.pdf', '0.00', '2026-06-04 23:01:24'),
(2, 3, 'Riassunto Diritto Privato (Trimarchi)', 'Schemi concettuali e riassunti dettagliati capitolo per capitolo per superare l\'esame di Privato.', 'Diritto', NULL, 'privato_schemi.pdf', '4.99', '2026-06-04 23:01:24'),
(3, 2, 'Guida Pratica alle Basi di Dati e SQL', 'Manuale tascabile con query ed esempi pratici di JOIN, GROUP BY e progettazione database ER.', 'Informatica', NULL, 'sql_guide.pdf', '0.00', '2026-06-04 23:01:24'),
(4, 4, 'Mappa Concettuale: La Seconda Guerra Mondiale', 'Schema riassuntivo in PDF ad alta risoluzione, ottimo per il ripasso veloce o per la maturità.', 'Storia', NULL, 'storia_2guerra.pdf', '1.50', '2026-06-04 23:01:24'),
(5, 5, 'Esercizi Svolti di Fisica Generale 1', 'Oltre 50 problemi risolti e commentati su cinematica, dinamica e termodinamica per ingegneria.', 'Fisica', NULL, 'physic1_esercizi.pdf', '3.50', '2026-06-04 23:01:24'),
(6, 3, 'Glossario Termini di Biologia Cellulare', 'Elenco completo e definizioni di organelli, mitosi, meiosi e sintesi proteica.', 'Biologia', NULL, 'biologia_cellula.pdf', '0.00', '2026-06-04 23:01:24'),
(7, 4, 'Riassunto \"Il Fu Mattia Pascal\" - Pirandello', 'Analisi dei personaggi, tematiche principali e commento critico capitolo per capitolo.', 'Letteratura', NULL, 'mattia_pascal.pdf', '2.00', '2026-06-04 23:01:24'),
(8, 2, 'Dispense Economia Politica (Micro e Macro)', 'Materiale ufficiale del corso con grafici ed equazioni spiegate in modo semplice per non frequentanti.', 'Economia', NULL, 'economia_dispense.pdf', '0.00', '2026-06-04 23:01:24'),
(9, 5, 'Corso Base di Chimica Organica', 'Appunti chiari sui meccanismi di reazione, alcani, alcheni e composti aromatici.', 'Chimica', NULL, 'chimica_organica.pdf', '5.00', '2026-06-04 23:01:24'),
(10, 3, 'Frasario e Regole di Grammatica Inglese (B2)', 'Tabella riassuntiva di tutti i tempi verbali (Phrasal Verbs inclusi) con esempi d\'uso quotidiano.', 'Lingue', NULL, 'english_b2.pdf', '0.00', '2026-06-04 23:01:24'),
(11, 7, 'Prova', 'Prova', 'Informatica', 'Superiori', 'doc_6a2659c5ee12c6.64543731.png', '0.00', '2026-06-08 05:57:25'),
(12, 6, 'Capolavoro di Informatica', 'Social Classroom è una piattaforma di condivisione di contenuti scolastici come: compiti svolti, appunti e programmi di studio. I contenuti possono essere condivisi a scopo di lucro, o semplicemente condivisi gratuitamente per il pubblico. Comprende funzioni come una lista di amicizie e chat, modifica del profilo e pubblicazione di recensioni da 1 a 5 stelle per i post condivisi, il tutto sviluppato con: HTML, CSS, JS, PHP, e Bootstrap.', 'Informatica', '5AIT', '6a26ce4fdae52.png', '0.00', '2026-06-08 14:14:39');

-- --------------------------------------------------------

--
-- Struttura della tabella `feedback`
--

CREATE TABLE `feedback` (
  `id` int NOT NULL,
  `content_id` int NOT NULL,
  `user_id` int NOT NULL,
  `valutazione` tinyint DEFAULT NULL,
  `commento` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dump dei dati per la tabella `feedback`
--

INSERT INTO `feedback` (`id`, `content_id`, `user_id`, `valutazione`, `commento`, `created_at`) VALUES
(1, 1, 3, 5, 'Appunti salvavita! Spiegazioni molto più chiare rispetto al libro di testo.', '2026-06-04 23:01:24'),
(2, 1, 4, 4, 'Ottimo materiale, l\'unica pecca è la grafia in alcune formule, ma si capisce tutto.', '2026-06-04 23:01:24'),
(3, 2, 2, 5, 'Riassunto fatto davvero bene. Gli schemi grafici aiutano tantissimo a memorizzare.', '2026-06-04 23:01:24'),
(4, 2, 5, 3, 'Buono, ma mancano le ultime riforme sul diritto di famiglia.', '2026-06-04 23:01:24'),
(5, 3, 4, 5, 'SQL spiegato ai principianti in modo perfetto. Super consigliato!', '2026-06-04 23:01:24'),
(6, 5, 3, 4, 'Gli esercizi sono complessi al punto giusto. Mi hanno aiutato a superare lo scritto.', '2026-06-04 23:01:24'),
(7, 7, 5, 5, 'Analisi critica impeccabile. Si vede che lo studente ha approfondito la materia.', '2026-06-04 23:01:24'),
(8, 8, 4, 4, 'Grafici macroeconomici chiarissimi. Perfetto per il ripasso pre-esame.', '2026-06-04 23:01:24'),
(9, 9, 3, 2, 'Un po\' troppo sbrigativo sulle reazioni di sostituzione nucleofila.', '2026-06-04 23:01:24'),
(10, 10, 4, 5, 'Utilissimo per la preparazione dell\'esame di certificazione B2, grazie!', '2026-06-04 23:01:24'),
(14, 11, 6, 5, 'Na bumma', '2026-06-08 08:11:29');

-- --------------------------------------------------------

--
-- Struttura della tabella `friends`
--

CREATE TABLE `friends` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `friend_id` int NOT NULL,
  `status` enum('pending','accepted') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `status`, `created_at`) VALUES
(1, 6, 2, 'pending', '2026-06-08 05:32:17'),
(3, 6, 7, 'accepted', '2026-06-08 05:57:45');

-- --------------------------------------------------------

--
-- Struttura della tabella `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `buyer_id` int NOT NULL,
  `content_id` int NOT NULL,
  `prezzo_pagato` decimal(10,2) NOT NULL,
  `data_acquisto` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruolo` enum('studente','docente','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'studente',
  `bio` text COLLATE utf8mb4_unicode_ci,
  `foto_profilo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `ruolo`, `bio`, `foto_profilo`, `created_at`) VALUES
(1, 'admin', 'admin@socialclassroom.it', 'ef92b778bafe42154857d0509a22ec3f319bc5c62d0bb24464c489c7c09c6214', 'docente', 'Account Amministratore della piattaforma Social Classroom. Monitoraggio e supporto.', 'default.png', '2026-06-04 23:01:24'),
(2, 'Prof_Rossi', 'mario.rossi@scuola.it', 'ef92b778bafe42154857d0509a22ec3f319bc5c62d0bb24464c489c7c09c6214', 'docente', 'Insegnante di Matematica e Fisica. Condivido dispense, appunti di lezioni ed esercizi svolti per supportare gli studenti nel percorso di studi.', 'default.png', '2026-06-04 23:01:24'),
(3, 'Marco_Student', 'marco.verdi@studio.it', 'ef92b778bafe42154857d0509a22ec3f319bc5c62d0bb24464c489c7c09c6214', 'studente', 'Studente universitario al secondo anno. Qui per condividere i miei schemi di sintesi e trovare riassunti utili per la sessione d\'esami.', 'default.png', '2026-06-04 23:01:24'),
(4, 'Elena_M', 'elena.bianchi@scuola.it', 'ef92b778bafe42154857d0509a22ec3f319bc5c62d0bb24464c489c7c09c6214', 'studente', 'Appassionata di materie umanistiche, storia e letteratura. Condivido mappe concettuali mirate per la preparazione alla maturità.', 'default.png', '2026-06-04 23:01:24'),
(5, 'Prof_DeLuca', 'antonio.deluca@universita.it', 'ef92b778bafe42154857d0509a22ec3f319bc5c62d0bb24464c489c7c09c6214', 'docente', 'Docente di Scienze Naturali e Chimica. Pubblico guide pratiche di laboratorio e appunti teorici per una comprensione rapida della materia.', 'default.png', '2026-06-04 23:01:24'),
(6, 'Dave', 'davidesferrazza217@gmail.com', '1d9e3f129e34933980541dc86fce4c79f845d35c22bc8243f138877f256bfca6', 'studente', 'Sono uno studente dell\'I.I.S nel corso di Informatica e Telecomunicazioni, classe Quinta.', 'avatar_6_1780906312.jpeg', '2026-06-06 15:08:21'),
(7, 'Angelo', 'angelosferrazza03@gmail.com', '187f5eb20d561e756abde6fd9cef72bff091dd94bb1d45527859e860b82111a9', 'studente', 'Studente alberghiero', 'default.png', '2026-06-08 05:55:50'),
(8, 'Beaa', 'beatricemoncada6@gmail.com', '6e1811cf019a447d96be422aa0899d9c500fe2a346c0e6baf209933b3f007a5e', 'studente', '', 'default.png', '2026-06-08 11:38:58');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Indici per le tabelle `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indici per le tabelle `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `contents`
--
ALTER TABLE `contents`
  ADD CONSTRAINT `contents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
