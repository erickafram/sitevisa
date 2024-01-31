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

// Defina a variável $descricao_categoria como vazia antes de exibi-la
$descricao_categoria = "";
$subtitulo_categoria = ""; // Adicione esta linha para o subtítulo

// Verifique se o formulário foi enviado para editar uma categoria
if (isset($_POST['editar_categoria'])) {
    $categoria_id = $_POST['categoria_id'];
    $novo_nome_categoria = $_POST['novo_nome_categoria'];
    $nova_descricao_categoria = $_POST['nova_descricao_categoria'];
    $novo_subtitulo_categoria = $_POST['novo_subtitulo_categoria']; // Adicione esta linha para o subtítulo

    // Atualize o nome, a descrição e o subtítulo da categoria no banco de dados
    $sql = "UPDATE categorias_documentos SET nome_categoria = ?, descricao = ?, subtitulo = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssi", $novo_nome_categoria, $nova_descricao_categoria, $novo_subtitulo_categoria, $categoria_id); // Adicione esta linha para o subtítulo

    if ($stmt->execute()) {
        $mensagem = 'Categoria atualizada com sucesso!';
    } else {
        $mensagem = 'Erro ao atualizar a categoria.';
    }

    $stmt->close();
}

// Verifique se o formulário foi enviado para obter os detalhes da categoria
if (isset($_POST['categoria_id'])) {
    $categoria_id = $_POST['categoria_id'];

    // Consulta para buscar os detalhes da categoria, incluindo a descrição e o subtítulo
    $sql = "SELECT nome_categoria, descricao, subtitulo FROM categorias_documentos WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $stmt->bind_result($nome_categoria, $descricao_categoria, $subtitulo_categoria); // Adicione esta linha para o subtítulo

    // Busque o nome, a descrição e o subtítulo da categoria
    $stmt->fetch();

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria de Documentos</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Editar Categoria de Documentos</h5>

    <form method="POST" action="">
        <input type="hidden" name="categoria_id" value="<?php echo $categoria_id; ?>">
        <div class="form-group mb-3">
            <label for="novo_nome_categoria" class="mb-2">Novo Nome da Categoria:</label>
            <input type="text" class="form-control" name="novo_nome_categoria" value="<?php echo $nome_categoria; ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="novo_subtitulo_categoria" class="mb-2">Novo Subtítulo:</label> <!-- Adicione esta linha para o subtítulo -->
            <input type="text" class="form-control" name="novo_subtitulo_categoria" value="<?php echo $subtitulo_categoria; ?>">
        </div>

        <div class="form-group mb-3">
            <label for="nova_descricao_categoria" class="mb-2">Nova Descrição da Categoria:</label>
            <textarea class="form-control" name="nova_descricao_categoria" rows="4"><?php echo $descricao_categoria; ?></textarea>
        </div>

        <div class="mb-3">
            <button type="submit" name="editar_categoria" class="btn btn-primary">Atualizar Categoria</button>
            <a href="listar_categorias.php" class="btn btn-secondary">Voltar</a>
        </div>
    </form>

    <?php if (!empty($mensagem)) : ?>
        <!-- Exibir a mensagem de sucesso ou erro -->
        <div class="alert alert-info">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

</div>
<!-- Inclua o TinyMCE -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@5.9.1/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: 'textarea[name=nova_descricao_categoria]',
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
