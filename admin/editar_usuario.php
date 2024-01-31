<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header('Location: /sitevisa/404.php');
    exit;
}

include('../db_conection.php'); // Inclua o arquivo de conexão com o banco de dados

// Verifica se o ID do usuário a ser editado foi fornecido na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userid = $_GET['id'];

    // Consulta o usuário com o ID fornecido
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Processar o formulário de edição
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome_completo = $_POST['nome_completo'];
            $cpf = $_POST['cpf'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $data_nascimento = date('Y-m-d', strtotime($_POST['data_nascimento']));
            $nivel_acesso = $_POST['nivel_acesso'];
            $area = $_POST['area']; // Adicione o campo 'Área' ao processamento

            // Atualizar as informações do usuário no banco de dados
            $update_sql = "UPDATE usuarios SET nome_completo = ?, cpf = ?, email = ?, telefone = ?, data_nascimento = ?, nivel_acesso = ?, area = ? WHERE id = ?";
            $update_stmt = $mysqli->prepare($update_sql);
            $update_stmt->bind_param("sssssssi", $nome_completo, $cpf, $email, $telefone, $data_nascimento, $nivel_acesso, $area, $userid); // Atualize a quantidade de parâmetros e tipos

            if ($update_stmt->execute()) {
                header('Location: lista.php');
                exit();
            } else {
                $erro = "Erro ao atualizar informações do usuário: " . htmlspecialchars($update_stmt->error);
            }

            $update_stmt->close();
        }
    } else {
        echo "Nenhum usuário encontrado com o ID fornecido.";
        exit();
    }
} else {
    echo "ID de usuário não fornecido ou inválido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Editar Usuário</h5>

    <form action="editar_usuario.php?id=<?php echo $userid; ?>" method="POST">
        <div class="form-group mb-3">
            <label for="nome_completo">Nome Completo:</label>
            <input type="text" class="form-control" name="nome_completo" required value="<?php echo $user['nome_completo']; ?>">
        </div>

        <div class="form-group mb-3">
            <label for="cpf">CPF:</label>
            <input type="text" class="form-control" name="cpf" id="cpf" required value="<?php echo $user['cpf']; ?>">
        </div>

        <div class="form-group mb-3">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" required value="<?php echo $user['email']; ?>">
        </div>

        <div class="form-group mb-3">
            <label for="telefone">Telefone:</label>
            <input type="text" class="form-control" name="telefone" id="telefone" required value="<?php echo $user['telefone']; ?>">
        </div>

        <div class="form-group mb-3">
            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="text" class="form-control date-input" name="data_nascimento" required value="<?php echo $user['data_nascimento']; ?>">
        </div>

        <div class="form-group mb-3">
            <label for="nivel_acesso">Nível de Acesso:</label>
            <select class="form-control" name="nivel_acesso">
                <option value="Administrador" <?php if ($user['nivel_acesso'] === 'Administrador') echo 'selected'; ?>>Administrador</option>
                <option value="Gestor" <?php if ($user['nivel_acesso'] === 'Gestor') echo 'selected'; ?>>Gestor</option>
                <option value="Técnico" <?php if ($user['nivel_acesso'] === 'Técnico') echo 'selected'; ?>>Técnico</option>
                <option value="Usuário" <?php if ($user['nivel_acesso'] === 'Usuário') echo 'selected'; ?>>Usuário</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="area">Área:</label>
            <select class="form-control" name="area">
                <option value="">Selecione</option>
                <option value="GIM" <?php if ($user['area'] === 'GIM') echo 'selected'; ?>>GIM</option>
                <option value="GLR" <?php if ($user['area'] === 'GLR') echo 'selected'; ?>>GLR</option>
                <option value="AASVS" <?php if ($user['area'] === 'AASVS') echo 'selected'; ?>>AASVS</option>
                <option value="ACAS" <?php if ($user['area'] === 'ACAS') echo 'selected'; ?>>ACAS</option>
                <option value="AI" <?php if ($user['area'] === 'AI') echo 'selected'; ?>>AI</option>
                <option value="DVISA" <?php if ($user['area'] === 'DVISA') echo 'selected'; ?>>DVISA</option>
            </select>
        </div>


        <button type="submit" class="btn btn-primary mt-3">Salvar</button>
    </form>

    <?php
    if (isset($erro)) {
        echo '<div class="alert alert-danger mt-3">' . $erro . '</div>';
    }
    ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function(){
        $('#cpf').mask('000.000.000-00');
        $('#telefone').mask('(00) 00000-0000');
        $('.date-input').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            clearBtn: true,
            endDate: '0d'
        });
    });
</script>

</body>
</html>
<?php
$mysqli->close();
?>
