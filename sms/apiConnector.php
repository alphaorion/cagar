<?php
/**
*	apiConnector merupakan konektor untuk mengirim dan menerima data dari API aplikasi utama.
*	@Author Fikry Maulana (fikry.maulana@gmail.com)
*/

Class apiConnector {
	function __construct($config){
		$this->config = $config;	
	}

	function getURL($url, $param='') {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
		if(is_array($param)) {
			curl_setopt($ch,CURLOPT_POST, TRUE);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
		}
		$content = curl_exec($ch);
		if ($content==false) {
			return false;
		}
		else {
			return $content;
		}
	}

	function sendData($data){
		$res = apiConnector::execAPI($data, 'submit.php');
		//error_log($res);
		return $res;
	}

	function getData($data){
		$res = apiConnector::execAPI($data, 'search.php');
		//error_log($res);
		return $res;
	}

	function getPoint($data){
		$res = apiConnector::execAPI($data, 'points.php');
		//error_log($res);
		return $res;
	}

	function execAPI($data, $nodes){
		if(is_array($data) && count($data) > 0){
			foreach ($data as $key => $val) {
				$par[] = $key.'='.urlencode($val);
			}
			$param = implode('&', $par);

			$sendURL = $this->config['api']['url'].$nodes.'?'.$param;
			error_log($sendURL);

			if($u = $this->getURL($sendURL)){
				return $u;
			}
			else {
				return false;
			}
		}	
		else {
			return false;
		}
	}
}

?>
