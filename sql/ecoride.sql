-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 01 juil. 2025 à 14:14
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `carpools`
--

CREATE TABLE `carpools` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `day` date NOT NULL,
  `begin` time NOT NULL,
  `end` time NOT NULL,
  `address_start` varchar(255) NOT NULL,
  `address_end` varchar(255) NOT NULL,
  `places_available` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `status` enum('A venir','En cours','Terminé','Confirmé','Vérification par la plateforme') NOT NULL DEFAULT 'A venir',
  `carpool_number` varchar(255) NOT NULL,
  `is_ecological` tinyint(1) NOT NULL DEFAULT 0,
  `startLat` float DEFAULT NULL,
  `startLon` float DEFAULT NULL,
  `endLat` float DEFAULT NULL,
  `endLon` float DEFAULT NULL,
  `duration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `carpools`
--

INSERT INTO `carpools` (`id`, `driver_id`, `car_id`, `day`, `begin`, `end`, `address_start`, `address_end`, `places_available`, `price`, `status`, `carpool_number`, `is_ecological`, `startLat`, `startLon`, `endLat`, `endLon`, `duration`) VALUES
(3, 13, 15, '2025-06-30', '08:00:00', '11:45:00', '7 rue d\'Estienne d\'Orves 94381 Bonneuil-sur-Marne', '263 avenue Général Leclerc 35042 RENNES', 2, 15, 'Confirmé', 'COV#eed700c8', 0, NULL, NULL, NULL, NULL, NULL),
(4, 13, 14, '2025-07-05', '09:00:00', '12:45:00', '263 avenue Général Leclerc 35042 RENNES', '7 rue d\'Estienne d\'Orves 94381 Bonneuil-sur-Marne', 2, 20, 'A venir', 'COV#fb8ce3d9', 1, 48.1026, -1.67842, 48.7623, 2.49397, NULL),
(5, 14, 16, '2025-07-05', '06:00:00', '10:10:00', '263 avenue Général Leclerc 35042 RENNES', '95700 Roissy-en-France', 3, 20, 'A venir', 'COV#82b11272', 1, 48.1026, -1.67842, 49.0037, 2.51736, NULL),
(6, 13, 15, '2025-06-23', '14:00:00', '15:40:00', 'Porte Maillot, 75017 Paris', 'Gare de Rouen  76000 Rouen', 2, 12, 'Confirmé', 'COV#153462b6', 0, NULL, NULL, NULL, NULL, NULL),
(7, 13, 15, '2025-07-10', '07:00:00', '12:00:00', '7 rue d\'Estienne d\'Orves 94381 Bonneuil-sur-Marne', 'Place Bellecour, 69002 Lyon', 3, 15, 'A venir', 'COV#49cca486', 0, 48.7743, 2.48776, 45.7577, 4.83243, 15900),
(8, 14, 16, '2025-07-10', '06:00:00', '11:00:00', '10 Rue Jean Gabin, 94000 Créteil', 'Gare de covoiturage – Quai Docteur Gailleton', 3, 20, 'A venir', 'COV#713c5e07', 1, 48.7819, 2.44697, 45.7512, 4.83244, 15540),
(9, 13, 14, '2025-07-10', '05:30:00', '09:59:00', '2 avenue Georges Pompidou, 94370 Sucy‑en‑Brie', 'Place de la Comédie, 69001 Lyon', 2, 20, 'A venir', 'COV#b6efed3c', 1, 48.7709, 2.52383, 45.7677, 4.83602, 16199);

-- --------------------------------------------------------

--
-- Structure de la table `carpools_users`
--

CREATE TABLE `carpools_users` (
  `carpools_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `is_ended` tinyint(1) NOT NULL DEFAULT 0,
  `is_answered` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `carpools_users`
--

INSERT INTO `carpools_users` (`carpools_id`, `users_id`, `is_confirmed`, `is_ended`, `is_answered`) VALUES
(3, 3, 1, 1, 1),
(6, 7, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `brand` varchar(30) NOT NULL,
  `model` varchar(30) NOT NULL,
  `color` varchar(30) NOT NULL,
  `seats` int(11) NOT NULL,
  `plate_number` varchar(15) NOT NULL,
  `first_registration` date NOT NULL,
  `energy` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cars`
--

INSERT INTO `cars` (`id`, `driver_id`, `brand`, `model`, `color`, `seats`, `plate_number`, `first_registration`, `energy`) VALUES
(14, 13, 'Porsche', 'Taycan', 'jaune', 2, 'JP 279 SK', '2020-02-03', 'Electrique'),
(15, 13, 'Renault', 'Clio', 'Blanche', 3, 'AB 123 CD', '2014-07-01', 'Essence'),
(16, 14, 'Peugeot', 'e-208', 'vert', 3, 'eb 208 pb', '2019-11-18', 'Electrique');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250613132743', '2025-06-13 15:30:51', 177);

-- --------------------------------------------------------

--
-- Structure de la table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `animals` tinyint(1) NOT NULL,
  `smoking` tinyint(1) NOT NULL,
  `preferences` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `drivers`
--

INSERT INTO `drivers` (`id`, `user_id`, `animals`, `smoking`, `preferences`) VALUES
(13, 4, 0, 0, 'Pas d\'enfants'),
(14, 7, 1, 1, 'peux faire un détour si proche de l\'adresse');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `carpool_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `commentary` longtext NOT NULL,
  `validate` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `driver_id`, `carpool_id`, `rate`, `commentary`, `validate`) VALUES
(1, 3, 13, 3, 5, 'Bon chauffeur , conduite calme', 1),
(3, 7, 13, 6, 2, 'Le chauffeur n\'a pas voulu faire un détour de quelques mètres pour me déposer', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `pseudo` varchar(30) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `credits` int(11) NOT NULL DEFAULT 20,
  `is_verified` tinyint(1) NOT NULL,
  `is_passenger` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `roles`, `password`, `pseudo`, `photo`, `credits`, `is_verified`, `is_passenger`) VALUES
(1, 'admin@mail.fr', '[\"ROLE_ADMIN\"]', '$2y$13$QC31qzvsuWBpHjkad.mvvem7bW7sP9okWpf0yrBqwdtI4LtrA7aNm', 'Jose', NULL, 26, 1, 1),
(3, 'passager@mail.fr', '[]', '$2y$13$2KxgxdKY88CKfuJdfFhaHuqWoD/70kDJnIpbzN9.fMbFVuvOsWosu', 'passager', NULL, 15, 0, 1),
(4, 'chauffeur@mail.fr', '[\"ROLE_USER\",\"ROLE_DRIVER\"]', '$2y$13$6mTnzrMAsVgJU6/Evw7jS.olFbrqzFIbyEWrjXBUXvg3iKqPwzZuS', 'chauffeur', '21714d9db526de60889fc1050e0fb8f4.webp', 43, 0, 0),
(7, 'chaufpass@mail.fr', '[\"ROLE_USER\",\"ROLE_DRIVER\"]', '$2y$13$Vj7fFWt.sCqGLY5M.6QzGO2v61CjxnR544HWLNOWSMVrzc4e7Wh5O', 'chaufpass', '9547f59f5a1e91149472621353ddf38e.webp', 8, 1, 1),
(8, 'employe@mail.fr', '[\"ROLE_STAFF\"]', '$2y$13$goNJ6lT8/38Ii96n59UMt.YNhxTscv4GoMuDd2JzEz6iw7W7f6KRe', 'employe', NULL, 20, 1, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `carpools`
--
ALTER TABLE `carpools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_89351C00C3423909` (`driver_id`),
  ADD KEY `IDX_89351C00C3C6F69F` (`car_id`);

--
-- Index pour la table `carpools_users`
--
ALTER TABLE `carpools_users`
  ADD PRIMARY KEY (`carpools_id`,`users_id`),
  ADD KEY `IDX_12BA1B6AD6855CC7` (`carpools_id`),
  ADD KEY `IDX_12BA1B6A67B3B43D` (`users_id`);

--
-- Index pour la table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_95C71D14C3423909` (`driver_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_E410C307A76ED395` (`user_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6970EB0FA76ED395` (`user_id`),
  ADD KEY `IDX_6970EB0FC3423909` (`driver_id`),
  ADD KEY `IDX_6970EB0F9A6F0DAE` (`carpool_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `carpools`
--
ALTER TABLE `carpools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `carpools`
--
ALTER TABLE `carpools`
  ADD CONSTRAINT `FK_89351C00C3423909` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_89351C00C3C6F69F` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`);

--
-- Contraintes pour la table `carpools_users`
--
ALTER TABLE `carpools_users`
  ADD CONSTRAINT `FK_12BA1B6A67B3B43D` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_12BA1B6AD6855CC7` FOREIGN KEY (`carpools_id`) REFERENCES `carpools` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `FK_95C71D14C3423909` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `FK_E410C307A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `FK_6970EB0F9A6F0DAE` FOREIGN KEY (`carpool_id`) REFERENCES `carpools` (`id`),
  ADD CONSTRAINT `FK_6970EB0FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_6970EB0FC3423909` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
