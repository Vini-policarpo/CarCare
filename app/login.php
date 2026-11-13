<?php

require_once __DIR__ . '/init.php';

// Se já estiver conectado, vai direto para a página inicial.
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$erro = '';
$email = '';

// O código abaixo só é executado se o form for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha o e-mail e a senha.';
    } else {
        $comando = $pdo->prepare(
            'SELECT usuarios.*,
                    clientes.id AS cliente_id,
                    funcionarios.id AS funcionario_id
             FROM usuarios
             LEFT JOIN clientes ON clientes.usuario_id = usuarios.id
             LEFT JOIN funcionarios ON funcionarios.usuario_id = usuarios.id
             WHERE usuarios.email = ?'
        );
        $comando->execute([$email]);
        $usuario = $comando->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Troca sessão depois do login.
            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];
            $_SESSION['cliente_id'] = $usuario['cliente_id'];
            $_SESSION['funcionario_id'] = $usuario['funcionario_id'];

            header('Location: index.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar | CarCare</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body class="login">
  <main class="login_layout">
    <section class="login_apresentacao">
      <a class="marca login_marca" href="apresentacao.php">
        <img class="logo" src="imagens/logo-carcare.png" alt="Logo do CarCare">
        <strong>CARCARE</strong>
      </a>
      <div class="login_mensagem">
        <h1>Olá, amigo.</h1>
        <h1 class="bem-vindo">Bem-vindo de volta!</h1>
      </div>
    </section>

    <section class="login_acesso">
      <div class="login_cartao">
        <h2>Entre na sua conta</h2>
        <p class="login_descricao">Digite seu e-mail e sua senha.</p>

        <?php if (isset($_GET['cadastro'])): ?>
          <div class="alerta sucesso">Conta criada. Agora faça seu login.</div>
        <?php endif; ?>

        <?php if ($erro !== ''): ?>
          <div class="alerta erro"><?php echo textoSeguro($erro); ?></div>
        <?php endif; ?>

        <form id="form-login" action="login.php" method="post">
          <label for="email">E-mail</label>
          <input id="email" name="email" type="email" value="<?php echo textoSeguro($email); ?>" required>

          <label for="senha">Senha</label>
          <input id="senha" name="senha" type="password" required>

          <button class="botao primario login_entrar" type="submit">
            Entrar <span>→</span>
          </button>
        </form>

        <p class="login_cadastro">
          É cliente? <a href="cadastro-usuario.php">Criar conta de cliente</a>
        </p>

        <p class="login_cadastro">
          É funcionário? <a href="cadastro-funcionario.php">Criar conta de funcionário</a>
        </p>
      </div>
    </section>
  </main>
</body>
</html>
