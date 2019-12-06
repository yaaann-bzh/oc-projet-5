-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  ven. 06 déc. 2019 à 08:53
-- Version du serveur :  5.7.26
-- Version de PHP :  7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `yannsjobs`
--

-- --------------------------------------------------------

--
-- Structure de la table `jobs_candidacies`
--

DROP TABLE IF EXISTS `jobs_candidacies`;
CREATE TABLE IF NOT EXISTS `jobs_candidacies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidateId` int(11) NOT NULL,
  `postId` int(11) NOT NULL,
  `recruiterId` int(11) NOT NULL,
  `cover` text COLLATE utf8_bin NOT NULL,
  `resumeFile` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `sendDate` datetime NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `isArchived` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `post_on_candidacy` (`postId`),
  KEY `recruiter_on_candidacy` (`recruiterId`),
  KEY `candidate_on_candidacy` (`candidateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `jobs_members`
--

DROP TABLE IF EXISTS `jobs_members`;
CREATE TABLE IF NOT EXISTS `jobs_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) COLLATE utf8_bin NOT NULL,
  `role` varchar(10) COLLATE utf8_bin NOT NULL,
  `lastname` varchar(25) COLLATE utf8_bin NOT NULL,
  `firstname` varchar(25) COLLATE utf8_bin NOT NULL,
  `email` varchar(30) COLLATE utf8_bin NOT NULL,
  `phone` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `pass` varchar(60) COLLATE utf8_bin NOT NULL,
  `inscriptionDate` datetime NOT NULL,
  `deleteDate` datetime DEFAULT NULL,
  `connexionId` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `firm` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `jobs_posts`
--

DROP TABLE IF EXISTS `jobs_posts`;
CREATE TABLE IF NOT EXISTS `jobs_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recruiterId` int(11) NOT NULL,
  `location` varchar(50) COLLATE utf8_bin NOT NULL,
  `title` varchar(50) COLLATE utf8_bin NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `addDate` datetime NOT NULL,
  `expirationDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recruiters` (`recruiterId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `jobs_saved`
--

DROP TABLE IF EXISTS `jobs_saved`;
CREATE TABLE IF NOT EXISTS `jobs_saved` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidateId` int(11) NOT NULL,
  `postId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate_on_saved` (`candidateId`),
  KEY `post_on_saved` (`postId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `jobs_candidacies`
--
ALTER TABLE `jobs_candidacies`
  ADD CONSTRAINT `candidate_on_candidacy` FOREIGN KEY (`candidateId`) REFERENCES `jobs_members` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `post_on_candidacy` FOREIGN KEY (`postId`) REFERENCES `jobs_posts` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `recruiter_on_candidacy` FOREIGN KEY (`recruiterId`) REFERENCES `jobs_members` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `jobs_posts`
--
ALTER TABLE `jobs_posts`
  ADD CONSTRAINT `recruiter_on_post` FOREIGN KEY (`recruiterId`) REFERENCES `jobs_members` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `jobs_saved`
--
ALTER TABLE `jobs_saved`
  ADD CONSTRAINT `candidate_on_saved` FOREIGN KEY (`candidateId`) REFERENCES `jobs_members` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `post_on_saved` FOREIGN KEY (`postId`) REFERENCES `jobs_posts` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
