<?php

require_once __DIR__ . '/init.php';
protegerPagina();

// A exclusão só acontece quando o formulário usa o método POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    $comando = $pdo->prepare('DELETE FROM veiculos WHERE id = ?');
    $comando->execute([$id]);
}

header('Location: veiculos.php?mensagem=excluido');
exit;
