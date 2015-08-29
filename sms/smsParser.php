<?php
/**
*	smsParser merupakan fitur yang berfungsi untuk memparsing sms yang masuk dan memastikan
*	sms yang datang menggunakan format yang telah ditetapkan.
*	@Author Fikry Maulana (fikry.maulana@gmail.com)
*/
require_once('dataManager.php');
require_once('apiConnector.php');


class smsParser {

	function __construct($config){
		$this->config = $config;
		$this->dat = new dataManager($config);
	}

	function smsProcess($msisdn, $sms){
		if(strlen($sms) > 0){
			$data = array();
			$msg = explode(' ', $sms);
			$command = $msg[0];
			$message = trim(str_replace($command, '', $sms));

			if($comm = $this->dat->getCommand($command)){
				if($comm->command_code == '001'){ //lapor
					list($data['jenisSentra'], $data['namaSentra'], $data['kodePos'], $data['namaKomoditas'], $data['jenisKomoditas'], $data['kuantitas'], $data['satuan'], $data['harga']) = explode('#', $message);
					$data['hp'] = $msisdn;
					$api = new apiConnector($this->config);
					$url = $api->sendData($data);
					$status = json_decode($url);
					if($status->status_code = '000'){
						$reply = array(
							'command' => '001',
							'msisdn' => $msisdn,
							'code' => '100', //sukses dan beri reply
							'status' => 'sukses'
						);
					}
					else {
						$reply = array(
							'command' => '001',
							'msisdn' => $msisdn,
							'code' => '901', //command not found
							'status' => 'error'
						);

					}
				}
				elseif($comm->command_code == '002'){ //cari
					list($data['jenisSentra'], $data['namaKomoditas'], $data['jenisKomoditas'], $data['kodePos']) = explode('#', $message);
					$data['hp'] = $msisdn;
					$api = new apiConnector($this->config);
					$url = $api->getData($data);
					$status = json_decode($url);
					if($status->status_code = '000'){
						if(isset($status->komoditas) && is_array($status->komoditas) && count($status->komoditas) > 0){
							$pesan = "Harga ".$status->nama_komoditas." ".$status->jenis_komoditas." per ".date('d/m/Y').":\r\n";
							$x = 1;
							foreach ($status->komoditas as $key => $val) {
								$pesan .= $x.". ".$val->nama_sentra." Rp. ".number_format($val->harga, 0, ",", ".")."\r\n";
								$x++;
							}
							$reply = array(
								'command' => '002',
								'msisdn' => $msisdn,
								'code' => '200', //sukses dan beri reply
								'status' => 'sukses',
								'replacement' => array(
									'{MESSAGE}' => $pesan,
								)
							);
						}
						else {
							$reply = array(
								'command' => '002',
								'msisdn' => $msisdn,
								'code' => '902', //data kosong
								'status' => 'sukses',
							);
						}
					}
					else {
						$reply = array(
							'command' => '002',
							'msisdn' => $msisdn,
							'code' => '902', //data tidak ditemukan
							'status' => 'error'
						);

					}

				}
				elseif($comm->command_code == '003'){ // point
					$api = new apiConnector($this->config);
					$data['hp'] = $msisdn;
					$url = $api->getPoint($data); 
					$status = json_decode($url);
					if($status->status_code = '000'){
						if(isset($status->point)){
							$reply = array(
								'command' => '003',
								'msisdn' => $msisdn,
								'code' => '300', //sukses dan beri reply
								'status' => 'sukses',
								'replacement' => array(
									'{POINT}' => $status->point,
								)
							);
						}
						else {
							$reply = array(
								'command' => '003',
								'msisdn' => $msisdn,
								'code' => '903', //data kosong
								'status' => 'sukses',
							);
						}
					}
					else {
						$reply = array(
							'command' => '003',
							'msisdn' => $msisdn,
							'code' => '902', //data tidak ditemukan
							'status' => 'error'
						);

					}
				}
				elseif($comm->command_code == '004'){ // help
						$reply = array(
							'command' => '004',
							'msisdn' => $msisdn,
							'code' => '400', //help
							'status' => 'sukses'
						);
				}
			}
			else {
				$reply = array(
					'command' => '000',
					'msisdn' => $msisdn,
					'code' => '900', //command not found
					'status' => 'error'
				);
			}
			//return $this->sendMessage($reply);
		}
		else {
			$reply = array(
				'command' => '000',
				'msisdn' => $msisdn,
				'code' => '909', //parameter not set
				'status' => 'error'
			);
		}
		return json_encode($reply);
	}

	function sendMessage($reply){
		$replacement = isset($reply['replacement']) ? $reply['replacement'] : array();
		$message = $this->dat->processMessage($reply['code'], $replacement);

		$url = $this->config['sms']['url'].'?msisdn='.$reply['msisdn'].'&message='.urlencode($message).'&trx_id='.urlencode($reply['trx_id']);

		$ret = file_get_contents($url);

		return $ret;
	}

}

 ?>
