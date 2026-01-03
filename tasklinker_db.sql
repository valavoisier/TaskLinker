-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 03 jan. 2026 à 18:33
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
('DoctrineMigrations\\Version20260103143656', '2026-01-03 15:37:57', 223);

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
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `employee`
--

INSERT INTO `employee` (`id`, `firstname`, `lastname`, `email`, `entry_date`, `status`) VALUES
(41, 'Augustin', 'Boulay', 'guy05@example.com', '2024-03-29', 'freelance'),
(42, 'François', 'Carre', 'christiane.chauveau@example.com', '2024-04-28', 'freelance'),
(43, 'Olivier', 'Duhamel', 'boulanger.sebastien@example.com', '2022-04-04', 'cdd'),
(44, 'René', 'Perrier', 'morel.thibaut@example.org', '2021-07-20', 'cdi'),
(45, 'Valérie', 'Nguyen', 'isabelle49@example.com', '2023-09-14', 'cdi'),
(46, 'Philippine', 'Delannoy', 'margot63@example.org', '2023-04-15', 'cdi'),
(47, 'Michelle', 'Barbier', 'adrienne22@example.org', '2022-06-01', 'freelance'),
(48, 'Christophe', 'Le Roux', 'humbert.louis@example.com', '2023-07-04', 'freelance'),
(49, 'Yves', 'Jacob', 'salmon.benjamin@example.org', '2022-06-10', 'cdi'),
(50, 'André', 'Faivre', 'theodore.gosselin@example.net', '2022-05-27', 'cdi'),
(51, 'Brigitte', 'Noel', 'eherve@example.net', '2023-03-12', 'freelance'),
(52, 'Tristan', 'Marion', 'boucher.marine@example.net', '2022-03-18', 'cdi'),
(53, 'Hortense', 'Guillot', 'marianne.ruiz@example.com', '2025-05-11', 'cdi'),
(54, 'Auguste', 'Regnier', 'fernandez.adelaide@example.com', '2021-09-22', 'freelance'),
(55, 'Isaac', 'Descamps', 'nicolas45@example.net', '2023-02-01', 'freelance'),
(56, 'Manon', 'Jacquot', 'laine.alain@example.net', '2024-02-18', 'cdd'),
(57, 'Marcel', 'Bruneau', 'maggie57@example.net', '2025-12-22', 'freelance'),
(58, 'Élisabeth', 'Dos Santos', 'benjamin43@example.net', '2021-04-14', 'freelance'),
(59, 'Joseph', 'Lombard', 'toussaint.nath@example.net', '2022-10-17', 'cdd'),
(60, 'Alphonse', 'Evrard', 'capucine.barre@example.org', '2021-10-15', 'cdd');

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
(11, 'Eveniet rerum quia.', 0),
(12, 'Commodi non accusamus voluptatem eveniet.', 0),
(13, 'Quibusdam natus sequi sequi culpa.', 1),
(14, 'Eum autem nam voluptatem.', 0),
(15, 'Suscipit sit ipsa sed.', 0),
(16, 'Deleniti velit praesentium.', 1),
(17, 'Ducimus fugiat quis commodi.', 0),
(18, 'Beatae et laudantium voluptatibus.', 0),
(19, 'Recusandae velit eveniet.', 1),
(20, 'Ut facere vitae.', 0);

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
(11, 60),
(12, 44),
(12, 53),
(12, 54),
(12, 58),
(13, 50),
(13, 51),
(13, 56),
(13, 59),
(13, 60),
(14, 43),
(14, 55),
(14, 59),
(15, 44),
(15, 53),
(16, 49),
(16, 52),
(16, 54),
(16, 59),
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
(20, 46),
(20, 58);

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
(51, 12, 57, 'Cumque consectetur nemo quia.', 'Ut voluptas aliquam nesciunt et. Sit libero quibusdam perspiciatis et hic. Eius voluptates sit accusantium adipisci dignissimos ratione. Earum facere non ipsa sunt.', '2026-05-20', 'doing'),
(52, 17, 42, 'Sed earum earum nobis.', 'Minima odit cum iste assumenda. Necessitatibus consequatur similique facilis voluptas. Autem qui rem dolores minima ipsum et saepe repellendus. Similique inventore ut ipsam.', '2026-04-21', 'doing'),
(53, 18, 52, 'Natus qui mollitia inventore.', 'Maiores veritatis est saepe ut. Est in aut perferendis. Consequatur amet aut dolor amet vitae consequuntur.', NULL, 'doing'),
(54, 17, 42, 'Explicabo at minima quod.', 'Quae et sit qui ut labore earum impedit. Debitis repellendus qui a rerum ut fuga. Voluptatem voluptatem repellat voluptates ea.', '2026-01-13', 'done'),
(55, 12, NULL, 'Et alias et voluptatibus quis iste.', 'Sequi et provident suscipit quod sint. Ipsa voluptatem molestiae libero ea aut. Voluptatem consequatur libero est vitae blanditiis accusamus velit vel. Voluptas sit in reprehenderit laborum.', NULL, 'todo'),
(56, 14, 59, 'Porro at vero cupiditate.', 'Ut et ullam accusamus ratione voluptatum. Aperiam corrupti magni saepe laborum ducimus ipsum consequatur magni. Recusandae ipsum sit odit aut sit voluptatem aperiam. Quia et quaerat aspernatur sed non beatae.', '2026-05-12', 'done'),
(57, 19, 48, 'Quia totam et occaecati occaecati.', 'Repudiandae tempore ab sint dolorem illum itaque praesentium. Voluptate tenetur est ut vel quia reprehenderit. Vero ut fugit voluptas praesentium. Reprehenderit modi facere animi maxime error quaerat.', '2026-04-20', 'doing'),
(58, 18, 55, 'Voluptatum numquam eos dolor blanditiis.', 'Totam ut atque alias voluptates ut hic ut ab. Est labore consequuntur et incidunt. Odio facilis rerum quibusdam sit.', NULL, 'doing'),
(59, 18, 51, 'Voluptates ut corrupti non necessitatibus numquam.', 'Sit perferendis consequuntur est vel culpa. Fuga dolore ut in est inventore sed minus. Velit laboriosam enim corporis consequatur eligendi nesciunt aut. Consequatur animi perspiciatis accusamus ut qui expedita praesentium minus. Est et dolore illum eaque sint odio doloremque.', '2026-05-11', 'todo'),
(60, 20, 52, 'Cupiditate maiores saepe voluptas quod alias.', 'Optio doloremque nemo voluptatem qui beatae ut quasi. Nostrum debitis qui ut doloribus aut. Rerum sit ut voluptatum consequatur omnis repellendus quis id.', '2026-03-30', 'doing'),
(61, 14, 44, 'Odio ad quisquam provident.', 'Minus enim totam et. Alias quod ut reiciendis sit iure ut dolor dolor. Eligendi a corrupti nisi nulla rerum. Amet itaque aliquam saepe sit et occaecati.', NULL, 'todo'),
(62, 18, 41, 'Sunt repellendus sit enim laudantium.', 'Dicta est reiciendis ut. Accusantium aut consequuntur sit dolor et illum aliquam quo.', NULL, 'done'),
(63, 20, 47, 'Et iste odio.', 'Nesciunt consequuntur voluptates molestias perferendis qui animi. Nihil accusantium veritatis delectus rem error. Sed ducimus incidunt quia ipsam vel.', NULL, 'done'),
(64, 20, 42, 'Corporis aspernatur iure eaque odio dolorem.', 'Tenetur illo aut enim et. Dolorem voluptates aut aspernatur nam fuga aut impedit. Corporis voluptates velit doloribus dolorum.', '2026-01-04', 'doing'),
(65, 13, 50, 'Ipsum eos eius rem voluptas.', 'Porro quod velit sit veritatis ex laborum. Eaque minima voluptas quia voluptas eaque. Corporis facere delectus suscipit et.', NULL, 'todo'),
(66, 14, 48, 'Aspernatur qui animi quae.', 'Nulla dicta officiis doloremque odio sed. Assumenda reiciendis et esse asperiores aut quibusdam expedita. Laborum quia quae dolores sit.', NULL, 'todo'),
(67, 12, 46, 'Cupiditate laboriosam illo corrupti.', 'Quos qui hic culpa quia voluptatem pariatur. Iure voluptas maiores a commodi. Laborum dignissimos temporibus illo eaque corrupti culpa consequatur. Aut unde cumque quod est.', NULL, 'done'),
(68, 13, 52, 'Explicabo perferendis et aut.', 'Est qui non aliquam dolorem culpa. Maxime pariatur nobis rerum at omnis.', '2026-06-18', 'done'),
(69, 11, 49, 'Ipsum possimus ut quo quo.', 'Nobis excepturi perferendis id error omnis velit veniam. Minus libero nulla dolore incidunt excepturi aut. Doloremque error ipsa et.', '2026-04-11', 'done'),
(70, 13, 55, 'Id excepturi sint aut minima quo.', 'Expedita esse enim nihil aut qui saepe. Tempore et illum provident quis. Aliquid id qui atque dolorem illo minima ut. Voluptatem aut voluptatem ullam molestiae.', NULL, 'todo'),
(71, 11, 50, 'Aliquid culpa est omnis modi.', 'Vel quibusdam rerum ratione aut qui error provident. Et voluptas modi cupiditate est rerum sunt aperiam totam. Repudiandae doloribus et libero et.', '2026-06-15', 'todo'),
(72, 19, 58, 'Sequi repellat at totam.', 'Nulla minima debitis ex quo sint quia. Provident rerum libero quos eius laudantium dicta dolorem.', '2026-05-04', 'todo'),
(73, 13, 54, 'Necessitatibus ut totam ad.', 'Laudantium repellat ullam molestias voluptatem laboriosam. Laudantium qui ut et quibusdam dolores omnis. Ratione qui esse nisi atque pariatur vitae. Est magnam omnis unde sequi.', NULL, 'todo'),
(74, 13, 58, 'Quibusdam esse repellendus.', 'Quod dolor quos consectetur similique quia esse. Eum nobis vero maiores occaecati cupiditate assumenda.', '2026-01-24', 'todo'),
(75, 13, 45, 'Qui ut doloribus ullam voluptate.', 'Hic deleniti animi similique repudiandae nisi id. Sit eius a id optio accusantium aliquam sunt. Aliquid fuga iusto necessitatibus et veritatis laborum placeat necessitatibus. Asperiores vel sed pariatur dolor.', NULL, 'done'),
(76, 12, 56, 'Aperiam sed non et.', 'Illo sit consequuntur minima. Error aut voluptatem sed ad dolorem quae velit numquam. Sed eos sit in.', '2026-03-22', 'done'),
(77, 12, NULL, 'Sint quia saepe similique.', 'Similique nobis quia voluptate itaque. Et quod accusantium occaecati sit. Voluptatem vitae autem minus rerum.', NULL, 'doing'),
(78, 18, 44, 'Provident ea vel non.', 'In magnam sint consequatur. Ut velit quasi doloremque tempore. Et explicabo omnis nesciunt corrupti. Quasi quo nihil incidunt iste.', '2026-04-19', 'todo'),
(79, 14, 59, 'Est vel ut consequatur.', 'Officia pariatur est amet minima itaque. In et vero et aut repellat.', NULL, 'done'),
(80, 13, 54, 'Enim velit voluptatibus nulla inventore.', 'Quia laudantium et exercitationem et optio. Voluptas ut quo dicta id qui assumenda. Ea ut enim reprehenderit repudiandae. Inventore eveniet et corrupti qui sint qui dolorum corporis. Tempora optio alias aut quibusdam vel itaque.', NULL, 'done'),
(81, 15, 53, 'Atque sint molestiae.', 'Similique voluptatem voluptas magnam voluptas. Rerum nemo enim vero sint. Illum natus quia a totam rerum odio. Qui esse commodi odit quaerat.', '2026-04-26', 'todo'),
(82, 13, 58, 'Dolor vitae ea iure.', 'Soluta vel in fuga fugit officiis modi dolor. Architecto et aperiam impedit. Voluptas facere non vel veritatis nesciunt.', '2026-04-08', 'done'),
(83, 18, 51, 'Exercitationem animi quia numquam inventore.', 'Sapiente eius enim nostrum eligendi fuga et. Repellat sunt voluptatem aliquam qui non corporis.', '2026-03-17', 'doing'),
(84, 15, 46, 'Deserunt sit beatae commodi nulla.', 'Sapiente ut nobis odit quae repellat est odio. Distinctio consectetur placeat fuga ab excepturi amet inventore. Nostrum neque laboriosam sit sit.', '2026-06-30', 'doing'),
(85, 12, 41, 'Quia saepe impedit.', 'Facere ab quo dignissimos dolore omnis repellendus. Maiores modi facilis et similique ut. Doloribus rerum vel sit sint. Autem ad dolore impedit ut doloribus occaecati. Molestias consectetur occaecati tenetur.', NULL, 'doing'),
(86, 19, NULL, 'Incidunt qui eum aperiam.', 'Libero sed inventore eos atque ipsam magni quia. Qui repellat temporibus quasi.', '2026-04-28', 'todo'),
(87, 11, 43, 'Et optio laborum minus.', 'Soluta et eligendi natus temporibus delectus praesentium in. Eos ipsa beatae quod hic earum occaecati dolores. Dolores non itaque sed laboriosam quis et earum molestias. Iste non omnis unde id rem necessitatibus. Est magni iure inventore nobis.', NULL, 'todo'),
(88, 15, 58, 'Officia harum natus quia rerum unde.', 'Nisi reiciendis sunt saepe porro enim similique consequatur. Maiores neque voluptas voluptatibus eligendi dolorem voluptatem. Corrupti quibusdam eum rerum fugit.', '2026-04-03', 'doing'),
(89, 14, NULL, 'Sed nisi quos inventore.', 'Quis accusamus id dolores quo. Debitis et nihil aperiam repudiandae sed nisi id. Doloremque ea et voluptas aperiam eos incidunt. Saepe molestiae id aut unde quae maiores voluptates iste.', NULL, 'todo'),
(90, 16, NULL, 'Ipsum repellendus quo reiciendis voluptas.', 'Autem libero enim minus blanditiis. Saepe inventore nesciunt dolorum similique aliquid.', NULL, 'done'),
(91, 15, 57, 'Voluptate et ipsa excepturi.', 'Fugit eos eos ut perspiciatis enim aut. Magnam aut ea quae velit fugit delectus. Omnis eos voluptate amet.', NULL, 'todo'),
(92, 20, NULL, 'Et eos quis sequi.', 'Ut incidunt repellat est ut dolores. Incidunt eligendi et consectetur impedit et. Omnis alias qui quia delectus vel.', NULL, 'doing'),
(93, 11, 53, 'Repellendus aliquid eius id et incidunt.', 'Architecto non tempora et cupiditate veritatis dolor maxime. At soluta autem ut et tempore eum. Error a eius id.', NULL, 'todo'),
(94, 14, 45, 'Aliquam aliquid necessitatibus et debitis ad.', 'Pariatur eveniet est dolores suscipit et. Ipsum consequuntur exercitationem fugiat rerum alias fugit harum accusamus. Commodi voluptate quaerat vero at distinctio dolore. Accusamus voluptatem dolorum repellendus tempora.', NULL, 'todo'),
(95, 18, 52, 'Aliquam ut et molestiae vitae.', 'Molestiae exercitationem velit corrupti. Aut vitae odio itaque voluptatibus ipsa ut veniam non.', NULL, 'doing'),
(96, 12, 46, 'In perferendis enim incidunt qui qui.', 'Omnis ut quibusdam eos perferendis ut iste ex. Magni ut veritatis voluptatibus officia fuga temporibus. Itaque sit ipsum rem quo. Quia non suscipit natus et laborum similique cumque et.', '2026-05-24', 'todo'),
(97, 15, 45, 'Perspiciatis rem magni molestiae omnis.', 'Officiis tempora quam sit sapiente iure. Sunt et corporis molestias illum sed tenetur aut. Placeat ipsum eius distinctio aliquam nemo.', '2026-01-10', 'todo'),
(98, 20, 48, 'Iusto non laudantium aut.', 'Nemo ducimus ab ea iure et molestias. Recusandae qui quis quia repudiandae voluptatem molestiae. Nisi sapiente cum aut error odit distinctio adipisci.', '2026-04-27', 'doing'),
(99, 18, NULL, 'Harum magni sint optio sint eligendi.', 'Libero est hic sapiente omnis porro necessitatibus omnis. Facilis inventore dolorem autem quia dolores quia illo. Consequuntur ea sed dolorem autem fuga exercitationem. Doloremque modi vero soluta sed earum facere.', '2026-04-16', 'doing'),
(100, 17, NULL, 'Aut quas est.', 'Exercitationem velit et qui est sed. Debitis eaque omnis iusto sapiente. Dolor blanditiis excepturi quae recusandae nam molestiae doloribus. Sed et dicta ratione ex sed.', NULL, 'doing');

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

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
  ADD CONSTRAINT `FK_527EDB258C03F15C` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
