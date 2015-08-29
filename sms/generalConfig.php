<?php
/**
*	File konfigurasi utama untuk sms gateway
*	@Author Fikry Maulana (fikry.maulana@gmail.com)
*/

// no yang digunakan pada sms gateway
$config['gw'] = "gw1";
$config['gw1']['msisdn'] = "081317022354";

// konfigurasi koneksi database
$config['db']['host'] = 'localhost';
$config['db']['username'] = 'gammu';
$config['db']['password'] = 'hackathon';
$config['db']['database'] = 'alphasms';

// konfigurasi url external
$config['api']['url'] = "http://devbox01.com/komoditi/";
$config['sms']['url'] = "http://localhost/sms/outbox.php";

?>
