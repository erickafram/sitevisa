<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || ($_SESSION['nivel_acesso'] !== 'Administrador' && $_SESSION['nivel_acesso'] !== 'Gestor')) {
    header('Location: /sitevisa/404.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Documento com Alerta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top: 50px;">
    <h5>Excluir Documento com Alerta</h5>

    <?php
    // Verifique se o ID do documento a ser excluído foi passado por meio de um parâmetro na URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];

        include('../db_conection.php');

        // Consulta SQL para excluir o documento com base no ID
        $sql = "DELETE FROM documentos_alertas WHERE id = ?";
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Verifique se a exclusão foi bem-sucedida
            if ($stmt->affected_rows > 0) {
                echo 'Documento excluído com sucesso.';
            } else {
                echo 'Falha ao excluir o documento.';
            }

            $stmt->close();
        }

        $mysqli->close();
    } else {
        echo 'ID do documento não especificado.';
    }
    ?>

</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
