<?php

require_once __DIR__ . '/init.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="CarCare: organize veículos, manutenções e o histórico de serviços em um só lugar.">
  <title>CarCare | Manutenção veicular organizada</title>
  <link rel="stylesheet" href="css/apresentacao.css">
</head>

<body>
  <a class="pular-conteudo" href="#conteudo-principal">Pular para o conteúdo</a>

  <header class="topo">
    <div class="limite topo-conteudo">
      <a class="marca-publica" href="apresentacao.php" aria-label="Página inicial do CarCare">
        <img src="imagens/logo-carcare.png" alt="">
        <strong>CARCARE</strong>
      </a>

      <nav class="navegacao-publica" aria-label="Navegação principal">
        <a href="#recursos">Recursos</a>
        <a class="link-entrar" href="login.php">Entrar</a>
        <a class="botao-publico botao-destaque" href="cadastro-usuario.php">Criar conta</a>
      </nav>
    </div>
  </header>

  <main id="conteudo-principal">
    <section class="destaque">
      <div class="limite destaque-conteudo">
        <div class="destaque-texto">
          <h1>Que bom<br>que você voltou!</h1>
          <p>Acesse sua conta para continuar gerenciando.</p>

          <div class="destaque-acoes">
            <a class="botao-publico botao-destaque" href="cadastro-usuario.php">Começar agora</a>
            <a class="botao-publico botao-contorno" href="login.php">Já tenho uma conta</a>
          </div>
        </div>

        <figure class="destaque-imagem-quadro">
          <img class="destaque-imagem-preview" src="imagens/mecanico-carcare.jpg" alt="Mecânico da CarCare realizando o diagnóstico de um veículo">
        </figure>
      </div>
    </section>

    <section id="recursos" class="recursos">
      <div class="limite">
        <h2 class="titulo-secao">Como funciona</h2>

        <div class="carrossel-tutorial" role="region" aria-label="Tutorial para cadastrar um veículo e solicitar uma manutenção">
          <input class="controle-carrossel" type="radio" name="passo-tutorial" id="tutorial-passo-1" aria-label="Mostrar passo 1: acesse seus veículos" checked>
          <input class="controle-carrossel" type="radio" name="passo-tutorial" id="tutorial-passo-2" aria-label="Mostrar passo 2: preencha os dados">
          <input class="controle-carrossel" type="radio" name="passo-tutorial" id="tutorial-passo-3" aria-label="Mostrar passo 3: solicite a manutenção">

          <div class="carrossel-janela">
            <div class="carrossel-trilho">
              <article class="tutorial-slide">
                <div class="tutorial-texto">
                  <h3>Acesse seus veículos</h3>
                  <p>Entre na área <strong>Veículos</strong> pelo menu e selecione <strong>Cadastrar veículo</strong> para começar.</p>
                </div>
                <figure class="tutorial-imagem">
                  <img src="imagens/tutorial1.png" width="881" height="594" alt="Tela de veículos com setas indicando o menu Veículos e o botão Cadastrar veículo">
                </figure>
              </article>

              <article class="tutorial-slide">
                <div class="tutorial-texto">
                  <h3>Preencha os dados</h3>
                  <p>Informe marca, modelo, placa, ano e quilometragem atual. Depois, selecione <strong>Cadastrar</strong>.</p>
                </div>
                <figure class="tutorial-imagem">
                  <img src="imagens/tutorial2.png" width="1043" height="716" alt="Formulário de cadastro de veículo preenchido com os dados do carro">
                </figure>
              </article>

              <article class="tutorial-slide">
                <div class="tutorial-texto">
                  <h3>Solicite a manutenção</h3>
                  <p>Com o veículo cadastrado, volte à <strong>Visão geral</strong> e selecione <strong>Solicitar manutenção</strong>.</p>
                </div>
                <figure class="tutorial-imagem">
                  <img src="imagens/tutorial3.png" width="1037" height="529" alt="Visão geral do CarCare com setas indicando o menu e o botão Solicitar manutenção">
                </figure>
              </article>
            </div>
          </div>

          <div class="carrossel-controles">
            <div class="grupo-setas grupo-setas-1" aria-hidden="true">
              <label class="seta-carrossel" for="tutorial-passo-3" title="Ir para o passo 3"><span aria-hidden="true">&#8592;</span><span class="rotulo-seta">Anterior</span></label>
              <label class="seta-carrossel" for="tutorial-passo-2" title="Ir para o passo 2"><span class="rotulo-seta">Próximo</span><span aria-hidden="true">&#8594;</span></label>
            </div>
            <div class="grupo-setas grupo-setas-2" aria-hidden="true">
              <label class="seta-carrossel" for="tutorial-passo-1" title="Ir para o passo 1"><span aria-hidden="true">&#8592;</span><span class="rotulo-seta">Anterior</span></label>
              <label class="seta-carrossel" for="tutorial-passo-3" title="Ir para o passo 3"><span class="rotulo-seta">Próximo</span><span aria-hidden="true">&#8594;</span></label>
            </div>
            <div class="grupo-setas grupo-setas-3" aria-hidden="true">
              <label class="seta-carrossel" for="tutorial-passo-2" title="Ir para o passo 2"><span aria-hidden="true">&#8592;</span><span class="rotulo-seta">Anterior</span></label>
              <label class="seta-carrossel" for="tutorial-passo-1" title="Voltar ao passo 1"><span class="rotulo-seta">Início</span><span aria-hidden="true">&#8594;</span></label>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="chamada-publica">
      <div class="limite chamada-conteudo">
        <h2>Faça parte da nossa equipe.</h2>
        <a class="botao-publico botao-destaque" href="cadastro-usuario.php">Criar minha conta</a>
      </div>
    </section>
  </main>

  <footer class="rodape-publico">
    <div class="limite">
      <a class="marca-publica" href="apresentacao.php" aria-label="Página inicial do CarCare">
        <img src="imagens/logo-carcare.png" alt="">
        <strong>CARCARE</strong>
      </a>
    </div>
  </footer>
</body>

</html>
