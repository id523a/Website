<?php
$redir = empty($_SERVER['SERVER_HTTPS']) ? 'http://' : 'https://';
$redir .= $_SERVER['SERVER_NAME'];
$redir .= dirname(dirname($_SERVER['PHP_SELF']));
$redir .= '?p=index';
header("Location: $redir");
exit();
?>