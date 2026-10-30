<?php

require_once __DIR__ . '/init.php';
protegerPagina();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    $comando = $pdo->prepare('DELETE FROM manutencoes WHERE id = ?');
    $comando->execute([$id]);
}

header('Location: manutencoes.php?mensagem=excluida');
exit;
