<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'manutencoes';
$erro = '';
$veiculoId = '';
$servico = '';
$dataAgendada = '';
$quilometragem = '';
$observacoes = '';

$veiculos = $pdo->query('SELECT id, nome_modelo, placa FROM veiculos ORDER BY nome_modelo')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $veiculoId = intval($_POST['veiculo_id'] ?? 0);
    $servico = trim($_POST['servico'] ?? '');
    $dataAgendada = $_POST['data_agendada'] ?? '';
    $quilometragem = intval($_POST['quilometragem'] ?? 0);
    $observacoes = trim($_POST['observacoes'] ?? '');

    if ($veiculoId === 0 || $servico === '' || $dataAgendada === '') {
        $erro = 'Preencha o veículo, o serviço e a data.';
    } elseif ($quilometragem < 0) {
        $erro = 'Quilometragem não pode ser negativa.';
    } else {
        $comando = $pdo->prepare(
            'INSERT INTO manutencoes
            (veiculo_id, servico, data_agendada, quilometragem, status, observacoes)
            VALUES (?, ?, ?, ?, ?, ?)'
        );

        $comando->execute([
            $veiculoId,
            $servico,
            $dataAgendada,
            $quilometragem,
            'Agendada',
            $observacoes
        ]);

        header('Location: manutencoes.php?mensagem=cadastrada');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agendar manutenção | CarCare</title>
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
        <div><h2>Agendar manutenção</h2><p>Registre o serviço e a data.</p></div>
        <a class="fechar" href="manutencoes.php">×</a>
      </div>

      <?php if ($erro !== ''): ?>
        <div class="alerta erro"><?php echo textoSeguro($erro); ?></div>
      <?php endif; ?>

      <?php if (count($veiculos) === 0): ?>
        <div class="alerta erro">Cadastre um veículo primeiro.</div>
      <?php else: ?>
        <form action="agendar-manutencao.php" method="post">
          <label>
            Veículo
            <select name="veiculo_id" required>
              <option value="">Selecione</option>
              <?php foreach ($veiculos as $veiculo): ?>
                <option value="<?php echo $veiculo['id']; ?>" <?php echo $veiculoId == $veiculo['id'] ? 'selected' : ''; ?>>
                  <?php echo textoSeguro($veiculo['nome_modelo']); ?> — <?php echo textoSeguro($veiculo['placa']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label>Serviço<input name="servico" value="<?php echo textoSeguro($servico); ?>" placeholder="Troca de óleo" required></label>

          <div class="colunas">
            <label>Data<input name="data_agendada" type="date" value="<?php echo textoSeguro($dataAgendada); ?>" required></label>
            <label>Quilometragem<input name="quilometragem" type="number" min="0" value="<?php echo textoSeguro($quilometragem); ?>"></label>
          </div>

          <label>Observações<textarea name="observacoes" rows="3"><?php echo textoSeguro($observacoes); ?></textarea></label>

          <div class="acoes">
            <a class="botao secundario" href="manutencoes.php">Cancelar</a>
            <button class="botao primario" type="submit">Agendar</button>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
