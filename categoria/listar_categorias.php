<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header('Location: /sitevisa/404.php');
    exit;
}
?>
<?php
// Inclua o arquivo de conexão com o banco de dados
include('../db_conection.php');

// Verifique se o formulário foi enviado para excluir uma categoria
if (isset($_POST['excluir_categoria'])) {
    $categoria_id = $_POST['categoria_id'];

    // Exclua a categoria do banco de dados
    $sql = "DELETE FROM categorias_documentos WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $categoria_id);

    if ($stmt->execute()) {
        $mensagem = 'Categoria excluída com sucesso!';
    } else {
        $mensagem = 'Erro ao excluir a categoria.';
    }

    $stmt->close();
}

// Consulta para buscar todas as categorias cadastradas
$sql = "SELECT id, nome_categoria FROM categorias_documentos";
$result = $mysqli->query($sql);

// Inicialize uma variável para armazenar a mensagem de sucesso ou erro
$mensagem = "";

// Verifique se existem categorias cadastradas
if ($result->num_rows > 0) {
    // Mensagem de sucesso
    $mensagem = "Categorias cadastradas:";
} else {
    // Mensagem de erro
    $mensagem = "Nenhuma categoria cadastrada.";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Categorias de Documentos</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Listar Categorias de Documentos</h5>

    <!-- Exibir a mensagem de sucesso ou erro -->
    <div class="alert alert-info">
        <?php echo $mensagem; ?>
    </div>

    <?php if ($result->num_rows > 0) : ?>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nome da Categoria</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nome_categoria']; ?></td>
                    <td>
                        <form action="editar_categoria.php" method="POST" style="display: inline;">
                            <input type="hidden" name="categoria_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Editar</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="categoria_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="excluir_categoria" class="btn btn-danger btn-sm">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>

<!-- Link para o arquivo JavaScript do Bootstrap, se necessário -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> -->
<!-- Se preferir, pode baixar o arquivo JavaScript do Bootstrap e incluí-lo localmente -->
</body>
</html>
