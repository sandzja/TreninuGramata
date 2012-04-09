-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Hosts: localhost
-- Izveidošanas laiks: 09.04.2012 19:58
-- Servera versija: 5.1.41
-- PHP Versija: 5.3.2-1ubuntu4.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datubāze: `trainingbook`
--

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `Challenge`
--

CREATE TABLE IF NOT EXISTS `Challenge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `record_id` int(10) unsigned DEFAULT NULL,
  `workout_id` int(10) unsigned DEFAULT NULL,
  `training_plan_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `opponent_user_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Challenge_Record1` (`record_id`),
  KEY `fk_Challenge_Workout1` (`workout_id`),
  KEY `fk_Challenge_TrainingPlan1` (`training_plan_id`),
  KEY `fk_Challenge_User1` (`user_id`),
  KEY `fk_Challenge_User2` (`opponent_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=107 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `ChallengeReport`
--

CREATE TABLE IF NOT EXISTS `ChallengeReport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `challenge_id` int(10) unsigned NOT NULL,
  `training_plan_report_id` int(10) unsigned NOT NULL,
  `didWinChallenge` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ChallengeReport_Challenge1` (`challenge_id`),
  KEY `fk_ChallengeReport_TrainingPlanReport1` (`training_plan_report_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `Exercise`
--

CREATE TABLE IF NOT EXISTS `Exercise` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trainingPlanId` int(10) unsigned NOT NULL,
  `goal_id` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `intensity` tinyint(4) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_Excercise_TrainingPlan1` (`trainingPlanId`),
  KEY `fk_Exercise_Goal1` (`goal_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32943 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `ExerciseReport`
--

CREATE TABLE IF NOT EXISTS `ExerciseReport` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exercise_id` int(10) unsigned NOT NULL,
  `training_plan_report_id` int(10) unsigned NOT NULL,
  `distance` double(8,2) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_ExerciseReport_Exercise1` (`exercise_id`),
  KEY `fk_ExerciseReport_TrainingPlanReport1` (`training_plan_report_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11839 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `FeedChallenge`
--

CREATE TABLE IF NOT EXISTS `FeedChallenge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `challenge_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_FeedChallenge_FeedPost1` (`id`),
  KEY `fk_FeedChallenge_Challenge1` (`challenge_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17777 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `FeedComment`
--

CREATE TABLE IF NOT EXISTS `FeedComment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feed_post_id` int(10) unsigned NOT NULL,
  `author_user_id` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Comment_Feed1` (`feed_post_id`),
  KEY `fk_Comment_User1` (`author_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=325 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `FeedNote`
--

CREATE TABLE IF NOT EXISTS `FeedNote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17779 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `FeedPicture`
--

CREATE TABLE IF NOT EXISTS `FeedPicture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16595 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `FeedPost`
--

CREATE TABLE IF NOT EXISTS `FeedPost` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_user_id` int(10) unsigned NOT NULL,
  `comment` text,
  `is_private` tinyint(1) NOT NULL,
  `send_facebook` tinyint(1) NOT NULL DEFAULT '0',
  `send_twitter` tinyint(1) NOT NULL DEFAULT '0',
  `facebook_post_id` varchar(255) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `discr` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Feed_User1` (`author_user_id`),
  KEY `INDEX_discr` (`discr`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19123 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `FeedTrainingPlan`
--

CREATE TABLE IF NOT EXISTS `FeedTrainingPlan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_plan_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_FeedTrainingPlan_FeedPost1` (`id`),
  KEY `fk_FeedTrainingPlan_TrainingPlan1` (`training_plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19123 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `FeedWorkout`
--

CREATE TABLE IF NOT EXISTS `FeedWorkout` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `workout_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_FeedWorkout_Workout1` (`workout_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19073 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `Friend`
--

CREATE TABLE IF NOT EXISTS `Friend` (
  `user_id` int(10) unsigned NOT NULL,
  `friend_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`friend_user_id`),
  KEY `fk_Friend_User1` (`user_id`),
  KEY `fk_Friend_User2` (`friend_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `Goal`
--

CREATE TABLE IF NOT EXISTS `Goal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `distance` double(8,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `is_challenge` tinyint(1) DEFAULT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32853 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `Message`
--

CREATE TABLE IF NOT EXISTS `Message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_user_id` int(10) unsigned NOT NULL,
  `receiver_user_id` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `date_sent` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Message_User1` (`author_user_id`),
  KEY `fk_Message_User2` (`receiver_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=98 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `Record`
--

CREATE TABLE IF NOT EXISTS `Record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sport_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `workout_id` int(10) unsigned NOT NULL,
  `distance` double(8,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `is_time_record` tinyint(1) NOT NULL,
  `is_miles` tinyint(1) NOT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_Record_Sport1` (`sport_id`),
  KEY `fk_Record_User1` (`user_id`),
  KEY `fk_Record_Workout1` (`workout_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=527 ;

-- --------------------------------------------------------

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3390 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=404 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `Sport`
--

CREATE TABLE IF NOT EXISTS `Sport` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `calories_factor` double(8,2) NOT NULL,
  `intensity_speed` double(8,2) DEFAULT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_Sport_User1` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `TrackPoint`
--

CREATE TABLE IF NOT EXISTS `TrackPoint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exerciseReportId` int(10) unsigned NOT NULL,
  `alt` double(8,2) DEFAULT NULL,
  `distanceToLastPoint` double(8,2) DEFAULT NULL,
  `heart` int(11) DEFAULT NULL,
  `isUploaded` tinyint(1) NOT NULL,
  `lat` varchar(20) NOT NULL,
  `lon` varchar(20) NOT NULL,
  `pulse` int(11) DEFAULT NULL,
  `speed` double(8,2) DEFAULT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_TrackPoint_ExerciseRaport1` (`exerciseReportId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=188381 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `TrainingPlan`
--

CREATE TABLE IF NOT EXISTS `TrainingPlan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `sport_id` int(10) unsigned NOT NULL,
  `original_training_plan_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `date` datetime NOT NULL,
  `execution_order` int(11) NOT NULL,
  `deleted_time` datetime DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL,
  `has_workout_goal` tinyint(1) NOT NULL,
  `is_challenge` tinyint(1) NOT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `set_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_TrainingPlan_Sport1` (`sport_id`),
  KEY `fk_TrainingPlan_TrainingPlan1` (`original_training_plan_id`),
  KEY `fk_TrainingPlan_User` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9728 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `TrainingPlanReport`
--

CREATE TABLE IF NOT EXISTS `TrainingPlanReport` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `training_plan_id` int(10) unsigned NOT NULL,
  `workout_id` int(10) unsigned NOT NULL,
  `sport_id` int(10) unsigned NOT NULL,
  `burned_calories` int(11) DEFAULT NULL,
  `heart` int(11) DEFAULT NULL,
  `pace` int(11) DEFAULT NULL,
  `distance` double(8,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_TrainingPlanReport_TrainingPlan1` (`training_plan_id`),
  KEY `fk_TrainingPlanReport_Workout1` (`workout_id`),
  KEY `fk_TrainingPlanReport_Sport1` (`sport_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10782 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `goal` decimal(10,0) DEFAULT NULL,
  `goal_type` enum('time','distance','workout','calories') DEFAULT NULL,
  `height` double(8,2) DEFAULT NULL,
  `weight` double(8,2) DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  `facebookUserId` varchar(45) DEFAULT NULL,
  `facebookAccessToken` text,
  `twitterOAuthToken` varchar(255) DEFAULT NULL,
  `twitterOauthTokenSecret` varchar(255) DEFAULT NULL,
  `twitterUserId` varchar(45) DEFAULT NULL,
  `sessionId` varchar(45) DEFAULT NULL,
  `sessionValidTime` datetime DEFAULT NULL,
  `profileImageUrl` varchar(255) DEFAULT NULL,
  `updatedTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1106 ;

-- --------------------------------------------------------

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Tabulas struktūra tabulai `Workout`
--

CREATE TABLE IF NOT EXISTS `Workout` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `distance` double(8,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_shared` tinyint(1) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `play_list` text,
  `is_synced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_Workout_User1` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8926 ;

--
-- Ierobežojumi izmestām tabulām
--

--
-- Ierobežojumi tabulai `Challenge`
--
ALTER TABLE `Challenge`
  ADD CONSTRAINT `fk_Challenge_Record1` FOREIGN KEY (`record_id`) REFERENCES `Record` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Challenge_TrainingPlan1` FOREIGN KEY (`training_plan_id`) REFERENCES `TrainingPlan` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Challenge_User1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Challenge_User2` FOREIGN KEY (`opponent_user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Challenge_Workout1` FOREIGN KEY (`workout_id`) REFERENCES `Workout` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `ChallengeReport`
--
ALTER TABLE `ChallengeReport`
  ADD CONSTRAINT `fk_ChallengeReport_Challenge1` FOREIGN KEY (`challenge_id`) REFERENCES `Challenge` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ChallengeReport_TrainingPlanReport1` FOREIGN KEY (`training_plan_report_id`) REFERENCES `TrainingPlanReport` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `Exercise`
--
ALTER TABLE `Exercise`
  ADD CONSTRAINT `fk_Excercise_TrainingPlan1` FOREIGN KEY (`trainingPlanId`) REFERENCES `TrainingPlan` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Exercise_Goal1` FOREIGN KEY (`goal_id`) REFERENCES `Goal` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `ExerciseReport`
--
ALTER TABLE `ExerciseReport`
  ADD CONSTRAINT `fk_ExerciseReport_Exercise1` FOREIGN KEY (`exercise_id`) REFERENCES `Exercise` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ExerciseReport_TrainingPlanReport1` FOREIGN KEY (`training_plan_report_id`) REFERENCES `TrainingPlanReport` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `FeedChallenge`
--
ALTER TABLE `FeedChallenge`
  ADD CONSTRAINT `fk_FeedChallenge_Challenge1` FOREIGN KEY (`challenge_id`) REFERENCES `Challenge` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_FeedChallenge_FeedPost1` FOREIGN KEY (`id`) REFERENCES `FeedPost` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `FeedComment`
--
ALTER TABLE `FeedComment`
  ADD CONSTRAINT `fk_Comment_Feed1` FOREIGN KEY (`feed_post_id`) REFERENCES `FeedPost` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Comment_User1` FOREIGN KEY (`author_user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `FeedNote`
--
ALTER TABLE `FeedNote`
  ADD CONSTRAINT `fk_FeedNote_FeedPost1` FOREIGN KEY (`id`) REFERENCES `FeedPost` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `FeedPicture`
--
ALTER TABLE `FeedPicture`
  ADD CONSTRAINT `fk_FeedPicture_FeedPost1` FOREIGN KEY (`id`) REFERENCES `FeedPost` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `FeedPost`
--
ALTER TABLE `FeedPost`
  ADD CONSTRAINT `fk_Feed_User1` FOREIGN KEY (`author_user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `FeedTrainingPlan`
--
ALTER TABLE `FeedTrainingPlan`
  ADD CONSTRAINT `fk_FeedTrainingPlan_FeedPost1` FOREIGN KEY (`id`) REFERENCES `FeedPost` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_FeedTrainingPlan_TrainingPlan1` FOREIGN KEY (`training_plan_id`) REFERENCES `TrainingPlan` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `FeedWorkout`
--
ALTER TABLE `FeedWorkout`
  ADD CONSTRAINT `fk_FeedWorkout_FeedPost1` FOREIGN KEY (`id`) REFERENCES `FeedPost` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_FeedWorkout_Workout1` FOREIGN KEY (`workout_id`) REFERENCES `Workout` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `Friend`
--
ALTER TABLE `Friend`
  ADD CONSTRAINT `fk_Friend_User1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Friend_User2` FOREIGN KEY (`friend_user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `Message`
--
ALTER TABLE `Message`
  ADD CONSTRAINT `fk_Message_User1` FOREIGN KEY (`author_user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Message_User2` FOREIGN KEY (`receiver_user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `Record`
--
ALTER TABLE `Record`
  ADD CONSTRAINT `fk_Record_Sport1` FOREIGN KEY (`sport_id`) REFERENCES `Sport` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Record_User1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_Record_Workout1` FOREIGN KEY (`workout_id`) REFERENCES `Workout` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `Sport`
--
ALTER TABLE `Sport`
  ADD CONSTRAINT `fk_Sport_User1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `TrackPoint`
--
ALTER TABLE `TrackPoint`
  ADD CONSTRAINT `fk_TrackPoint_ExerciseRaport1` FOREIGN KEY (`exerciseReportId`) REFERENCES `ExerciseReport` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `TrainingPlan`
--
ALTER TABLE `TrainingPlan`
  ADD CONSTRAINT `fk_TrainingPlan_Sport1` FOREIGN KEY (`sport_id`) REFERENCES `Sport` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_TrainingPlan_TrainingPlan1` FOREIGN KEY (`original_training_plan_id`) REFERENCES `TrainingPlan` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_TrainingPlan_User` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `TrainingPlanReport`
--
ALTER TABLE `TrainingPlanReport`
  ADD CONSTRAINT `fk_TrainingPlanReport_Sport1` FOREIGN KEY (`sport_id`) REFERENCES `Sport` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_TrainingPlanReport_TrainingPlan1` FOREIGN KEY (`training_plan_id`) REFERENCES `TrainingPlan` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_TrainingPlanReport_Workout1` FOREIGN KEY (`workout_id`) REFERENCES `Workout` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ierobežojumi tabulai `Workout`
--
ALTER TABLE `Workout`
  ADD CONSTRAINT `fk_Workout_User1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
