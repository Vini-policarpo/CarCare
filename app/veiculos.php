<?php

require_once __DIR__ . '/init.php';
protegerPagina();

$paginaAtual = 'veiculos';
$busca = trim($_GET['busca'] ?? '');
$mensagem = '';
$mensagemErro = false;

if (($_GET['mensagem'] ?? '') === 'cadastrado') {
    $mensagem = 'Veículo cadastrado com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'editado') {
    $mensagem = 'Veículo atualizado com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'excluido') {
    $mensagem = 'Veículo excluído com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'diagnostico_salvo') {
    $mensagem = 'Diagnóstico técnico salvo com sucesso.';
} elseif (($_GET['mensagem'] ?? '') === 'erro') {
    $mensagem = 'Não foi possível salvar o diagnóstico.';
    $mensagemErro = true;
}

function descricao($nome, $falha, $situacao)
{
    if ($situacao === 'Com problema') {
        if (strpos($nome, 'Sonda lambda') !== false && strpos($falha, 'Circuito aberto') !== false) {
            return 'Circuito aberto na sonda lambda. Sem continuidade elétrica e sem leitura; precisa de reparo.';
        }

        if (strpos($falha, 'Sem sinal') !== false) {
            return $falha . '. O componente não está enviando leitura e precisa de reparo.';
        }

        if (strpos($falha, 'Falha') !== false) {
            return $falha . '. O funcionamento está comprometido e precisa de reparo.';
        }

        if (strpos($falha, 'Alta tensão') !== false || strpos($falha, 'Baixa tensão') !== false) {
            $alta = strpos($falha, 'Alta tensão') !== false;

            if (strpos($nome, 'Sonda lambda') !== false) {
                $valor = $alta ? '1,1 V' : '0,05 V';
                return $falha . '. Leitura em ' . $valor . '. Faixa normal: 0,1 V a 0,9 V; precisa de reparo.';
            }

            if (strpos($nome, 'Relé') !== false || strpos($nome, 'Bobina') !== false || strpos($nome, 'Eletroinjetor') !== false) {
                $valor = $alta ? '15,5 V' : '10,5 V';
                return $falha . '. Leitura em ' . $valor . '. Faixa normal: 12 V a 14,5 V; precisa de reparo.';
            }

            $valor = $alta ? '4,9 V' : '0,2 V';
            return $falha . '. Leitura em ' . $valor . '. Faixa normal: 0,5 V a 4,5 V; precisa de reparo.';
        }

        if (strpos($falha, 'Sinal incorreto') !== false || strpos($falha, 'Sem regulagem') !== false) {
            return $falha . '. A leitura está fora do padrão e precisa de reparo.';
        }

        return $falha . '. O componente precisa de reparo.';
    }

    if ($situacao === 'Tenha atenção') {
        if (strpos($nome, 'temperatura da água') !== false) {
            return 'Temperatura em 108 °C. Faixa normal: 80 °C a 105 °C; tenha atenção.';
        }

        if (strpos($nome, 'Sonda lambda') !== false) {
            return 'Tensão em 0,95 V. Faixa normal: 0,1 V a 0,9 V; tenha atenção.';
        }

        if (strpos($nome, 'rotação') !== false) {
            return 'Rotação em 650 rpm. Faixa normal: 700 rpm a 950 rpm; tenha atenção.';
        }

        if (strpos($nome, 'detonação') !== false) {
            return 'Sinal em 4,6 V. Faixa normal: 0,5 V a 4,5 V; tenha atenção.';
        }

        if (strpos($nome, 'posição da borboleta') !== false) {
            return 'Tensão em 1 V na marcha lenta. Faixa normal: 0,5 V a 0,9 V; tenha atenção.';
        }

        if (strpos($nome, 'velocidade') !== false) {
            return 'Sinal de velocidade intermitente. O componente ainda responde, mas tenha atenção.';
        }

        if (strpos($nome, 'pressão absoluta') !== false) {
            return 'Pressão em 48 kPa. Faixa normal: 25 kPa a 45 kPa; tenha atenção.';
        }

        if (strpos($nome, 'Eletroinjetor') !== false || strpos($nome, 'Eletroinjetores') !== false) {
            return 'Resistência em 17 Ω. Faixa normal: 12 Ω a 16 Ω; tenha atenção.';
        }

        if (strpos($nome, 'Bobina') !== false) {
            return 'Resistência primária em 1,7 Ω. Faixa normal: 0,5 Ω a 1,5 Ω; tenha atenção.';
        }

        if (strpos($nome, 'marcha lenta') !== false || strpos($nome, 'Motor de passo') !== false) {
            return 'Marcha lenta em 1.000 rpm. Faixa normal: 700 rpm a 950 rpm; tenha atenção.';
        }

        if (strpos($nome, 'Relé') !== false) {
            return 'Tensão em 11,7 V. Faixa normal: 12 V a 14,5 V; tenha atenção.';
        }

        if (strpos($nome, 'EGR') !== false) {
            return 'Abertura e fechamento mais lentos que o esperado; tenha atenção.';
        }

        return 'Desgaste inicial identificado; tenha atenção.';
    }

    if (strpos($nome, 'temperatura da água') !== false) {
        return 'Temperatura em 90 °C. Faixa normal: 80 °C a 105 °C.';
    }

    if (strpos($nome, 'Sonda lambda') !== false) {
        return 'Tensão em 0,45 V. Faixa normal: 0,1 V a 0,9 V.';
    }

    if (strpos($nome, 'rotação') !== false) {
        return 'Rotação estável em 850 rpm. Faixa normal: 700 rpm a 950 rpm.';
    }

    if (strpos($nome, 'detonação') !== false) {
        return 'Sinal em 2,5 V. Faixa normal: 0,5 V a 4,5 V.';
    }

    if (strpos($nome, 'posição da borboleta') !== false) {
        return 'Tensão em 0,7 V na marcha lenta. Faixa normal: 0,5 V a 0,9 V.';
    }

    if (strpos($nome, 'velocidade') !== false) {
        return 'Leitura em 0 km/h com o veículo parado. Dentro dos requisitos.';
    }

    if (strpos($nome, 'pressão absoluta') !== false) {
        return 'Pressão em 35 kPa. Faixa normal: 25 kPa a 45 kPa.';
    }

    if (strpos($nome, 'Eletroinjetor') !== false || strpos($nome, 'Eletroinjetores') !== false) {
        return 'Resistência em 14 Ω. Faixa normal: 12 Ω a 16 Ω.';
    }

    if (strpos($nome, 'Bobina') !== false) {
        return 'Resistência primária em 0,8 Ω. Faixa normal: 0,5 Ω a 1,5 Ω.';
    }

    if (strpos($nome, 'marcha lenta') !== false || strpos($nome, 'Motor de passo') !== false) {
        return 'Marcha lenta estável em 850 rpm. Faixa normal: 700 rpm a 950 rpm.';
    }

    if (strpos($nome, 'Relé') !== false) {
        return 'Tensão em 12,4 V. Faixa normal: 12 V a 14,5 V.';
    }

    if (strpos($nome, 'EGR') !== false) {
        return 'Abertura e fechamento dentro dos requisitos.';
    }

    return 'Dentro dos requisitos.';
}

// O funcionário enxerga todos. O cliente enxerga somente os próprios veículos.
$sql = 'SELECT veiculos.*, clientes.nome AS proprietario
        FROM veiculos
        INNER JOIN clientes ON clientes.id = veiculos.cliente_id';
$parametros = [];
$filtros = [];

if (usuarioEhCliente()) {
    $filtros[] = 'veiculos.cliente_id = ?';
    $parametros[] = $_SESSION['cliente_id'];
}

if ($busca !== '') {
    $filtros[] = '(veiculos.modelo LIKE ?
                    OR veiculos.marca LIKE ?
                    OR veiculos.placa LIKE ?
                    OR clientes.nome LIKE ?)';
    $termo = '%' . $busca . '%';
    array_push($parametros, $termo, $termo, $termo, $termo);
}

if (count($filtros) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $filtros);
}

$sql .= ' ORDER BY veiculos.id DESC';
$comando = $pdo->prepare($sql);
$comando->execute($parametros);
$veiculos = $comando->fetchAll();

$contagem = [];
$diagnostico = (int) ($_GET['diagnostico'] ?? 0);
$selecionado = null;
$itens = [];
$situacoes = [];

try {
    $consulta = $pdo->query(
        "SELECT veiculo_id, COUNT(*) AS total
         FROM veiculo_diagnosticos
         WHERE situacao IN ('Com problema', 'Tenha atenção')
         GROUP BY veiculo_id"
    );
    $contagem = $consulta->fetchAll(PDO::FETCH_KEY_PAIR);

    if ($diagnostico > 0) {
        foreach ($veiculos as $carro) {
            if ((int) $carro['id'] === $diagnostico) {
                $selecionado = $carro;
                break;
            }
        }

        if ($selecionado) {
            $consulta = $pdo->query('SELECT * FROM codigos_falha ORDER BY CAST(codigo AS UNSIGNED)');
            $itens = $consulta->fetchAll();

            $consulta = $pdo->prepare(
                "SELECT codigo_falha_id, situacao
                FROM veiculo_diagnosticos
                WHERE veiculo_id = ?"
            );
            $consulta->execute([$diagnostico]);
            $situacoes = $consulta->fetchAll(PDO::FETCH_KEY_PAIR);
        }
    }
} catch (Throwable $e) {
    $contagem = [];
    $selecionado = null;
    $mensagem = 'Erro ao abrir diagnóstico: ' . $e->getMessage();
    $mensagemErro = true;
}

$filtro = $busca !== '' ? '&busca=' . urlencode($busca) : '';
$voltar = $busca !== '' ? 'veiculos.php?busca=' . urlencode($busca) : 'veiculos.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veículos | CarCare</title>
  <link rel="icon" href="imagens/carcare-favicon.png">
  <link rel="stylesheet" href="css/style.css?v=3">
</head>
<body>
  <div class="app">
    <?php include __DIR__ . '/parciais/nav.php'; ?>

    <main class="conteudo">
      <section class="pagina">
        <?php if ($mensagem !== ''): ?>
          <div class="alerta <?php echo $mensagemErro ? 'erro' : 'sucesso'; ?>"><?php echo textoSeguro($mensagem); ?></div>
        <?php endif; ?>

        <header class="cabecalho">
          <div>
            <h1>Proprietários e veículos</h1>
            <p><?php echo usuarioEhFuncionario() ? 'Consulte os veículos dos clientes e registre diagnósticos.' : 'Cadastre e acompanhe seus veículos.'; ?></p>
          </div>

          <?php if (usuarioEhCliente()): ?>
            <a class="botao primario" href="cadastro-veiculo.php">Cadastrar veículo</a>
          <?php endif; ?>
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
            <p><?php echo usuarioEhCliente() ? 'Cadastre um veículo para começar.' : 'Nenhum cliente possui veículo cadastrado.'; ?></p>
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
                  <th>Diagnóstico</th>
                  <?php if (usuarioEhCliente()): ?><th>Ações</th><?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($veiculos as $veiculo): ?>
                  <?php $total = (int) ($contagem[$veiculo['id']] ?? 0); ?>
                  <tr>
                    <td>
                      <strong><?php echo textoSeguro(trim($veiculo['marca'] . ' ' . $veiculo['modelo'])); ?></strong>
                      <small>Ano <?php echo intval($veiculo['ano']); ?></small>
                    </td>
                    <td><span class="placa"><?php echo textoSeguro($veiculo['placa']); ?></span></td>
                    <td><?php echo textoSeguro($veiculo['proprietario']); ?></td>
                    <td><?php echo number_format($veiculo['quilometragem'], 0, ',', '.'); ?> km</td>

                    <td>
                      <?php if (usuarioEhFuncionario()): ?>
                        <a class="link-diagnostico <?php echo $total > 0 ? 'com-pendencia' : ''; ?>"
                          href="veiculos.php?diagnostico=<?php echo (int) $veiculo['id'] . $filtro; ?>#bloco-diagnostico">
                          <?php echo $total > 0 ? "Avaliar ({$total} pendências)" : 'Avaliar'; ?>
                        </a>
                      <?php else: ?>
                        <a class="link-diagnostico <?php echo $total > 0 ? 'com-pendencia' : ''; ?>"
                          href="veiculos.php?diagnostico=<?php echo (int) $veiculo['id'] . $filtro; ?>#bloco-diagnostico">
                          <?php echo $total > 0 ? "Ver diagnóstico ({$total} pendências)" : 'Ver diagnóstico'; ?>
                        </a>
                      <?php endif; ?>
                    </td>

                    <?php if (usuarioEhCliente()): ?>
                      <td>
                        <div class="acoes-tabela">
                          <a href="editar-veiculo.php?id=<?php echo $veiculo['id']; ?>">Editar</a>

                          <form action="excluir-veiculo.php" method="post">
                            <input type="hidden" name="id" value="<?php echo $veiculo['id']; ?>">
                            <button type="submit">Excluir</button>
                          </form>
                        </div>
                      </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

        <?php if ($selecionado): ?>
          <section class="bloco-diagnostico" id="bloco-diagnostico">
            <div class="diagnostico-topo">
              <div>
                <h2>
                  Diagnóstico técnico —
                  <?php echo textoSeguro(trim($selecionado['marca'] . ' ' . $selecionado['modelo'])); ?>
                  (<?php echo textoSeguro($selecionado['placa']); ?>)
                </h2>
                <p>
                  <?php echo usuarioEhFuncionario()
                      ? 'Selecione apenas os componentes que precisam de reparo e clique em salvar.'
                      : 'Situação técnica dos componentes avaliados pela oficina.'; ?>
                </p>
              </div>
              <a class="botao secundario" href="<?php echo textoSeguro($voltar); ?>">Fechar</a>
            </div>

            <?php if (usuarioEhFuncionario()): ?>
              <form action="salvar-diagnostico.php" method="post">
                <input type="hidden" name="veiculo_id" value="<?php echo (int) $selecionado['id']; ?>">

                <div class="tabela-diagnostico-box">
                  <table class="tabela-diagnostico">
                    <thead>
                      <tr>
                        <th>Código</th>                          
                        <th>Componente</th>
                        <th>Descrição</th>
                        <th>Situação</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($itens as $item): ?>
                        <?php $situacao = $situacoes[$item['id']] ?? 'Normal'; ?>
                        <?php $classe = $situacao === 'Com problema' ? 'problema' : ($situacao === 'Tenha atenção' ? 'atencao' : 'normal'); ?>
                        <?php $normal = descricao($item['componente'], $item['descricao_falha'], 'Normal'); ?>
                        <?php $atencao = descricao($item['componente'], $item['descricao_falha'], 'Tenha atenção'); ?>
                        <?php $falha = descricao($item['componente'], $item['descricao_falha'], 'Com problema'); ?>
                        <?php $texto = descricao($item['componente'], $item['descricao_falha'], $situacao); ?>
                        <tr class="linha-<?php echo $classe; ?>">
                          <td class="centro"><?php echo textoSeguro($item['codigo']); ?></td>
                          <td><?php echo textoSeguro($item['componente']); ?></td>
                          <td class="texto-falha"><?php echo textoSeguro($texto); ?></td>
                          <td class="centro">
                            <select name="situacoes[<?php echo (int) $item['id']; ?>]"
                                    class="situacao-select <?php echo $classe; ?>"
                                    data-normal="<?php echo textoSeguro($normal); ?>"
                                    data-atencao="<?php echo textoSeguro($atencao); ?>"
                                    data-problema="<?php echo textoSeguro($falha); ?>">
                              <option value="Normal" <?php echo $situacao === 'Normal' ? 'selected' : ''; ?>>Normal</option>
                              <option value="Tenha atenção" <?php echo $situacao === 'Tenha atenção' ? 'selected' : ''; ?>>Tenha atenção</option>
                              <option value="Com problema" <?php echo $situacao === 'Com problema' ? 'selected' : ''; ?>>Com problema</option>
                            </select>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <div class="diagnostico-rodape">
                  <button type="submit" class="botao primario">Salvar diagnóstico</button>
                </div>
              </form>

              <script>
                document.querySelectorAll('.situacao-select').forEach(caixa => {
                  caixa.addEventListener('change', function () {
                    const linha = this.closest('tr');
                    const texto = linha.querySelector('.texto-falha');
                    let classe = 'normal';
                    let descricao = this.dataset.normal;

                    if (this.value === 'Tenha atenção') {
                      classe = 'atencao';
                      descricao = this.dataset.atencao;
                    } else if (this.value === 'Com problema') {
                      classe = 'problema';
                      descricao = this.dataset.problema;
                    }

                    this.className = 'situacao-select ' + classe;
                    linha.className = 'linha-' + classe;
                    texto.textContent = descricao;
                  });
                });
              </script>
            <?php else: ?>
              <div class="tabela-diagnostico-box">
                <table class="tabela-diagnostico">
                  <thead>
                    <tr>
                      <th>Componente</th>
                      <th>Descrição</th>
                      <th>Situação</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($itens as $item): ?>
                      <?php $situacao = $situacoes[$item['id']] ?? 'Normal'; ?>
                      <?php $classe = $situacao === 'Com problema' ? 'problema' : ($situacao === 'Tenha atenção' ? 'atencao' : 'normal'); ?>
                      <?php $texto = descricao($item['componente'], $item['descricao_falha'], $situacao); ?>
                      <tr class="linha-<?php echo $classe; ?>">
                        <td><?php echo textoSeguro($item['componente']); ?></td>
                        <td><?php echo textoSeguro($texto); ?></td>
                        <td class="centro">
                          <span class="texto-situacao <?php echo $classe; ?>"><?php echo textoSeguro($situacao); ?></span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </section>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
