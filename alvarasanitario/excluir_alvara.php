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
include('../db_conection.php');

if ($mysqli->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Evite exclusões acidentais ou não autorizadas adicionando verificações aqui, por exemplo, verificando se o usuário tem permissão para excluir o registro.

        // Execute a consulta para excluir o registro com base no ID
        $query = "DELETE FROM alvaras_sanitarias WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Exclusão bem-sucedida, redirecione para a página de lista novamente ou exiba uma mensagem de sucesso
            header('Location: lista.php');
            exit;
        } else {
            // Erro na exclusão, exiba uma mensagem de erro
            echo '<div class="alert alert-danger">Erro ao excluir o registro. Por favor, tente novamente.</div>';
        }
    }
}

// Feche a conexão com o banco de dados
$mysqli->close();
?>
