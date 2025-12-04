<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('conexao.php');

$sql = "SELECT id_ordem, descricao, data_entrada, valor, forma_pgt, garantia, id_relogio, status FROM ordem_servico";
$result = $conn->query($sql);

$sql = "
    SELECT 
        os.*,                                     
        r.marca, r.modelo,                        
        c.nome AS nome_cliente,                   
        c.sobrenome AS sobrenome_cliente,
        os.foto_entrada,
        os.foto_saida
    FROM 
        ordem_servico os
    JOIN 
        relogio r ON os.id_relogio = r.id_relogio 
    JOIN 
        cliente c ON r.id_cliente = c.id_cliente
    ORDER BY os.id_ordem DESC
";

$result = $conn->query($sql);
$result = $conn->query($sql);
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Ordens de Serviços - Relojoaria</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="stylelogin.css"> 
    <style>
   

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap; 
}


.status-badge.status-EmAnálise,
.status-badge.status-Aguardando {
    background-color: #ffc107; 
    color: #343a40;
}


.status-badge.status-Emconserto {
    background-color: #007bff; 
    color: var(--cor-clara);
}


.status-badge.status-Concluído,
.status-badge.status-Aguardando retirada {
    background-color: #28a745; 
    color: var(--cor-clara);
}



body.dark-mode .status-badge.status-EmAnálise,
body.dark-mode .status-badge.status-Aguardando {
    background-color: #ffd700; 
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
    transform: translateY(-1px); /
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
    <h1>Lista de Ordens de Serviços</h1>

    <div class="tabela-responsiva">
        <table>
            <thead>
               <tr>
    <th>ID</th>
    <th>Descricão</th>
    <th>Data Entrada</th>
    <th>Valor</th>
    <th>Pagamento</th>
    <th>Garantia</th>
    <th>Relógio</th>      <th>Cliente</th>      <th>Status</th>
    <th>Foto Entrada</th>   <th>Foto Saída</th>     
    <th>Ações</th>
</tr>
            </thead>
           <tbody>
<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        echo "<tr>";
        echo "<td>{$row['id_ordem']}</td>";
        echo "<td>{$row['descricao']}</td>";
        echo "<td>{$row['data_entrada']}</td>";

        echo "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";

        echo "<td>{$row['forma_pgt']}</td>";
        echo "<td>{$row['garantia']}</td>";

        echo "<td>{$row['marca']} {$row['modelo']}</td>";

        echo "<td>{$row['nome_cliente']} {$row['sobrenome_cliente']}</td>";

        
        echo "<td><span class='status-badge status-{$row['status']}'>{$row['status']}</span></td>";

       

echo "<td>";
if (!empty($row['foto_entrada'])) {
    
    echo "<img class='thumb' src='{$row['foto_entrada']}' onclick=\"showPopup('{$row['foto_entrada']}')\">";

} else {
    echo "N/A";
}
echo "</td>";

        
        echo "<td>";
        if (!empty($row['foto_saida'])) {
            echo "<img class='thumb' src='{$row['foto_saida']}' onclick=\"showPopup('{$row['foto_saida']}')\">";

        } else {
            echo "N/A";
        }
        echo "</td>";

        
        echo "<td>
                <a href='EditarOrdem.php?id={$row['id_ordem']}' class='btn-acao editar'>Editar</a>
                <a href='ExcluirOrdem.php?id={$row['id_ordem']}' class='btn-acao excluir' onclick='return confirm(\"Tem certeza que deseja excluir?\")'>Excluir</a>
              </td>";

        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='12' style='text-align:center;'>Nenhuma Ordem de Serviço Cadastrada</td></tr>";
}
?>
</tbody>

        </table>
    </div>
    
    <p class="link-voltar"><a href="PaginaInicial.php">Voltar ao início</a></p>

</main>
</div>


<footer class="footer">
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
function showPopup(src) {
    const overlay = document.getElementById('img-popup-overlay');
    const popupImg = document.getElementById('popup-img');


    popupImg.src = src;
    overlay.classList.remove('hidden');
    overlay.classList.add('visible');

    
    document.addEventListener('keydown', escListener);
}

function closePopup() {
    const overlay = document.getElementById('img-popup-overlay');
    overlay.classList.remove('visible');
    overlay.classList.add('hidden');

    
    document.removeEventListener('keydown', escListener);


    document.getElementById('popup-img').src = '';
}

function escListener(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        closePopup();
    }
}
</script>
<div id="img-popup-overlay" class="hidden" onclick="closePopup()">
    <img id="popup-img" src="" alt="Imagem ampliada" onclick="event.stopPropagation()">
</div>

</body>
</html>