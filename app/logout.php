<?php

require_once __DIR__ . '/init.php';

// Limpa e encerra a sessão.
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
