<?php
//$conn = mysqli_connect("blogaliance.mysql.dbaas.com.br", "blogaliance", "blogv321", "blogaliance") or die ('Nao conectado<BR>');

try{
$param = 'mysql:host=localhost:3306;dbname=anihall;charset=utf8';
$user = 'hayuki';
$pass = '123453';

$conn = new PDO($param, $user, $pass);
}catch(PDOException $ex){
	echo $ex->getMessage();
}

$tabela_controle = 'contentid_token';//Nome da tabela para salvar as infos

function id_insert($tabela, $contentid, $token, $url_post, $conn){
	
	$sql = "INSERT INTO $tabela (contentid, token, url_post) VALUES ('$contentid','$token', '$url_post')";
	$conn->exec($sql);
	
}

function id_select($tabela, $contentid, $conn){
	
	
	
	$select = $conn->query("SELECT token FROM ".$tabela." WHERE contentid = '".$contentid."' LIMIT 1");
	$result = $select->fetch(PDO::FETCH_ASSOC);
	return($result);
	
}


?>