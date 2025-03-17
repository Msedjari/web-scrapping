-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 11-02-2025 a las 17:20:44
-- Versión del servidor: 8.0.41-0ubuntu0.24.04.1
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `Fastscore`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `match_data`
--

CREATE TABLE `match_data` (
  `id` int NOT NULL,
  `url` text NOT NULL,
  `team_home` text NOT NULL,
  `team_away` text NOT NULL,
  `score_home` int DEFAULT NULL,
  `score_away` int DEFAULT NULL,
  `match_time` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `news_data`
--

CREATE TABLE `news_data` (
  `id` int NOT NULL,
  `link` text NOT NULL,
  `title` text NOT NULL,
  `text` text,
  `content` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `team_info`
--

CREATE TABLE `team_info` (
  `id` bigint UNSIGNED NOT NULL,
  `league` varchar(255) DEFAULT NULL,
  `club_name` varchar(255) DEFAULT NULL,
  `president` varchar(255) DEFAULT NULL,
  `coach` varchar(255) DEFAULT NULL,
  `foundation_year` int DEFAULT NULL,
  `website` text,
  `stadium_name` varchar(255) DEFAULT NULL,
  `stadium_capacity` int DEFAULT NULL,
  `stadium_address` text,
  `stadium_image` text,
  `escudo_image` text,
  `plantilla_image` text,
  `url` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `team_logos`
--

CREATE TABLE `team_logos` (
  `id` int NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `league_name` varchar(100) NOT NULL,
  `logo` text,
  `logo_path` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `team_logos`
--


--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `match_data`
--
ALTER TABLE `match_data`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `news_data`
--
ALTER TABLE `news_data`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `team_info`
--
ALTER TABLE `team_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `club_name` (`club_name`);

--
-- Indices de la tabla `team_logos`
--
ALTER TABLE `team_logos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `match_data`
--
ALTER TABLE `match_data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=541;

--
-- AUTO_INCREMENT de la tabla `news_data`
--
ALTER TABLE `news_data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `team_info`
--
ALTER TABLE `team_info`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=446;

--
-- AUTO_INCREMENT de la tabla `team_logos`
--
ALTER TABLE `team_logos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;