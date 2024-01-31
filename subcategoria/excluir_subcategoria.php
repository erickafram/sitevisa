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
// Verifique se o ID da subcategoria foi enviado via POST
if (isset($_POST['id'])) {
    // Conecte-se ao banco de dados (substitua com suas próprias credenciais)
    include('../db_conection.php');

    // Obtenha o ID da subcategoria a ser excluída
    $subcategoriaId = $_POST['id'];

    // Execute a exclusão no banco de dados
    $sql = "DELETE FROM subcategorias_documentos WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $subcategoriaId);

    if ($stmt->execute()) {
        // Sucesso na exclusão
        echo 'success';
    } else {
        // Erro na exclusão
        echo 'error';
    }

    // Feche a conexão com o banco de dados
    $stmt->close();
    $mysqli->close();
}
?>
