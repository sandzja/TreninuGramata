<?php
/**
 * @author Tõnis Tobre <tobre@bitweb.ee>
 * @version 3.0
 * @copyright Copyright 2010. All rights reserved.
 * Date:
 * Changelog:
 */

// SV: application keys and secrets
$twitter = array (
		'consumerKey' => 'XCKtWHPKeoIbUhqqIanOWA',
		'consumerSecret' => '9W9xTQutJ5EqKwQJUTaEnZZ1ASZ4VPjgU0sPGMeoiWo',
	);
$facebook = array (
		'appId' => '206959362723186',
		'secret' => 'f79f18a5170038eb3aaf0a66d54d6d32',
		'loginParams' => array (
			'display' => 'popup',
			'req_perms' => 'email,read_stream,publish_stream,user_about_me,user_birthday,friends_about_me',
		),
		'cookie' => true,
	);
// SV: application keys and secrets change for env
if (APPLICATION_ENV == 'development') {
$twitter['consumerKey'] = 'cG180Oamw5px4ewQvp97gA'; 
$twitter['consumerSecret'] = 'kK8WfoEN6yeBHgf11r2pp2Wql1jqfSWysmYMa8'; 

$facebook['appId'] = '115339601926091'; 
$facebook['secret'] = 'bbf359861a08feeb7d5a3b8fdd914df4'; 
}

return array (
	'system' => array (
		'isDebug' => false,
		'profiler' => false,
	),
	'meta' => array (
		'title' => 'TRAININGBOOK.com - your virtual motivator',
		'titleSeparator' => ' | ',
		'defaultKeywords' => 'training book, motivator, trainingbook, trainingbook.com, tracker, sport tracker, sports tracker, GPS tracking, effective training, fun training, social training, challenge friends, share music playlists, result oriented, faster speed, voice assistant, weight control',
		'defaultDescription' => 'TRAININGBOOK is your virtual motivator in living active and healthy life',
		'domainName' => 'http://trainingbook.local/',
	),
	'twitter' => $twitter,
	'facebook' => $facebook,
	'filePaths' => array (
		'feedPostPicture' => APPLICATION_PATH . '/data/feedPost/',
	),
	'socialMediaMessages' => array (
		'Beat me! Can you do better? Dare to challenge?',
		'You are what you measure! I have all my personal bests in my own TB!',
		'Get moving! Log out, go run! ',
		'I\'m training more effective! I\'m having a lot of fun! Trainingbook is so social!',
	),
);