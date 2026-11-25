<?php

require_once __DIR__ . '/init.php';

$erro = '';
$nome = '';
$email = '';
$telefone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas estão diferentes.';
    } else {
        // Verifica se o e-mail já foi utilizado.
        $consulta = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);

        if ($consulta->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            // A senha é transformada em um código protegido antes de ser salva.
            $senhaProtegida = password_hash($senha, PASSWORD_DEFAULT);

            try {
                // As duas inserções formam um único cadastro de cliente.
                $pdo->beginTransaction();

                $comando = $pdo->prepare(
                    "INSERT INTO usuarios (nome, email, senha, tipo)
                     VALUES (?, ?, ?, 'cliente')"
                );
                $comando->execute([$nome, $email, $senhaProtegida]);

                $usuarioId = $pdo->lastInsertId();

                $comandoCliente = $pdo->prepare(
                    'INSERT INTO clientes (usuario_id, nome, telefone, email)
                     VALUES (?, ?, ?, ?)'
                );
                $comandoCliente->execute([$usuarioId, $nome, $telefone, $email]);

                $pdo->commit();

                header('Location: login.php?cadastro=ok');
                exit;
            } catch (PDOException $erroBanco) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $erro = 'Não foi possível criar a conta. Tente novamente.';
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
  <title>Criar conta | CarCare</title>
  <link rel="icon" href="imagens/carcare-favicon.png">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/login.css">
</head>
<body class="login">
  <main class="login_layout">
    <section class="login_apresentacao">
      <a class="marca login_marca" href="login.php">
        <img class="logo" src="imagens/logo-carcare.png" alt="Logo do CarCare">
        <strong>CARCARE</strong>
      </a>
      <div class="login_mensagem">
        <h1>Primeiro acesso.</h1>
        <h1 class="bem-vindo">Crie sua conta.</h1>
      </div>
    </section>

    <section class="login_acesso">
      <div class="login_cartao">
        <h2>Criar conta</h2>
        <p class="login_descricao">Preencha os dados abaixo.</p>

        <?php if ($erro !== ''): ?>
          <div class="alerta erro"><?php echo textoSeguro($erro); ?></div>
        <?php endif; ?>

        <form id="form-login" action="cadastro-usuario.php" method="post">
          <label for="nome">Nome</label>
          <input id="nome" name="nome" value="<?php echo textoSeguro($nome); ?>" required>

          <label for="email">E-mail</label>
          <input id="email" name="email" type="email" value="<?php echo textoSeguro($email); ?>" required>

          <label for="telefone">Telefone</label>
          <input id="telefone" name="telefone" value="<?php echo textoSeguro($telefone); ?>" placeholder="(11) 99999-9999">

          <label for="senha">Senha</label>
          <input id="senha" name="senha" type="password" minlength="8" required>

          <label for="confirmar_senha">Confirmar senha</label>
          <input id="confirmar_senha" name="confirmar_senha" type="password" minlength="8" required>

          <button class="botao primario login_entrar" type="submit">Criar conta</button>
        </form>

        <p class="login_cadastro"><a href="login.php">Voltar para o login</a></p>
      </div>
    </section>
  </main>
</body>
</html>
