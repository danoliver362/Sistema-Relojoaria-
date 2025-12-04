<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('conexao.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $cpf = $_POST['cpf'];
    $tipo = $_POST['tipo'];
    $telefone = $_POST['telefone'];

    $sql = "INSERT INTO cliente (nome, sobrenome, cpf, tipo, telefone)
            VALUES ('$nome', '$sobrenome', '$cpf', '$tipo', '$telefone')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Cliente cadastrado com sucesso!');</script>";
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente - Relojoaria</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylelogin.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
                input[type="text"],
input[type="password"],
select { /* Adicionado o SELECT aqui */
    padding: 10px;
    border: 1px solid var(--cor-borda);
    border-radius: 4px;
    font-size: 1rem;
    background-color: var(--cor-fundo); 
    color: var(--cor-texto);
    width: 100%; /* Garante que ocupem 100% da largura do formulário */
    margin-bottom: 15px; /* Espaço após o campo */
    transition: border-color 0.3s, background-color 0.3s;
}
input[type="text"]:focus,
input[type="password"]:focus,
select:focus {
    border-color: var(--cor-primaria);
    outline: none;
}
.botoes {
    margin-top: 25px;
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

button {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 700;
    flex: 1;
    transition: background-color 0.3s, opacity 0.3s;
}

button[type="submit"] {
    background-color: var(--cor-primaria); 
    color: var(--cor-clara);
}

button[type="reset"], .btn-secundario {
    background-color: var(--cor-secundaria); 
    color: var(--cor-clara);
}

button:hover {
    opacity: 0.9;
}
.link-voltar {
    margin-top: 20px;
    text-align: center;
}

.link-voltar a {
    color: var(--cor-secundaria);
    font-weight: 600;
    font-size: 0.9rem;
    transition: color 0.3s;
}

.link-voltar a:hover {
    color: var(--cor-primaria);
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
            <a href="PaginaInicial.php">INÍCIO</a> 
        </nav>
        <button id="darkModeToggle" class="dark-mode-toggle" style="background: none; border: none; cursor: pointer; color: var(--cor-escura); font-size: 1.2rem;">
            ☀️
        </button>
    </div>
</header>

<main>
    <h1>Cadastrar Cliente</h1>

    <form method="POST">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required>

        <label for="sobrenome">Sobrenome</label>
        <input type="text" id="sobrenome" name="sobrenome" required>

        <label for="cpf">CPF (11 dígitos)</label>
        <input type="text" id="cpf" name="cpf" maxlength="14" onblur="conferirCPF()" oninput="mascaraCPF(this)" required>

        <label for="tipo">Tipo de Cliente</label>
        <select id="tipo" name="tipo">
            <option value="Varejo">Varejo</option>
            <option value="Atacado">Atacado</option>
        </select>

        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" oninput="mascaraTelefone(this)">

        <div class="botoes">
            <button type="submit">CADASTRAR</button>
            <button type="reset" class="btn-secundario">LIMPAR</button>
        </div>
    </form>
    
    <p class="link-voltar"><a href="PaginaInicial.php">Voltar ao início</a></p>

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

     function mascaraCPF(campo) {
        let cpf = campo.value.replace(/\D/g, "");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2");
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        campo.value = cpf;
    }

    
    function mascaraTelefone(campo) {
        let tel = campo.value.replace(/\D/g, "");
        tel = tel.replace(/^(\d{2})(\d)/, "($1) $2");
        tel = tel.replace(/(\d{5})(\d)/, "$1-$2");
        campo.value = tel;
    }
     function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]/g, "");

        if (cpf.length !== 11) return false;
        if (/^(\d)\1{10}$/.test(cpf)) return false;

        let soma = 0;
        for (let i = 0; i < 9; i++) soma += cpf[i] * (10 - i);
        let dig1 = 11 - (soma % 11);
        dig1 = dig1 > 9 ? 0 : dig1;
        if (dig1 != cpf[9]) return false;

        soma = 0;
        for (let i = 0; i < 10; i++) soma += cpf[i] * (11 - i);
        let dig2 = 11 - (soma % 11);
        dig2 = dig2 > 9 ? 0 : dig2;
        if (dig2 != cpf[10]) return false;

        return true;
    }

    function conferirCPF() {
        const campo = document.getElementById("cpf");
        let cpf = campo.value;

        if (!validarCPF(cpf)) {
            alert("CPF inválido! Verifique e tente novamente.");
            campo.value = "";
            campo.focus();
        }
    }
</script>

</body>
</html>
