<?php
//sessão usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('db_conection.php'); // Inclua seu arquivo de conexão com o banco de dados
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Buscar usuário no banco de dados
    $sql = "SELECT id, nome_completo, senha, nivel_acesso, area FROM usuarios WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($usuario = $result->fetch_assoc()) {
        if (password_verify($senha, $usuario['senha'])) {
            // Credenciais corretas, definir sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome_completo'];
            $_SESSION['nivel_acesso'] = $usuario['nivel_acesso']; // Armazenar o nível de acesso na sessão
            $_SESSION['area'] = $usuario['area']; // Armazenar o nível de acesso na sessão
            header('Location: index.php');
            exit();
        } else {
            $erro = 'Senha incorreta.';
        }
    } else {
        $erro = 'Usuário não encontrado.';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f7f7f7;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            background-color: #fff;
            border-radius: 8px;
        }
        .login-title {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="login-container">
    <h5 class="login-title">Login</h5>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group mb-3">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="form-group mb-3">
            <label for="senha">Senha:</label>
            <input type="password" class="form-control" name="senha" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>

        <!-- Adicione o link "Não tem uma conta? Registrar" aqui -->
        <center><p class="mt-3">Não tem uma conta? <a href="usuario/cadastro_usuario.php">Registrar</a></p></center>
</div>


</body>
</html>