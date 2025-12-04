<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit;
}

require_once "conexao.php";


require_once "dompdf/autoload.inc.php";

use Dompdf\Dompdf;


function table_exists($conn, $name) {
    $sql = "SELECT COUNT(*) AS cnt FROM information_schema.tables
            WHERE table_schema = DATABASE() AND table_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return ($res && $res['cnt'] > 0);
}

$tbl_clientes = table_exists($conn, 'clientes') ? 'clientes' : (table_exists($conn, 'cliente') ? 'cliente' : null);
$tbl_relogios = table_exists($conn, 'relogios') ? 'relogios' : (table_exists($conn, 'relogio') ? 'relogio' : null);
$tbl_ordens   = table_exists($conn, 'ordens_servico') ? 'ordens_servico' : (table_exists($conn, 'ordem_servico') ? 'ordem_servico' : null);


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


$res_clientes = $conn->query(
    "SELECT * FROM `$tbl_clientes` ORDER BY id_cliente DESC LIMIT $limit"
);

$res_relogios = $conn->query(
    "SELECT r.*, c.nome AS cliente_nome, c.sobrenome AS cliente_sobrenome
     FROM `$tbl_relogios` r
     LEFT JOIN `$tbl_clientes` c ON r.id_cliente = c.id_cliente
     ORDER BY r.id_relogio DESC LIMIT $limit"
);

$res_ordens = $conn->query(
    "SELECT o.*, r.marca AS rel_marca, r.modelo AS rel_modelo
     FROM `$tbl_ordens` o
     LEFT JOIN `$tbl_relogios` r ON o.id_relogio = r.id_relogio
     ORDER BY o.id_ordem DESC LIMIT $limit"
);

$nome_usuario = $_SESSION['usuario_nome'] ?? 'Usuário';



$html = "
<style>
body { font-family: Arial, sans-serif; }
h1 { font-size: 20px; text-align: center; }
h3 { margin-top:20px; background:#eee; padding:6px; }
table { width:100%; border-collapse: collapse; font-size:12px; }
th, td { border:1px solid #ccc; padding:6px; }
th { background:#f1f1f1; }
img.thumb { width:50px; height:35px; object-fit:cover; border-radius:4px; }
.small { color:#666; font-size:12px; }
</style>

<h1>Relatório do Sistema — Relojoaria</h1>
<p>Gerado por <strong>{$nome_usuario}</strong> — ".date('d/m/Y H:i')."</p>

<h3>Resumo</h3>
<table>
  <tr><th>Total de Clientes</th><td>{$total_clientes}</td></tr>
  <tr><th>Total de Relógios</th><td>{$total_relogios}</td></tr>
  <tr><th>Total de Ordens</th><td>{$total_ordens}</td></tr>
</table>

<h3>Últimos Clientes</h3>
<table>
<thead><tr><th>ID</th><th>Nome</th><th>CPF</th><th>Telefone</th></tr></thead>
<tbody>";

if ($res_clientes->num_rows > 0) {
    while($c = $res_clientes->fetch_assoc()) {
        $html .= "<tr>
            <td>{$c['id_cliente']}</td>
            <td>{$c['nome']} {$c['sobrenome']}</td>
            <td>{$c['cpf']}</td>
            <td>{$c['telefone']}</td>
        </tr>";
    }
} else {
    $html .= "<tr><td colspan='4'>Nenhum cliente cadastrado.</td></tr>";
}

$html .= "
</tbody>
</table>

<h3>Últimos Relógios</h3>
<table>
<thead><tr><th>ID</th><th>Relógio</th><th>Cliente</th><th>Série</th><th>Foto</th></tr></thead>
<tbody>";

while($r = $res_relogios->fetch_assoc()) {

    
   $foto = "—";
if (!empty($r['foto_relogio'])) {
    $serverPath = __DIR__ . '/' . $r['foto_relogio']; 

    if (file_exists($serverPath)) {
        $base64 = base64_encode(file_get_contents($serverPath));
        $foto = '<img width="80" src="data:image/jpeg;base64,' . $base64 . '" />';
    }
}

    $html .= "<tr>
        <td>{$r['id_relogio']}</td>
        <td>{$r['marca']} {$r['modelo']}</td>
        <td>{$r['cliente_nome']} {$r['cliente_sobrenome']}</td>
        <td>{$r['num_serie']}</td>
        <td>{$foto}</td>
    </tr>";
}

$html .= "
</tbody>
</table>

<h3>Últimas Ordens</h3>
<table>
<thead><tr><th>ID</th><th>Descrição</th><th>Data</th><th>Valor</th><th>Relógio</th><th>Status</th><th>Foto</th></tr></thead>
<tbody>";

while($o = $res_ordens->fetch_assoc()) {

    $campos = ['foto_entrada', 'foto_saida'];
$foto = "—";

foreach ($campos as $campo) {

    if (!empty($o[$campo])) {

        
        $serverPath = __DIR__ . '/' . $o[$campo];

        
        if (file_exists($serverPath) && is_file($serverPath)) {
            
            $mime = mime_content_type($serverPath);
            $base64 = base64_encode(file_get_contents($serverPath));
            
            $foto = '<img width="80" src="data:' . $mime . ';base64,' . $base64 . '" />';
            break; 
        }
    }
}

    

    $html .= "<tr>
        <td>{$o['id_ordem']}</td>
        <td>{$o['descricao']}</td>
        <td>{$o['data_entrada']}</td>
        <td>{$o['valor']}</td>
        <td>{$o['rel_marca']} {$o['rel_modelo']}</td>
        <td>{$o['status']}</td>
        <td>{$foto}</td>
    </tr>";
}

$html .= "
</tbody>
</table>

<p class='small'>Banco de dados: <strong>".$conn->query("SELECT DATABASE()")->fetch_row()[0]."</strong></p>
<p class='small'>PDF gerado automaticamente.</p>
";



$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("relatorio.pdf", ["Attachment" => false]);
exit;

?>
