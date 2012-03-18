INSERT INTO `User` (`id`, `name`, `email`, `goal`, `is_time_goal`, `height`, `weight`, `birthDate`, `facebookUserId`, `facebookAccessToken`, `twitterUserId`, `sessionId`, `sessionValidTime`, `profileImageUrl`, `updatedTime`, `is_featured`) VALUES
(126, 'Nordea Riga marathon', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://graph.facebook.com/NordeaRigaMarathon/picture?type=large', NULL, 0),
(128, 'VSK Noskrien', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://graph.facebook.com/noskrien/picture?type=large', NULL, 0),
(129, 'Trainingbook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash2/373487_190495544370465_1322598424_n.jpg', NULL, 0);


INSERT INTO `SetSets` (`id`, `sport_id`, `coach_id`, `name`, `event`, `event_date`, `intensity`, `usage`, `likes`, `image`) VALUES
(1, 1, 128, 'Nordea Riga marathon 10 weeks plan', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg'),
(3, 1, 126, '10km low intensity workout plan (3 workout per week)', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(4, 1, 126, 'Half-marathon low intensity workout plan (3 workout per week)', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(5, 1, 129, '10km | Low intensity | 3 workouts per week', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(6, 1, 129, '21km | Low intensity | 3 workouts per week', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(7, 1, 129, '10km | Low intensity | 4 workouts per week', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(8, 1, 129, '21km | Low intensity | 4 workouts per week', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(9, 1, 128, '10km | Medium intensity | 3 workouts per week', 'Nordea Riga marathon', '2012-05-20', 2, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(10, 1, 128, '21km | Medium intensity | 3 workouts per week', 'Nordea Riga marathon', '2012-05-20', 2, 0, 0, '/partner/nordea_riga_marathon.jpg ');
