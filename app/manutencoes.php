<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'manutencoes';
$mensagem = '';
$mensagemErro = false;

if (($_GET['mensagem'] ?? '') === 'cadastrada') {
    $mensagem = 'Solicitação enviada para a oficina.';
} elseif (($_GET['mensagem'] ?? '') === 'atualizada') {
    $mensagem = 'Status atualizado com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'excluida') {
    $mensagem = 'Manutenção excluída com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'erro') {
    $mensagem = 'Não foi possível atualizar o status.';
    $mensagemErro = true;
} elseif (($_GET['mensagem'] ?? '') === 'erroAgendar') {
    $mensagem = 'Preencha corretamente os dados da manutenção.';
    $mensagemErro = true;
}

$sql = 'SELECT manutencoes.*,
               veiculos.marca,
               veiculos.modelo,
               veiculos.placa,
               clientes.nome AS proprietario,
               funcionarios.nome AS funcionario
        FROM manutencoes
        INNER JOIN veiculos ON veiculos.id = manutencoes.veiculo_id
        INNER JOIN clientes ON clientes.id = veiculos.cliente_id
        LEFT JOIN funcionarios ON funcionarios.id = manutencoes.funcionario_id';
$parametros = [];

if (usuarioEhCliente()) {
    $sql .= ' WHERE veiculos.cliente_id = ?';
    $parametros[] = $_SESSION['cliente_id'];
}

$sql .= ' ORDER BY manutencoes.data_agendamento DESC';
$comando = $pdo->prepare($sql);
$comando->execute($parametros);
$manutencoes = $comando->fetchAll();

$solicitadas = 0;
$agendadas = 0;
$andamento = 0;
$concluidas = 0;

foreach ($manutencoes as $manutencao) {
    if ($manutencao['status'] === 'Solicitado') {
        $solicitadas++;
    } elseif ($manutencao['status'] === 'Agendado') {
        $agendadas++;
    } elseif ($manutencao['status'] === 'Em andamento') {
        $andamento++;
    } elseif ($manutencao['status'] === 'Concluido') {
        $concluidas++;
    }
}

// Busca veículos e serviços para o formulário do cliente
$veiculos = [];
$servicos = [];
$veiculoId = '';
$servicoId = '';
$dataAgendamento = '';
$quilometragem = '';

if (usuarioEhCliente()) {
    $stmtVeiculos = $pdo->prepare('SELECT id, marca, modelo, placa FROM veiculos WHERE cliente_id = ? ORDER BY marca, modelo');
    $stmtVeiculos->execute([$_SESSION['cliente_id']]);
    $veiculos = $stmtVeiculos->fetchAll();

    $stmtServicos = $pdo->query('SELECT id, nome, valor FROM servicos ORDER BY nome');
    $servicos = $stmtServicos->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manutenções | CarCare</title>
  <link rel="stylesheet" href="https://unpkg.com/maplibre-gl@5.6.1/dist/maplibre-gl.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/manutencoes.css">
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/parciais/nav.php'; ?>

    <main class="conteudo">
      <section class="pagina" id="pagina-manutencoes">
        <div class="layout-manutencoes">
          <div class="coluna-acompanhamento">

        <?php if ($mensagem !== ''): ?>
          <div class="alerta <?php echo $mensagemErro ? 'erro' : 'sucesso'; ?>"><?php echo textoSeguro($mensagem); ?></div>
        <?php endif; ?>

        <header class="cabecalho">
          <div>
            <h1><?php echo usuarioEhFuncionario() ? 'Solicitações dos clientes' : 'Minhas manutenções'; ?></h1>
            <p>
              <?php echo usuarioEhFuncionario()
                  ? 'Analise os pedidos e altere o status de cada serviço.'
                  : 'Acompanhe as atualizações feitas pela oficina.'; ?>
            </p>
          </div>

        </header>

        <div class="indicadores resumo">
          <article><span>Solicitadas</span><strong><?php echo $solicitadas; ?></strong></article>
          <article><span>Agendadas</span><strong><?php echo $agendadas; ?></strong></article>
          <article><span>Em andamento</span><strong><?php echo $andamento; ?></strong></article>
          <article><span>Concluídas</span><strong><?php echo $concluidas; ?></strong></article>
        </div>

        <?php if (count($manutencoes) === 0): ?>
          <div class="painel central">
            <div class="miniatura"><img src="imagens/icone-manutencao.png" alt=""></div>
            <h2>Nenhuma manutenção registrada</h2>
            <p><?php echo usuarioEhCliente() ? 'Envie sua primeira solicitação.' : 'Nenhum cliente enviou uma solicitação.'; ?></p>
          </div>
        <?php else: ?>
          <div class="tabela-container">
            <table>
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Veículo</th>
                  <?php if (usuarioEhFuncionario()): ?><th>Cliente</th><?php endif; ?>
                  <th>Serviço</th>
                  <th>Status</th>
                  <th><?php echo usuarioEhFuncionario() ? 'Ações' : 'Responsável'; ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($manutencoes as $manutencao): ?>
                  <tr>
                    <td><?php echo date('d/m/Y', strtotime($manutencao['data_agendamento'])); ?></td>
                    <td>
                      <strong><?php echo textoSeguro(trim($manutencao['marca'] . ' ' . $manutencao['modelo'])); ?></strong>
                      <small><?php echo textoSeguro($manutencao['placa']); ?></small>
                    </td>

                    <?php if (usuarioEhFuncionario()): ?>
                      <td><?php echo textoSeguro($manutencao['proprietario']); ?></td>
                    <?php endif; ?>

                    <td>
                      <strong><?php echo textoSeguro($manutencao['descricao']); ?></strong>
                      <?php if ($manutencao['observacoes']): ?>
                        <small><?php echo textoSeguro($manutencao['observacoes']); ?></small>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="status <?php echo classeStatus($manutencao['status']); ?>">
                        <?php echo textoSeguro(nomeStatus($manutencao['status'])); ?>
                      </span>
                    </td>
                    <td>
                      <?php if (usuarioEhFuncionario()): ?>
                        <div class="acoes-tabela">
                          <form action="atualizar-manutencao.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $manutencao['id']; ?>">
                            <select name="status" aria-label="Novo status">
                              <option value="Solicitado" <?php echo $manutencao['status'] === 'Solicitado' ? 'selected' : ''; ?>>Solicitado</option>
                              <option value="Agendado" <?php echo $manutencao['status'] === 'Agendado' ? 'selected' : ''; ?>>Agendado</option>
                              <option value="Em andamento" <?php echo $manutencao['status'] === 'Em andamento' ? 'selected' : ''; ?>>Em andamento</option>
                              <option value="Concluido" <?php echo $manutencao['status'] === 'Concluido' ? 'selected' : ''; ?>>Concluído</option>
                              <option value="Cancelado" <?php echo $manutencao['status'] === 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                            <button type="submit">Salvar</button>
                          </form>

                          <form action="excluir-manutencao.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $manutencao['id']; ?>">
                            <button type="submit">Excluir</button>
                          </form>
                        </div>
                      <?php else: ?>
                        <?php echo textoSeguro($manutencao['funcionario'] ?: 'Aguardando atendimento'); ?>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
          </div>

        <?php if (usuarioEhCliente()): ?>
          <form class="bloco-manutencao" id="solicitar-manutencao" action="agendar-manutencao.php" method="post">
            <header class="cabecalho-agendamento">

              <h2>Pontos de manutenção</h2>
              <p>Escolha uma unidade e veja uma estimativa antes de solicitar o atendimento.</p>
            </header>

            <div class="coluna-mapa">
              <div class="mapa-topo">
                <div>
                  <strong>Unidades CarCare</strong>
                  <span>Selecione um ponto próximo de você</span>
                </div>
              </div>
              <div id="mapa" class="maps" role="region" aria-label="Mapa com unidades de manutenção CarCare"></div>
            </div>

            <div class="coluna-campos">
              <div class="cabecalho-orcamento">
                <strong>Orçamento inicial</strong>
                <span>Preencha os dados do atendimento</span>
              </div>

              <label class="campo-box">
                <span>Veículo</span>
                <select name="veiculo_id" required>
                  <option value="">Selecione o veículo</option>
                  <?php foreach ($veiculos as $veiculo): ?>
                    <option value="<?php echo $veiculo['id']; ?>" <?php echo $veiculoId == $veiculo['id'] ? 'selected' : ''; ?>>
                      <?php echo textoSeguro(trim($veiculo['marca'] . ' ' . $veiculo['modelo'])); ?> — <?php echo textoSeguro($veiculo['placa']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>

              <label class="campo-box">
                <span>Serviço ou peça</span>
                <select name="servico_id" id="select-servico" required>
                  <option value="" data-valor="0">Selecione o serviço</option>
                  <?php foreach ($servicos as $servico): ?>
                    <option value="<?php echo $servico['id']; ?>"
                            data-valor="<?php echo (float) $servico['valor']; ?>"
                            <?php echo $servicoId == $servico['id'] ? 'selected' : ''; ?>>
                      <?php echo textoSeguro($servico['nome']); ?> — R$ <?php echo number_format($servico['valor'], 2, ',', '.'); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>

              <label class="campo-box">
                <span>Unidade de atendimento</span>
                <input type="text" name="local" id="campo-local" list="unidades-carcare" placeholder="Digite ou escolha uma unidade" autocomplete="off" required>
                <datalist id="unidades-carcare"></datalist>
              </label>

              <div class="linha-dupla">
                <label class="campo-box">
                  <span>Data disponível</span>
                  <input name="data_agendamento" type="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo textoSeguro($dataAgendamento); ?>" required>
                </label>

                <label class="campo-box">
                  <span>Quilometragem</span>
                  <input name="quilometragem" type="number" min="0" placeholder="Ex: 45000" value="<?php echo textoSeguro($quilometragem); ?>" required>
                </label>
              </div>
            </div>

            <div class="coluna-preco">
              <div class="resumo-preco">
                <span>Estimativa do serviço</span>
                <strong class="valor-destaque" id="preco-estimado">R$ 0,00</strong>
              </div>
              <p class="aviso-preco">
                Valor de referência. O orçamento final será confirmado após a avaliação presencial.
              </p>
              <button type="submit" class="botao-solicitar">Solicitar manutenção</button>
            </div>
          </form>
        <?php endif; ?>
        </div>
      </section>
    </main>
  </div>

  <?php if (usuarioEhCliente()): ?>
    <script src="https://unpkg.com/maplibre-gl@5.6.1/dist/maplibre-gl.js"></script>
    <script>
      const selectServico = document.getElementById('select-servico');
      const precoEstimado = document.getElementById('preco-estimado');

      if (selectServico && precoEstimado) {
        selectServico.addEventListener('change', function () {
          const opcao = this.options[this.selectedIndex];
          const valor = parseFloat(opcao.dataset.valor || 0);
          precoEstimado.textContent = valor.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
          });
        });
      }

      const mapa = new maplibregl.Map({
        container: 'mapa',
        style: 'https://tiles.openfreemap.org/styles/liberty',
        center: [-47.0, -23.2],
        zoom: 6.3
      });

      mapa.addControl(new maplibregl.NavigationControl(), 'top-right');

      const lojas = [
        {
          nome: "Carcare Conceição",
          marca: "Carcare",
          endereco: "Av. Hélio Pellegrino, 35, Vila Nova Conceição, São Paulo, SP"
        },
        {
          nome: "Carcare José",
          marca: "Carcare",
          endereco: "Av. Deputado Benedito Matarazzo, 9575, São José dos Campos, SP"
        },
        {
          nome: "Carcare Campinas",
          marca: "Carcare",
          endereco: "Av. Antônio Carlos Couto de Barros, 1555, Campinas, SP"
        },
        {
          nome: "Carcare ABC",
          marca: "Carcare",
          endereco: "Av. Dr. Rudge Ramos, 859, São Bernardo do Campo, SP"
        },
        {
          nome: "Carcare Guarulhos",
          marca: "Carcare",
          endereco: "Rua Eduardo Froner, 1070, Guarulhos, SP"
        },
        {
          nome: "Carcare Sorocaba",
          marca: "Carcare",
          endereco: "SP-270, Km 104, Sorocaba, SP"
        },
        {
          nome: "Carcare Santos",
          marca: "Carcare",
          endereco: "Av. Washington Luís, 78, Vila Mathias, Santos, SP"
        }
      ];

      const datalistUnidades = document.getElementById('unidades-carcare');
      const campoLocal = document.getElementById('campo-local');

      lojas.forEach(loja => {
        const option = document.createElement('option');
        option.value = `${loja.nome} - ${loja.endereco}`;
        datalistUnidades.appendChild(option);
      });

      campoLocal.addEventListener('change', function () {
        const valorDigitado = this.value.toLowerCase();
        const lojaEncontrada = lojas.find(l =>
          `${l.nome} - ${l.endereco}`.toLowerCase() === valorDigitado ||
          l.nome.toLowerCase().includes(valorDigitado)
        );

        if (lojaEncontrada && lojaEncontrada.coordenadas) {
          mapa.flyTo({
            center: lojaEncontrada.coordenadas,
            zoom: 14
          });
          if (lojaEncontrada.marcador) {
            lojaEncontrada.marcador.togglePopup();
          }
        }
      });

      mapa.on('load', async function () {
        const limites = new maplibregl.LngLatBounds();

        for (const loja of lojas) {
          try {
            const url = new URL('https://photon.komoot.io/api/');
            url.searchParams.set('q', loja.endereco);
            url.searchParams.set('limit', '1');

            const resposta = await fetch(url);
            if (!resposta.ok) {
              throw new Error("Erro ao consultar endereço");
            }

            const dados = await resposta.json();

            if (dados.features.length > 0) {
              const coordenadas = dados.features[0].geometry.coordinates;
              loja.coordenadas = coordenadas;
              limites.extend(coordenadas);

              const icone = document.createElement('img');
              icone.src = 'imagens/icone-carcare.png';
              icone.alt = 'CarCare';
              icone.style.width = '40px';
              icone.style.height = '40px';
              icone.style.objectFit = 'contain';
              icone.style.cursor = 'pointer';
              icone.onerror = function () {
                this.src = 'imagens/logo-carcare.png';
              };

              icone.addEventListener('click', () => {
                campoLocal.value = `${loja.nome} - ${loja.endereco}`;
              });

              const marcador = new maplibregl.Marker({
                element: icone,
                anchor: 'bottom'
              })
                .setLngLat(coordenadas)
                .setPopup(
                  new maplibregl.Popup({ offset: 25 }).setHTML(`
                    <strong>${loja.nome}</strong><br>
                    <small>${loja.endereco}</small>
                  `)
                )
                .addTo(mapa);

              loja.marcador = marcador;
            }

            await new Promise(resolve => setTimeout(resolve, 350));
          } catch (erro) {
            console.error("Erro ao localizar:", loja.nome, erro);
          }
        }

        if (!limites.isEmpty()) {
          mapa.fitBounds(limites, {
            padding: 50,
            maxZoom: 9
          });
        }
      });

      mapa.on('error', function (evento) {
        console.error('Erro no mapa:', evento.error);
      });
    </script>
  <?php endif; ?>
</body>
</html>
