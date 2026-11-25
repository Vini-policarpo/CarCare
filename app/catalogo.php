<?php

require_once __DIR__ . '/init.php';
protegerFuncionario();

$paginaAtual = 'catalogo';
$erro = '';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $valorTexto = str_replace(',', '.', $_POST['valor'] ?? '0');
    $valor = floatval($valorTexto);

    if ($nome === '' || $valor < 0) {
        $erro = 'Preencha o nome e informe um valor válido.';
    } elseif ($acao === 'cadastrar_servico') {
        $descricao = trim($_POST['descricao'] ?? '');

        $comando = $pdo->prepare(
            'INSERT INTO servicos (nome, descricao, valor) VALUES (?, ?, ?)'
        );
        $comando->execute([$nome, $descricao, $valor]);
        $mensagem = 'Serviço cadastrado com sucesso.';
    } elseif ($acao === 'cadastrar_peca') {
        $estoque = intval($_POST['estoque'] ?? 0);

        if ($estoque < 0) {
            $erro = 'O estoque não pode ser negativo.';
        } else {
            $comando = $pdo->prepare(
                'INSERT INTO pecas (nome, valor, estoque) VALUES (?, ?, ?)'
            );
            $comando->execute([$nome, $valor, $estoque]);
            $mensagem = 'Peça cadastrada com sucesso.';
        }
    }
}

$servicos = $pdo->query('SELECT * FROM servicos ORDER BY nome')->fetchAll();
$pecas = $pdo->query('SELECT * FROM pecas ORDER BY nome')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Serviços e peças | CarCare</title>
  <link rel="icon" href="imagens/carcare-favicon.png">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/parciais/nav.php'; ?>

    <main class="conteudo">
      <section class="pagina">
        <?php if ($mensagem !== ''): ?>
          <div class="alerta sucesso"><?php echo textoSeguro($mensagem); ?></div>
        <?php endif; ?>

        <?php if ($erro !== ''): ?>
          <div class="alerta erro"><?php echo textoSeguro($erro); ?></div>
        <?php endif; ?>

        <header class="cabecalho">
          <div>
            <h1>Serviços e peças</h1>
            <p>Cadastre os itens oferecidos pela oficina.</p>
          </div>
        </header>

        <div class="grade-catalogo">
          <div class="painel formulario-catalogo">
            <h2>Novo serviço</h2>
            <form action="catalogo.php" method="post">
              <input type="hidden" name="acao" value="cadastrar_servico">
              <label>Nome<input name="nome" placeholder="Troca de óleo" required></label>
              <label>Descrição<textarea name="descricao" rows="3"></textarea></label>
              <label>Valor<input name="valor" type="number" min="0" step="0.01" required></label>
              <button class="botao primario" type="submit">Cadastrar serviço</button>
            </form>
          </div>

          <div class="painel formulario-catalogo">
            <h2>Nova peça</h2>
            <form action="catalogo.php" method="post">
              <input type="hidden" name="acao" value="cadastrar_peca">
              <label>Nome<input name="nome" placeholder="Filtro de óleo" required></label>
              <div class="colunas">
                <label>Valor<input name="valor" type="number" min="0" step="0.01" required></label>
                <label>Estoque<input name="estoque" type="number" min="0" required></label>
              </div>
              <button class="botao primario" type="submit">Cadastrar peça</button>
            </form>
          </div>
        </div>

        <h2 class="titulo-lista">Serviços cadastrados</h2>
        <div class="tabela-container">
          <table>
            <thead><tr><th>Serviço</th><th>Descrição</th><th>Valor</th></tr></thead>
            <tbody>
              <?php foreach ($servicos as $servico): ?>
                <tr>
                  <td><strong><?php echo textoSeguro($servico['nome']); ?></strong></td>
                  <td><?php echo textoSeguro($servico['descricao']); ?></td>
                  <td>R$ <?php echo number_format($servico['valor'], 2, ',', '.'); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <h2 class="titulo-lista">Peças cadastradas</h2>
        <div class="tabela-container">
          <table>
            <thead><tr><th>Peça</th><th>Valor</th><th>Estoque</th></tr></thead>
            <tbody>
              <?php if (count($pecas) === 0): ?>
                <tr><td colspan="3">Nenhuma peça cadastrada.</td></tr>
              <?php else: ?>
                <?php foreach ($pecas as $peca): ?>
                  <tr>
                    <td><strong><?php echo textoSeguro($peca['nome']); ?></strong></td>
                    <td>R$ <?php echo number_format($peca['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo intval($peca['estoque']); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
