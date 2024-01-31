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
include('../db_conection.php'); // Inclua o arquivo de conexão com o banco de dados

// Verifique se o ID do documento foi fornecido na consulta GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_documento = $_GET['id'];

    // Consulta para excluir o documento com o ID especificado
    $sql = "DELETE FROM documentos WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_documento);

    if ($stmt->execute()) {
        // Redirecione de volta para a página de lista de documentos após a exclusão bem-sucedida
        header('Location: lista_documentos.php');
        exit();
    } else {
        // Exiba uma mensagem de erro em caso de falha na exclusão
        $erro = "Erro ao excluir o documento.";
    }

    $stmt->close();
} else {
    // Redirecione de volta para a página de lista de documentos se o ID do documento não for válido
    header('Location: lista_documentos.php');
    exit();
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link para a biblioteca Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Se você preferir, pode baixar o arquivo CSS do Bootstrap e incluí-lo localmente -->

    <!-- Inclua outros cabeçalhos necessários -->
    <link rel="stylesheet" href="assets/css/estilo.css"> <!-- Seu arquivo de estilo personalizado -->
    <title>Excluir Documento</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h5>Excluir Documento</h5>

    <?php
    if (isset($erro)) {
        echo '<div class="alert alert-danger">' . $erro . '</div>';
    }
    ?>

    <p>Tem certeza de que deseja excluir este documento?</p>

    <a href="lista_documentos.php" class="btn btn-secondary">Cancelar</a>
    <a href="excluir-documento.php?id=<?php echo $id_documento; ?>&confirmar=1" class="btn btn-danger">Confirmar Exclusão</a>
</div>

<?php //include('includes/footer.php'); ?>

<!-- Link para o arquivo JavaScript do Bootstrap, se necessário -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> -->
<!-- Se preferir, pode baixar o arquivo JavaScript do Bootstrap e incluí-lo localmente -->
</body>
</html>
