<?php
/**
*	dataManager merupakan fitur CRUD yang terhubung dengan database
*	@Author Fikry Maulana (fikry.maulana@gmail.com)
*/
Class dataManager {
	function __construct($config){
		$this->config = $config;
	}

	function dbConn(){
		$host = $this->config['db']['host'];
		$database = $this->config['db']['database'];
		$username = $this->config['db']['username'];
		$password = $this->config['db']['password'];

		try {
			$dbh = new PDO("mysql:host=$host;dbname=$database", $username, $password);
			return $dbh;
		}
		catch(PDOException $e){
			return $e->getMessage();
		}
	}

	function getCommand($command){
		$dbh = $this->dbConn();

		if(isset($command) && strlen($command) > 0){
			$sql = "SELECT * FROM sms_command WHERE command_name = '".trim(strtoupper($command))."'";

			$res = $dbh->prepare($sql);
		    $res->execute();
		 
		    if($data = $res->fetchObject()){
		    	return $data;
		    }
		    else {
		    	return false;
		    }
		}
		else {
			return false;
		}
	}

	function getMessage($message_id){
		$dbh = $this->dbConn();

		if(isset($message_id) && strlen($message_id) > 0){
			$sql = "SELECT messages FROM reply_messages WHERE message_code = '".$message_id."'";

			$res = $dbh->prepare($sql);
		    $res->execute();
		 
		    if($data = $res->fetch(PDO::FETCH_ASSOC)){
		    	return $data;
		    }
		    else {
		    	return false;
		    }
		}
		else {
			return false;
		}
	}

	function setLog($data){
		$dbh = $this->dbConn();
		$r_message = isset($data['reply_message']) ? $data['reply_message'] : '';
		$r_dt = isset($data['reply_datetime']) ? $data['reply_datetime'] : '';
		$sql = "INSERT INTO sms_log (trx_id, receive_datetime, sender, receiver, message, reply_message, reply_datetime, command_code, reply_code, status) VALUES ('".$data['trx_id']."', '".$data['receive_datetime']."', '".$data['sender']."', '".$data['receiver']."', '".$data['message']."', '".$r_message."', '".$r_dt."', '".$data['command_code']."', '".$data['reply_code']."', '".$data['status']."')";
		$sql .= " ON DUPLICATE KEY UPDATE ";
		$sql .= "trx_id ='".$data['trx_id']."',";
		$sql .= "receive_datetime ='".$data['receive_datetime']."',";
		$sql .= "sender = '".$data['sender']."',";
		$sql .= "receiver = '".$data['receiver']."',";
		$sql .= "message = '".$data['message']."',";
		$sql .= "reply_message = '".$r_message."',";
		$sql .= "reply_datetime = '".$r_dt."',";
		$sql .= "command_code = '".$data['command_code']."',";
		$sql .= "reply_code = '".$data['reply_code']."',";
		$sql .= "status = '".$data['status']."'";

		$res = $dbh->prepare($sql);
		$res->execute();
		return true;
	}

	function processMessage($message_id, $replacement){
		$message = $this->getMessage($message_id);
		$data = str_replace(array_keys($replacement), array_values($replacement), $message['messages']);
		return htmlentities($data);
	}

	function getSMS(){
		$dbh = $this->dbConn();
		$sql = "SELECT * FROM log WHERE type = 'inbox' AND status = '0' LIMIT 10";

		$res = $dbh->prepare($sql);
		$res->execute();
		if($data = $res->fetchAll(PDO::FETCH_ASSOC)){
			return $data;
		}
		else {
			return false;
		}
	}

	function updateSMS($data){
		$dbh = $this->dbConn();
		$sql = "UPDATE log SET status = '".$data['status']."' WHERE id = '".$data['id']."'";

		$res = $dbh->prepare($sql);
		$res->execute();
		return true;
	}
}

?>