<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('conexao.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ListagemRelogio.php");
    exit();
}

$id_relogio = intval($_GET['id']);


$sql = "SELECT * FROM relogio WHERE id_relogio = $id_relogio";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Relógio não encontrado.");
}

$row = $result->fetch_assoc();

$sql_clientes = "SELECT id_cliente, nome, sobrenome FROM cliente ORDER BY nome ASC";
$result_clientes = $conn->query($sql_clientes);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $num_serie = $_POST['num_serie'];
    $id_cliente = $_POST['id_cliente'];
    $id_relogio_post = $_POST['id_relogio'];


    $caminho_foto = $row['foto_relogio'];

    if (isset($_FILES['foto_relogio']) && $_FILES['foto_relogio']['error'] == 0) {

        $diretorio = "uploads/relogios/";

        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        $ext = pathinfo($_FILES['foto_relogio']['name'], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid() . "." . $ext;
        $caminho_completo = $diretorio . $nome_arquivo;

        if (move_uploaded_file($_FILES['foto_relogio']['tmp_name'], $caminho_completo)) {

            if (!empty($row['foto_relogio']) && file_exists($row['foto_relogio'])) {
                unlink($row['foto_relogio']);
            }

            $caminho_foto = $caminho_completo;
        }
    }



    $sql_update = "UPDATE relogio SET
        marca = ?,
        modelo = ?,
        num_serie = ?,
        id_cliente = ?,
        foto_relogio = ?
        WHERE id_relogio = ?";

    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sssisi",
        $marca,
        $modelo,
        $num_serie,
        $id_cliente,
        $caminho_foto,
        $id_relogio_post
    );

    if ($stmt->execute()) {
        header("Location: EditarRelogio.php?id={$id_relogio_post}&sucesso=true");
        exit();
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Relógio</title>
    <link rel="stylesheet" href="stylelogin.css">
    <style>        input[type="text"],
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
}</style>
</head>
<body>

<header>
    <div class="logo"><a>RELOJOARIA</a></div>
    <div class="header-right">
        <nav><a href="ListagemRelogio.php">VOLTAR</a></nav>
        <button id="darkModeToggle" class="dark-mode-toggle" style="background: none; border: none; cursor: pointer; color: var(--cor-escura); font-size: 1.2rem;">
            ☀️
        </button>
    </div>
    </div>
</header>

<main>

    <h1>Editar Relógio</h1>

    <?php if(isset($_GET['sucesso'])): ?>
        <p class="sucesso">✔ Relógio atualizado com sucesso!</p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <label>Marca</label>
        <input type="text" name="marca" value="<?= htmlspecialchars($row['marca']) ?>" required>

        <label>Modelo</label>
        <input type="text" name="modelo" value="<?= htmlspecialchars($row['modelo']) ?>" required>

        <label>Número de Série</label>
        <input type="text" name="num_serie" value="<?= htmlspecialchars($row['num_serie']) ?>" required>

        <label>Foto do Relógio</label>
        <?php if (!empty($row['foto_relogio'])): ?>
            <img src="<?= $row['foto_relogio'] ?>" style="max-width:200px;margin-bottom:10px;">
        <?php endif; ?>
        <input type="file" name="foto_relogio" accept="image/*">

        <label>Cliente</label>
        <select name="id_cliente" required>
            <option value="">Selecione</option>

            <?php while($c = $result_clientes->fetch_assoc()): ?>
                <option value="<?= $c['id_cliente'] ?>" 
                    <?= $c['id_cliente'] == $row['id_cliente'] ? 'selected' : '' ?>>
                    <?= $c['nome'] . " " . $c['sobrenome'] ?>
                </option>
            <?php endwhile; ?>
        </select> <br>

        <input type="hidden" name="id_relogio" value="<?= $row['id_relogio'] ?>">

        <button type="submit">SALVAR ALTERAÇÕES</button>
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
