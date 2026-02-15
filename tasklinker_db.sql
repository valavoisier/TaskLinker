-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 15 fév. 2026 à 16:01
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tasklinker_db`
--

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
('DoctrineMigrations\\Version20260103143656', '2026-01-03 15:37:57', 223),
('DoctrineMigrations\\Version20260109174840', '2026-01-09 18:49:10', 120),
('DoctrineMigrations\\Version20260211215431', '2026-02-11 22:54:52', 48),
('DoctrineMigrations\\Version20260214145208', '2026-02-14 15:52:32', 24),
('DoctrineMigrations\\Version20260214170000', '2026-02-14 22:25:40', 29),
('DoctrineMigrations\\Version20260215094759', '2026-02-15 10:48:11', 30),
('DoctrineMigrations\\Version20260215121415', '2026-02-15 13:23:56', 360);

-- --------------------------------------------------------

--
-- Structure de la table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `entry_date` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `google_authenticator_secret` longtext DEFAULT NULL,
  `is_two_factor_enabled` tinyint(1) NOT NULL,
  `hide2_faprompt` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `employee`
--

INSERT INTO `employee` (`id`, `firstname`, `lastname`, `email`, `entry_date`, `status`, `roles`, `password`, `google_authenticator_secret`, `is_two_factor_enabled`, `hide2_faprompt`) VALUES
(42, 'François', 'Carre', 'f.carre@gmail.com', '2024-04-28', 'cdi', '[\"ROLE_ADMIN\"]', '$2y$10$L9FYMBUxlC1bKvWJ0WMA6.mGUX/YI3d.LPFUVM.YokcDyrNZcrszy', NULL, 0, 0),
(43, 'Olivier', 'Duhamel', 'boulanger.sebastien@example.com', '2022-04-04', 'cdd', '[\"ROLE_USER\"]', '$2y$10$xxfdoa5J/avMzZOeS/Iaj.5ROlSFcq0B7aB506bZl2BiUrWLWYqJm', NULL, 0, 0),
(44, 'René', 'Perrier', 'morel.thibaut@example.org', '2021-07-20', 'cdi', '[\"ROLE_USER\"]', '$2y$10$woCnTg1M59ahsBVVlFS2M.1AUv2NM5zkzy8DqcrqxTo1DxbU.nF1.', NULL, 0, 0),
(45, 'Valérie', 'Nguyen', 'isabelle49@example.com', '2023-09-14', 'cdi', '[\"ROLE_USER\"]', '$2y$10$lvOzI6aKsagKxSr8MHPUIeFaSYAmmic7sKqetMT1qwYWzuD1qZQKS', NULL, 0, 0),
(46, 'Philippine', 'Delannoy', 'margot63@example.org', '2023-04-15', 'cdi', '[\"ROLE_USER\"]', '$2y$10$D0Kw6LQXOGdBxLwH918vJ.i.sLvR3xcnfet8dkQ6qeaoyN2/bivxK', NULL, 0, 0),
(47, 'Michelle', 'Barbier', 'adrienne22@example.org', '2022-06-01', 'freelance', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOoxhWx3905A.u.Tas0N4gZ3N6p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(48, 'Christophe', 'Le Roux', 'humbert.louis@example.com', '2023-07-04', 'freelance', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOoxhWx3907A.u.Tas0N4gZ3N6p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(49, 'Yves', 'Jacob', 'salmon.benjamin@example.org', '2022-06-10', 'cdi', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JxOoxhWx3905A.u.Taj0N4gZ3N6p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(50, 'André', 'Faivre', 'theodore.gosselin@example.net', '2022-05-27', 'cdi', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOoxhWx3905A.u.Tas0N4gZ3V7p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(51, 'Brigitte', 'Noel', 'eherve@example.net', '2023-03-12', 'freelance', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOoxhWx3905A.u.Tas0N4gZ3N6p0unpaPsfv93CoIHz88K', NULL, 0, 0),
(52, 'Tristan', 'Marion', 'boucher.marine@example.net', '2022-03-18', 'cdi', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOoxhWx3905A.u.Tas0N4gZ3N6p0inpaPsfv92CoIHz88K', NULL, 0, 0),
(53, 'Hortense', 'Guillot', 'h.guillot@gmail.com', '2025-05-11', 'cdi', '[\"ROLE_USER\"]', '$2y$10$nGwdzhgEW7SAfTMjPwtT.OEmuGcDpMmaLgIszCUbCXodfEPtPRgVC', NULL, 0, 0),
(54, 'Auguste', 'Regnier', 'fernandez.adelaide@example.com', '2021-09-22', 'freelance', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOoxhWx3905A.u.Tcv0N4gZ3N6p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(56, 'Manon', 'Jacquot', 'laine.alain@example.net', '2024-02-18', 'cdd', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwWkxhWx3905A.u.Tas0N4gZ3N6p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(57, 'Marcel', 'Bruneau', 'maggie57@example.net', '2025-12-22', 'freelance', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOoxhWx3905A.u.Tas0N4eV3N6p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(58, 'Élisabeth', 'Dos Santos', 'benjamin43@example.net', '2021-04-14', 'freelance', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOoxhWx4444A.u.Tas0N4gZ3N6p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(59, 'Joseph', 'Lombard', 'toussaint.nath@example.net', '2022-10-17', 'cdd', '[\"ROLE_USER\"]', '$2y$10$8ovYe7JwOomlGt3905A.u.Tas0N4gZ3N6p0unpaPsfv92CoIHz88K', NULL, 0, 0),
(61, 'Valérie', 'Lavoisier', 'valavoisier@hotmail.fr', '2026-02-12', 'cdi', '[\"ROLE_ADMIN\"]\r\n', '$2y$13$rLlNMD7QCf5NgaLxKM5wXuOlfXsuFLSeHr/AsqeqOV4r0GPakgsz2', 'OXY4iGCMuwAaRTKK67i5k5qDMVZHXepeyRGKmdd5VuRaztLWs0ZhdVV8PVVSBQRfP0mRC8B+vsJ3ve3Nn/eMezC+kE4IBMsRdKb5zmC6g686GPMFBuh0BoGUhp8=', 1, 0),
(62, 'Ruby', 'Quinsapou', 'ruby@gmail.com', '2026-02-12', 'cdi', '[\"ROLE_USER\"]\r\n', '$2y$13$6qmB8UYi9sTINUiDABmDg.aZ.wi15TpaejEo.4x/kelKnZ66R1qJG', NULL, 0, 1),
(64, 'Mi', 'Yuyu', 'yuyu@gmail.com', '2026-02-13', 'cdi', '[\"ROLE_USER\"]', '$2y$13$XTE9sLP/MvMXssDvx9pwcOND8rZpfR/KiipZ7.hJEz3ahLzObVOf.', NULL, 0, 0);

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
-- Structure de la table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `project`
--

INSERT INTO `project` (`id`, `title`, `archived`) VALUES
(11, 'TaskLinkers', 0),
(12, 'Site vitrine Les Soeurs Marchand', 0),
(13, 'Quibusdam natus sequi sequi culpa.', 1),
(17, 'Ducimus fugiat quis commodi.', 1),
(18, 'Beatae et laudantium voluptatibus.', 0),
(19, 'Recusandae velit eveniet.', 1),
(20, 'Ut facere vitae.', 0),
(21, 'Mon Nouveau Projet Walwebcreation', 0),
(23, 'projet de l\'année 2025', 1),
(24, 'projet 2026', 0),
(25, 'Prospection en soirée', 1),
(26, 'essai 0226', 1);

-- --------------------------------------------------------

--
-- Structure de la table `project_employee`
--

CREATE TABLE `project_employee` (
  `project_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `project_employee`
--

INSERT INTO `project_employee` (`project_id`, `employee_id`) VALUES
(11, 46),
(11, 56),
(11, 57),
(11, 58),
(12, 44),
(12, 53),
(12, 54),
(12, 58),
(13, 50),
(13, 51),
(13, 56),
(13, 59),
(17, 43),
(17, 44),
(17, 48),
(18, 43),
(18, 53),
(18, 58),
(19, 44),
(19, 50),
(19, 53),
(19, 57),
(19, 59),
(20, 42),
(20, 46),
(21, 43),
(21, 51),
(21, 53),
(21, 58),
(23, 42),
(23, 43),
(24, 44),
(24, 45),
(24, 61),
(24, 62),
(25, 46),
(25, 49),
(26, 61),
(26, 62),
(26, 64);

-- --------------------------------------------------------

--
-- Structure de la table `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `deadline` date DEFAULT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `task`
--

INSERT INTO `task` (`id`, `project_id`, `employee_id`, `title`, `description`, `deadline`, `status`) VALUES
(51, 12, 53, 'Cumque consectetur nemo quia.', 'Ut voluptas aliquam nesciunt et. Sit libero quibusdam perspiciatis et hic. Eius voluptates sit accusantium adipisci dignissimos ratione. Earum facere non ipsa sunt.', '2026-05-20', 'doing'),
(52, 17, 43, 'Sed earum earum nobis.', 'Minima odit cum iste assumenda. Necessitatibus consequatur similique facilis voluptas. Autem qui rem dolores minima ipsum et saepe repellendus. Similique inventore ut ipsam.', '2026-04-21', 'doing'),
(53, 18, 53, 'Natus qui mollitia inventore.', 'Maiores veritatis est saepe ut. Est in aut perferendis. Consequatur amet aut dolor amet vitae consequuntur.', NULL, 'doing'),
(54, 17, 48, 'Explicabo at minima quod.', 'Quae et sit qui ut labore earum impedit. Debitis repellendus qui a rerum ut fuga. Voluptatem voluptatem repellat voluptates ea.', '2026-01-13', 'done'),
(55, 12, NULL, 'Et alias et voluptatibus quis iste.', 'Sequi et provident suscipit quod sint. Ipsa voluptatem molestiae libero ea aut. Voluptatem consequatur libero est vitae blanditiis accusamus velit vel. Voluptas sit in reprehenderit laborum.', NULL, 'todo'),
(57, 19, 48, 'Quia totam et occaecati occaecati.', 'Repudiandae tempore ab sint dolorem illum itaque praesentium. Voluptate tenetur est ut vel quia reprehenderit. Vero ut fugit voluptas praesentium. Reprehenderit modi facere animi maxime error quaerat.', '2026-04-20', 'doing'),
(58, 18, 58, 'Voluptatum numquam eos dolor blanditiis.', 'Totam ut atque alias voluptates ut hic ut ab. Est labore consequuntur et incidunt. Odio facilis rerum quibusdam sit.', NULL, 'doing'),
(59, 18, 43, 'Voluptates ut corrupti non necessitatibus numquam.', 'Sit perferendis consequuntur est vel culpa. Fuga dolore ut in est inventore sed minus. Velit laboriosam enim corporis consequatur eligendi nesciunt aut. Consequatur animi perspiciatis accusamus ut qui expedita praesentium minus. Est et dolore illum eaque sint odio doloremque.', '2026-05-11', 'todo'),
(60, 20, 46, 'Cupiditate maiores saepe voluptas quod alias.', 'Optio doloremque nemo voluptatem qui beatae ut quasi. Nostrum debitis qui ut doloribus aut. Rerum sit ut voluptatum consequatur omnis repellendus quis id.', '2026-03-30', 'doing'),
(62, 18, 53, 'Sunt repellendus sit enim laudantium.', 'Dicta est reiciendis ut. Accusantium aut consequuntur sit dolor et illum aliquam quo.', NULL, 'done'),
(63, 20, 46, 'Et iste odio.', 'Nesciunt consequuntur voluptates molestias perferendis qui animi. Nihil accusantium veritatis delectus rem error. Sed ducimus incidunt quia ipsam vel.', NULL, 'done'),
(64, 20, 42, 'Corporis aspernatur iure eaque odio dolorem.', 'Tenetur illo aut enim et. Dolorem voluptates aut aspernatur nam fuga aut impedit. Corporis voluptates velit doloribus dolorum.', '2026-01-04', 'doing'),
(65, 13, 50, 'Ipsum eos eius rem voluptas.', 'Porro quod velit sit veritatis ex laborum. Eaque minima voluptas quia voluptas eaque. Corporis facere delectus suscipit et.', NULL, 'todo'),
(67, 12, 58, 'Cupiditate laboriosam illo corrupti.', 'Quos qui hic culpa quia voluptatem pariatur. Iure voluptas maiores a commodi. Laborum dignissimos temporibus illo eaque corrupti culpa consequatur. Aut unde cumque quod est.', NULL, 'done'),
(68, 13, 56, 'Explicabo perferendis et aut.', 'Est qui non aliquam dolorem culpa. Maxime pariatur nobis rerum at omnis.', '2026-06-18', 'done'),
(69, 11, 58, 'Ipsum possimus ut quo quo.', 'Nobis excepturi perferendis id error omnis velit veniam. Minus libero nulla dolore incidunt excepturi aut. Doloremque error ipsa et.', '2026-04-11', 'done'),
(70, 13, 51, 'Id excepturi sint aut minima quo.', 'Expedita esse enim nihil aut qui saepe. Tempore et illum provident quis. Aliquid id qui atque dolorem illo minima ut. Voluptatem aut voluptatem ullam molestiae.', NULL, 'todo'),
(71, 11, 46, 'Aliquid culpa est omnis modi.', 'Vel quibusdam rerum ratione aut qui error provident. Et voluptas modi cupiditate est rerum sunt aperiam totam. Repudiandae doloribus et libero et.', '2026-06-15', 'todo'),
(72, 19, 58, 'Sequi repellat at totam.', 'Nulla minima debitis ex quo sint quia. Provident rerum libero quos eius laudantium dicta dolorem.', '2026-05-04', 'todo'),
(73, 13, 56, 'Necessitatibus ut totam ad.', 'Laudantium repellat ullam molestias voluptatem laboriosam. Laudantium qui ut et quibusdam dolores omnis. Ratione qui esse nisi atque pariatur vitae. Est magnam omnis unde sequi.', NULL, 'todo'),
(74, 13, 59, 'Quibusdam esse repellendus.', 'Quod dolor quos consectetur similique quia esse. Eum nobis vero maiores occaecati cupiditate assumenda.', '2026-01-24', 'todo'),
(75, 13, 51, 'Qui ut doloribus ullam voluptate.', 'Hic deleniti animi similique repudiandae nisi id. Sit eius a id optio accusantium aliquam sunt. Aliquid fuga iusto necessitatibus et veritatis laborum placeat necessitatibus. Asperiores vel sed pariatur dolor.', NULL, 'done'),
(76, 12, 53, 'Aperiam sed non et.', 'Illo sit consequuntur minima. Error aut voluptatem sed ad dolorem quae velit numquam. Sed eos sit in.', '2026-03-22', 'done'),
(77, 12, 54, 'Sint quia saepe similique.', 'Similique nobis quia voluptate itaque. Et quod accusantium occaecati sit. Voluptatem vitae autem minus rerum.', NULL, 'doing'),
(78, 18, NULL, 'Provident ea vel non.', 'In magnam sint consequatur. Ut velit quasi doloremque tempore. Et explicabo omnis nesciunt corrupti. Quasi quo nihil incidunt iste.', '2026-04-19', 'todo'),
(80, 13, 56, 'Enim velit voluptatibus nulla inventore.', 'Quia laudantium et exercitationem et optio. Voluptas ut quo dicta id qui assumenda. Ea ut enim reprehenderit repudiandae. Inventore eveniet et corrupti qui sint qui dolorum corporis. Tempora optio alias aut quibusdam vel itaque.', NULL, 'done'),
(82, 13, 59, 'Dolor vitae ea iure.', 'Soluta vel in fuga fugit officiis modi dolor. Architecto et aperiam impedit. Voluptas facere non vel veritatis nesciunt.', '2026-04-08', 'done'),
(83, 18, NULL, 'Exercitationem animi quia numquam inventore.', 'Sapiente eius enim nostrum eligendi fuga et. Repellat sunt voluptatem aliquam qui non corporis.', '2026-03-17', 'doing'),
(85, 12, 44, 'Quia saepe impedit.', 'Facere ab quo dignissimos dolore omnis repellendus. Maiores modi facilis et similique ut. Doloribus rerum vel sit sint. Autem ad dolore impedit ut doloribus occaecati. Molestias consectetur occaecati tenetur.', NULL, 'done'),
(86, 19, NULL, 'Incidunt qui eum aperiam.', 'Libero sed inventore eos atque ipsam magni quia. Qui repellat temporibus quasi.', '2026-04-28', 'todo'),
(87, 11, 56, 'Et optio laborum minus.', 'Soluta et eligendi natus temporibus delectus praesentium in. Eos ipsa beatae quod hic earum occaecati dolores. Dolores non itaque sed laboriosam quis et earum molestias. Iste non omnis unde id rem necessitatibus. Est magni iure inventore nobis.', NULL, 'todo'),
(92, 20, NULL, 'Et eos quis sequi.', 'Ut incidunt repellat est ut dolores. Incidunt eligendi et consectetur impedit et. Omnis alias qui quia delectus vel.', NULL, 'doing'),
(93, 11, 57, 'Repellendus aliquid eius id et incidunt.', 'Architecto non tempora et cupiditate veritatis dolor maxime. At soluta autem ut et tempore eum. Error a eius id.', NULL, 'todo'),
(95, 18, 43, 'Aliquam ut et molestiae vitae.', 'Molestiae exercitationem velit corrupti. Aut vitae odio itaque voluptatibus ipsa ut veniam non.', NULL, 'doing'),
(96, 12, 44, 'In perferendis enim incidunt qui qui.', 'Omnis ut quibusdam eos perferendis ut iste ex. Magni ut veritatis voluptatibus officia fuga temporibus. Itaque sit ipsum rem quo. Quia non suscipit natus et laborum similique cumque et.', '2026-05-24', 'todo'),
(98, 20, 46, 'Iusto non laudantium aut.', 'Nemo ducimus ab ea iure et molestias. Recusandae qui quis quia repudiandae voluptatem molestiae. Nisi sapiente cum aut error odit distinctio adipisci.', '2026-04-27', 'doing'),
(99, 18, 58, 'Harum magni sint optio sint eligendi.', 'Libero est hic sapiente omnis porro necessitatibus omnis. Facilis inventore dolorem autem quia dolores quia illo. Consequuntur ea sed dolorem autem fuga exercitationem. Doloremque modi vero soluta sed earum facere.', '2026-04-16', 'doing'),
(100, 17, 44, 'Aut quas est.', 'Exercitationem velit et qui est sed. Debitis eaque omnis iusto sapiente. Dolor blanditiis excepturi quae recusandae nam molestiae doloribus. Sed et dicta ratione ex sed.', NULL, 'doing'),
(101, 20, NULL, 'Refonte Site Abeille', 'nouvelle interface site abeille asurances', '2026-01-06', 'todo'),
(106, 21, 51, 'tache 3', 'portfolio', '2026-01-07', 'todo'),
(107, 21, 58, 'tache 4', 'mise en production', '2026-01-07', 'todo'),
(108, 21, 43, 'tache 1', 'etude de projet', '2026-01-02', 'done'),
(109, 21, 53, 'Tache 2', 'integration maquette', '2026-01-04', 'doing'),
(110, 23, NULL, 'bilan annuel', 'reunion', '2025-12-31', 'todo'),
(112, 25, 49, 'tache du soir', 'telephoner aux prospects', '2026-01-08', 'todo'),
(113, 25, 46, 'rapport  d\'analyse', 'analyser réponses', '2026-01-09', 'todo'),
(114, 25, 46, 'liste prospects', 'préparer listing', '2026-01-06', 'doing'),
(115, 11, NULL, 'new', 'zzzfze', '2026-01-16', 'doing'),
(117, 26, 64, 'tache fevrier', 'essai', '2026-02-15', 'doing'),
(118, 26, 62, 'essai2 fevrier26', 'essai2 fevrier26', '2026-02-15', 'doing'),
(119, 26, 62, 'sss', 'sssssfsfsfsfs', '2026-02-15', 'todo');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `project_employee`
--
ALTER TABLE `project_employee`
  ADD PRIMARY KEY (`project_id`,`employee_id`),
  ADD KEY `IDX_60D1FE7A166D1F9C` (`project_id`),
  ADD KEY `IDX_60D1FE7A8C03F15C` (`employee_id`);

--
-- Index pour la table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_527EDB25166D1F9C` (`project_id`),
  ADD KEY `IDX_527EDB258C03F15C` (`employee_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `project_employee`
--
ALTER TABLE `project_employee`
  ADD CONSTRAINT `FK_60D1FE7A166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_60D1FE7A8C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `FK_527EDB25166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
  ADD CONSTRAINT `FK_527EDB258C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
