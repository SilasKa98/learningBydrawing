-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 24. Jul 2022 um 17:21
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
(1, 'Zahlen', 'Null,Eins,Zwei,Drei,Vier,Fünf,Sechs,Sieben,Acht,Neun'),
(2, 'Buchstaben', 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z'),
(3, 'Formen', 'placeholder,placeholder'),
(4, 'Hiragana', 'O,Ki,Su,Tsu,Na,Ha,Ma,Ya,Re,Wo');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `learningplans`
--

CREATE TABLE `learningplans` (
  `ID` int(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `data` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `learningplans`
--

INSERT INTO `learningplans` (`ID`, `uuid`, `category`, `data`, `name`) VALUES
(10, '62cd8d8180eea', 'Buchstaben', 'b,q,p,e,d,h', 'schwere Buchstaben 1'),
(12, '62cd8d8180eea', 'Zahlen', 'Zwei,Drei,Vier,Fünf,Sechs,Sieben,Acht', 'TestFoo'),
(13, '62cd8d8180eea', 'Zahlen', 'Null,Sechs,Sieben,Drei,Vier', 'NeuerPlan'),
(14, '62cd8d8180eea', 'Buchstaben', 'm,o,q,r,s,s,k,h,g', 'Buchstaben2'),
(15, '62cd8d8180eea', 'Hiragana', 'Ki,Su,Tsu,Na,Ha', 'japanisch1'),
(16, '62cd8d8180eea', 'Buchstaben', 'a,e,g,j,i,h,l,m,n', 'Lernplan2'),
(17, '62cd8d8180eea', 'Buchstaben', 'b,a,c,d,e,f', 'Testneu'),
(18, '62cd8d8180eea', 'Zahlen', 'Null,Eins,Zwei,Fünf,Sieben,Sechs,Fünf,Vier,Zwei,Eins', 'TestNeu3'),
(19, '62cf3c5393c16', 'Hiragana', 'Ki,Su,Tsu,Na,Ma,Ha,O', 'TestHiragana'),
(20, '62cf3c5393c16', 'Zahlen', 'Null,Eins,Fünf,Vier,Drei,Sieben', 'Zahlen1'),
(21, '62cd8d8180eea', 'Zahlen', 'Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins,Eins', 'Einsen');

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
(14, '62cd8d8180eea', 'Zahlen', 'Sieben', 53, 28),
(15, '62cd8d8180eea', 'Zahlen', 'Neun', 95, 93),
(16, '62cd8d8180eea', 'Zahlen', 'Zwei', 65, 7),
(17, '62cd8d8180eea', 'Zahlen', 'Vier', 46, 23),
(18, '62cd8d8180eea', 'Zahlen', 'Sechs', 68, 52),
(19, '62cd8d8180eea', 'Zahlen', 'Fünf', 55, 12),
(20, '62cd8d8180eea', 'Zahlen', 'Eins', 42, 92),
(21, '62cd8d8180eea', 'Zahlen', 'Null', 67, 20),
(22, '62cd8d8180eea', 'Zahlen', 'Acht', 57, 30),
(23, '62cd8d8180eea', 'Zahlen', 'Drei', 65, 23),
(24, '62cf1764d2192', 'Zahlen', 'Acht', 1, 0),
(25, '62cf1764d2192', 'Zahlen', 'Vier', 2, 0),
(26, '62cf1764d2192', 'Zahlen', 'Null', 2, 1),
(27, '62cf1764d2192', 'Zahlen', 'Sieben', 1, 1),
(28, '62cf1764d2192', 'Zahlen', 'Zwei', 2, 2),
(29, '62cf3c5393c16', 'Zahlen', 'Eins', 1, 8),
(30, '62cf3c5393c16', 'Zahlen', 'Drei', 2, 0),
(31, '62cf3c5393c16', 'Zahlen', 'Null', 8, 5),
(32, '62cf3c5393c16', 'Zahlen', 'Acht', 5, 5),
(33, '62cf3c5393c16', 'Zahlen', 'Neun', 3, 9),
(34, '62cf3c5393c16', 'Zahlen', 'Vier', 4, 1),
(35, '62cf3c5393c16', 'Zahlen', 'Sieben', 1, 0),
(36, '62cf3c5393c16', 'Zahlen', 'Fünf', 1, 0),
(39, '62cd8d8180eea', 'Buchstaben', 'I', 2, 0),
(40, '62cd8d8180eea', 'Buchstaben', 'D', 8, 7),
(41, '62cd8d8180eea', 'Buchstaben', 'L', 1, 0),
(42, '62cd8d8180eea', 'Buchstaben', 'T', 5, 2),
(43, '62cd8d8180eea', 'Buchstaben', 'Z', 3, 2),
(44, '62cd8d8180eea', 'Buchstaben', 'G', 8, 5),
(45, '62cd8d8180eea', 'Buchstaben', 'E', 1, 0),
(46, '62cd8d8180eea', 'Buchstaben', 'F', 2, 0),
(47, '62cd8d8180eea', 'Buchstaben', 'K', 5, 8),
(48, '62cd8d8180eea', 'Buchstaben', 'H', 2, 1),
(49, '62cd8d8180eea', 'Buchstaben', 'A', 4, 9),
(50, '62cd8d8180eea', 'Buchstaben', 'V', 1, 2),
(51, '62cd8d8180eea', 'Buchstaben', 'Y', 1, 0),
(52, '62cd8d8180eea', 'Buchstaben', 'S', 0, 1),
(53, '62cd8d8180eea', 'Buchstaben', 'R', 1, 2),
(54, '62cd8d8180eea', 'Buchstaben', 'X', 1, 0),
(55, '62cd8d8180eea', 'Buchstaben', 'W', 1, 0),
(56, '62cd8d8180eea', 'Buchstaben', 'P', 3, 1),
(57, '62cd8d8180eea', 'Buchstaben', 'M', 0, 2),
(58, '62cd8d8180eea', 'Buchstaben', 'J', 3, 1),
(59, '62cf3c5393c16', 'Buchstaben', 'W', 3, 0),
(60, '62cf3c5393c16', 'Buchstaben', 'F', 2, 1),
(61, '62cf3c5393c16', 'Buchstaben', 'D', 4, 16),
(62, '62cf3c5393c16', 'Buchstaben', 'A', 1, 0),
(63, '62cf3c5393c16', 'Buchstaben', 'J', 3, 7),
(64, '62cf3c5393c16', 'Buchstaben', 'K', 3, 1),
(65, '62cf3c5393c16', 'Buchstaben', 'Q', 2, 0),
(66, '62cf3c5393c16', 'Buchstaben', 'P', 2, 1),
(67, '62cf3c5393c16', 'Buchstaben', 'B', 1, 0),
(68, '62cf3c5393c16', 'Buchstaben', 'S', 2, 0),
(69, '62cf3c5393c16', 'Buchstaben', 'C', 1, 0),
(70, '62cf3c5393c16', 'Buchstaben', 'R', 2, 3),
(71, '62cd8d8180eea', 'Hiragana', 'Re', 1, 3),
(72, '62cd8d8180eea', 'Hiragana', 'Ma', 0, 4),
(73, '62cd8d8180eea', 'Hiragana', 'Tsu', 6, 15),
(74, '62cd8d8180eea', 'Hiragana', 'Ki', 0, 1),
(75, '62cd8d8180eea', 'Hiragana', 'O', 0, 1),
(76, '62cd8d8180eea', 'Hiragana', 'Ha', 1, 0),
(77, '62cd8d8180eea', 'Hiragana', 'Wo', 0, 1),
(78, '62cd8d8180eea', 'Buchstaben', 'B', 1, 0),
(79, '62cd8d8180eea', 'Buchstaben', 'C', 0, 1),
(80, '62cf3c5393c16', 'Zahlen', 'Zwei', 3, 0),
(81, '62cf3c5393c16', 'Buchstaben', 'i', 1, 0),
(82, '62cf3c5393c16', 'Buchstaben', 'v', 1, 0),
(83, '62cf3c5393c16', 'Zahlen', 'Sechs', 0, 1),
(84, '62cd8d8180eea', 'Buchstaben', 'q', 1, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `loginsystem`
--

CREATE TABLE `loginsystem` (
  `user_id` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_pwd` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `allowImageSave` int(255) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `loginsystem`
--

INSERT INTO `loginsystem` (`user_id`, `user_email`, `user_pwd`, `uuid`, `allowImageSave`) VALUES
('Silas', 'Silas@silas.de', '$2y$10$KzHVkbjnql0m.0NOUVqJrOtwJea5hBe98bXKtW9f0rPGP1cWLg3f.', '62cd8d8180eea', 1),
('TestUser', 'Test@test.de', '$2y$10$MqDdPYUIO2CVOc8Bbz0tq.H5/uvSG9cg4M4X1gv/Bsz96miFlDkWK', '62cf1764d2192', 0),
('Fanny', 'fanny@fanny.de', '$2y$10$j8GS3N7HQ4bXL6BLmvD4p.YpL7fYjngouaU5gfLxZmakMW9Pt0dji', '62cf3c5393c16', 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `datasets`
--
ALTER TABLE `datasets`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `learningplans`
--
ALTER TABLE `learningplans`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk2_uuid` (`uuid`);

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
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `datasets`
--
ALTER TABLE `datasets`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `learningplans`
--
ALTER TABLE `learningplans`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT für Tabelle `learningresults`
--
ALTER TABLE `learningresults`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `learningplans`
--
ALTER TABLE `learningplans`
  ADD CONSTRAINT `fk2_uuid` FOREIGN KEY (`uuid`) REFERENCES `loginsystem` (`uuid`);

--
-- Constraints der Tabelle `learningresults`
--
ALTER TABLE `learningresults`
  ADD CONSTRAINT `fk_uuid` FOREIGN KEY (`uuid`) REFERENCES `loginsystem` (`uuid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
