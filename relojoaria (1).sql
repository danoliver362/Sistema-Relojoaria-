-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/12/2025 às 18:25
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
-- Banco de dados: `relojoaria`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nome` varchar(30) DEFAULT NULL,
  `sobrenome` varchar(50) DEFAULT NULL,
  `cpf` varchar(11) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome`, `sobrenome`, `cpf`, `tipo`, `telefone`) VALUES
(2, 'Heron', 'ALVARENGA DANIELETTO', '10826224784', 'Atacado', '(21) 99657-8000'),
(3, 'Rosilane ', 'Oliveira Rosa', '108.262.247', 'Varejo', '(21) 99544-6751'),
(4, 'vanderlei ', 'alvarenga', '052.379.867', 'Varejo', '(21) 99514-5653'),
(5, 'DANIEL', 'ALVARENGA', '181.768.227', 'Atacado', '(21) 99744-2808');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ordem_servico`
--

CREATE TABLE `ordem_servico` (
  `id_ordem` int(11) NOT NULL,
  `descricao` varchar(150) DEFAULT NULL,
  `data_entrada` date DEFAULT NULL,
  `valor` float DEFAULT NULL,
  `forma_pgt` varchar(30) DEFAULT NULL,
  `garantia` varchar(30) DEFAULT NULL,
  `id_relogio` int(11) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Em análise',
  `foto_entrada` varchar(255) DEFAULT NULL,
  `foto_saida` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ordem_servico`
--

INSERT INTO `ordem_servico` (`id_ordem`, `descricao`, `data_entrada`, `valor`, `forma_pgt`, `garantia`, `id_relogio`, `status`, `foto_entrada`, `foto_saida`) VALUES
(6, 'bateria', '2025-10-25', 60, 'PIX', '6 meses', 5, 'Concluído', 'uploads/ordens_servico/foto_entrada_6_6930e0af4530f.jpg', 'uploads/ordens_servico/foto_saida_6_6930e0af45a72.jpg'),
(8, 'Troca de bateria', '2025-12-01', 20, 'Cartão de Crédito', '6 meses', 7, 'Em Análise', 'uploads/ordens_servico/foto_entrada_8_692e225b9d5b7.jpg', 'uploads/ordens_servico/foto_saida_8_692e225b9d956.jpg'),
(9, 'Troca de pulseira', '2025-11-30', 30, 'Cartão de Débito', 'Sem garantia', 8, 'Aguardando retirada', 'uploads/ordens_servico/foto_entrada_9_692e3ffa5abbc.jpg', 'uploads/ordens_servico/foto_saida_9_692e40044376d.jpg'),
(13, 'troca de pulseira ', '2025-11-25', 25, 'Cartão de Crédito', 'Sem garantia', 12, 'Concluído', 'uploads/ordens_servico/foto_entrada_13_6931c05c25bb3.jpg', 'uploads/ordens_servico/foto_saida_13_6931c05c2630d.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `relogio`
--

CREATE TABLE `relogio` (
  `id_relogio` int(11) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `num_serie` varchar(50) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `foto_relogio` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `relogio`
--

INSERT INTO `relogio` (`id_relogio`, `marca`, `modelo`, `num_serie`, `id_cliente`, `foto_relogio`) VALUES
(5, 'Invicta ', 'Bolt Zeus', '29998', 2, 'uploads/relogios/6930e04c0642c.jpg'),
(7, 'Bulova', 'Marine Star', '96b256', 4, 'uploads/relogios/692e21d2d2247.jpg'),
(8, 'Bulova', 'Marine Star', '98A187', 4, 'uploads/relogios/692e3f501b17f.jpg'),
(9, 'Champion', 'LED', 'CH40080V', 3, 'uploads/relogios/6930856e196ea.jpg'),
(11, 'Mondaine', 'Analógico', '32720LPMKBE3', 3, 'uploads/relogios/1764788815_51yPBuN3aML._AC_SX679_.jpg'),
(12, 'Champion', 'Analógico Couro', 'CN20364M', 2, 'uploads/relogios/1764788937_61+5VjNO2kL._AC_SX679_.jpg'),
(13, 'Casio', 'Digital Femenino', 'LW-204-1ADF', 5, 'uploads/relogios/1764789900_casio.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `login`, `senha`) VALUES
(1, 'admin', '1234'),
(2, 'fatudo', '1234');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Índices de tabela `ordem_servico`
--
ALTER TABLE `ordem_servico`
  ADD PRIMARY KEY (`id_ordem`),
  ADD KEY `id_relogio` (`id_relogio`);

--
-- Índices de tabela `relogio`
--
ALTER TABLE `relogio`
  ADD PRIMARY KEY (`id_relogio`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `ordem_servico`
--
ALTER TABLE `ordem_servico`
  MODIFY `id_ordem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `relogio`
--
ALTER TABLE `relogio`
  MODIFY `id_relogio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `ordem_servico`
--
ALTER TABLE `ordem_servico`
  ADD CONSTRAINT `ordem_servico_ibfk_1` FOREIGN KEY (`id_relogio`) REFERENCES `relogio` (`id_relogio`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `relogio`
--
ALTER TABLE `relogio`
  ADD CONSTRAINT `relogio_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
