<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('conexao.php');


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID inválido.");
}


$stmt = $conn->prepare("SELECT nome, sobrenome, cpf, tipo, telefone FROM cliente WHERE id_cliente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    die("Cliente não encontrado.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $cpf = $_POST['cpf'];
    $tipo = $_POST['tipo'];
    $telefone = $_POST['telefone'];

    $stmt = $conn->prepare("UPDATE cliente SET nome=?, sobrenome=?, cpf=?, tipo=?, telefone=? WHERE id_cliente=?");
    $stmt->bind_param("sssssi", $nome, $sobrenome, $cpf, $tipo, $telefone, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Cliente atualizado com sucesso!'); window.location='ListagemClientes.php';</script>";
        exit;
    } else {
        echo "Erro: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - Relojoaria</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylelogin.css"> 
    <style>
        input[type="text"],
input[type="password"],
select { 
    padding: 10px;
    border: 1px solid var(--cor-borda);
    border-radius: 4px;
    font-size: 1rem;
    background-color: var(--cor-fundo); 
    color: var(--cor-texto);
    width: 100%; 
    margin-bottom: 15px; 
    transition: border-color 0.3s, background-color 0.3s;
}
    </style>
</head>
<body>

<header>
    <div class="logo">
        <a>RELOJOARIA</a>
    </div>
    <div class="header-right"> 
        <nav>
            <a href="ListagemClientes.php">VOLTAR</a> 
            <a href="PaginaInicial.php">INÍCIO</a>
        </nav>
        <button id="darkModeToggle" class="dark-mode-toggle" style="background: none; border: none; cursor: pointer; color: var(--cor-escura); font-size: 1.2rem;">
            ☀️
        </button>
    </div>
</header>

<main>
    <h1>Editar Cliente</h1>

    <form method="post">
        
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($row['nome']) ?>" required>

        <label for="sobrenome">Sobrenome</label>
        <input type="text" id="sobrenome" name="sobrenome" value="<?= htmlspecialchars($row['sobrenome']) ?>" required>

        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" value="<?= htmlspecialchars($row['cpf']) ?>" required>

        <label for="tipo">Tipo de Cliente</label>
        <select id="tipo" name="tipo" required>
            <?php $tipoAtual = htmlspecialchars($row['tipo']); ?>
            <option value="Varejo" <?= ($tipoAtual == 'Varejo') ? 'selected' : '' ?>>Varejo</option>
            <option value="Atacado" <?= ($tipoAtual == 'Atacado') ? 'selected' : '' ?>>Atacado</option>
        </select>

        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($row['telefone']) ?>" required>

        <div class="botoes">
            <button type="submit">SALVAR ALTERAÇÕES</button>
            <button type="reset" class="btn-secundario">RESETAR CAMPOS</button>
        </div>
    </form>
    

</main>

<footer class="footer"></footer>

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