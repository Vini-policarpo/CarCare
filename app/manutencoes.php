<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'manutencoes';
$mensagem = '';

if (($_GET['mensagem'] ?? '') === 'cadastrada') {
    $mensagem = 'Manutenção agendada com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'atualizada') {
    $mensagem = 'Status atualizado com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'excluida') {
    $mensagem = 'Manutenção excluída com sucesso.';
}

// Junta a manutenção com os dados do veículo.
$comando = $pdo->query(
    'SELECT manutencoes.*, veiculos.nome_modelo, veiculos.placa
     FROM manutencoes
     INNER JOIN veiculos ON veiculos.id = manutencoes.veiculo_id
     ORDER BY manutencoes.data_agendada'
);
$manutencoes = $comando->fetchAll();

$agendadas = 0;
$andamento = 0;
$concluidas = 0;
$atrasadas = 0;

// Conta os status usando um laço simples.
foreach ($manutencoes as $manutencao) {
    if ($manutencao['status'] === 'Agendada') {
        $agendadas++;
        if ($manutencao['data_agendada'] < date('Y-m-d')) {
            $atrasadas++;
        }
    } elseif ($manutencao['status'] === 'Em andamento') {
        $andamento++;
    } elseif ($manutencao['status'] === 'Concluída') {
        $concluidas++;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manutenções | CarCare</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/manutencoes.css">
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/parciais/nav.php'; ?>

    <main class="conteudo">
      <section class="pagina">
        <?php if ($mensagem !== ''): ?>
          <div class="alerta sucesso"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <header class="cabecalho">
          <div>
            <h1>Manutenções e agenda</h1>
            <p>Organize serviços, prazos e o andamento.</p>
          </div>
          <a class="botao primario" href="agendar-manutencao.php">Agendar manutenção</a>
        </header>

        <div class="indicadores resumo">
          <article><span>Agendadas</span><strong><?php echo $agendadas; ?></strong></article>
          <article><span>Em andamento</span><strong><?php echo $andamento; ?></strong></article>
          <article><span>Concluídas</span><strong><?php echo $concluidas; ?></strong></article>
          <article><span>Atrasadas</span><strong><?php echo $atrasadas; ?></strong></article>
        </div>

        <?php if (count($manutencoes) === 0): ?>
          <div class="painel central">
            <div class="miniatura"><img src="imagens/icone-manutencao.png" alt=""></div>
            <h2>Nenhuma manutenção registrada</h2>
            <p>Use o botão acima para criar o primeiro agendamento.</p>
          </div>
        <?php else: ?>
          <div class="tabela-container">
            <table>
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Veículo</th>
                  <th>Serviço</th>
                  <th>Status</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($manutencoes as $manutencao): ?>
                  <tr>
                    <td><?php echo date('d/m/Y', strtotime($manutencao['data_agendada'])); ?></td>
                    <td>
                      <strong><?php echo textoSeguro($manutencao['nome_modelo']); ?></strong>
                      <small><?php echo textoSeguro($manutencao['placa']); ?></small>
                    </td>
                    <td>
                      <strong><?php echo textoSeguro($manutencao['servico']); ?></strong>
                    </td>
                    <td><span class="status"><?php echo textoSeguro($manutencao['status']); ?></span></td>
                    <td>
                      <div class="acoes-tabela">
                        <form action="atualizar-manutencao.php" method="post">
                          <input type="hidden" name="id" value="<?php echo $manutencao['id']; ?>">
                          <select name="status">
                            <option <?php echo $manutencao['status'] === 'Agendada' ? 'selected' : ''; ?>>Agendada</option>
                            <option <?php echo $manutencao['status'] === 'Em andamento' ? 'selected' : ''; ?>>Em andamento</option>
                            <option <?php echo $manutencao['status'] === 'Concluída' ? 'selected' : ''; ?>>Concluída</option>
                            <option <?php echo $manutencao['status'] === 'Cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                          </select>
                          <button type="submit">Salvar</button>
                        </form>

                        <form action="excluir-manutencao.php" method="post">
                          <input type="hidden" name="id" value="<?php echo $manutencao['id']; ?>">
                          <button type="submit">Excluir</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
