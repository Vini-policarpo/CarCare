<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'inicio';

// Cada consulta conta uma informação para o painel.
$totalVeiculos = $pdo->query('SELECT COUNT(*) FROM veiculos')->fetchColumn();
$agendadas = $pdo->query("SELECT COUNT(*) FROM manutencoes WHERE status = 'Agendada'")->fetchColumn();
$andamento = $pdo->query("SELECT COUNT(*) FROM manutencoes WHERE status = 'Em andamento'")->fetchColumn();
$atrasadas = $pdo->query("SELECT COUNT(*) FROM manutencoes WHERE status = 'Agendada' AND data_agendada < CURDATE()")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visão geral | CarCare</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/parciais/nav.php'; ?>

    <main class="conteudo">
      <section class="pagina">
        <header class="cabecalho">
          <div>
            <h1>Bem-vindo ao CarCare</h1>
            <p>Cadastre, consulte e acompanhe todos os veículos.</p>
          </div>
          <a class="botao primario" href="cadastro-veiculo.php">Cadastrar veículo</a>
        </header>

        <div class="indicadores">
          <article class="indicador">
            <div><span>Veículos cadastrados</span><strong><?php echo $totalVeiculos; ?></strong><small>Total no sistema</small></div>
            <b><img src="imagens/icone-carro.png" alt=""></b>
          </article>
          <article class="indicador">
            <div><span>Manutenções agendadas</span><strong><?php echo $agendadas; ?></strong><small>Próximos serviços</small></div>
            <b><img src="imagens/icone-calendario.png" alt=""></b>
          </article>
          <article class="indicador">
            <div><span>Serviços em andamento</span><strong><?php echo $andamento; ?></strong><small>Sendo realizados</small></div>
            <b><img src="imagens/icone-manutencao.png" alt=""></b>
          </article>
          <article class="indicador">
            <div><span>Manutenções atrasadas</span><strong><?php echo $atrasadas; ?></strong><small>Precisam de atenção</small></div>
            <b><img src="imagens/icone-lembrete.png" alt=""></b>
          </article>
        </div>

        <div class="painel chamada">
          <div>
            <h2>Consulte os veículos cadastrados</h2>
          </div>
          <a class="botao secundario" href="veiculos.php">Ver veículos</a>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
