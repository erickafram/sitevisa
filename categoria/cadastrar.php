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

// Defina uma variável para armazenar a mensagem de sucesso ou erro
$mensagem = "";

// Verifique se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valide os dados do formulário
    $nomeCategoria = $_POST['nome_categoria'];
    $descricaoCategoria = $_POST['descricao']; // Adicione esta linha
    $subtituloCategoria = $_POST['subtitulo']; // Adicione esta linha

    // Verifique se o campo de nome da categoria não está vazio
    if (empty($nomeCategoria)) {
        $mensagem = 'O campo Nome da Categoria é obrigatório.';
    } else {
        // Verifique se a categoria já existe no banco de dados
        $sqlVerificar = "SELECT nome_categoria FROM categorias_documentos WHERE nome_categoria = ?";
        $stmtVerificar = $mysqli->prepare($sqlVerificar);
        $stmtVerificar->bind_param("s", $nomeCategoria);
        $stmtVerificar->execute();
        $stmtVerificar->store_result();

        if ($stmtVerificar->num_rows > 0) {
            $mensagem = 'Categoria com o mesmo nome já existe.';
        } else {
            // Insira a categoria no banco de dados, incluindo a descrição e subtitulo
            $sql = "INSERT INTO categorias_documentos (nome_categoria, descricao, subtitulo) VALUES (?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sss", $nomeCategoria, $descricaoCategoria, $subtituloCategoria); // Adicione esta linha

            if ($stmt->execute()) {
                // Defina a mensagem de sucesso
                $mensagem = 'Categoria cadastrada com sucesso!';
            } else {
                $mensagem = 'Erro ao cadastrar a categoria.';
            }

            $stmt->close();
        }

        $stmtVerificar->close();
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Categoria de Documentos</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Cadastrar Categoria de Documentos</h5>

    <!-- Exibir a mensagem de sucesso ou erro -->
    <?php if (!empty($mensagem)) : ?>
        <div class="alert <?php echo $mensagem === 'Categoria cadastrada com sucesso!' ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group mb-3">
            <label for="nome_categoria" class="mb-2">Nome da Categoria:</label>
            <input type="text" class="form-control" name="nome_categoria" required>
        </div>

        <div class="form-group mb-3">
            <label for="subtitulo" class="mb-2">Subtítulo:</label>
            <input type="text" class="form-control" name="subtitulo">
        </div>


        <div class="form-group mb-3">
            <label for="descricao" class="mb-2">Aviso para Categoria:</label>
            <textarea id="descricao" name="descricao" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </div>
    </form>
</div>

<script>
    tinymce.init({
        selector: '#descricao',
        plugins: 'autolink lists link image charmap print preview',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link image',
        skin: 'oxide-dark',
        content_css: 'dark',
        height: 300
    });
</script>

<?php include('../includes/footer.php'); ?>

<!-- Link para o arquivo JavaScript do Bootstrap, se necessário -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> -->
<!-- Se preferir, pode baixar o arquivo JavaScript do Bootstrap e incluí-lo localmente -->
</body>
</html>
