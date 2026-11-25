<?php

require_once __DIR__ . '/init.php';
protegerCliente();

$paginaAtual = 'veiculos';
$erro = '';
$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

// O cliente só consegue abrir um veículo dele
$consulta = $pdo->prepare(
    'SELECT * FROM veiculos WHERE id = ? AND cliente_id = ?'
);
$consulta->execute([$id, $_SESSION['cliente_id']]);
$veiculo = $consulta->fetch();

if (!$veiculo) {
    http_response_code(404);
    exit('Veículo não encontrado.');
}

$marca = $veiculo['marca'];
$modelo = $veiculo['modelo'];
$placa = $veiculo['placa'];
$ano = $veiculo['ano'];
$quilometragem = $veiculo['quilometragem'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $placa = strtoupper(trim($_POST['placa'] ?? ''));
    $placa = str_replace(['-', ' '], '', $placa);
    $ano = intval($_POST['ano'] ?? 0);
    $quilometragem = intval($_POST['quilometragem'] ?? 0);

    if ($marca === '' || $modelo === '' || $placa === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($placa) !== 7) {
        $erro = 'A placa precisa ter 7 caracteres.';
    } elseif ($ano < 1900 || $ano > date('Y') + 1) {
        $erro = 'Digite um ano válido.';
    } elseif ($quilometragem < 0) {
        $erro = 'A quilometragem não pode ser negativa.';
    } else {
        $placaRepetida = $pdo->prepare(
            'SELECT id FROM veiculos WHERE placa = ? AND id != ?'
        );
        $placaRepetida->execute([$placa, $id]);

        if ($placaRepetida->fetch()) {
            $erro = 'Esta placa pertence a outro veículo.';
        } else {
            $comando = $pdo->prepare(
                'UPDATE veiculos
                 SET marca = ?, modelo = ?, placa = ?, ano = ?, quilometragem = ?
                 WHERE id = ? AND cliente_id = ?'
            );
            $comando->execute([
                $marca,
                $modelo,
                $placa,
                $ano,
                $quilometragem,
                $id,
                $_SESSION['cliente_id']
            ]);

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
  <link rel="icon" href="imagens/carcare-favicon.png">
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

        <div class="colunas">
          <label>Marca<input name="marca" value="<?php echo textoSeguro($marca); ?>" required></label>
          <label>Modelo<input name="modelo" value="<?php echo textoSeguro($modelo); ?>" required></label>
        </div>

        <div class="colunas">
          <label>Placa<input name="placa" value="<?php echo textoSeguro($placa); ?>" required></label>
          <label>Ano<input name="ano" type="number" value="<?php echo intval($ano); ?>" required></label>
        </div>

        <label>Quilometragem<input name="quilometragem" type="number" min="0" value="<?php echo intval($quilometragem); ?>" required></label>

        <div class="acoes">
          <a class="botao secundario" href="veiculos.php">Cancelar</a>
          <button class="botao primario" type="submit">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
