<?php

$code = $_GET['code'];
$refresh .= '';

function http_post ($url, $data)
{
    $data_url = http_build_query ($data);
	$data_len = strlen ($data_url);
	$header_url = "HTTP/1.1\r\nHost: www.googleapis.com\r\ncontent-type: application/x-www-form-urlencoded\r\nContent-Length: $data_len\r\n";

		
			$ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header_url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $resp = curl_exec($ch);
			return $resp;
				
}


$data_array['client_id'] = '990786067965-1mldmohqnv886u3tibjnsktjdlvqkfv7.apps.googleusercontent.com';
$data_array['client_secret'] = '4zvYjpDQK8nWmzSbGdr3OKeC';

if(empty($refresh)){
$data_array['redirect_uri'] = 'https://animeonline.site/blogger/akkeyr.php';
$data_array['grant_type'] = 'authorization_code';
$data_array['access_type'] = 'offline';
$data_array['code'] = $code;
}else{
$data_array['grant_type'] = 'refresh_token';
$data_array['access_type'] = 'offline';
$data_array['refresh_token'] = $refresh;
}

$content = @json_decode(http_post('https://www.googleapis.com/oauth2/v4/token', $data_array), true);
//@var_dump($content);


$acess_token = $content['access_token'];
//echo "<BR><BR>atObtido: $acess_token<BR><BR>";
$refresh_token = $content['refresh_token'];

if(!empty($acess_token)){
	
	include(__DIR__ . '/tokenwriter.php');
	if(empty($refresh)){
		header('Location: https://animeonline.site');//ESSA LINHA PODE SER COMENTADA, SERVE APENAS PRA INDICAR LOGIN SUCEDIDO AO DIRECIONAR PARA INDEX
	}
	
}else{
	
	if(empty($refresh)){
		header('Location: https://animeonline.site/blogger/akauthg.php');//AO NAO CONSEGUIR GERAR TOKEN
	}
	
}


?>