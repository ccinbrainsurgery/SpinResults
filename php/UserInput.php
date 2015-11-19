<?php

$endpoint = "http://localhost/SpinResults/php/SpinResults.php";

$data = array('playerID' => '213145', 'SaltValue' => '342324', 'CoinsBet' => '1000', 'CoinsWon' => '2000');
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,$endpoint);


$result = curl_exec($ch);

if($result === FALSE){
	die(curl_error($ch));
}

curl_close($ch);

echo "JSON Response : " . $result;

?>