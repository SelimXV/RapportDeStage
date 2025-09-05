-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 17 juin 2025 à 16:32
-- Version du serveur : 10.11.10-MariaDB
-- Version de PHP : 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `u937355202_SelimxvBDD`
--

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL,
  `id_cr` int(11) NOT NULL,
  `id_prof` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `date_commentaire` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`id`, `id_cr`, `id_prof`, `commentaire`, `date_commentaire`) VALUES
(1, 1, 9, 'Voici un commentaire', '2025-01-31 10:15:00'),
(2, 1, 9, 'c\'est très bien Sélim', '2025-01-31 10:15:11'),
(3, 2, 9, 'test pour voir', '2025-01-31 11:39:19'),
(4, 7, 9, 'Test ?', '2025-02-14 08:33:30'),
(5, 9, 9, 'bravo teddy', '2025-03-20 17:44:44'),
(6, 9, 9, 'je reste les commentaires', '2025-03-30 18:54:49');

-- --------------------------------------------------------

--
-- Structure de la table `CR`
--

CREATE TABLE `CR` (
  `id` int(11) NOT NULL,
  `sujet` varchar(50) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `dateCR` date DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `date_modif` datetime DEFAULT NULL,
  `vu` tinyint(1) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `CR`
--

INSERT INTO `CR` (`id`, `sujet`, `contenu`, `dateCR`, `date_creation`, `date_modif`, `vu`, `id_user`) VALUES
(1, 'Sujet Test', 'Contenu du compte rendu', '2024-11-21', '2024-11-21 16:51:00', NULL, 0, 1),
(2, 'Rapport de Stage 1', 'Youssef > Teddy', '2024-11-21', '2024-11-21 00:00:00', '2025-01-31 13:17:47', 0, 2),
(6, 'Test', 'Oui', NULL, '2024-11-21 18:18:04', '2024-11-21 18:28:21', NULL, 2),
(7, 'Test Compte Rendu', 'Voici le compte rendu de Ted', NULL, '2024-11-27 19:59:22', '2024-11-27 20:01:35', NULL, 8),
(9, 'Je teste le compte rendu', '<p>Voici mon compte rendu finalisé</p>', NULL, '2025-03-20 17:43:36', NULL, NULL, 8);

-- --------------------------------------------------------

--
-- Structure de la table `stage`
--

CREATE TABLE `stage` (
  `id` int(11) NOT NULL,
  `titre` varchar(30) DEFAULT NULL,
  `dateD` date DEFAULT NULL,
  `dateF` date DEFAULT NULL,
  `monEntreprise` varchar(30) DEFAULT NULL,
  `monTuteur` varchar(20) DEFAULT NULL,
  `telTuteur` varchar(12) DEFAULT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `codePostal` varchar(6) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stage`
--

INSERT INTO `stage` (`id`, `titre`, `dateD`, `dateF`, `monEntreprise`, `monTuteur`, `telTuteur`, `adresse`, `ville`, `codePostal`, `id_user`) VALUES
(1, 'Test Stage', '2024-11-22', '2024-11-24', 'Ensitech', 'Gravouil', '0395838291', '29 avenue de la Maladrerie', 'Poissy', '78305', 2),
(2, 'Entreprise Familiale', '2024-11-11', '2025-01-21', 'Cy-Tech', 'Asalama', '0395838292', '29 avenue du PC', 'PC', '78305', 8);

-- --------------------------------------------------------

--
-- Structure de la table `statut`
--

CREATE TABLE `statut` (
  `id` int(11) NOT NULL,
  `role` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `statut`
--

INSERT INTO `statut` (`id`, `role`) VALUES
(1, 'prof'),
(2, 'élève');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `dateN` date DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  `mdp` varchar(50) DEFAULT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `adresse` varchar(100) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `id_statut` int(11) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `nom`, `prenom`, `dateN`, `email`, `login`, `mdp`, `tel`, `adresse`, `ville`, `code_postal`, `id_statut`, `reset_token`, `token_expiry`) VALUES
(1, 'Test', 'Utilisateur', '1990-01-01', 'test@gmail.com', 'testuser', 'cc03e747a6afbbcbf8be7668acfebee5', '0123456789', NULL, NULL, NULL, 1, NULL, NULL),
(2, 'Eleve', 'Sélim', '2000-01-01', 'selikhal@hotmail.fr', 'test1', '5a105e8b9d40e1329780d62ea2265d8a', '6649542240', '29 avenue de la Maladrerie', 'Carriere', '78300', 2, NULL, NULL),
(3, 'Test', 'User', '2000-01-01', 'prof.gravouil@gmail.com', 'testuser2', 'password123', '0000000000', NULL, NULL, NULL, 1, 'd37ffb0010a81422b297627cf798b19b1b2ed21f9761b58e012ee2fa04ee4994df7ec458e411322af2c38e34b267296c3484', '2024-11-22 13:24:39'),
(8, 'Nsoki', 'Teddy', '2024-11-27', 'badasz2003@gmail.com', 'ted', '870fa8ee962d90af50c7eaed792b075a', '0760394756', '29 avenue de la Maladreri', 'Carrieres', '95000', 2, NULL, NULL),
(9, 'prof1', 'gravouil', '0000-00-00', 'selim.khalfane@ensitech.eu', 'prof1', '4f5fdb3de5aa701eae2961743a00c01c', '0845723381', NULL, NULL, NULL, 1, NULL, NULL),
(10, 'Roubert', 'Akash', '0000-00-00', 'akash@hotmail.fr', 'akash', '94754d0abb89e4cf0a7f1c494dbb9d2c', NULL, NULL, NULL, NULL, 2, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cr` (`id_cr`),
  ADD KEY `id_prof` (`id_prof`);

--
-- Index pour la table `CR`
--
ALTER TABLE `CR`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_users` (`id_user`);

--
-- Index pour la table `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_user` (`id_user`);

--
-- Index pour la table `statut`
--
ALTER TABLE `statut`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_statut` (`id_statut`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `CR`
--
ALTER TABLE `CR`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `stage`
--
ALTER TABLE `stage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `statut`
--
ALTER TABLE `statut`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`id_cr`) REFERENCES `CR` (`id`),
  ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (`id_prof`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `CR`
--
ALTER TABLE `CR`
  ADD CONSTRAINT `fk_id_users` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `fk_id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_id_statut` FOREIGN KEY (`id_statut`) REFERENCES `statut` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
