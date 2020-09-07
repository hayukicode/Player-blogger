<?php



//echo 'writer incluido';
$new_acess_token = $acess_token;
$new_refresh_token = $refresh_token;

include(__DIR__ . '/atoken.php');

$back_acess_token = $acess_token;
$back_refresh_token = $refresh_token; 

$acess_token = $new_acess_token;
$refresh_token = $new_refresh_token;

if(empty($acess_token)){
	$acess_token = $back_acess_token;
	//echo 'Novo token vazio, usando backup<BR><BR>';
	
}
if(empty($refresh_token)){
	$refresh_token = $back_refresh_token;
	//echo 'Novo token refresher vazio, usando backup<BR><BR>';
}

//echo "<BR><BR>nToken: $acess_token<BR>nRefresh: $refresh_token<BR><BR>";
//echo 'apagar: '. __DIR__ . '/atoken.php<BR><BR>';
@unlink(__DIR__ ."/atoken.php");

$arquivo = fopen(__DIR__ . '/atoken.php', 'w');
fwrite($arquivo, '<?php $acess_token="'.$acess_token.'";
$refresh_token="'.$refresh_token.'"; ?>');
fclose($arquivo);



?>