<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header('Location: /sitevisa/404.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Subcategoria</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h5>Cadastrar Subcategoria</h5>

    <form action="processar_cadastro_subcategoria.php" method="POST">
        <div class="form-group mb-3">
            <label for="nome_subcategoria" class="mb-2">Nome da Subcategoria:</label>
            <input type="text" class="form-control" name="nome_subcategoria" required>
        </div>

        <div class="form-group mb-3">
            <label for="categoria" class="mb-2">Categoria:</label>
            <select class="form-control" name="categoria_id" required>
                <option value="">Selecione a Categoria</option>
                <?php
                include('../db_conection.php');
                $sql = "SELECT id, nome_categoria FROM categorias_documentos";
                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row["id"] . '">' . $row["nome_categoria"] . '</option>';
                    }
                }
                $mysqli->close();
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<?php //include('includes/rodape.php'); ?>
</body>
</html>
