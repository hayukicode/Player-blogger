<?php
$auth = 'https://accounts.google.com/o/oauth2/v2/auth?scope=https://www.googleapis.com/auth/blogger&access_type=offline&include_granted_scopes=true&redirect_uri=https://animeonline.site/blogger/akkeyr.php&client_id=990786067965-1mldmohqnv886u3tibjnsktjdlvqkfv7.apps.googleusercontent.com&response_type=code';
header('Location: '.$auth);
?>