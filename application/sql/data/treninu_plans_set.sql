INSERT INTO `User` (`id`, `name`, `email`, `goal`, `height`, `weight`, `birthDate`, `facebookUserId`, `facebookAccessToken`, `twitterUserId`, `sessionId`, `sessionValidTime`, `profileImageUrl`, `updatedTime`) VALUES
(1101, 'Veikals Maratons', NULL, NULL, NULL,  NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://graph.facebook.com/127153377129/picture?type=large', NULL),
(1102, 'VSK Noskrien', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://graph.facebook.com/noskrien/picture?type=large', NULL),
(1103, 'Trainingbook.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash2/373487_190495544370465_1322598424_n.jpg', NULL),
(1104, 'Romāns Melderis', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://a0.twimg.com/profile_images/1930869312/image.jpg', NULL),
(1105, 'Māris Ābele', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'http://www.noskrien.lv/wp-content/uploads/2011/05/abele.jpg', NULL);


INSERT INTO `SetSets` (`id`, `sport_id`, `coach_id`, `distance`, `name`, `event`, `event_date`, `intensity`, `usage`, `likes`, `image`) VALUES
(3, 1, 1103, '10k training plan','Low intensity | 3 workouts per week', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(4, 1, 1101, 'Half-Marathon training plan','Low intensity | 3 workouts per week', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(5, 1, 1103, '10k training plan','Low intensity | 4 workouts per week', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(6, 1, 1103, 'Half-Marathon training plan','Low intensity | 4 workouts per week', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(7, 1, 1103, '10k training plan','Medium intensity | 3 workouts per week', 'Nordea Riga marathon', '2012-05-20', 2, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(8, 1, 1101, 'Half-Marathon training plan','Medium intensity | 3 workouts per week', 'Nordea Riga marathon', '2012-05-20', 2, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(9, 1, 1102, 'Half-Marathon training plan','Finish time 02h 00m', 'Nordea Riga marathon', '2012-05-20', 1, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(10, 1, 1102, 'Half-Marathon training plan','Finish time 01h 45m', 'Nordea Riga marathon', '2012-05-20', 2, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(11, 1, 1104, '10k training plan','Medium intensity | 4 workouts per week', 'Nordea Riga marathon', '2012-05-20', 2, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(12, 1, 1104, 'Half-Marathon training plan','Medium intensity | 4 workouts per week', 'Nordea Riga marathon', '2012-05-20', 2, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(13, 1, 1104, '10k training plan','High intensity | For expert runners only', 'Nordea Riga marathon', '2012-05-20', 3, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(14, 1, 1104, 'Half-Marathon training plan','High intensity | For expert runners only', 'Nordea Riga marathon', '2012-05-20', 3, 0, 0, '/partner/nordea_riga_marathon.jpg '),
(15, 1, 1105, 'Half-Marathon training plan','Half-Marathon training plan for speed and endurance', 'Nordea Riga marathon', '2012-05-20', 2, 0, 0, '/partner/nordea_riga_marathon.jpg ');
