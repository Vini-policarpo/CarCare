<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'historico';

$sql = "SELECT manutencoes.*,
               veiculos.marca,
               veiculos.modelo,
               veiculos.placa,
               clientes.nome AS proprietario,
               funcionarios.nome AS funcionario
        FROM manutencoes
        INNER JOIN veiculos ON veiculos.id = manutencoes.veiculo_id
        INNER JOIN clientes ON clientes.id = veiculos.cliente_id
        LEFT JOIN funcionarios ON funcionarios.id = manutencoes.funcionario_id
        WHERE manutencoes.status = 'Concluido'";
$parametros = [];

if (usuarioEhCliente()) {
    $sql .= ' AND veiculos.cliente_id = ?';
    $parametros[] = $_SESSION['cliente_id'];
}

$sql .= ' ORDER BY manutencoes.data_conclusao DESC';
$comando = $pdo->prepare($sql);
$comando->execute($parametros);
$historico = $comando->fetchAll();

$maiorQuilometragem = 0;

foreach ($historico as $registro) {
    if ($registro['quilometragem'] > $maiorQuilometragem) {
        $maiorQuilometragem = $registro['quilometragem'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico | CarCare</title>
  <link rel="icon" href="imagens/carcare-favicon.png">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/historico.css">
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/parciais/nav.php'; ?>

    <main class="conteudo">
      <section class="pagina">
        <header class="cabecalho">
          <div>
            <h1>Histórico</h1>
            <p><?php echo usuarioEhFuncionario() ? 'Consulte todos os serviços concluídos.' : 'Consulte as revisões concluídas dos seus veículos.'; ?></p>
          </div>
        </header>

        <div class="indicadores resumo dois">
          <article><span>Revisões concluídas</span><strong><?php echo count($historico); ?></strong></article>
          <article><span>Maior quilometragem</span><strong><?php echo number_format($maiorQuilometragem, 0, ',', '.'); ?> km</strong></article>
        </div>

        <?php if (count($historico) === 0): ?>
          <div class="painel central">
            <div class="miniatura"><img src="imagens/icone-historico.png" alt=""></div>
            <h2>O histórico está vazio</h2>
            <p>As manutenções concluídas pelo funcionário aparecerão aqui.</p>
          </div>
        <?php else: ?>
          <div class="tabela-container">
            <table>
              <thead>
                <tr>
                  <th>Conclusão</th>
                  <th>Veículo</th>
                  <?php if (usuarioEhFuncionario()): ?><th>Cliente</th><?php endif; ?>
                  <th>Serviço</th>
                  <th>Funcionário</th>
                  <th>Quilometragem</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($historico as $registro): ?>
                  <tr>
                    <td><?php echo date('d/m/Y', strtotime($registro['data_conclusao'] ?: $registro['data_agendamento'])); ?></td>
                    <td>
                      <strong><?php echo textoSeguro(trim($registro['marca'] . ' ' . $registro['modelo'])); ?></strong>
                      <small><?php echo textoSeguro($registro['placa']); ?></small>
                    </td>
                    <?php if (usuarioEhFuncionario()): ?>
                      <td><?php echo textoSeguro($registro['proprietario']); ?></td>
                    <?php endif; ?>
                    <td><?php echo textoSeguro($registro['descricao']); ?></td>
                    <td><?php echo textoSeguro($registro['funcionario'] ?: 'Não informado'); ?></td>
                    <td><?php echo number_format($registro['quilometragem'], 0, ',', '.'); ?> km</td>
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
