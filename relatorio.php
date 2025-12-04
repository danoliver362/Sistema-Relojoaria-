<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'conexao.php';


function table_exists($conn, $name) {
    $sql = "SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return ($res && $res['cnt'] > 0);
}

$tbl_clientes = table_exists($conn, 'clientes') ? 'clientes' : (table_exists($conn, 'cliente') ? 'cliente' : null);
$tbl_relogios = table_exists($conn, 'relogios') ? 'relogios' : (table_exists($conn, 'relogio') ? 'relogio' : null);
$tbl_ordens   = table_exists($conn, 'ordens_servico') ? 'ordens_servico' : (table_exists($conn, 'ordem_servico') ? 'ordem_servico' : null);

if (!$tbl_clientes || !$tbl_relogios || !$tbl_ordens) {
    
    die("Erro: não foi possível localizar as tabelas necessárias no banco de dados. Verifique se as tabelas de clientes, relógios e ordens existem. Detectados: clientes={$tbl_clientes}, relogios={$tbl_relogios}, ordens={$tbl_ordens}");
}


function get_count($conn, $table) {
    $sql = "SELECT COUNT(*) AS cnt FROM `$table`";
    $r = $conn->query($sql);
    $row = $r->fetch_assoc();
    return (int)$row['cnt'];
}

$total_clientes = get_count($conn, $tbl_clientes);
$total_relogios = get_count($conn, $tbl_relogios);
$total_ordens   = get_count($conn, $tbl_ordens);


$limit = 8;


$sql = "SELECT * FROM `$tbl_clientes` ORDER BY id_cliente DESC LIMIT $limit";
$res_clientes = $conn->query($sql);


$sql = "SELECT r.*, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome
        FROM `$tbl_relogios` r
        LEFT JOIN `$tbl_clientes` c ON r.id_cliente = c.id_cliente
        ORDER BY r.id_relogio DESC LIMIT $limit";
$res_relogios = $conn->query($sql);


$sql = "SELECT o.*, r.marca AS rel_marca, r.modelo AS rel_modelo
        FROM `$tbl_ordens` o
        LEFT JOIN `$tbl_relogios` r ON o.id_relogio = r.id_relogio
        ORDER BY o.id_ordem DESC LIMIT $limit";
$res_ordens = $conn->query($sql);

$nome_usuario = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Relatório — Sistema Relojoaria</title>
<style>
  :root{
    --bg:#f5f7fb; --card:#fff; --text:#111; --muted:#666; --accent:#0066cc;
  }
  @media (prefers-color-scheme: dark){
    :root{ --bg:#0b0f14; --card:#0f1720; --text:#e6eef8; --muted:#9bb0d1; --accent:#4f9dff; }
  }

  body{font-family:Inter,system-ui,Arial; background:var(--bg); color:var(--text); margin:0; padding:20px;}
  .container{max-width:1200px;margin:0 auto;}
  header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
  header h1{font-size:20px;margin:0;}
  .meta{color:var(--muted); font-size:14px;}
  .actions{display:flex;gap:8px;}
  button{background:var(--accent);color:#fff;border:none;padding:8px 12px;border-radius:6px;cursor:pointer;}
  button.secondary{background:transparent;color:var(--accent);border:1px solid rgba(0,0,0,0.08);}
  .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:18px;}
  .card{background:var(--card);padding:16px;border-radius:8px;box-shadow:0 4px 14px rgba(2,6,23,0.06);}
  .card h3{margin:0 0 8px;font-size:14px;}
  .big{font-size:28px;font-weight:700;}
  table{width:100%;border-collapse:collapse;}
  th,td{padding:8px 10px;border-bottom:1px solid rgba(0,0,0,0.06);text-align:left;font-size:14px;}
  th{background:transparent;color:var(--muted);font-weight:600;}
  
img.thumb {
    width: 70px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: zoom-in;
}


img.thumb:hover {
    transform: scale(2.5);        
    z-index: 999;                 
    position: relative;
    box-shadow: 0 6px 14px rgba(0,0,0,0.3);
}

  .small{font-size:13px;color:var(--muted);}
  footer{margin-top:20px;text-align:center;color:var(--muted);font-size:13px;}
  .print-hide{display:inline-block;}
  @media print {
    body{padding:8mm;}
    .actions, .print-hide{display:none;}
    header h1{font-size:18px;}
    .card{box-shadow:none;border:1px solid #ddd;}
  }
#img-popup-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;

    display: flex;
    justify-content: center;
    align-items: center;

    background: rgba(0,0,0,0.8);
    z-index: 999999 !important;

    opacity: 0;
    pointer-events: none;
    transition: opacity .2s ease;
}

#img-popup-overlay.visible {
    opacity: 1;
    pointer-events: auto;
}

#img-popup-overlay img {
    max-width: 90%;
    max-height: 90%;
    border-radius: 10px;
    box-shadow: 0 0 30px rgba(0,0,0,0.5);
}


.thumb {
    width: 70px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    cursor: zoom-in;
}
.thumb:hover {
    transform: scale(2.5);
    z-index: 999;
    position: relative;
    box-shadow: 0 6px 14px rgba(0,0,0,0.3);
}
</style>
</head>
<body>
<div class="container">
  <header>
    <div>
      <h1>Relatório do Sistema — Relojoaria</h1>
      <div class="meta">Gerado por <strong><?=htmlspecialchars($nome_usuario)?></strong> — <?=date('d/m/Y H:i')?></div>
    </div>

    <div class="actions print-hide">
      <button onclick="window.print()">Imprimir</button>
      <a href="relatorio_pdf.php" target="_blank"><button>Gerar PDF</button></a>
      <a href="PaginaInicial.php"><button class="secondary">Voltar ao sistema</button></a>
    </div>
  </header>

  
  <section class="grid">
    <div class="card">
      <h3>Total de Clientes</h3>
      <div class="big"><?= $total_clientes ?></div>
      <div class="small">Registros cadastrados na tabela de clientes</div>
    </div>

    <div class="card">
      <h3>Total de Relógios</h3>
      <div class="big"><?= $total_relogios ?></div>
      <div class="small">Relógios cadastrados</div>
    </div>

    <div class="card">
      <h3>Total de Ordens</h3>
      <div class="big"><?= $total_ordens ?></div>
      <div class="small">Ordens de serviço registradas</div>
    </div>
  </section>

  
  <section class="card" style="margin-bottom:12px;">
    <h3>Últimos Clientes (<?= $limit ?>)</h3>
    <table>
      <thead><tr><th>ID</th><th>Nome</th><th>CPF</th><th>Telefone</th></tr></thead>
      <tbody>
      <?php if ($res_clientes && $res_clientes->num_rows>0): ?>
        <?php while($c = $res_clientes->fetch_assoc()): ?>
          <tr>
            <td><?=htmlspecialchars($c['id_cliente'])?></td>
            <td><?=htmlspecialchars(($c['nome'] ?? '') . ' ' . ($c['sobrenome'] ?? ''))?></td>
            <td><?=htmlspecialchars($c['cpf'] ?? '')?></td>
            <td><?=htmlspecialchars($c['telefone'] ?? '')?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4">Nenhum cliente cadastrado.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </section>

  <section class="card" style="margin-bottom:12px;">
    <h3>Últimos Relógios (<?= $limit ?>)</h3>
    <table>
      <thead><tr><th>ID</th><th>Relógio</th><th>Cliente</th><th>Série</th><th>Foto</th></tr></thead>
      <tbody>
      <?php if ($res_relogios && $res_relogios->num_rows>0): ?>
        <?php while($r = $res_relogios->fetch_assoc()): ?>
          <tr>
            <td><?=htmlspecialchars($r['id_relogio'])?></td>
            <td><?=htmlspecialchars($r['marca'] . ' ' . $r['modelo'])?></td>
            <td><?=htmlspecialchars(trim(($r['cliente_nome'] ?? '') . ' ' . ($r['cliente_sobrenome'] ?? '')))?></td>
            <td><?=htmlspecialchars($r['num_serie'] ?? '')?></td>
            <td>
    <?php if (!empty($r['foto_relogio'])): ?>
<img class="thumb"
     src="<?= htmlspecialchars($r['foto_relogio'], ENT_QUOTES, 'UTF-8') ?>"
     onclick="showPopup('<?= htmlspecialchars($r['foto_relogio'], ENT_QUOTES, 'UTF-8') ?>')"
     alt="foto do relógio">
<?php else: ?>
    <span class="small">—</span>
<?php endif; ?>



            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5">Nenhum relógio cadastrado.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </section>

  <section class="card">
    <h3>Últimas Ordens (<?= $limit ?>)</h3>
    <table>
      <thead><tr><th>ID</th><th>Descrição</th><th>Data</th><th>Valor</th><th>Relógio</th><th>Status</th><th>Foto</th></tr></thead>
      <tbody>
      <?php if ($res_ordens && $res_ordens->num_rows>0): ?>
        <?php while($o = $res_ordens->fetch_assoc()): ?>
          <tr>
            <td><?=htmlspecialchars($o['id_ordem'])?></td>
            <td><?=htmlspecialchars($o['descricao'])?></td>
            <td><?=htmlspecialchars($o['data_entrada'])?></td>
            <td><?=htmlspecialchars($o['valor'])?></td>
            <td><?=htmlspecialchars((($o['rel_marca'] ?? '') . ' ' . ($o['rel_modelo'] ?? '')))?></td>
            <td><?=htmlspecialchars($o['status'] ?? '')?></td>
            <td>
            <?php
$possible = ['foto', 'foto_entrada', 'foto_saida'];
$found = false;

foreach ($possible as $p) {
    if (!empty($o[$p])) {
     echo '<img class="thumb" src="'.htmlspecialchars($o[$p], ENT_QUOTES, 'UTF-8').'" onclick="showPopup(\''.htmlspecialchars($o[$p], ENT_QUOTES, 'UTF-8').'\')" alt="foto os">';

        $found = true;
        break;
    }
}

if (!$found) {
    echo '<span class="small">—</span>';
}
?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">Nenhuma ordem de serviço cadastrada.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </section>

  <footer>
    <p>Relatório gerado a partir do banco: <strong><?= htmlspecialchars($conn->query("SELECT DATABASE()")->fetch_row()[0]) ?></strong></p>
    <p class="small">Imprima ou gere o PDF. Desenvolvido por Dan.</p>
  </footer>
</div>
<script>
function showPopup(src) {
    const overlay = document.getElementById('img-popup-overlay');
    const popupImg = document.getElementById('popup-img');

    popupImg.src = src;
    overlay.classList.add('visible');

    document.addEventListener('keydown', escClose);
}

function closePopup() {
    const overlay = document.getElementById('img-popup-overlay');
    overlay.classList.remove('visible');
    document.getElementById('popup-img').src = "";
    document.removeEventListener('keydown', escClose);
}

function escClose(e) {
    if (e.key === "Escape") closePopup();
}



</script>
<div id="img-popup-overlay" class="hidden" onclick="closePopup()">
    <img id="popup-img" src="" alt="Imagem ampliada" onclick="event.stopPropagation()">
</body>
</html>
