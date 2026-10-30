<?php

// Inicia a sessão para guardar os dados de quem fez login.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Carrega a conexão com o MySQL.
require_once __DIR__ . '/banco/database.php';
require_once __DIR__ . '/banco/crud.php';

// Redireciona para o login quando o usuário não está conectado.
function protegerPagina()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Evita que códigos HTML sejam executados ao mostrar dados do banco.
function textoSeguro($texto)
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}
