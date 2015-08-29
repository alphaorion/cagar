<?php
/**
*	checksms merupakan fitur pengecekan sms masuk yang disalin dari database gammu
*	file di hook ke daemon gammu sehingga akan dieksekusi secara otomatis saat ada sms masuk
*	@Author Fikry Maulana (fikry.maulana@gmail.com)
*/

require_once('generalConfig.php');
require_once('outputConfig.php');
require_once('dataManager.php');
require_once('smsParser.php');


sleep(2);

$dm = new dataManager($config);
$dat = new smsParser($config);

if($dr = $dm->getSMS()){
	if(count($dr) > 0){
		foreach ($dr as $key => $val) {
			$data = array(
				'msisdn' => preg_replace("/\+62(.*)/", "0$1", $val['msisdn_from']),
				'message' => $val['message']
			);
			$url = $config['inbox']['url'].'?msisdn='.$data['msisdn'].'&text='.urlencode($data['message']).'&trx_id='.urlencode($val['id']);
			$ret = file_get_contents($url);
			$r = json_decode($ret);
			if($r->status == "001"){
				$rs = $dm->updateSMS(array('status' => 1, 'id' => $val['id']));
			}
			else {
				print_r(json_encode($r));
			}
		}

	}
}

?>