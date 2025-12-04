<?php
include_once("conexao.php");


$sql = "SELECT status, COUNT(*) AS total 
        FROM ordem_servico 
        GROUP BY status";
$result = $conn->query($sql);

$status = [];
$totais = [];

while ($row = $result->fetch_assoc()) {
    $status[] = $row['status'];
    $totais[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gráfico - Ordens de Serviço</title>

<div class="actions print-hide">
      <a href="PaginaInicial.php"><button class="secondary">Voltar ao sistema</button></a>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    body{
        font-family: Arial;
        background: #f0f0f0;
        padding: 30px;
    }

    .box{
        width: 60%;
        margin: auto;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px #bbb;
    }

    h2{
        text-align: center;
        margin-bottom: 20px;
    }
    .actions{display:flex;gap:8px;}
</style>

</head>

<body>
    <div class="box">
        <h2>Ordens de Serviço por Status</h2>
        <canvas id="grafico"></canvas>
    </div>

<script>
    const ctx = document.getElementById('grafico');

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($status); ?>,
            datasets: [{
                data: <?php echo json_encode($totais); ?>,
                borderWidth: 1
            }]
        }
    });
</script>

</body>
</html>
