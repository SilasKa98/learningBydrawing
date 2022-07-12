-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 12. Jul 2022 um 18:17
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
(11, '62cd8d8180eea', 'Zahlen', '9', 0, 1),
(12, '62cd8d8180eea', 'Zahlen', '3', 1, 0),
(13, '62cd8d8180eea', 'Zahlen', '8', 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `loginsystem`
--

CREATE TABLE `loginsystem` (
  `ID` int(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_pwd` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `loginsystem`
--

INSERT INTO `loginsystem` (`ID`, `user_id`, `user_email`, `user_pwd`, `uuid`) VALUES
(3, 'Silas', 'Silas@silas.de', '$2y$10$KzHVkbjnql0m.0NOUVqJrOtwJea5hBe98bXKtW9f0rPGP1cWLg3f.', '62cd8d8180eea');

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
-- Indizes für die Tabelle `learningresults`
--
ALTER TABLE `learningresults`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `loginsystem`
--
ALTER TABLE `loginsystem`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `learningresults`
--
ALTER TABLE `learningresults`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT für Tabelle `loginsystem`
--
ALTER TABLE `loginsystem`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `settings`
--
ALTER TABLE `settings`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
