<?php
require 'inc/conf.php';
require 'inc/constants.php';
require 'inc/init.php';
require 'inc/functions.php';
require 'inc/classes.php';
require 'inc/dbmanager.php';

$dbManager = new DbManager();

$chatRoom = $dbManager->GetChatroom($_POST['roomId']);
$userName = $_POST['user'];
$message = $_POST['message'];
$time = $_SERVER['REQUEST_TIME'];

if(!is_null($chatRoom)) {
    $dbManager->UpdateChatRoomDateLastMessage($_POST['roomId'], $time);
    
    $dbManager->AddMessage($_POST['roomId'], $message, $userName, getHashForIp(), $time);
    
    echo 'true';
    exit;
}

echo 'false';
?>