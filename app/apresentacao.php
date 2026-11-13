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
      <a class="marca-publica" href="index.php" aria-label="Página inicial do CarCare">
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

        <figure class="destaque-imagem">
          <div class="destaque-imagem-quadro">
            <img class="destaque-imagem-preview" src="imagens/mecanico-carcare.jpg" alt="Mecânico da CarCare realizando o diagnóstico de um veículo">
          </div>
        </figure>
      </div>
    </section>

    <section id="recursos" class="recursos">
      <div class="limite">
        <div class="tutorial-layout" aria-label="Tutorial da CarCare">
          <div class="tutorial-info">
            <div class="tutorial-titulo">
              <h2>Como funciona</h2>
            </div>

            <div class="tutorial-conteudos">
              <article class="tutorial-conteudo" data-descricao aria-live="polite">
                <p>vá em veiculos e em seguida, cadastrar para começar a ver os detalhes.</p>
              </article>
              <article class="tutorial-conteudo" data-descricao hidden>
                <p>Basta digitar sua placa e conferir se as informações do seu carro estão corretas!</p>
              </article>
              <article class="tutorial-conteudo" data-descricao hidden>
                <p>Volte paara visão geral ou manutenções e Solicite uma manutenção em caso de interferencia.</p>
              </article>
            </div>
          </div>

          <div class="tutorial-carrossel" aria-roledescription="carrossel" aria-label="Imagens do tutorial">
            <article class="tutorial-slide" data-slide aria-roledescription="slide" aria-label="1 de 3">
              <figure class="tutorial-imagem">
                <img src="imagens/tutorial1.png" alt="Tela de cadastro de veículo na CarCare">
              </figure>
            </article>

            <article class="tutorial-slide" data-slide aria-roledescription="slide" aria-label="2 de 3" hidden>
              <figure class="tutorial-imagem">
                <img src="imagens/tutorial2.png" alt="Tela para solicitar um serviço na CarCare">
              </figure>
            </article>

            <article class="tutorial-slide tutorial-slide--imagem-reduzida" data-slide aria-roledescription="slide" aria-label="3 de 3" hidden>
              <figure class="tutorial-imagem">
                <img src="imagens/tutorial3.png" alt="Tela de acompanhamento de serviços na CarCare">
              </figure>
            </article>

            <div class="tutorial-controles">
              <button class="tutorial-botao" type="button" data-anterior aria-label="Ver instrução anterior">Voltar</button>
              <p class="tutorial-indicador" data-indicador aria-live="polite">1 / 3</p>
              <button class="tutorial-botao" type="button" data-proximo aria-label="Ver próxima instrução">Próximo</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="chamada-publica">
      <div class="limite chamada-conteudo">
        <div>
          <h2>Venha fazer parte da nossa equipe!</h2>
        </div>
        <a class="botao-publico botao-destaque" href="cadastro-usuario.php">Criar minha conta</a>
      </div>
    </section>
  </main>

  <footer class="rodape-publico">
    <div class="limite rodape-conteudo">
      <div class="marca-publica">
        <img src="imagens/logo-carcare.png" alt="">
        <strong>CARCARE</strong>
      </div>
    </div>
  </footer>

  <script>
    const carrossel = document.querySelector('.tutorial-carrossel');

    if (carrossel) {
      const slides = Array.from(carrossel.querySelectorAll('[data-slide]'));
      const descricoes = Array.from(document.querySelectorAll('[data-descricao]'));
      const anterior = carrossel.querySelector('[data-anterior]');
      const proximo = carrossel.querySelector('[data-proximo]');
      const indicador = carrossel.querySelector('[data-indicador]');
      let indiceAtual = 0;

      function mostrarSlide(indice) {
        indiceAtual = (indice + slides.length) % slides.length;

        slides.forEach((slide, posicao) => {
          slide.hidden = posicao !== indiceAtual;
        });

        descricoes.forEach((descricao, posicao) => {
          descricao.hidden = posicao !== indiceAtual;
        });

        indicador.textContent = `${indiceAtual + 1} / ${slides.length}`;
      }

      anterior.addEventListener('click', () => mostrarSlide(indiceAtual - 1));
      proximo.addEventListener('click', () => mostrarSlide(indiceAtual + 1));
    }
  </script>
</body>

</html>
