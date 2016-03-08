<?php
require 'inc/conf.php';
require 'inc/constants.php';
require 'inc/init.php';
require 'inc/functions.php';
require 'inc/classes.php';
require 'inc/dbmanager.php';

$dbManager = new DbManager();

$chatRoom = $dbManager->GetChatroom($_POST['roomId']);

if(!$chatRoom->isRemovable) {
	echo 'error';
	exit;
} else if ($chatRoom->removePassword != $_POST['removePassword']) {
	echo 'wrongPassword';
	exit;
} else {
	$dbManager->DeleteChatroom($_POST['roomId']);
	echo 'removed';
	exit;
}
?>