<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('conexao.php');


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID inválido.");
}


$stmt = $conn->prepare("SELECT id_ordem FROM ordem_servico WHERE id_ordem = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("OS não encontrado.");
}


$stmt = $conn->prepare("DELETE FROM ordem_servico WHERE id_ordem = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('OS excluída com sucesso!'); window.location='ListagemOrdem.php';</script>";
    exit;
} else {
    echo "Erro ao excluir: " . $stmt->error;
}
?>
