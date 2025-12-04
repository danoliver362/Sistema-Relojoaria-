<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('conexao.php');

if ($_SERVER['REQUEST_METHOD']== "POST"){
    $descricao = $_POST['descricao'];
    $data_entrada = $_POST['data_entrada'];
    $valor = $_POST['valor'];
    $forma_pgt = $_POST['forma_pgt'];
    $garantia = $_POST['garantia'];
    $id_relogio = $_POST['id_relogio'];
    $status = $_POST['status'];
  
    $foto_entrada = $_FILES['foto_entrada']['name'] ?? null;
    $foto_saida   = $_FILES['foto_saida']['name'] ?? null;



$uploadDirServer = __DIR__ . '/uploads/ordens/';


$uploadDirWeb = 'uploads/ordens/';


if (!is_dir($uploadDirServer)) {
    
    mkdir($uploadDirServer, 0755, true);
}


function sanitize_filename($filename) {
    
    $filename = basename($filename);
    
    $filename = preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', $filename);
    return $filename;
}


$foto_entrada = null;
if (!empty($_FILES['foto_entrada']['name']) && $_FILES['foto_entrada']['error'] === UPLOAD_ERR_OK) {
    $origName = sanitize_filename($_FILES['foto_entrada']['name']);
    $uniqueName = time() . "_entrada_" . $origName;
    $serverPath = $uploadDirServer . $uniqueName;
    $webPath = $uploadDirWeb . $uniqueName; 

    if (move_uploaded_file($_FILES['foto_entrada']['tmp_name'], $serverPath)) {
        $foto_entrada = $webPath;
    } else {
        
        error_log("Falha ao mover foto_entrada para $serverPath");
    }
}

$foto_saida = null;
if (!empty($_FILES['foto_saida']['name']) && $_FILES['foto_saida']['error'] === UPLOAD_ERR_OK) {
    $origName = sanitize_filename($_FILES['foto_saida']['name']);
    $uniqueName = time() . "_saida_" . $origName;
    $serverPath = $uploadDirServer . $uniqueName;
    $webPath = $uploadDirWeb . $uniqueName;

    if (move_uploaded_file($_FILES['foto_saida']['tmp_name'], $serverPath)) {
        $foto_saida = $webPath;
    } else {
        error_log("Falha ao mover foto_saida para $serverPath");
    }
}




    $sql = "INSERT INTO ordem_servico(descricao, data_entrada, valor, forma_pgt, garantia, id_relogio, status, foto_entrada, foto_saida)
            VALUES ('$descricao', '$data_entrada', '$valor', '$forma_pgt', '$garantia', '$id_relogio', '$status', '$foto_entrada', '$foto_saida')";
            
            if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Ordem de Serviço cadastrada com sucesso!');</script>";
    } else {
        echo "Erro: " . $conn->error;
    }
}



$sql_relogios = "
    SELECT 
        r.id_relogio, 
        r.marca, 
        r.modelo, 
        c.nome AS nome_cliente,        
        c.sobrenome AS sobrenome_cliente 
    FROM 
        relogio r
    JOIN 
        cliente c ON r.id_cliente = c.id_cliente
    ORDER BY c.nome, r.marca
";

$result_relogios = $conn->query($sql_relogios);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Ordem de Serviço - Relojoaria</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="stylelogin.css">
    <style>
        
input[type="text"],
input[type="password"],
input[type="date"], 
select,
textarea{
    padding: 10px;
    border: 1px solid var(--cor-borda);
    border-radius: 4px;
    font-size: 1rem;
    background-color: var(--cor-fundo); 
    color: var(--cor-texto);
    width: 100%;
    margin-bottom: 15px; 
    transition: border-color 0.3s, background-color 0.3s;
    resize: vertical; 
}

input[type="text"]:focus,
input[type="password"]:focus,
input[type="date"]:focus,
select:focus,
textarea:focus {
    border-color: var(--cor-primaria);
    outline: none;
}

.arquivos-upload {
    margin-top: 20px;
    margin-bottom: 20px;
    padding: 15px;
    border: 1px dashed var(--cor-borda);
    border-radius: 4px;
}

.arquivos-upload label {
    display: block; 
    margin-top: 0;
    margin-bottom: 5px;
    color: var(--cor-escura);
}

body.dark-mode .arquivos-upload label {
    color: var(--cor-texto); 
}

.arquivos-upload input[type="file"] {
    width: 100%;
    padding: 5px 0;
    margin-bottom: 10px;
    font-size: 0.9rem;
    color: var(--cor-texto);
    background: transparent;
    border: none;
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
    <h1>Cadastrar Ordem de Serviço</h1>
    
    <form method="post" enctype="multipart/form-data">
        
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva aqui qual o serviço será feito no relógio"></textarea>

        <label for="data_entrada">Data de Entrada</label>
        <input type="date" id="data_entrada" name="data_entrada" required>
        
        <label for="valor">Valor</label>
        <input type="text" id="valor" name="valor" required>
        
        <label for="forma_pgt">Forma de Pagamento</label>
        <select id="forma_pgt" name="forma_pgt" required>
            <option value="">Selecione a forma de pagamento</option>
            <option value="Dinheiro">Dinheiro</option>
            <option value="Cartão de Crédito">Cartão de Crédito</option>
            <option value="Cartão de Débito">Cartão de Débito</option>
            <option value="PIX">PIX</option>
        </select>

        <label for="garantia">Garantia</label>
        <input type="text" id="garantia" name="garantia" required>
        
        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="">Selecione um tipo</option>
            <option value="Em Análise">Em Análise</option>
            <option value="Em conserto">Em conserto</option>
            <option value="Concluído">Concluído</option>
            <option value="Aguardando retirada">Aguardando retirada</option>
        </select>
        
      <label for="id_relogio">Relógio & Cliente</label>
<select id="id_relogio" name="id_relogio" required>
    <option value="">Selecione o Relógio (Cliente - Marca - Modelo)</option>
    <?php
    if ($result_relogios->num_rows > 0) {
        
        $result_relogios->data_seek(0); 
        
        while ($row = $result_relogios->fetch_assoc()) {
            $display_text = "{$row['nome_cliente']} {$row['sobrenome_cliente']} - {$row['marca']} {$row['modelo']}";
            echo "<option value='{$row['id_relogio']}'>{$display_text}</option>";
        }
    } else {
        echo "<option disabled>Nenhum relógio encontrado</option>";
    }
    ?>
</select>
        
        <div class="arquivos-upload">
            <label for="foto_entrada">Foto de Entrada:</label>
            <input type="file" id="foto_entrada" name="foto_entrada">
            
            <label for="foto_saida">Foto de Saída:</label>
            <input type="file" id="foto_saida" name="foto_saida">
        </div>

        <div class="botoes">
            <button type="submit">CADASTRAR ORDEM DE SERVIÇO</button>
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