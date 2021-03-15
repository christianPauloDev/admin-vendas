-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 14-Mar-2021 às 21:15
-- Versão do servidor: 10.4.13-MariaDB
-- versão do PHP: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projeto_vendas`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `id_informacoes_empresa_fk` int(11) NOT NULL,
  `titulo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `id_informacoes_empresa_fk`, `titulo`) VALUES
(1, 1, 'Sandália');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cupons`
--

CREATE TABLE `cupons` (
  `id` int(11) NOT NULL,
  `id_informacoes_empresa_fk` int(11) DEFAULT NULL,
  `titulo` varchar(50) NOT NULL,
  `tipo_desconto` enum('dinheiro','porcentagem') NOT NULL,
  `valor_desconto` float NOT NULL,
  `valor_minimo` decimal(5,2) DEFAULT NULL,
  `data_validade` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `cupons`
--

INSERT INTO `cupons` (`id`, `id_informacoes_empresa_fk`, `titulo`, `tipo_desconto`, `valor_desconto`, `valor_minimo`, `data_validade`, `status`) VALUES
(1, 1, 'app10', 'dinheiro', 10, '10.00', '2021-03-21', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `razao_social` varchar(100) NOT NULL,
  `login` varchar(100) NOT NULL,
  `senha` text NOT NULL,
  `status` enum('ativo','bloqueado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `empresas`
--

INSERT INTO `empresas` (`id`, `razao_social`, `login`, `senha`, `status`) VALUES
(1, 'Projeto Vendas', 'vendas', '$2y$10$U16VYm0QN8B4Kzt9NraZ.OQiW6W1j1bVz6fKVOG/FS8YT3McckPrK', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura da tabela `informacoes_empresas`
--

CREATE TABLE `informacoes_empresas` (
  `id` int(11) NOT NULL,
  `id_empresa_fk` int(11) NOT NULL,
  `nome_fantasia` varchar(100) NOT NULL,
  `fone` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cnpj` varchar(18) NOT NULL,
  `descricao` text DEFAULT NULL,
  `img` varchar(100) DEFAULT NULL,
  `status` enum('aberto','fechado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `informacoes_empresas`
--

INSERT INTO `informacoes_empresas` (`id`, `id_empresa_fk`, `nome_fantasia`, `fone`, `email`, `cnpj`, `descricao`, `img`, `status`) VALUES
(1, 1, 'Vendas', '85988759706', 'c', '000000000000000000', NULL, NULL, 'fechado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `id_categoria_fk` int(11) NOT NULL,
  `nome_produto` varchar(80) NOT NULL,
  `preco` decimal(5,2) NOT NULL,
  `img` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `status` enum('disponível','esgotado','indisponível') NOT NULL DEFAULT 'disponível'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `id_categoria_fk`, `nome_produto`, `preco`, `img`, `descricao`, `status`) VALUES
(1, 1, 'Tiras Pretas', '50.00', '1.png', 'Tiras Pretas', 'disponível'),
(2, 1, 'Tiras Brancas', '80.00', '1.png', 'Esta sandália tem tiras vermelhas', 'disponível');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `informacoes_empresas_fk_idx` (`id_informacoes_empresa_fk`);

--
-- Índices para tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cupons_informacoes_empresa_fk_idx` (`id_informacoes_empresa_fk`);

--
-- Índices para tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`);

--
-- Índices para tabela `informacoes_empresas`
--
ALTER TABLE `informacoes_empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj_UNIQUE` (`cnpj`),
  ADD UNIQUE KEY `fone_UNIQUE` (`fone`),
  ADD KEY `empresa_fk_idx` (`id_empresa_fk`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_fk_idx` (`id_categoria_fk`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `cupons`
--
ALTER TABLE `cupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `informacoes_empresas`
--
ALTER TABLE `informacoes_empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_informacoes_empresas_fk` FOREIGN KEY (`id_informacoes_empresa_fk`) REFERENCES `informacoes_empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `cupons`
--
ALTER TABLE `cupons`
  ADD CONSTRAINT `cupons_informacoes_empresa_fk` FOREIGN KEY (`id_informacoes_empresa_fk`) REFERENCES `informacoes_empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `informacoes_empresas`
--
ALTER TABLE `informacoes_empresas`
  ADD CONSTRAINT `informacoes_empresas_empresa_fk` FOREIGN KEY (`id_empresa_fk`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_categoria_fk` FOREIGN KEY (`id_categoria_fk`) REFERENCES `categorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
