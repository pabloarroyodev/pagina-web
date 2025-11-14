-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-11-2025 a las 11:39:49
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `calcular_valor_del_peso_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calculations`
--

CREATE TABLE `calculations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `input_amount` decimal(15,2) NOT NULL,
  `start_year` int(11) NOT NULL,
  `start_month` tinyint(4) NOT NULL,
  `end_year` int(11) NOT NULL,
  `end_month` tinyint(4) NOT NULL,
  `result_amount` decimal(15,2) NOT NULL,
  `accumulated_inflation` decimal(10,4) NOT NULL,
  `avg_monthly_inflation` decimal(10,4) NOT NULL,
  `avg_yearly_inflation` decimal(10,4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `calculations`
--

INSERT INTO `calculations` (`id`, `user_id`, `type`, `input_amount`, `start_year`, `start_month`, `end_year`, `end_month`, `result_amount`, `accumulated_inflation`, `avg_monthly_inflation`, `avg_yearly_inflation`, `created_at`) VALUES
(1, 3, 'inflation', 2342.00, 2024, 1, 2025, 11, 6369.43, 171.9655, 4.6527, 72.5868, '2025-11-14 10:11:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dollar_calculations`
--

CREATE TABLE `dollar_calculations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `dollar_type` enum('blue','official') NOT NULL,
  `peso_amount` decimal(15,2) NOT NULL,
  `start_year` int(11) NOT NULL,
  `start_month` tinyint(4) NOT NULL,
  `end_year` int(11) NOT NULL,
  `end_month` tinyint(4) NOT NULL,
  `start_usd_equivalent` decimal(15,4) NOT NULL,
  `end_usd_equivalent` decimal(15,4) NOT NULL,
  `percentage_change` decimal(10,4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dollar_calculations`
--

INSERT INTO `dollar_calculations` (`id`, `user_id`, `dollar_type`, `peso_amount`, `start_year`, `start_month`, `end_year`, `end_month`, `start_usd_equivalent`, `end_usd_equivalent`, `percentage_change`, `created_at`) VALUES
(1, 3, 'blue', 3123.00, 2023, 1, 2024, 5, 9.0785, 3.0029, -66.9231, '2025-11-14 10:22:44'),
(2, 3, 'official', 123441.00, 2024, 10, 2025, 11, 122.7218, 82.9917, -32.3742, '2025-11-14 10:23:10'),
(3, 2, 'official', 2333.00, 2024, 6, 2025, 11, 2.4902, 1.5685, -37.0125, '2025-11-14 10:29:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `price_comparison_calculations`
--

CREATE TABLE `price_comparison_calculations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `amount1` decimal(15,2) NOT NULL,
  `year1` int(11) NOT NULL,
  `month1` tinyint(4) NOT NULL,
  `amount2` decimal(15,2) NOT NULL,
  `year2` int(11) NOT NULL,
  `month2` tinyint(4) NOT NULL,
  `adjusted_amount` decimal(15,2) NOT NULL,
  `percentage_difference` decimal(10,4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `price_comparison_calculations`
--

INSERT INTO `price_comparison_calculations` (`id`, `user_id`, `amount1`, `year1`, `month1`, `amount2`, `year2`, `month2`, `adjusted_amount`, `percentage_difference`, `created_at`) VALUES
(1, 3, 1233.00, 2021, 1, 43234.00, 2025, 11, 0.00, 40.8243, '2025-11-14 10:21:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$W26wLURDVdpMDkqa9HVgBuqGMCDYTTKumXOjMr4ww59E4djEwuzN2', 'admin', '2025-11-14 08:01:03'),
(2, 'pablo', 'pabloarroyoib@gmail.com', '$2y$10$flmk/ux3VoCp.g/GHzgDX.yh978rIuZrPlNf8/C15EsXFEAYt3VpW', 'user', '2025-11-14 08:45:57'),
(3, 'nacho', 'nacho@gmail.com', '$2y$10$JCoQT/8EXQgF4ST8ZB9p2uxQknwzHkgkjqYfXcTqUIcLFVZe5K3He', 'user', '2025-11-14 09:56:18');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calculations`
--
ALTER TABLE `calculations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_calculations_user` (`user_id`);

--
-- Indices de la tabla `dollar_calculations`
--
ALTER TABLE `dollar_calculations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dollar_calc_user` (`user_id`);

--
-- Indices de la tabla `price_comparison_calculations`
--
ALTER TABLE `price_comparison_calculations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_price_comp_user` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calculations`
--
ALTER TABLE `calculations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `dollar_calculations`
--
ALTER TABLE `dollar_calculations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `price_comparison_calculations`
--
ALTER TABLE `price_comparison_calculations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calculations`
--
ALTER TABLE `calculations`
  ADD CONSTRAINT `fk_calculations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `dollar_calculations`
--
ALTER TABLE `dollar_calculations`
  ADD CONSTRAINT `fk_dollar_calc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `price_comparison_calculations`
--
ALTER TABLE `price_comparison_calculations`
  ADD CONSTRAINT `fk_price_comp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
