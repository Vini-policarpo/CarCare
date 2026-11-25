<?php

require_once __DIR__ . '/init.php';
protegerCliente();

$paginaAtual = 'veiculos';
$erro = '';
$marca = '';
$modelo = '';
$placa = '';
$ano = '';
$quilometragem = '';

$exemplo = [
    'placa' => 'BRA2E19',
    'marca' => 'Volkswagen',
    'modelo' => 'Gol 1.6 MSI',
    'ano' => 2020,
    'quilometragem' => 45820
];

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
        $consulta = $pdo->prepare('SELECT id FROM veiculos WHERE placa = ?');
        $consulta->execute([$placa]);

        if ($consulta->fetch()) {
            $erro = 'Esta placa já está cadastrada.';
        } else {
            try {
                $pdo->beginTransaction();

                $comando = $pdo->prepare(
                    'INSERT INTO veiculos
                    (cliente_id, marca, modelo, placa, ano, quilometragem)
                    VALUES (?, ?, ?, ?, ?, ?)'
                );

                $comando->execute([
                    $_SESSION['cliente_id'],
                    $marca,
                    $modelo,
                    $placa,
                    $ano,
                    $quilometragem
                ]);

                $id = $pdo->lastInsertId();
                $nomes = $pdo->query(
                    'SELECT DISTINCT componente FROM codigos_falha ORDER BY RAND() LIMIT 3'
                )->fetchAll(PDO::FETCH_COLUMN);
                $total = random_int(1, 2);
                $posicao = 0;

                $buscar = $pdo->prepare(
                    'SELECT id FROM codigos_falha
                    WHERE componente = ? ORDER BY RAND() LIMIT 1'
                );

                $salvar = $pdo->prepare(
                    'INSERT INTO veiculo_diagnosticos
                    (veiculo_id, codigo_falha_id, situacao)
                    VALUES (?, ?, ?)'
                );

                foreach ($nomes as $nome) {
                    $buscar->execute([$nome]);
                    $falha = $buscar->fetchColumn();
                    $situacao = $posicao < $total ? 'Com problema' : 'Tenha atenção';
                    $salvar->execute([$id, $falha, $situacao]);
                    $posicao++;

                    if ($posicao > $total) {
                        break;
                    }
                }

                $pdo->commit();

                header('Location: veiculos.php?mensagem=cadastrado');
                exit;
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $erro = 'Não foi possível cadastrar o veículo.';
            }
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
        <div>
          <h2>Cadastrar veículo</h2>
          <p>O veículo será vinculado automaticamente à sua conta.</p>
        </div>
        <a class="fechar" href="veiculos.php">×</a>
      </div>

      <?php if ($erro !== ''): ?>
        <div class="alerta erro"><?php echo textoSeguro($erro); ?></div>
      <?php endif; ?>

      <form action="cadastro-veiculo.php" method="post">
        <div class="colunas">
          <label>Marca<input name="marca" value="<?php echo textoSeguro($marca); ?>" placeholder="Chevrolet" required></label>
          <label>Modelo<input name="modelo" value="<?php echo textoSeguro($modelo); ?>" placeholder="SUV" required></label>
        </div>

        <div class="colunas">
          <label>Placa<input name="placa" maxlength="8" value="<?php echo textoSeguro($placa); ?>" placeholder="BRA2E19" required></label>
          <label>Ano<input name="ano" type="number" value="<?php echo textoSeguro($ano); ?>" required></label>
        </div>

        <label>Quilometragem atual<input name="quilometragem" type="number" min="0" value="<?php echo textoSeguro($quilometragem); ?>" required></label>

        <div class="acoes">
          <a class="botao secundario" href="veiculos.php">Cancelar</a>
          <button class="botao primario" type="submit">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const exemplo = <?php echo json_encode($exemplo, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const placa = document.querySelector('input[name="placa"]');
    const marca = document.querySelector('input[name="marca"]');
    const modelo = document.querySelector('input[name="modelo"]');
    const ano = document.querySelector('input[name="ano"]');
    const quilometragem = document.querySelector('input[name="quilometragem"]');
    let automatico = false;

    placa.addEventListener('input', function () {
      this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

      if (exemplo && this.value === exemplo.placa) {
        marca.value = exemplo.marca;
        modelo.value = exemplo.modelo;
        ano.value = exemplo.ano;
        quilometragem.value = exemplo.quilometragem;
        automatico = true;
      } else if (automatico) {
        marca.value = '';
        modelo.value = '';
        ano.value = '';
        quilometragem.value = '';
        automatico = false;
      }
    });
  </script>
</body>
</html>
