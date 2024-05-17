<?php
session_start();
session_destroy();

$redirect_uri   = 'http://' . $_SERVER['HTTP_HOST'];
header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
?>