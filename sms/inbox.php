<?php
/**
*	inbox merupakan pintu masuk dari sms modem ke sistem sms gateway
*	@Author Fikry Maulana (fikry.maulana@gmail.com)
*/

require_once('generalConfig.php');
require_once('smsParser.php');
require_once('dataManager.php');


$msisdn = isset($_REQUEST['msisdn']) ? $_REQUEST['msisdn'] : '';
$trx_id = isset($_REQUEST['trx_id']) ? $_REQUEST['trx_id'] : substr(md5(time()), 5, 16);
$message = isset($_REQUEST['text']) ? $_REQUEST['text'] : '';

if($msisdn != '' && $message != ''){
	// inisialisasi sms log
	$start = date('Y-m-d H:i:s');
	$sms_log = array(
		'trx_id' => $trx_id,
		'receive_datetime' => $start,
		'sender' => $msisdn,
		'receiver' => $config[$config['gw']]['msisdn'],
		'message' => $message,
		'reply_message' => '',
		'reply_datetime' => '',
		'command_code' => '',
		'reply_code' => '',
		'status' => 1
	);
	// cek command dan ambil reply
	$sm = new smsParser($config);
	$res = $sm->smsProcess($sms_log['sender'], $sms_log['message']);
	$r = json_decode($res);
	$sms_log['command_code'] = $r->command;
	$sms_log['reply_code'] = $r->code;

	//generate & send reply
	$sdm = json_decode($res, TRUE);
	$sdm['trx_id'] = $trx_id;
	$rep = $sm->sendMessage($sdm);
	$s = json_decode($rep);
	if($s->code == "001"){
		$end = date('Y-m-d H:i:s');
		$sms_log['reply_message'] = $s->message;
		$sms_log['reply_datetime'] = $end;
	}

	//set log
	$dm = new dataManager($config);
	$dm->setLog($sms_log);
}

print_r(json_encode(array('status' => "001")));

?>