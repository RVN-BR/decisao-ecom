-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1.natty~ppa.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 06, 2013 at 12:25 PM
-- Server version: 5.1.63
-- PHP Version: 5.3.5-1ubuntu7.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sadcce`
--

-- --------------------------------------------------------

--
-- Table structure for table `confiometro`
--

CREATE TABLE IF NOT EXISTS `confiometro` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idEmpresa` int(11) unsigned NOT NULL,
  `nome` varchar(150) NOT NULL,
  `site` varchar(150) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `dateCadastro` date NOT NULL,
  `reclamacaoRespondida` int(8) unsigned NOT NULL,
  `reclamacaoNaoRespondida` int(8) unsigned NOT NULL,
  `elogio` int(8) unsigned NOT NULL,
  `opiniao` int(8) unsigned NOT NULL,
  `opiniaoRespondida` int(8) unsigned NOT NULL,
  `notaConsumidor` decimal(4,2) unsigned NOT NULL,
  `tempoMedioResposta` varchar(20) NOT NULL,
  `opiniaoConcluida` int(8) unsigned NOT NULL,
  `taxaSolucao` decimal(4,2) unsigned NOT NULL,
  `voltariaFazerNegocio` decimal(4,2) unsigned NOT NULL,
  `atualizacao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idEmpresa_2` (`idEmpresa`),
  KEY `idEmpresa` (`idEmpresa`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebit`
--

CREATE TABLE IF NOT EXISTS `ebit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idEmpresa` int(11) unsigned NOT NULL,
  `url` varchar(100) NOT NULL,
  `medalha` varchar(20) NOT NULL,
  `avaliacao` varchar(20) NOT NULL,
  `atualizacao` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idEmpresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13843 ;

-- --------------------------------------------------------

--
-- Table structure for table `empresa`
--

CREATE TABLE IF NOT EXISTS `empresa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `site` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `atualizacao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13843 ;

-- --------------------------------------------------------

--
-- Table structure for table `receitafederal`
--

CREATE TABLE IF NOT EXISTS `receitafederal` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idEmpresa` int(11) unsigned NOT NULL,
  `cnpj` char(19) NOT NULL,
  `dataAbertura` date NOT NULL,
  `nomeEmpresarial` varchar(150) NOT NULL,
  `nomeFantasia` varchar(150) NOT NULL,
  `atividadePrimaria` varchar(100) NOT NULL,
  `atividadeSecundaria` varchar(100) NOT NULL,
  `tipoEmpresa` varchar(100) NOT NULL,
  `logradouro` varchar(100) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(100) NOT NULL,
  `cep` char(10) NOT NULL,
  `bairro` varchar(75) NOT NULL,
  `municipio` varchar(75) NOT NULL,
  `uf` char(2) NOT NULL,
  `situacaoCadastral` varchar(20) NOT NULL,
  `dataSituacaoCadastral` date NOT NULL,
  `atualizacao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idEmpresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=174 ;

-- --------------------------------------------------------

--
-- Table structure for table `reclameaqui`
--

CREATE TABLE IF NOT EXISTS `reclameaqui` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idEmpresa` int(11) unsigned NOT NULL,
  `nome` varchar(100) NOT NULL,
  `dateCadastro` date DEFAULT NULL,
  `site` varchar(100) NOT NULL,
  `fone` varchar(40) DEFAULT NULL,
  `reputacao` varchar(100) DEFAULT NULL,
  `atendida` decimal(4,2) unsigned DEFAULT NULL,
  `solucao` decimal(4,2) unsigned DEFAULT NULL,
  `voltariaFazerNegocio` decimal(4,2) unsigned DEFAULT NULL,
  `notaConsumidor` decimal(4,2) unsigned DEFAULT NULL,
  `tempoMedioResposta` varchar(100) DEFAULT NULL,
  `avaliacao` int(8) unsigned DEFAULT NULL,
  `avaliacaoNaoAtendida` int(8) unsigned DEFAULT NULL,
  `avaliacaoAtendida` int(8) unsigned DEFAULT NULL,
  `atualizacao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idEmpresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=217 ;

-- --------------------------------------------------------

--
-- Table structure for table `registrobr`
--

CREATE TABLE IF NOT EXISTS `registrobr` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idEmpresa` int(11) unsigned NOT NULL,
  `entidade` varchar(100) NOT NULL,
  `documento` varchar(20) DEFAULT NULL,
  `pais` varchar(40) NOT NULL,
  `responsavel` varchar(100) NOT NULL,
  `dataExpiracao` date NOT NULL,
  `dataCriacao` date NOT NULL,
  `dataAlteracao` date NOT NULL,
  `status` varchar(20) NOT NULL,
  `atualizacao` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idEmpresa` (`idEmpresa`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=437 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `confiometro`
--
ALTER TABLE `confiometro`
  ADD CONSTRAINT `confiometro_ibfk_1` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`);

--
-- Constraints for table `ebit`
--
ALTER TABLE `ebit`
  ADD CONSTRAINT `ebit_ibfk_1` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`);

--
-- Constraints for table `receitafederal`
--
ALTER TABLE `receitafederal`
  ADD CONSTRAINT `receitafederal_ibfk_1` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`);

--
-- Constraints for table `reclameaqui`
--
ALTER TABLE `reclameaqui`
  ADD CONSTRAINT `reclameaqui_ibfk_1` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`);

--
-- Constraints for table `registrobr`
--
ALTER TABLE `registrobr`
  ADD CONSTRAINT `registrobr_ibfk_1` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
