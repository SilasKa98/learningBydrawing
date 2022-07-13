-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 13. Jul 2022 um 20:23
-- Server-Version: 10.4.24-MariaDB
-- PHP-Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `learningbydrawing`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `datasets`
--

CREATE TABLE `datasets` (
  `ID` int(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `data` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `datasets`
--

INSERT INTO `datasets` (`ID`, `category`, `data`) VALUES
(1, 'Zahlen', 'Null,Eins,Zwei,Drei,Vier,Fünf,Sechs,Sieben,Acht,Neun');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learningresults`
--

CREATE TABLE `learningresults` (
  `ID` int(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `tested_value` varchar(255) NOT NULL,
  `right_answers` int(255) NOT NULL,
  `wrong_answers` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `learningresults`
--

INSERT INTO `learningresults` (`ID`, `uuid`, `category`, `tested_value`, `right_answers`, `wrong_answers`) VALUES
(14, '62cd8d8180eea', 'Zahlen', 'Sieben', 3, 1),
(15, '62cd8d8180eea', 'Zahlen', 'Neun', 1, 25),
(16, '62cd8d8180eea', 'Zahlen', 'Zwei', 1, 0),
(17, '62cd8d8180eea', 'Zahlen', 'Vier', 4, 2),
(18, '62cd8d8180eea', 'Zahlen', 'Sechs', 5, 13),
(19, '62cd8d8180eea', 'Zahlen', 'Fünf', 4, 0),
(20, '62cd8d8180eea', 'Zahlen', 'Eins', 7, 5),
(21, '62cd8d8180eea', 'Zahlen', 'Null', 3, 1),
(22, '62cd8d8180eea', 'Zahlen', 'Acht', 6, 10),
(23, '62cd8d8180eea', 'Zahlen', 'Drei', 7, 10);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `loginsystem`
--

CREATE TABLE `loginsystem` (
  `user_id` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_pwd` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `loginsystem`
--

INSERT INTO `loginsystem` (`user_id`, `user_email`, `user_pwd`, `uuid`) VALUES
('Silas', 'Silas@silas.de', '$2y$10$KzHVkbjnql0m.0NOUVqJrOtwJea5hBe98bXKtW9f0rPGP1cWLg3f.', '62cd8d8180eea');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

CREATE TABLE `settings` (
  `ID` int(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `repeats` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `datasets`
--
ALTER TABLE `datasets`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `learningresults`
--
ALTER TABLE `learningresults`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_uuid` (`uuid`);

--
-- Indizes für die Tabelle `loginsystem`
--
ALTER TABLE `loginsystem`
  ADD PRIMARY KEY (`uuid`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `datasets`
--
ALTER TABLE `datasets`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `learningresults`
--
ALTER TABLE `learningresults`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `settings`
--
ALTER TABLE `settings`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `learningresults`
--
ALTER TABLE `learningresults`
  ADD CONSTRAINT `fk_uuid` FOREIGN KEY (`uuid`) REFERENCES `loginsystem` (`uuid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
