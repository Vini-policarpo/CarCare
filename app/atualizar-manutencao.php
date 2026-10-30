<?php

require_once __DIR__ . '/init.php';
protegerPagina();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $statusPermitidos = ['Agendada', 'Em andamento', 'Concluída', 'Cancelada'];

    if (in_array($status, $statusPermitidos)) {
        $comando = $pdo->prepare('UPDATE manutencoes SET status = ? WHERE id = ?');
        $comando->execute([$status, $id]);
    }
}

header('Location: manutencoes.php?mensagem=atualizada');
exit;
