<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'veiculos';
$erro = '';
$nomeModelo = '';
$placa = '';
$ano = '';
$proprietario = '';
$quilometragem = '';

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
    } elseif ($ano < 1900 || $ano > date('Y') + 1) {
        $erro = 'Digite um ano válido.';
    } elseif ($quilometragem < 0) {
        $erro = 'A quilometragem não pode ser negativa.';
    } else {
        // Confere se a placa já está no banco.
        $consulta = $pdo->prepare('SELECT id FROM veiculos WHERE placa = ?');
        $consulta->execute([$placa]);

        if ($consulta->fetch()) {
            $erro = 'Esta placa já está cadastrada.';
        } else {
            $comando = $pdo->prepare(
                'INSERT INTO veiculos
                (usuario_id, nome_modelo, placa, ano, proprietario, quilometragem)
                VALUES (?, ?, ?, ?, ?, ?)'
            );

            $comando->execute([
                $_SESSION['usuario_id'],
                $nomeModelo,
                $placa,
                $ano,
                $proprietario,
                $quilometragem
            ]);

            header('Location: veiculos.php?mensagem=cadastrado');
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
  <title>Cadastrar veículo | CarCare</title>
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
        <div>
          <h2>Cadastrar veículo</h2>
          <p>Preencha os dados principais do veículo.</p>
        </div>
        <a class="fechar" href="veiculos.php">×</a>
      </div>

      <?php if ($erro !== ''): ?>
        <div class="alerta erro"><?php echo textoSeguro($erro); ?></div>
      <?php endif; ?>

      <form action="cadastro-veiculo.php" method="post">
        <label>
          Nome ou modelo
          <input name="nome_modelo" value="<?php echo textoSeguro($nomeModelo); ?>" placeholder="Volkswagen Gol 1.6 MSI" required>
        </label>

        <div class="colunas">
          <label>
            Placa
            <input name="placa" maxlength="8" value="<?php echo textoSeguro($placa); ?>" placeholder="BRA2E19" required>
          </label>
          <label>
            Ano
            <input name="ano" type="number" value="<?php echo textoSeguro($ano); ?>" required>
          </label>
        </div>

        <label>
          Proprietário
          <input name="proprietario" value="<?php echo textoSeguro($proprietario); ?>" required>
        </label>

        <label>
          Quilometragem atual
          <input name="quilometragem" type="number" min="0" value="<?php echo textoSeguro($quilometragem); ?>" required>
        </label>

        <div class="acoes">
          <a class="botao secundario" href="veiculos.php">Cancelar</a>
          <button class="botao primario" type="submit">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
