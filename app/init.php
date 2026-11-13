<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/banco/database.php';
require_once __DIR__ . '/banco/crud.php';

// Impede o acesso de quem não fez loginn
function protegerPagina()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: apresentacao.php');
        exit;
    }
}

function usuarioEhFuncionario()
{
    return ($_SESSION['usuario_tipo'] ?? '') === 'funcionario';
}

function usuarioEhCliente()
{
    return ($_SESSION['usuario_tipo'] ?? '') === 'cliente';
}

// aqui seria a proteção de infos do func
function protegerFuncionario()
{
    protegerPagina();

    if (!usuarioEhFuncionario()) {
        http_response_code(403);
        exit('Acesso permitido somente para funcionários.');
    }
}

// e esse do cliente
function protegerCliente()
{
    protegerPagina();

    if (!usuarioEhCliente()) {
        http_response_code(403);
        exit('Acesso permitido somente para clientes.');
    }
}

// aqui o func consegue mudar o status
function nomeStatus($status)
{
    $nomes = [
        'Solicitado' => 'Solicitado',
        'Agendado' => 'Agendado',
        'Em andamento' => 'Em andamento',
        'Concluido' => 'Concluído',
        'Cancelado' => 'Cancelado'
    ];

    return $nomes[$status] ?? $status;
}

function classeStatus($status)
{
    $classes = [
        'Solicitado' => 'solicitado',
        'Agendado' => 'agendado',
        'Em andamento' => 'em_andamento',
        'Concluido' => 'concluido',
        'Cancelado' => 'cancelado'
    ];

    return $classes[$status] ?? '';
}

function textoSeguro($texto)
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}
