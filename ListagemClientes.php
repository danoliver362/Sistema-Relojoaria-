<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('conexao.php');

$sql = "SELECT id_cliente, nome, sobrenome, cpf, tipo, telefone FROM cliente";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes - Relojoaria</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="stylelogin.css"> 
 <style>
    
tbody tr td:last-child {
    text-align: center; 
    white-space: nowrap; 
}
.btn-acao {
    display: inline-block;
    padding: 6px 10px; 
    margin: 3px; 
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    transition: transform 0.2s, opacity 0.2s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
}

.btn-acao:hover {
    transform: translateY(-1px); 
    opacity: 0.9;
}


.editar {
    background-color: #ffeb3b; 
    color: #343a40;
}

.excluir {
    background-color: #dc3545;
    color: var(--cor-clara);
}
body.dark-mode .editar {
    background-color: #ffeb3b; 
    color: #343a40;
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

<main class="listagem-dados">
    <h1>Lista de Clientes</h1>

    <div class="tabela-responsiva">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Sobrenome</th>
                    <th>CPF</th>
                    <th>Tipo</th>
                    <th>Telefone</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                if (isset($result) && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id_cliente']}</td>";
                        echo "<td>{$row['nome']}</td>";
                        echo "<td>{$row['sobrenome']}</td>";
                        echo "<td>{$row['cpf']}</td>";
                        echo "<td>{$row['tipo']}</td>";
                        echo "<td>{$row['telefone']}</td>";
                        echo "<td>
                                <a href='EditarCliente.php?id={$row['id_cliente']}' class='btn-acao editar'>Editar</a>
                                <a href='ExcluirCliente.php?id={$row['id_cliente']}' class='btn-acao excluir' onclick='return confirm(\"Tem certeza que deseja excluir?\")'>Excluir</a>
                            </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align: center;'>Nenhum cliente cadastrado.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
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
</script>

</body>
</html>
