<?php
class DbManager {
    private $db;
    
    function DbManager() {
        try {
            $this->db = new PDO('sqlite:' . DB_FILE_NAME);
            $this->db->setAttribute(PDO::ATTR_PERSISTENT, true /*, PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION*/);
            $this->db->exec('PRAGMA temp_store = MEMORY; PRAGMA synchronous=OFF;');
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function CreateChatroom($chatRoom) {
        if(is_null($chatRoom)) {
            die('Parameter error.');   
        }
        try {
			$query = 'INSERT INTO ChatRoom ( Id, DateCreation, DateLastNewMessage, ConnectedUsers, DateEnd, NoMoreThanOneVisitor, IsRemovable, RemovePassword, UserHash )';
			$query .= ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
			
			$req = $this->db->prepare($query);

			$req->execute(array($chatRoom->id, $chatRoom->dateCreation, $chatRoom->dateLastNewMessage, json_encode($chatRoom->users)
								, $chatRoom->dateEnd, $chatRoom->noMoreThanOneVisitor ? 1 : 0, $chatRoom->isRemovable ? 1 : 0, $chatRoom->removePassword, $chatRoom->userId));
            if($this->db->errorCode() != '00000') 
            {
                die('Error: database error.');
            }
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function CleanChatrooms($time) {
        try {
            $query = 'DELETE FROM ChatMessage WHERE IdChatRoom IN (SELECT Id FROM ChatRoom WHERE ( DateEnd <> 0 AND DateEnd < ? ) OR DateLastNewMessage < ?)';
			
			$idleTime = $time - (DAYS_TO_DELETE_IDLE_CHATROOM * 24 * 60 * 60);
            
            $req = $this->db->prepare($query);
            
            $req->execute(array($time, $idleTime));
            
            $query = 'DELETE FROM ChatRoom WHERE ( DateEnd <> 0 AND DateEnd < ? ) OR DateLastNewMessage < ?';
            
            $req = $this->db->prepare($query);
            
            $req->execute(array($time, $idleTime));
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function DeleteChatroom($chatRoomId) {
        try {
            $query = 'DELETE FROM ChatMessage WHERE IdChatRoom = ?';
            
            $req = $this->db->prepare($query);
            
            $req->execute(array($chatRoomId));
            
            $query = 'DELETE FROM ChatRoom WHERE Id = ?';
            
            $req = $this->db->prepare($query);
            
            $req->execute(array($chatRoomId));
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function GetChatroom($id) {
        if(is_null($id) || $id == '') {
            return null;    
        }
        
        try {            
            $query = 'SELECT Id, DateCreation, DateLastNewMessage, ConnectedUsers, DateEnd, NoMoreThanOneVisitor, IsRemovable, RemovePassword, UserHash FROM ChatRoom WHERE Id = ?';
            
            $req = $this->db->prepare($query);
            
            $req->execute(array($id));
            
            $result = $req->fetchAll();
            
            if(is_null($result) || count($result) != 1) {
                return null;
            }
            
            $resultRow = $result[0];
            
            $chatRoom = new ChatRoom;
            $chatRoom->id = $resultRow['Id'];
            $chatRoom->dateCreation = $resultRow['DateCreation'];
            $chatRoom->dateLastNewMessage = $resultRow['DateLastNewMessage'];
            $chatRoom->users = json_decode($resultRow['ConnectedUsers'], true);
            $chatRoom->dateEnd = $resultRow['DateEnd'];
            $chatRoom->noMoreThanOneVisitor = $resultRow['NoMoreThanOneVisitor'] == 1;
			$chatRoom->isRemovable = $resultRow['IsRemovable'] == 1;
			$chatRoom->removePassword = $resultRow['RemovePassword'];
			$chatRoom->userId = $resultRow['UserHash'];
            
            return $chatRoom;
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function UpdateChatRoomUsers($chatRoom) {
        try {
            $query = 'UPDATE ChatRoom SET ConnectedUsers = ? WHERE Id = ?';
            
            $req = $this->db->prepare($query);
            
            $req->execute(array(json_encode($chatRoom->users), $chatRoom->id));
            
            if($this->db->errorCode() != '00000') 
            {
                die('Error: database error.');
            }
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function GetLastMessages($chatRoomId, $nbMessages) {
        try {
            $query = 'SELECT Message, Hash, User, Date FROM ChatMessage WHERE IdChatRoom = ? ORDER BY Date DESC LIMIT ?';
            
            $req = $this->db->prepare($query);
            
            $req->execute(array($chatRoomId, $nbMessages));
            
            $messages = array();
            
            while ($line = $req->fetch()) { 
                $chatMessage = new ChatMessage;
                $chatMessage->message = $line['Message'];
                $chatMessage->hash = $line['Hash'];
                $chatMessage->userId = $line['User'];
                $chatMessage->date = $line['Date'];
                array_unshift($messages, $chatMessage);
            } 
            
            return $messages;
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function UpdateChatRoomDateLastMessage($chatRoomId, $time) {
        try {
            $query = 'UPDATE ChatRoom SET DateLastNewMessage = ? WHERE Id = ?';
            
            $req = $this->db->prepare($query);
            
            $req->execute(array($time, $chatRoomId));
            
            if($this->db->errorCode() != '00000') 
            {
                die('Error: database error.');
            }
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function AddMessage($chatRoomId, $message, $userMessage, $hash, $time) {
        try {
            $query = 'INSERT INTO ChatMessage ( IdChatRoom, Message, Hash, User, Date ) VALUES ( ?, ?, ?, ?, ? )';
            
            $req = $this->db->prepare($query);
            
            $req->execute(array($chatRoomId, $message, $hash, $userMessage, $time));
            
            if($this->db->errorCode() != '00000') 
            {
                die('Error: database error.');
            }
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function GetNbChatRooms() {
        try {
            $query = 'SELECT COUNT(Id) FROM ChatRoom';
            
            $req = $this->db->prepare($query);
            
            $req->execute();
            
            $result = $req->fetchAll();
            
            if(is_null($result) || count($result) != 1) {
                return -1;
            }
            
            $resultRow = $result[0];
            
            return $resultRow[0];
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
    
    function GetNbMessages() {
        try {
            $query = 'SELECT COUNT(IdChatRoom) FROM ChatMessage';
            
            $req = $this->db->prepare($query);
            
            $req->execute();
            
            $result = $req->fetchAll();
            
            if(is_null($result) || count($result) != 1) {
                return -1;
            }
            
            $resultRow = $result[0];
            
            return $resultRow[0];
        }
        catch (Exception $e) 
        {
            logException($e);
            die('Error: database error.');
        }
    }
}
