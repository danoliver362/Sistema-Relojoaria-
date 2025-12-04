<?php
session_start();
include_once ("conexao.php");

$erro = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    $senha = $_POST['senha'];

  
    $sql = "SELECT * FROM usuario WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        
        if ($senha === $row['senha']) {

            
            $_SESSION['usuario_id'] = $row['id_usuario'];
            $_SESSION['usuario_nome'] = $row['login'];
            $_SESSION['logado'] = true;

            
            header("Location: PaginaInicial.php");
            exit;
        } 
        else {
            $erro = "Senha incorreta!";
        }
    } 
    else {
        $erro = "Usuário não encontrado!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="stylelogin.css">
</head>
<body body class="dark-mode">

<header>
  <div class="logo">
    <a>RELOJOARIA</a>
  </div>
  <div class="header-right"> <nav>
        </nav>
        <button id="darkModeToggle" class="dark-mode-toggle" style="background: none; border: none; cursor: pointer; color: var(--cor-escura); font-size: 1.2rem;">
            ☀️
        </button>
</header>

<main>
    <h1>LOGIN</h1>

    <?php if (!empty($erro)) { ?>
        <p style="color:red; font-weight: bold;"><?= $erro ?></p>
    <?php } ?>

    <form method="POST" action="">
      <label for="usuario">Usuário</label>
      <input type="text" name="usuario" required>

      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" required>

      <div class="botoes">
        <button type="submit">ENTRAR</button>
        <button type="reset">LIMPAR</button>
      </div>
    </form>
</main>

<footer class="footer"></footer>
<script>
    const toggleButton = document.getElementById('darkModeToggle');
    const body = document.body;
    const currentMode = localStorage.getItem('darkMode');

    // 1. Aplica o modo salvo no localStorage ou o modo padrão
    if (currentMode === 'enabled') {
        body.classList.add('dark-mode');
        toggleButton.textContent = '🌙';
    }

    // 2. Listener para o clique no botão
    toggleButton.addEventListener('click', () => {
        if (body.classList.contains('dark-mode')) {
            // Desativar Dark Mode
            body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
            toggleButton.textContent = '☀️';
        } else {
            // Ativar Dark Mode
            body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
            toggleButton.textContent = '🌙';
        }
    });
</script>
</body>
</html>
