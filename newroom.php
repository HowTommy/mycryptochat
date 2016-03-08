<?php
require 'inc/conf.php';
require 'inc/constants.php';
require 'inc/init.php';
require 'inc/functions.php';
require 'inc/classes.php';
require 'inc/dbmanager.php';

if(array_key_exists($_POST['nbMinutesToLive'], $allowedTimes)) {
    $nbMinutesToLive = $_POST['nbMinutesToLive'];
} else {
    exit('cheater');
}

$time = $_SERVER['REQUEST_TIME'];

$selfDestroys = isset($_POST['selfDestroys']) && $_POST['selfDestroys'] == 'true';

$isRemovable = isset($_POST['isRemovable']) && $_POST['isRemovable'] == 'true';
$removePassword = $_POST['removePassword'];

$userHash = getHashForIp();

// we generate a random key
$key = randomString(20);

// we create the chat room object
$chatRoom = new ChatRoom();
$chatRoom->id = $key;
$chatRoom->dateCreation = $time;
$chatRoom->dateLastNewMessage = $time;
$chatRoom->dateEnd = $nbMinutesToLive != 0 ? $time + ($nbMinutesToLive * 60) : 0;
$chatRoom->noMoreThanOneVisitor = $selfDestroys;
$chatRoom->isRemovable = $isRemovable;
$chatRoom->removePassword = $removePassword;
$chatRoom->userId = $userHash;

$chatUser = array();
$chatUser['id'] = $userHash;
$chatUser['dateLastSeen'] = $time;

array_push($chatRoom->users, $chatUser);

$dbManager = new DbManager();
// we delete old chatrooms
$dbManager->CleanChatrooms($time);
// we save the chat room in sqlite
$dbManager->CreateChatroom($chatRoom);

header('Location: chatroom.php?id=' . $key);
