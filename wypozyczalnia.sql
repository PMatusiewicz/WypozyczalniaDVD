-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 11:29 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wypozyczalnia`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `katalog`
--

CREATE TABLE `katalog` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `rezyseria` varchar(100) NOT NULL,
  `gatunek` varchar(100) NOT NULL,
  `ilosc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `katalog`
--

INSERT INTO `katalog` (`id`, `nazwa`, `rezyseria`, `gatunek`, `ilosc`) VALUES
(1, 'Incepcja', 'Christopher Nolan', 'Sci-Fi', 5),
(2, 'Ojciec chrzestny', 'Francis Ford Coppola', 'Gangsterski', 4),
(3, 'Skazani na Shawshank', 'Frank Darabont', 'Dramat', 10),
(4, 'Matrix', 'Lana i Lilly Wachowski', 'Sci-Fi', 8),
(5, 'Gladiator', 'Ridley Scott', 'Historyczny', 12),
(6, 'Pulp Fiction', 'Quentin Tarantino', 'Kryminał', 9),
(7, 'Forrest Gump', 'Robert Zemeckis', 'Dramat', 2),
(8, 'Fight Club', 'David Fincher', 'Thriller', 3),
(9, 'Titanic', 'James Cameron', 'Romans', 13),
(10, 'Django', 'Quentin Tarantino', 'Western', 8),
(11, 'Interstellar', 'Christopher Nolan', 'Sci-Fi', 5),
(12, 'Requiem dla snu', 'Darren Aronofsky', 'Psychologiczny', 2),
(13, 'Joker', 'Todd Phillips', 'Dramat', 11),
(14, 'Król Lew', 'Roger Allers', 'Animacja', 16),
(15, 'Avengers: Koniec gry', 'Russo Brothers', 'Akcja', 19),
(16, 'Lot nad kukułczym gniazdem', 'Miloš Forman', 'Dramat', 1),
(17, 'Siedem', 'David Fincher', 'Thriller', 9),
(18, 'Władca Pierścieni: Drużyna Pierścienia', 'Peter Jackson', 'Fantasy', 18),
(19, 'Piraci z Karaibów', 'Gore Verbinski', 'Przygodowy', 6),
(20, 'American Beauty', 'Sam Mendes', 'Dramat', 13),
(21, 'Czas Apokalipsy', 'Francis Ford Coppola', 'Wojenny', 2),
(22, 'Braveheart', 'Mel Gibson', 'Historyczny', 6),
(23, 'Szeregowiec Ryan', 'Steven Spielberg', 'Wojenny', 20),
(24, 'La La Land', 'Damien Chazelle', 'Muzyczny', 12),
(25, 'Blade Runner 2049', 'Denis Villeneuve', 'Sci-Fi', 9),
(26, 'Gran Torino', 'Clint Eastwood', 'Dramat', 5),
(27, 'Whiplash', 'Damien Chazelle', 'Muzyczny', 14),
(28, 'The Social Network', 'David Fincher', 'Biograficzny', 8),
(29, 'Her', 'Spike Jonze', 'Romans', 0),
(30, 'Birdman', 'Alejandro G. Iñárritu', 'Dramat', 7),
(31, 'Parasite', 'Bong Joon-ho', 'Thriller', 10),
(32, 'Oldboy', 'Park Chan-wook', 'Akcja', 6),
(33, 'Życie jest piękne', 'Roberto Benigni', 'Dramat', 4),
(34, 'Grand Budapest Hotel', 'Wes Anderson', 'Komedia', 13),
(35, '1917', 'Sam Mendes', 'Wojenny', 3),
(36, 'Grawitacja', 'Alfonso Cuarón', 'Sci-Fi', 10),
(37, 'Spotlight', 'Tom McCarthy', 'Biograficzny', 6),
(38, 'Drive', 'Nicolas Winding Refn', 'Thriller', 1),
(39, 'Moonlight', 'Barry Jenkins', 'Dramat', 9),
(40, 'The Revenant', 'Alejandro G. Iñárritu', 'Przygodowy', 5);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `nazwa` varchar(20) NOT NULL,
  `haslo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wypozyczenia`
--

CREATE TABLE `wypozyczenia` (
  `id` int(11) NOT NULL,
  `id_katalog` int(11) NOT NULL,
  `id_uzytkownicy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `katalog`
--
ALTER TABLE `katalog`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `katalog`
--
ALTER TABLE `katalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wypozyczenia`
--
ALTER TABLE `wypozyczenia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
