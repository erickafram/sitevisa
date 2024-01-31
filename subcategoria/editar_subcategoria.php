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
// Verifique se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conecte-se ao banco de dados (substitua com suas próprias credenciais)
    include('../db_conection.php');

    // Obtenha os dados do formulário
    $subcategoriaId = $_POST['id'];
    $novoNomeSubcategoria = $_POST['novoNomeSubcategoria'];

    // Execute a atualização no banco de dados
    $sql = "UPDATE subcategorias_documentos SET nome_subcategoria = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("si", $novoNomeSubcategoria, $subcategoriaId);

    if ($stmt->execute()) {
        // Sucesso na atualização
        echo 'success';
    } else {
        // Erro na atualização
        echo 'error';
    }

    // Feche a conexão com o banco de dados
    $stmt->close();
    $mysqli->close();
}
?>
