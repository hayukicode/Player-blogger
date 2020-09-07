<?php

$code = $_GET['code'];
$refresh .= '';

function http_post ($url, $data)
{
    $data_url = http_build_query ($data);
    $data_len = strlen ($data_url);

    return array ('content'=>@file_get_contents ($url, false, stream_context_create (array ('http'=>array ('method'=>'POST'
            , 'header'=>"HTTP/1.1\r\nHost: www.googleapis.com\r\ncontent-type: application/x-www-form-urlencoded\r\nContent-Length: $data_len\r\n"
            , 'content'=>$data_url
            ))))
        , 'headers'=>$http_response_header
        );
}


$data_array['client_id'] = '990786067965-1mldmohqnv886u3tibjnsktjdlvqkfv7.apps.googleusercontent.com';
$data_array['client_secret'] = '4zvYjpDQK8nWmzSbGdr3OKeC';

if(empty($refresh)){
$data_array['redirect_uri'] = 'https://anihallanimes.site/player/keyr.php';
$data_array['grant_type'] = 'authorization_code';
$data_array['access_type'] = 'offline';
$data_array['code'] = $code;
}else{
$data_array['grant_type'] = 'refresh_token';
$data_array['access_type'] = 'offline';
$data_array['refresh_token'] = $refresh;
}

$content = @json_decode(http_post('https://www.googleapis.com/oauth2/v4/token', $data_array)['content'], true);
//@var_dump($content);


$acess_token = $content['access_token'];
//echo "<BR><BR>atObtido: $acess_token<BR><BR>";
$refresh_token = $content['refresh_token'];

if(!empty($acess_token)){
	
	include(__DIR__ . '/tokenwriter.php');
	if(empty($refresh)){
		header('Location: https://anihallanimes.site/player/');
	}
	
}else{
	
	if(empty($refresh)){
		header('Location: https://anihallanimes.site/player/authg.php');
	}
	
}

//grant_type=refresh_token
//&refresh_token=1%2Fl_wM2nw9kS6HUqVTU5OsKL60QXhblGqiBZyM-eHx6fA

?>