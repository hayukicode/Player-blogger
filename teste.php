<?php

include('bd.php');

id_insert($tabela_controle, 'IDTESTE', 'TOKENTESTE123', 'https://urldeteste.com/123', $conn);

$q = id_select($tabela_controle, 'IDTESTE', $conn);

var_dump($q);
?>