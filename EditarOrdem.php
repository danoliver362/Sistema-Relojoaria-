<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('conexao.php');

// =========================================================================
// 1. VERIFICAÇÃO E CARREGAMENTO DE DADOS INICIAL
// =========================================================================

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID de Ordem de Serviço inválido.");
}

// 1.1. Consulta segura para buscar os dados da OS (incluindo as fotos)
// As colunas de foto foram adicionadas aqui para garantir que a variável $row as contenha.
$stmt = $conn->prepare("SELECT 
    id_ordem, descricao, data_entrada, valor, forma_pgt, garantia, id_relogio, status, 
    foto_entrada, foto_saida 
    FROM ordem_servico WHERE id_ordem = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("OS não encontrada.");
}

// O $row principal agora tem todos os dados, incluindo os caminhos de foto antigos.


// 1.2. Consulta para buscar todos os Relógios E Clientes
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


// =========================================================================
// 2. PROCESSAMENTO DO FORMULÁRIO (POST)
// =========================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 🚨 CORREÇÃO PRINCIPAL: COLETAR AS VARIÁVEIS DE $_POST
    $descricao = $_POST['descricao'] ?? '';
    $data_entrada = $_POST['data_entrada'] ?? '';
    $valor = $_POST['valor'] ?? 0.00;
    $forma_pgt = $_POST['forma_pgt'] ?? '';
    $garantia = $_POST['garantia'] ?? '';
    $status = $_POST['status'] ?? '';
    $id_relogio = $_POST['id_relogio'] ?? 0;
    $id_os_post = $_POST['id_ordem'] ?? $id; // ID enviado por campo hidden ou o ID do GET

    // --- 2.1. Função de Upload (Mantida) ---
    function handle_upload($file_key, $caminho_antigo, $id_os, $conn) {
        
        $caminho_foto = $caminho_antigo; // Mantém a foto antiga por padrão
        
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] == 0) {
            
            $diretorio_destino = "uploads/ordens_servico/";
            
            if (!is_dir($diretorio_destino)) {
                mkdir($diretorio_destino, 0777, true);
            }
            
            $extensao = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
            $nome_arquivo = $file_key . "_" . $id_os . "_" . uniqid() . "." . $extensao;
            $caminho_completo = $diretorio_destino . $nome_arquivo;

            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $caminho_completo)) {
                
                // Exclui a foto antiga se houver uma nova
                if (!empty($caminho_antigo) && file_exists($caminho_antigo)) {
                     @unlink($caminho_antigo); 
                }
                
                $caminho_foto = $caminho_completo; 
            }

        }
        return $caminho_foto;
        // ... (Dentro do if (POST) no EditarOrdem.php)

// 1. Chamar a função para cada foto
$id_os_post = $_POST['id_ordem'] ?? $id;

// AQUI: Verifique se a sua variável $caminho_completo está sendo definida corretamente dentro da função.
// E se a função está retornando este valor.

$foto_entrada_caminho = handle_upload('foto_entrada', $row['foto_entrada'], $id_os_post, $conn);
$foto_saida_caminho = handle_upload('foto_saida', $row['foto_saida'], $id_os_post, $conn);

// 2. Consulta SQL de UPDATE
$sql_update = "UPDATE ordem_servico SET 
    // ... (outros campos)
    foto_entrada = '{$foto_entrada_caminho}', /* DEVE CONTER O CAMINHO COMPLETO */
    foto_saida = '{$foto_saida_caminho}'     /* DEVE CONTER O CAMINHO COMPLETO */
    WHERE id_ordem = {$id_os_post}";
// ...
    }
    
    // 2.2. Chamar a função para cada foto
    // Agora, $row['foto_entrada'] e $row['foto_saida'] existem e são usadas como caminhos antigos.
    $foto_entrada_caminho = handle_upload('foto_entrada', $row['foto_entrada'], $id_os_post, $conn);
    $foto_saida_caminho = handle_upload('foto_saida', $row['foto_saida'], $id_os_post, $conn);

    
    // --- 2.3. Consulta SQL de UPDATE ---
    $sql_update = "UPDATE ordem_servico SET 
        descricao = '{$descricao}', 
        data_entrada = '{$data_entrada}', 
        valor = '{$valor}', 
        forma_pgt = '{$forma_pgt}', 
        garantia = '{$garantia}',
        status = '{$status}',
        id_relogio = '{$id_relogio}',
        foto_entrada = '{$foto_entrada_caminho}', /* USAMOS A VARIÁVEL DO NOVO CAMINHO */
        foto_saida = '{$foto_saida_caminho}'     /* USAMOS A VARIÁVEL DO NOVO CAMINHO */
        WHERE id_ordem = {$id_os_post}"; // 🚨 Atenção: Usei 'ordem_servico' aqui, ajuste se for 'ordens_servico'
        
    if ($conn->query($sql_update) === TRUE) {
        // Redireciona para recarregar os dados (incluindo as novas fotos)
        header("Location: EditarOrdem.php?id={$id_os_post}&sucesso=true");
        exit();
    } else {
        die("Erro ao atualizar OS: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ordem de Serviço - Relojoaria</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylelogin.css"> 
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
    </style>
</head>
<body>

<header>
    <div class="logo">
        <a>RELOJOARIA</a>
    </div>
    <div class="header-right"> 
        <nav>
            <a href="ListagemOrdem.php">VOLTAR</a> 
        </nav>
        <button id="darkModeToggle" class="dark-mode-toggle" style="background: none; border: none; cursor: pointer; color: var(--cor-escura); font-size: 1.2rem;">
            ☀️
        </button>
    </div>
</header>

<main>
    <h1>Editar Ordem de Serviço</h1>
    
    <form method="post" enctype="multipart/form-data">
        
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva aqui qual o serviço será feito no relógio"><?= htmlspecialchars($row['descricao']) ?></textarea>

        <label for="data_entrada">Data de Entrada</label>
        <input type="date" id="data_entrada" name="data_entrada" value="<?= htmlspecialchars($row['data_entrada']) ?>" required>
        
        <label for="valor">Valor</label>
        <input type="text" id="valor" name="valor" value="<?= htmlspecialchars($row['valor']) ?>" required>
        
        <label for="forma_pgt">Forma de Pagamento</label>
        <select id="forma_pgt" name="forma_pgt" required>
            <?php $pgtAtual = htmlspecialchars($row['forma_pgt']); ?>
            <option value="">Selecione a forma de pagamento</option>
            <option value="Dinheiro" <?= ($pgtAtual == 'Dinheiro') ? 'selected' : '' ?>>Dinheiro</option>
            <option value="Cartão de Crédito" <?= ($pgtAtual == 'Cartão de Crédito') ? 'selected' : '' ?>>Cartão de Crédito</option>
            <option value="Cartão de Débito" <?= ($pgtAtual == 'Cartão de Débito') ? 'selected' : '' ?>>Cartão de Débito</option>
            <option value="PIX" <?= ($pgtAtual == 'PIX') ? 'selected' : '' ?>>PIX</option>
        </select>

        <label for="garantia">Garantia</label>
        <input type="text" id="garantia" name="garantia" value="<?= htmlspecialchars($row['garantia']) ?>" required>
        
        <label for="status">Status</label>
        <select id="status" name="status" required>
            <?php $statusAtual = htmlspecialchars($row['status']); ?>
            <option value="">Selecione um tipo</option>
            <option value="Em Análise" <?= ($statusAtual == 'Em Análise') ? 'selected' : '' ?>>Em Análise</option>
            <option value="Em conserto" <?= ($statusAtual == 'Em conserto') ? 'selected' : '' ?>>Em conserto</option>
            <option value="Concluído" <?= ($statusAtual == 'Concluído') ? 'selected' : '' ?>>Concluído</option>
            <option value="Aguardando retirada" <?= ($statusAtual == 'Aguardando retirada') ? 'selected' : '' ?>>Aguardando retirada</option>
        </select>
        
        <label for="id_relogio">Relógio & Cliente</label>
        <select id="id_relogio" name="id_relogio" required>
            <option value="">Selecione o Relógio (Cliente - Marca Modelo)</option>
            <?php
            $relogioAtual = htmlspecialchars($row['id_relogio']);
            
            if (isset($result_relogios) && $result_relogios->num_rows > 0) {
                $result_relogios->data_seek(0); 
                while ($relogio_row = $result_relogios->fetch_assoc()) {
                    $display_text = "{$relogio_row['nome_cliente']} {$relogio_row['sobrenome_cliente']} - {$relogio_row['marca']} {$relogio_row['modelo']}";
                    $selected = ($relogio_row['id_relogio'] == $relogioAtual) ? 'selected' : '';
                    echo "<option value='{$relogio_row['id_relogio']}' {$selected}>{$display_text}</option>";
                }
            } else {
                echo "<option disabled>Nenhum relógio encontrado</option>";
            }
            ?>
        </select>
        <input type="hidden" name="id_ordem" value="<?= htmlspecialchars($row['id_ordem']) ?>">
        <br>
       <div class="arquivos-upload">
    
    <label for="foto_entrada">Foto de Entrada:</label>
    <?php if (!empty($row['foto_entrada'])): ?>
        <div class="foto-atual">
            <p>Atual:</p>
            <img src="<?= htmlspecialchars($row['foto_entrada']) ?>" alt="Foto de entrada" style="max-width: 150px; height: auto; display: block; margin-bottom: 5px;"> 
        </div>
    <?php endif; ?>
    <input type="file" id="foto_entrada" name="foto_entrada" accept="image/*">
    
    <br>

    <label for="foto_saida">Foto de Saída (Após o serviço):</label>
    <?php if (!empty($row['foto_saida'])): ?>
        <div class="foto-atual">
            <p>Atual:</p>
            <img src="<?= htmlspecialchars($row['foto_saida']) ?>" alt="Foto de saída" style="max-width: 150px; height: auto; display: block; margin-bottom: 5px;"> 
        </div>
    <?php endif; ?>
    <input type="file" id="foto_saida" name="foto_saida" accept="image/*">
    
</div>

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