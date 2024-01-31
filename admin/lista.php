<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header('Location: /sitevisa/404.php');
    exit;
}

include('../db_conection.php'); // Inclua o arquivo de conexão com o banco de dados

// Lógica para Redefinir Senha e outras ações (Editar e Excluir) aqui...
if(isset($_GET['action'])){
    if($_GET['action'] === 'reset' && isset($_GET['userid'])){
        $userid = $_GET['userid'];
        $nova_senha = password_hash('visa@2024', PASSWORD_DEFAULT);
        $update_sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
        $stmt = $mysqli->prepare($update_sql);
        $stmt->bind_param("si", $nova_senha, $userid);
        if($stmt->execute()){
            // Senha redefinida com sucesso
            echo '<script>alert("Senha redefinida com sucesso para o usuário com ID ' . $userid . '");</script>';
        } else {
            // Erro ao redefinir a senha
            echo '<script>alert("Erro ao redefinir a senha.");</script>';
        }
    }
}

$sql = "SELECT * FROM usuarios";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Usuários</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Listagem de Usuários</h5>

    <table class="table table-striped mt-3">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nome Completo</th>
            <th>CPF</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Data de Nascimento</th>
            <th>Nível de Acesso</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["nome_completo"] . "</td>";
                echo "<td>" . $row["cpf"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>" . $row["telefone"] . "</td>";
                echo "<td>" . $row["data_nascimento"] . "</td>";
                echo "<td>" . $row["nivel_acesso"] . "</td>";
                echo "<td>";
                echo '<div class="dropdown">';
                echo '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton' . $row['id'] . '" data-bs-toggle="dropdown" aria-expanded="false">';
                echo 'Ações';
                echo '</button>';
                echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $row['id'] . '">';
                echo '<li><a class="dropdown-item" href="editar_usuario.php?id=' . $row["id"] . '">Editar</a></li>';
                echo '<li><a class="dropdown-item" href="excluir_usuario.php?id=' . $row["id"] . '">Excluir</a></li>';
                echo '<li><a class="dropdown-item" href="lista.php?action=reset&userid=' . $row["id"] . '">Redefinir Senha</a></li>';
                echo '</ul>';
                echo '</div>';
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>Nenhum usuário cadastrado</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
<?php
$mysqli->close();
?>
