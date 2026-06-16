-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13/05/2026 às 01:28
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cantina`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(8,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `categoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco`, `created_at`, `updated_at`, `foto`, `categoria`) VALUES
(4, 'Croissant', 9.00, '2026-03-11 00:53:24', '2026-03-17 23:21:39', NULL, ''),
(5, 'Pastel de flango', 9.00, '2026-03-11 00:55:27', '2026-03-17 23:27:11', NULL, ''),
(6, 'Brigadeiro', 4.00, '2026-03-11 01:03:55', '2026-03-18 00:08:54', NULL, ''),
(7, 'Sanduíche de presunto', 6.50, '2026-03-17 22:26:18', '2026-03-17 23:21:21', NULL, ''),
(8, 'Hambúrguer', 15.00, NULL, NULL, NULL, ''),
(9, 'Pizza', 25.00, NULL, NULL, NULL, ''),
(10, 'Bife com fritas', 18.50, NULL, NULL, NULL, ''),
(11, 'Churros', 6.00, NULL, NULL, NULL, ''),
(12, 'Torta de frango', 10.00, NULL, NULL, NULL, ''),
(13, 'Salada de atum', 12.00, NULL, NULL, NULL, ''),
(14, 'Risoto de camarão', 22.00, NULL, NULL, NULL, ''),
(15, 'Misto quente', 8.00, NULL, NULL, NULL, ''),
(16, 'Pão de queijo', 4.00, NULL, NULL, NULL, ''),
(17, 'Bolinho de bacalhau', 7.50, NULL, NULL, NULL, ''),
(19, 'Beijinho', 4.00, '2026-04-15 00:56:47', '2026-04-15 00:57:40', '1776214607_b9b278a34da7cfbac704.jpg', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(5) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tipo` varchar(255) NOT NULL DEFAULT 'usuario',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `senha_hash`, `created_at`, `updated_at`, `tipo`, `reset_token`, `reset_token_date`) VALUES
(1, 'admin@admin.com', '$2y$10$H.6N6sT0QyypFS12jpI7weGF2yBmbVOllBjAtphBo43RYtHXoEikW', '2026-04-28 23:11:02', '2026-04-28 23:11:02', 'admin', NULL, NULL),
(4, 'luiz.2020313187@aluno.iffar.edu.br', '$2y$10$odIreY2YBJRbyrJ6rRPUVOvoHlvTkjs8yr.uczaRHxytKpsVTPzoa', '2026-05-12 22:49:33', '2026-05-12 23:25:31', 'admin', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
