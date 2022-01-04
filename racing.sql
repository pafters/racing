-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Янв 04 2022 г., 19:32
-- Версия сервера: 8.0.24
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `racing`
--

-- --------------------------------------------------------

--
-- Структура таблицы `arrival`
--

CREATE TABLE `arrival` (
  `id` int NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `race_id` int NOT NULL,
  `status` varchar(16) NOT NULL,
  `racer_1` int DEFAULT NULL,
  `racer_2` int DEFAULT NULL,
  `racer_3` int DEFAULT NULL,
  `racer_4` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `arrival`
--

INSERT INTO `arrival` (`id`, `name`, `race_id`, `status`, `racer_1`, `racer_2`, `racer_3`, `racer_4`) VALUES
(9, 'weeewgd', 1, 'racing', 15, 2, 4, 107),
(10, 'idk', 1, 'racing', 107, 107, 107, 107),
(12, 'sdfs', 1, 'racing', 107, 107, 107, 107),
(13, 'апвв', 1, 'racing', 107, 107, 107, 107);

-- --------------------------------------------------------

--
-- Структура таблицы `ball`
--

CREATE TABLE `ball` (
  `arrival_id` int DEFAULT NULL,
  `id` int NOT NULL,
  `x` float DEFAULT NULL,
  `y` float DEFAULT NULL,
  `speed_y` int DEFAULT NULL,
  `speed_x` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `ball`
--

INSERT INTO `ball` (`arrival_id`, `id`, `x`, `y`, `speed_y`, `speed_x`) VALUES
(9, 1, 584, 166, 3, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `laser`
--

CREATE TABLE `laser` (
  `arrival_id` int DEFAULT NULL,
  `id` int NOT NULL,
  `x` float DEFAULT NULL,
  `x2` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `laser`
--

INSERT INTO `laser` (`arrival_id`, `id`, `x`, `x2`) VALUES
(9, 1, 773, 773);

-- --------------------------------------------------------

--
-- Структура таблицы `race`
--

CREATE TABLE `race` (
  `id` int NOT NULL,
  `name` varchar(32) NOT NULL,
  `data` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `race`
--

INSERT INTO `race` (`id`, `name`, `data`) VALUES
(1, '666', NULL),
(2, 'Cool race', NULL),
(3, 'Awesome race', NULL),
(4, 'I have no problems with fantasy', NULL),
(6, 'idk', NULL),
(7, 'абалаба', NULL),
(8, 'dfdfdf', NULL),
(9, 'вапоы', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `racer`
--

CREATE TABLE `racer` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `x` float DEFAULT NULL,
  `y` float DEFAULT NULL,
  `angle` float DEFAULT NULL,
  `speed` float DEFAULT NULL,
  `coin` int DEFAULT NULL,
  `life` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `racer`
--

INSERT INTO `racer` (`id`, `user_id`, `x`, `y`, `angle`, `speed`, `coin`, `life`) VALUES
(104, 107, -200, -200, 0, 0, 18, 0),
(105, 2, -200, -200, 0, 0, 25, 0),
(106, 4, -200, -200, 0, 0, 37, 0),
(107, 15, 167.603, 390.228, 163, 6, 81, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(32) NOT NULL,
  `login` varchar(16) NOT NULL,
  `password` varchar(32) NOT NULL,
  `token` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `login`, `password`, `token`) VALUES
(1, 'Мария', 'masha', '111', NULL),
(2, 'Шрек', 'shrek', 'fiona', NULL),
(3, 'Антонина', 'tonya5000', '123', NULL),
(6, 'Клементина', 'baltazar1998', 'alopecia', NULL),
(9, 'Joan', 'rockandroll', '000', NULL),
(10, 'kdjhfksjh', 'sdf', 'qww222', NULL),
(11, 'ssads', 'asasasasa', '1213423', NULL),
(12, 'жижа', 'THUNDER8000', 'alcozelcer', NULL),
(13, 'Violet', 'katyaToWin', 'queennn', NULL),
(14, 'Nicky', 'youdontknowme', 'ammastar', NULL),
(15, 'reeeeee', 'edic', '124sa', 'cbc4c683fed3d7aa6753eaa25d5211cf');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `arrival`
--
ALTER TABLE `arrival`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ball`
--
ALTER TABLE `ball`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `laser`
--
ALTER TABLE `laser`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `race`
--
ALTER TABLE `race`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `racer`
--
ALTER TABLE `racer`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `arrival`
--
ALTER TABLE `arrival`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `ball`
--
ALTER TABLE `ball`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `laser`
--
ALTER TABLE `laser`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `race`
--
ALTER TABLE `race`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `racer`
--
ALTER TABLE `racer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
