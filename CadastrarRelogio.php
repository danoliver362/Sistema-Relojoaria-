<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include('conexao.php'); 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $num_serie = $_POST['num_serie'];
    $id_cliente = $_POST['id_cliente']; 

    $foto_relogio = $_FILES['foto_relogio']['name'] ?? null;

    $foto_relogio = null;

if (!empty($_FILES['foto_relogio']['name'])) {
    
    $nomeArquivo = time() . "_" . $_FILES['foto_relogio']['name'];

    
    $destino = "uploads/relogios/" . $nomeArquivo;

    
    if (move_uploaded_file($_FILES['foto_relogio']['tmp_name'], $destino)) {
        $foto_relogio = $destino;
    }
}


    $sql = "INSERT INTO relogio (marca, modelo, num_serie, id_cliente, foto_relogio)
            VALUES ('$marca', '$modelo', '$num_serie', '$id_cliente', '$foto_relogio')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Relogio cadastrado com sucesso!');</script>";
    } else {
        echo "Erro: " . $conn->error;
    }
}

$sql_clientes = "SELECT id_cliente, nome, sobrenome FROM cliente";
$result_clientes = $conn->query($sql_clientes);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Relógio - Relojoaria</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    <h1>Cadastrar Relógio</h1>
    
    <form method="post" enctype="multipart/form-data">
        
        <label for="marca">Marca</label>
        <input type="text" id="marca" name="marca" required>
        
        <label for="modelo">Modelo</label>
        <input type="text" id="modelo" name="modelo" required>
        
        <label for="num_serie">Número de Série</label>
        <input type="text" id="num_serie" name="num_serie" required>

        <label for="foto_relogio">Foto do Relógio:</label>
        <input type="file" id="foto_relogio" name="foto_relogio">
        
        <label for="id_cliente">Cliente</label>
        <select id="id_cliente" name="id_cliente" required>
            <option value="">Selecione o Cliente</option>
            <?php
            
            if (isset($result_clientes) && $result_clientes->num_rows > 0) {
                while ($row = $result_clientes->fetch_assoc()) {
                    echo "<option value='{$row['id_cliente']}'>{$row['nome']} {$row['sobrenome']}</option>";
                }
            } else {
                echo "<option disabled>Nenhum cliente cadastrado</option>";
            }
            ?>
        </select>
        
        <div class="botoes">
            <button type="submit">CADASTRAR RELÓGIO</button>
            <button type="reset" class="btn-secundario">LIMPAR</button>
        </div>
    </form>
    
    <p class="link-voltar"><a href="PaginaInicial.php">Voltar ao Início</a></p>

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