<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'historico';

$comando = $pdo->query(
    "SELECT manutencoes.*, veiculos.nome_modelo, veiculos.placa
     FROM manutencoes
     INNER JOIN veiculos ON veiculos.id = manutencoes.veiculo_id
     WHERE manutencoes.status = 'Concluída'
     ORDER BY manutencoes.data_agendada DESC"
);
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
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/historico.css">
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/parciais/nav.php'; ?>

    <main class="conteudo">
      <section class="pagina">
        <header class="cabecalho">
          <div><h1>Histórico</h1><p>Consulte as manutenções concluídas.</p></div>
        </header>

        <div class="indicadores resumo dois">
          <article><span>Revisões concluídas</span><strong><?php echo count($historico); ?></strong></article>
          <article><span>Maior quilometragem</span><strong><?php echo number_format($maiorQuilometragem, 0, ',', '.'); ?> km</strong></article>
        </div>

        <?php if (count($historico) === 0): ?>
          <div class="painel central">
            <div class="miniatura"><img src="imagens/icone-historico.png" alt=""></div>
            <h2>Seu histórico está vazio</h2>
            <p>As manutenções concluídas aparecerão aqui.</p>
          </div>
        <?php else: ?>
          <div class="tabela-container">
            <table>
              <thead><tr><th>Data</th><th>Veículo</th><th>Serviço</th><th>Quilometragem</th></tr></thead>
              <tbody>
                <?php foreach ($historico as $registro): ?>
                  <tr>
                    <td><?php echo date('d/m/Y', strtotime($registro['data_agendada'])); ?></td>
                    <td><strong><?php echo textoSeguro($registro['nome_modelo']); ?></strong><small><?php echo textoSeguro($registro['placa']); ?></small></td>
                    <td><?php echo textoSeguro($registro['servico']); ?></td>
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
