<?php
session_start();


if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}

$nome_usuario = $_SESSION['usuario_nome'];
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Relojoaria</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
      <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="stylelogin.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">
        <a>RELOJOARIA</a>
    </div>
    <div class="header-right"> 
        <nav>
            <a href="login.php">SAIR</a> </nav>
        <button id="darkModeToggle" class="dark-mode-toggle" style="background: none; border: none; cursor: pointer; color: var(--cor-escura); font-size: 1.2rem;">
            ☀️
        </button>
    </div>
</header>

<main class="menu-inicial">
    <h1>Bem-Vindo, <strong><?= $nome_usuario ?></strong>!</h1>
    
    <h2>Escolha uma opção:</h2>
    
    <div class="opcoes-menu">
        <a href="cadastrarCliente.php" class="menu-link">Cadastrar Cliente</a>
        <a href="CadastrarRelogio.php" class="menu-link">Cadastrar Relógio</a>
        <a href="CadastrarOS.php" class="menu-link">Nova Ordem de Serviço</a>
        <a href="ListagemClientes.php" class="menu-link">Listar Clientes</a>
        <a href="ListagemRelogio.php" class="menu-link">Listar Relógios</a>
        <a href="ListagemOrdem.php" class="menu-link">Listar Ordens de Serviço</a>
        <a href="relatorio.php" class="menu-link">Relatórios</a> 
        <a href="grafico_pizza.php" class="menu-link">Gráficos</a>
    </div>
</main>

<footer class="footer">
    <h4>Redes Sociais do Criador</h4>

<a href="https://www.instagram.com/dan_oliversmnar/" 
   target="_blank" 
   title="Instagram">
    <i class="fa-brands fa-instagram"></i>
</a>

<a href="https://www.linkedin.com/in/daniel-alvarenga-6775b1179/" 
   target="_blank" 
   title="LinkedIn">
    <i class="fa-brands fa-linkedin-in"></i>
</a>

</footer>

<script>
    const toggleButton = document.getElementById('darkModeToggle');
    const body = document.body;
    const currentMode = localStorage.getItem('darkMode');

    
    if (currentMode === 'enabled') {
        body.classList.add('dark-mode');
        toggleButton.textContent = '🌙';
    }

    
    toggleButton.addEventListener('click', () => {
        if (body.classList.contains('dark-mode')) {
            
            body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
            toggleButton.textContent = '☀️';
        } else {
            
            body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
            toggleButton.textContent = '🌙';
        }
    });
</script>

</body>
</html>