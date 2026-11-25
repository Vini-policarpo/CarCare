<?php

require_once __DIR__ . '/init.php';

$erro = '';
$nome = '';
$email = '';
$cargo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $email === '' || $cargo === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Digite um e-mail válido.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas estão diferentes.';
    } else {
        $consulta = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $consulta->execute([$email]);

        if ($consulta->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            try {
                $pdo->beginTransaction();

                $senhaProtegida = password_hash($senha, PASSWORD_DEFAULT);
                $comando = $pdo->prepare(
                    "INSERT INTO usuarios (nome, email, senha, tipo)
                     VALUES (?, ?, ?, 'funcionario')"
                );
                $comando->execute([$nome, $email, $senhaProtegida]);

                $usuarioId = $pdo->lastInsertId();
                $comandoFuncionario = $pdo->prepare(
                    'INSERT INTO funcionarios (usuario_id, nome, cargo)
                     VALUES (?, ?, ?)'
                );
                $comandoFuncionario->execute([$usuarioId, $nome, $cargo]);

                $pdo->commit();

                header('Location: login.php?cadastro=ok');
                exit;
            } catch (PDOException $erroBanco) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $erro = 'Não foi possível criar o funcionário.';
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
  <title>Cadastro de funcionário | CarCare</title>
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
        <h1>Área da oficina.</h1>
        <h1 class="bem-vindo">Novo funcionário.</h1>
      </div>
    </section>

    <section class="login_acesso">
      <div class="login_cartao">
        <h2>Criar funcionário</h2>
        <p class="login_descricao">Preencha os dados para criar uma conta de funcionário.</p>

        <?php if ($erro !== ''): ?>
          <div class="alerta erro"><?php echo textoSeguro($erro); ?></div>
        <?php endif; ?>

        <form id="form-login" action="cadastro-funcionario.php" method="post">
          <label for="nome">Nome</label>
          <input id="nome" name="nome" value="<?php echo textoSeguro($nome); ?>" required>

          <label for="email">E-mail</label>
          <input id="email" name="email" type="email" value="<?php echo textoSeguro($email); ?>" required>

          <label for="cargo">Cargo</label>
          <input id="cargo" name="cargo" value="<?php echo textoSeguro($cargo); ?>" placeholder="Mecânico responsável" required>

          <label for="senha">Senha</label>
          <input id="senha" name="senha" type="password" minlength="8" required>

          <label for="confirmar_senha">Confirmar senha</label>
          <input id="confirmar_senha" name="confirmar_senha" type="password" minlength="8" required>

          <button class="botao primario login_entrar" type="submit">Criar funcionário</button>
        </form>

        <p class="login_cadastro"><a href="login.php">Voltar para o login</a></p>
      </div>
    </section>
  </main>
</body>
</html>
