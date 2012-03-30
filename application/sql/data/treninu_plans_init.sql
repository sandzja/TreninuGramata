--
-- Tabulas struktūra tabulai `UserConfig`
--

CREATE TABLE IF NOT EXISTS `UserConfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `param_name` varchar(100) NOT NULL,
  `param_value` varchar(500) CHARACTER SET utf8 NOT NULL,
  `param_key` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`param_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Izmaiņas defaultai tabulai `TrainingPlan`
--

ALTER TABLE  `TrainingPlan` ADD  `set_id` INT NULL;

--
-- Tabulas struktūra tabulai `SetExercise`
--

CREATE TABLE IF NOT EXISTS `SetExercise` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trainingPlanId` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `intensity` tinyint(4) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `distance` double(8,2) NOT NULL,
  `duration` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Excercise_TrainingPlan1` (`trainingPlanId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `SetSets`
--

CREATE TABLE IF NOT EXISTS `SetSets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sport_id` int(11) NOT NULL,
  `coach_id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `event` varchar(100) CHARACTER SET utf8 NOT NULL,
  `event_date` date NOT NULL,
  `intensity` int(11) NOT NULL DEFAULT '1',
  `usage` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0',
  `image` varchar(100) NOT NULL,
  `distance` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Tabulas struktūra tabulai `SetTrainingPlan`
--

CREATE TABLE IF NOT EXISTS `SetTrainingPlan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `set_id` int(11) NOT NULL,
  `sport_id` int(10) unsigned NOT NULL,
  `name` varchar(45) NOT NULL,
  `date` datetime NOT NULL,
  `execution_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_TrainingPlan_Sport1` (`sport_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;
