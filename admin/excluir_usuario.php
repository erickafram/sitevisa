<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header('Location: /sitevisa/404.php');
    exit;
}

include('../db_conection.php'); // Inclua o arquivo de conexão com o banco de dados

// Verifica se o ID do usuário a ser excluído foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userid = $_GET['id'];

    // Consulta o usuário com o ID fornecido
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Exibe uma página de confirmação antes de excluir o usuário
        $user_data = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmação de Exclusão</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        </head>
        <body>
        <?php include('../includes/header.php'); ?>

        <div class="container mt-5" style="padding-top:50px;">
            <h5>Confirmação de Exclusão</h5>

            <p>Deseja realmente excluir o usuário:</p>
            <p><strong>ID:</strong> <?php echo $user_data['id']; ?></p>
            <p><strong>Nome Completo:</strong> <?php echo $user_data['nome_completo']; ?></p>
            <p><strong>CPF:</strong> <?php echo $user_data['cpf']; ?></p>
            <p><strong>Email:</strong> <?php echo $user_data['email']; ?></p>

            <form action="excluir_usuario.php?id=<?php echo $userid; ?>" method="post">
                <button type="submit" class="btn btn-danger">Sim, Excluir</button>
                <a href="lista.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>

        </body>
        </html>
        <?php
    } else {
        echo "Nenhum usuário encontrado com o ID fornecido.";
        exit();
    }
} else {
    echo "ID de usuário não fornecido ou inválido.";
    exit();
}
?>
