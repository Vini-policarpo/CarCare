<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'veiculos';
$busca = trim($_GET['busca'] ?? '');
$mensagem = '';

// Mostra uma mensagem de acordo com a ação realizada.
if (($_GET['mensagem'] ?? '') === 'cadastrado') {
    $mensagem = 'Veículo cadastrado com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'editado') {
    $mensagem = 'Veículo atualizado com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'excluido') {
    $mensagem = 'Veículo excluído com sucesso.';
}

if ($busca !== '') {
    $comando = $pdo->prepare(
        'SELECT * FROM veiculos
         WHERE nome_modelo LIKE ? OR placa LIKE ? OR proprietario LIKE ?
         ORDER BY id DESC'
    );
    $termo = '%' . $busca . '%';
    $comando->execute([$termo, $termo, $termo]);
    $veiculos = $comando->fetchAll();
} else {
    $veiculos = $pdo->query('SELECT * FROM veiculos ORDER BY id DESC')->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veículos | CarCare</title>
  <link rel="stylesheet" href="css/style.css">
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
            <h1>Proprietários e veículos</h1>
            <p>Cadastre, consulte e acompanhe todos os veículos.</p>
          </div>
          <a class="botao primario" href="cadastro-veiculo.php">Cadastrar veículo</a>
        </header>

        <form class="filtros" action="veiculos.php" method="get">
          <label class="busca">
            <img src="imagens/icone-busca.png" alt="">
            <input name="busca" type="search" value="<?php echo textoSeguro($busca); ?>" placeholder="Buscar por proprietário, placa ou veículo">
          </label>
          <span><?php echo count($veiculos); ?> veículo(s)</span>
        </form>

        <?php if (count($veiculos) === 0): ?>
          <div class="vazio">
            <img class="ilustracao" src="imagens/estado-sem-veiculo.png" alt="Ilustração de carro">
            <h2>Nenhum veículo encontrado</h2>
            <p>Cadastre um veículo para começar.</p>
          </div>
        <?php else: ?>
          <div class="tabela-container">
            <table>
              <thead>
                <tr>
                  <th>Veículo</th>
                  <th>Placa</th>
                  <th>Proprietário</th>
                  <th>Quilometragem</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($veiculos as $veiculo): ?>
                  <tr>
                    <td>
                      <strong><?php echo textoSeguro($veiculo['nome_modelo']); ?></strong>
                      <small>Ano <?php echo $veiculo['ano']; ?></small>
                    </td>
                    <td><span class="placa"><?php echo textoSeguro($veiculo['placa']); ?></span></td>
                    <td><?php echo textoSeguro($veiculo['proprietario']); ?></td>
                    <td><?php echo number_format($veiculo['quilometragem'], 0, ',', '.'); ?> km</td>
                    <td>
                      <div class="acoes-tabela">
                        <a href="editar-veiculo.php?id=<?php echo $veiculo['id']; ?>">Editar</a>

                        <form action="excluir-veiculo.php" method="post">
                          <input type="hidden" name="id" value="<?php echo $veiculo['id']; ?>">
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
