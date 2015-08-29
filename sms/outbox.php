<?php
/**
*	Outbox untuk pengiriman sms melalui gammu/kalkun.
*	Metode yang digunakan adalah menginject langsung ke database gammu/kalkun.
*	@Author Fikry Maulana (fikry.maulana@gmail.com)
*/
require_once('outputConfig.php');

Class mySMS {
	function __construct($config){
		$this->config = $config;
	}

	function dbConn(){
		$host = $this->config['sms']['host'];
		$database = $this->config['sms']['database'];
		$username = $this->config['sms']['username'];
		$password = $this->config['sms']['password'];

		try {
			$dbh = new PDO("mysql:host=$host;dbname=$database", $username, $password);
			return $dbh;
		}
		catch(PDOException $e){
			return $e->getMessage();
		}
	}

	function sendSMS($data){
		$dbh = $this->dbConn();

		$sql = "INSERT INTO outbox(DestinationNumber, TextDecoded, CreatorID) VALUES('".$data['msisdn']."', '".urldecode($data['message'])."', 'riceKicker')";

		$res = $dbh->prepare($sql);
		$res->execute();
		return true;
	}
}

$data['msisdn'] = isset($_REQUEST['msisdn']) ? $_REQUEST['msisdn'] : '';
$data['message'] = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
$data['trx_id'] = isset($_REQUEST['trx_id']) ? $_REQUEST['trx_id'] : '';

$reply = array();

if($data['msisdn'] != '' && $data['msisdn'] != ''){

	$sms = new mySMS($config);
	$sms->sendSMS($data);
	$reply = array(
		'code' => '001',
		'status' => 'success',
		'message' => $data['message']
	);

}
else {
	$reply = array(
		'code' => '909', //parameter not set
		'status' => 'error',
		'message' => 'Invalid Parameter'
	);
}

print_r(json_encode($reply));

?>