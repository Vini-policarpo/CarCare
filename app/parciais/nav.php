<aside class="lateral">
  <div class="marca">
    <img class="logo" src="imagens/logo-carcare.png" alt="Logo do CarCare">
    <div><strong>CARCARE</strong></div>
  </div>

  <nav class="menu">
    <a class="item <?php echo $paginaAtual === 'inicio' ? 'ativo' : ''; ?>" href="index.php">
      <img src="imagens/icone-inicio.png">
      <span>Visão geral</span>
    </a>
    <a class="item <?php echo $paginaAtual === 'veiculos' ? 'ativo' : ''; ?>" href="veiculos.php">
      <img src="imagens/icone-carro.png">
      <span>Veículos</span>
    </a>
    <a class="item <?php echo $paginaAtual === 'manutencoes' ? 'ativo' : ''; ?>" href="manutencoes.php">
      <img src="imagens/icone-manutencao.png">
      <span>Manutenções</span>
    </a>
    <a class="item <?php echo $paginaAtual === 'historico' ? 'ativo' : ''; ?>" href="historico.php">
      <img src="imagens/icone-historico.png">
      <span>Histórico</span>
    </a>
  </nav>

  <div class="perfil">
    <div class="avatar">CC</div>
    <div class="perfil_dados">
      <strong><?php echo textoSeguro($_SESSION['usuario_nome']); ?></strong>
      <span>Usuário</span>
    </div>
    <a class="sair" href="logout.php">Sair</a>
  </div>
</aside>
