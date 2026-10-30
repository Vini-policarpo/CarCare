<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'veiculos';
$erro = '';
$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

// Busca o veículo que será editado.
$consulta = $pdo->prepare('SELECT * FROM veiculos WHERE id = ?');
$consulta->execute([$id]);
$veiculo = $consulta->fetch();

if (!$veiculo) {
    exit('Veículo não encontrado.');
}

$nomeModelo = $veiculo['nome_modelo'];
$placa = $veiculo['placa'];
$ano = $veiculo['ano'];
$proprietario = $veiculo['proprietario'];
$quilometragem = $veiculo['quilometragem'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomeModelo = trim($_POST['nome_modelo'] ?? '');
    $placa = strtoupper(trim($_POST['placa'] ?? ''));
    $placa = str_replace(['-', ' '], '', $placa);
    $ano = intval($_POST['ano'] ?? 0);
    $proprietario = trim($_POST['proprietario'] ?? '');
    $quilometragem = intval($_POST['quilometragem'] ?? 0);

    if ($nomeModelo === '' || $placa === '' || $proprietario === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($placa) !== 7) {
        $erro = 'A placa precisa ter 7 caracteres.';
    } else {
        // Procura a mesma placa em outro veículo.
        $placaRepetida = $pdo->prepare('SELECT id FROM veiculos WHERE placa = ? AND id != ?');
        $placaRepetida->execute([$placa, $id]);

        if ($placaRepetida->fetch()) {
            $erro = 'Esta placa pertence a outro veículo.';
        } else {
            $comando = $pdo->prepare(
                'UPDATE veiculos
                 SET nome_modelo = ?, placa = ?, ano = ?, proprietario = ?, quilometragem = ?
                 WHERE id = ?'
            );
            $comando->execute([$nomeModelo, $placa, $ano, $proprietario, $quilometragem, $id]);

            header('Location: veiculos.php?mensagem=editado');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar veículo | CarCare</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/cadastro-veiculo.css">
</head>
<body>
  <div class="app" inert>
    <?php include __DIR__ . '/parciais/nav.php'; ?>
  </div>

  <div class="sobreposicao">
    <div class="caixa cadastro-veiculo">
      <div class="formulario_titulo">
        <div><h2>Editar veículo</h2><p>Altere os dados necessários.</p></div>
        <a class="fechar" href="veiculos.php">×</a>
      </div>

      <?php if ($erro !== ''): ?>
        <div class="alerta erro"><?php echo textoSeguro($erro); ?></div>
      <?php endif; ?>

      <form action="editar-veiculo.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <label>Nome ou modelo<input name="nome_modelo" value="<?php echo textoSeguro($nomeModelo); ?>" required></label>

        <div class="colunas">
          <label>Placa<input name="placa" value="<?php echo textoSeguro($placa); ?>" required></label>
          <label>Ano<input name="ano" type="number" value="<?php echo $ano; ?>" required></label>
        </div>

        <label>Proprietário<input name="proprietario" value="<?php echo textoSeguro($proprietario); ?>" required></label>
        <label>Quilometragem<input name="quilometragem" type="number" min="0" value="<?php echo $quilometragem; ?>" required></label>

        <div class="acoes">
          <a class="botao secundario" href="veiculos.php">Cancelar</a>
          <button class="botao primario" type="submit">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
