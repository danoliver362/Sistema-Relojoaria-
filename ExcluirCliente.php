<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('conexao.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID inválido.");
}


$stmt = $conn->prepare("SELECT id_cliente FROM cliente WHERE id_cliente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Cliente não encontrado.");
}


$sql = "
    DELETE os 
    FROM ordem_servico os
    INNER JOIN relogio r ON os.id_relogio = r.id_relogio
    WHERE r.id_cliente = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();


$stmt = $conn->prepare("DELETE FROM relogio WHERE id_cliente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();


$stmt = $conn->prepare("DELETE FROM cliente WHERE id_cliente = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Cliente e dados relacionados excluídos com sucesso!!'); window.location='ListagemClientes.php';</script>";
    exit;
} else {
    echo "Erro ao excluir: " . $stmt->error;
}
?>


