<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'inicio';

if (usuarioEhFuncionario()) {
    // O funcionário acompanha os status oficina :VV.
    $totalVeiculos = $pdo->query('SELECT COUNT(*) FROM veiculos')->fetchColumn();

    $resumo = $pdo->query(
        "SELECT
            SUM(status = 'Solicitado') AS solicitadas,
            SUM(status = 'Em andamento') AS andamento,
            SUM(
                status NOT IN ('Concluido', 'Cancelado')
                AND data_agendamento < CURDATE()
            ) AS atrasadas
         FROM manutencoes"
    )->fetch();
} else {
    // O cliente acompanha somente os dados do próprio perfil.
    $consultaVeiculos = $pdo->prepare(
        'SELECT COUNT(*) FROM veiculos WHERE cliente_id = ?'
    );
    $consultaVeiculos->execute([$_SESSION['cliente_id']]);
    $totalVeiculos = $consultaVeiculos->fetchColumn();

    $consultaResumo = $pdo->prepare(
        "SELECT
            SUM(manutencoes.status = 'Solicitado') AS solicitadas,
            SUM(manutencoes.status = 'Em andamento') AS andamento,
            SUM(
                manutencoes.status NOT IN ('Concluido', 'Cancelado')
                AND manutencoes.data_agendamento < CURDATE()
            ) AS atrasadas
         FROM manutencoes
         INNER JOIN veiculos ON veiculos.id = manutencoes.veiculo_id
         WHERE veiculos.cliente_id = ?"
    );
    $consultaResumo->execute([$_SESSION['cliente_id']]);
    $resumo = $consultaResumo->fetch();
}

$solicitadas = intval($resumo['solicitadas'] ?? 0);
$andamento = intval($resumo['andamento'] ?? 0);
$atrasadas = intval($resumo['atrasadas'] ?? 0);
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
            <h1><?php echo usuarioEhFuncionario() ? 'Painel do funcionário' : 'Bem-vindo ao CarCare'; ?></h1>
            <p>
              <?php echo usuarioEhFuncionario()
                  ? 'Acompanhe as solicitações e atualize o andamento dos serviços.'
                  : 'Cadastre seu veículo, solicite uma manutenção e acompanhe o status.'; ?>
            </p>
          </div>

          <?php if (usuarioEhCliente()): ?>
            <a class="botao primario" href="agendar-manutencao.php">Solicitar manutenção</a>
          <?php else: ?>
            <a class="botao primario" href="manutencoes.php">Ver solicitações</a>
          <?php endif; ?>
        </header>

        <div class="indicadores">
          <article class="indicador">
            <div><span>Veículos cadastrados</span><strong><?php echo $totalVeiculos; ?></strong><small><?php echo usuarioEhFuncionario() ? 'Total de clientes' : 'Na sua conta'; ?></small></div>
            <b><img src="imagens/icone-carro.png" alt=""></b>
          </article>
          <article class="indicador">
            <div><span>Solicitações pendentes</span><strong><?php echo $solicitadas; ?></strong><small>Aguardando funcionário</small></div>
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
            <h2><?php echo usuarioEhFuncionario() ? 'Atenda as solicitações dos clientes' : 'Acompanhe seus veículos'; ?></h2>
            <p>
              <?php echo usuarioEhFuncionario()
                  ? 'Somente funcionários podem mudar um serviço para concluído.'
                  : 'O status é definido automaticamente e atualizado pela oficina.'; ?>
            </p>
          </div>
          <a class="botao secundario" href="<?php echo usuarioEhFuncionario() ? 'manutencoes.php' : 'veiculos.php'; ?>">
            <?php echo usuarioEhFuncionario() ? 'Gerenciar serviços' : 'Ver veículos'; ?>
          </a>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
