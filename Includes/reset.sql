SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `music_collection`
--
# CREATE DATABASE IF NOT EXISTS `sb_data` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
# USE `sb_data`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `albums`
--

DROP TABLE IF EXISTS `openingstijden`;
CREATE TABLE IF NOT EXISTS `openingstijden`
(
    `id`    int unsigned NOT NULL AUTO_INCREMENT,
    `start` int unsigned NOT NULL, # dit zou nog na 2038 moeten werken omdat het unsigned is
    `end`   int unsigned NOT NULL,
    PRIMARY KEY (`id`)
);

# INSERT INTO `openingstijden` (`id`, `start`, `end`) VALUES
# (1, 1609407543, 1609407543)

DROP TABLE IF EXISTS `behandelingen`;
CREATE TABLE IF NOT EXISTS `behandelingen`
(
    `id`     int unsigned NOT NULL AUTO_INCREMENT,
    `name`   varchar(100) NOT NULL,
    `length` int unsigned,
    `desc`   varchar(255),
    `price`  int unsigned,
    `cat`    int unsigned,
    PRIMARY KEY (`id`)
);

# INSERT INTO `behandelingen` VALUES
# (1, 'Quick Refresh', 25, 'reiniging, dieptereiniging, masker, advies', 20, 1),
# (2, 'Basis Reinigende Behandeling', 45, 'reiniging, dieptereiniging, onzuiverheden verwijderen, masker, advies', 30, 1),
# (3, 'Basis Relax Behandeling', 45, 'reiniging, dieptereiniging, nek-schouder-decolleté massage, masker, advies', 30, 1),
# (4, 'Luxe Behandeling', 60, 'reiniging, dieptereiniging, onzuiverheden verwijderen, wenkbrauwen epileren, nek-schouder-decolleté massage, masker, advies', 40, 1),
# (5, 'All-in-one behandeling', 75, 'reiniging, dieptereiniging, onzuiverheden verwijderen, wenkbrauwen epileren, uitgebreide massage, masker, dagverzorging, advies', 40, 1),
# (6, 'Acné behandeling gezicht', null, 'ook mogelijk in kuurvorm, 10% korting op totaalprijs bij 5 behandelingen', 50, 1),
# (7, 'Wenkbrauwen epileren of harsen', null, null, 10, 2),
# (8, 'Wenkbrauwen verven', null, null, 11, 2),
# (9, 'All-in-one wenkbrauw treatment', null, 'epileren of harsen, verven en bijknippen', 20, 2),
# (10, 'Wimpers verven', null, null, 12, 2);

DROP TABLE IF EXISTS `categorieen`;
CREATE TABLE IF NOT EXISTS `categorieen`
(
    `id`            int unsigned NOT NULL AUTO_INCREMENT,
    `name`          varchar(100) NOT NULL,
    `display_order` int unsigned NOT NULL,
    PRIMARY KEY (`id`)
);
# INSERT INTO `categorieen` VALUES
# (1 , 'Gezichtsbehandelingen', 1),
# (2 , 'Losse Behandelingen', 2);

DROP TABLE IF EXISTS `afspraken`;
CREATE TABLE IF NOT EXISTS `afspraken`
(
    `id`          int unsigned NOT NULL AUTO_INCREMENT,
    `start`       int unsigned NOT NULL,
    `end`         int unsigned NOT NULL,
    `behandel_id` varchar(50)  NOT NULL, # "3,102,13"
    `tracker_id`  varchar(8)   NOT NULL, # zodat je niet id's van andere afspraken kan gokken
    `email`       varchar(320) NOT NULL,
    `tel`         varchar(15)  NOT NULL,
    `status`      tinyint unsigned,      # 0=niet bekeken, 1=geaccepteert, 2=afgewezen
    PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `admin_accounts`;
CREATE TABLE IF NOT EXISTS `admin_accounts`
(
    `id`           int unsigned NOT NULL AUTO_INCREMENT,
    `email`        varchar(320) NOT NULL,
    `pass`         varchar(255) NOT NULL,
    `add_accounts` boolean,
    PRIMARY KEY (`id`)
);
