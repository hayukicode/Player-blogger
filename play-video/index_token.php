<?php
 error_reporting(E_ALL);
 ini_set('display_errors', 1);

function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}



$blogid = '2319334561272368699';
include '../atoken.php';

$refresh = '';

include'../bd.php';
include'../anti_injection.php';

$contentId = anti_injection($_GET['id']);
$token = '';


function postar_blog ($id, $data, $token)
{
    //$data_len = strlen ($data);

	try{
            $head = array();
            $head[] = 'Content-Type: application/json';
            $head[] = 'Authorization: Bearer '.$token;
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/blogger/v3/blogs/'.$id.'/posts/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $resp = curl_exec($ch);
			return $resp;
			
}catch (Exception $e){
	return $e;
}
}

function mapear($newcontentid, $idblog, $key){
	$blogpost['kind'] = 'blogger#post';
	$blogpost['blog']['id'] = $idblog;
	$blogpost['title'] = 'EP ('.$newcontentid.')';
	$blogpost['content'] = '
<div class="separator" style="clear: both; display: none; text-align: center;">
<object class="BLOG_video_class" contentid="'.$newcontentid.'" height="266" id="BLOG_video-'.$newcontentid.'" width="320"></object>
<br />
'.$newcontentid.'<br />
<br />
</div>
	';

$blogpost = json_encode($blogpost);

//echo $blogpost;//

return postar_blog($idblog, $blogpost, $key);

}

function extrair_link_direto_expiravel($url){
	$html = file_get_contents_curl($url);
	 echo $html;
	$type = $_REQUEST['type'];
	$x = explode('"play_url":"', $html)[$type];
	$encoded_link = explode('"', $x)[0];
	$link_final = str_replace('\u003d', '=', $encoded_link);
	$link_final = str_replace('\u0026', '&', $link_final);
	return $link_final;
}

function sucesso_post_map($cid, $post_retorno, $tabela, $conn){
	//echo "Postado com sucesso<BR>";
	$array_sucesso = json_decode($post_retorno, true);
	$url_post = $array_sucesso['url'];
	//echo $url_post;
	$url_post = str_replace("http", "https", $url_post);
	$html = file_get_contents_curl($url_post);
	//echo $html;
	$x = explode('/video.g?token=', $html)[1];
	$video_token = explode('"', $x)[0];
	
	//echo $video_token.'<BR>';
	
	if(!empty($video_token)){
	//@mysql_query("INSERT INTO $tabela (contentid, token, url_post) VALUES ('$cid', '$video_token', '$url_post')");
	//mysqli_query($conn, "INSERT INTO $tabela (contentid, token, url_post) VALUES ('$cid', '$video_token', '$url_post')");
	
	id_insert($tabela, $cid, $video_token, $url_post, $conn);
	
	$ir = extrair_link_direto_expiravel('https://www.blogger.com/video.g?token='.$video_token);
	//echo $ir;
	header('Location: '.$ir);
	}else{
		echo 'ERR2';
	}
	
	
	
}
//$q1 = mysql_query("SELECT token FROM contentid_token WHERE contentid = '$contentId'");
//$q1 = mysqli_query($conn, "SELECT token FROM contentid_token WHERE contentid = '$contentId'");
$q1 = id_select($tabela_controle, $contentId, $conn);
$token = $q1['token'];
if(!empty($token)){
	//echo 'ja tem<BR>';
	//echo "video ja esta mapeado no bd";
	$ir = extrair_link_direto_expiravel('https://www.blogger.com/video.g?token='.$token);
	header('Location: '.$ir);
}else{
	//echo 'nao tem<BR>';
	//echo "video ainda não esta mapeado no bd / mapear";
	$post_resp = mapear($contentId, $blogid, $acess_token);
	
	if(strpos($post_resp, "We're sorry, but you don't have permission to access this resource.")!==false || strpos($post_resp, 'Invalid Credentials')!==false){
		
		//echo "Sem permissao ou token invalido 1a<BR>";
		
		if(empty($refresh)){
			$refresh = $refresh_token;
			include '../akkeyr.php';
			//echo '<BR><BR>';
			$post_resp = mapear($contentId, $blogid, $acess_token);
			if(strpos($post_resp, 'published')!==false){
			//sucesso
			sucesso_post_map($contentId, $post_resp, $tabela_controle, $conn);
			}else{
				echo 'ERR1';
			}
			
		}
		
		
	}else{
		//sucesso
		if(strpos($post_resp, 'published')!==false){
			sucesso_post_map($contentId, $post_resp, $tabela_controle, $conn);
		}
		
	}
	
	//echo 'teste2';
	echo $post_resp;
	
}
mysqli_close($conn);

//ERR1: Erro ao tentar fazer a postagem do video para mapeamento, na geralmente é causado por falta de permissão, seja token errado ou expirado e sem condicoes de refresh.
//Fazer o login manualmente (com a conta do blog) em /blogger/authg.php provavelmente vai corrigir

//ERR2: O conteudo foi postado e a resposta não indicou um erro conhecido, mas não foi possivel obter o token do video

?>
