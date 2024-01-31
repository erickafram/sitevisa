<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header('Location: /sitevisa/404.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Documento</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Cadastrar Documento</h5>

    <form action="processar_cadastro.php" method="POST" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="nome_documento" class="mb-2">Nome do Documento:</label>
            <input type="text" class="form-control" name="nome_documento">
        </div>

        <div class="form-group mb-3">
            <label for="categoria" class="mb-2">Categoria:</label>
            <select class="form-control" name="categoria" id="categoria" required>
                <option value="">Selecione a Categoria</option>
                <?php
                include('../db_conection.php');
                $sql = "SELECT id, nome_categoria FROM categorias_documentos";
                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row["id"] . '">' . $row["nome_categoria"] . '</option>';
                    }
                }
                $mysqli->close();
                ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="subcategoria" class="mb-2">Subcategoria:</label>
            <select class="form-control" name="subcategoria" id="subcategoria" required>
                <option value="">Selecione a Subcategoria</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="tipo_documento" class="mb-2">Tipo de Documento:</label>
            <select class="form-control" name="tipo_documento" required>
                <option value="">Selecione o Tipo de Documento</option>
                <option value="pdf">PDF</option>
                <option value="doc">DOC</option>
                <option value="docx">DOCX</option>
                <option value="png">PNG</option>
                <option value="jpg">JPG</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="documento" class="mb-2">Selecione o Documento:</label>
            <input type="file" class="form-control" name="documento[]" accept=".pdf, .doc, .docx, .png, .jpg, .xls, .xlsx" multiple required>
        </div>

        <hr class="my-4">

        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#categoria').change(function() {
            var categoriaId = $(this).val();
            $.ajax({
                url: 'buscar_subcategorias.php',
                type: 'GET',
                data: { categoria_id: categoriaId },
                dataType: 'json',
                success: function(subcategorias) {
                    var subcategoriaSelect = $('#subcategoria');
                    subcategoriaSelect.empty().append('<option value="">Selecione a Subcategoria</option>');
                    $.each(subcategorias, function(i, subcategoria) {
                        subcategoriaSelect.append('<option value="' + subcategoria.id + '">' + subcategoria.nome_subcategoria + '</option>');
                    });
                }
            });
        });
    });
</script>

<?php //include('includes/rodape.php'); ?>
</body>
</html>
