<?php
require 'inc/conf.php';
require 'inc/constants.php';
require 'inc/init.php';
require 'inc/functions.php';
require 'inc/classes.php';
require 'inc/dbmanager.php';

$dbManager = new DbManager();

$chatRoom = $dbManager->GetChatroom($_POST['roomId']);
$dateLastNewMessage = $_POST['dateLastGetMessages'];
$nbIps = $_POST['nbIps'];

if(!is_null($chatRoom)) {
    $userHash = getHashForIp();
    $time = $_SERVER['REQUEST_TIME'];

    if($chatRoom->dateEnd != 0 && $chatRoom->dateEnd <= $time) {
        echo 'noRoom';
        exit;
    }

    $currentUser = null;

    foreach($chatRoom->users as $key => $user) {
        if(!is_null($user)) {
            if($user['id'] == $userHash) {
                $currentUser = $user;
            }
            if(!$chatRoom->noMoreThanOneVisitor && $user['dateLastSeen'] + NB_SECONDS_USER_TO_BE_DISCONNECTED < $time) {
                unset($chatRoom->users[$key]);
            }
        }
    }

    if(is_null($currentUser)) {
        $currentUser = array();
        $currentUser['id'] = $userHash;
        $currentUser['dateLastSeen'] = $time;
        array_push($chatRoom->users, $currentUser);
    } else {
        $currentUser['dateLastSeen'] = $time;
    }

    if($chatRoom->noMoreThanOneVisitor && count($chatRoom->users) > 2) {
        $dbManager->DeleteChatroom($_POST['roomId']);
        echo 'destroyed';
        exit;
    }

    if($dateLastNewMessage < $chatRoom->dateLastNewMessage) {
        $dbManager->UpdateChatRoomUsers($chatRoom);
        $messages = $dbManager->GetLastMessages($chatRoom->id, NB_MESSAGES_TO_KEEP);
        header('Content-Type: application/json');
        echo '{ "dateLastGetMessages": ',$time,', "chatLines": ',json_encode($messages),', "nbIps": ',count($chatRoom->users),' }';
        exit;
    } else if ($nbIps != count($chatRoom->users)) {
        $dbManager->UpdateChatRoomUsers($chatRoom);
        header('Content-Type: application/json');
        echo '{ "nbIps": ',count($chatRoom->users),' }';
        exit;
    } else {
        echo 'noNew';
        exit;
    }
}
echo 'noRoom';
?>