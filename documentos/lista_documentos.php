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
    <!-- Link para a biblioteca Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Se você preferir, pode baixar o arquivo CSS do Bootstrap e incluí-lo localmente -->

    <!-- Inclua outros cabeçalhos necessários -->
    <link rel="stylesheet" href="assets/css/estilo.css"> <!-- Seu arquivo de estilo personalizado -->
    <title>Lista de Documentos</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Lista de Documentos</h5>

    <?php
    include('../db_conection.php');

    // Verifica se uma categoria foi selecionada
    $filtro_categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

    // Consulta para buscar documentos cadastrados no banco de dados com informações de categoria
    $sql = "SELECT documentos.id, documentos.nome_documento, documentos.tipo_documento, documentos.caminho_documento, documentos.data_upload, categorias_documentos.nome_categoria 
            FROM documentos 
            LEFT JOIN categorias_documentos ON documentos.categoria_id = categorias_documentos.id";

    // Aplica o filtro de categoria, se uma categoria foi selecionada
    if (!empty($filtro_categoria)) {
        $sql .= " WHERE categorias_documentos.nome_categoria = '$filtro_categoria'";
    }

    $result = $mysqli->query($sql);

    // Exibe o formulário de filtro de categoria
    echo '<form method="get" action="">
            <div class="mb-3">
                <label for="categoria" class="form-label">Filtrar por Categoria:</label>
                <select class="form-control" name="categoria" onchange="this.form.submit()">
                    <option value="">Todas as Categorias</option>';

    // Consulta para obter todas as categorias existentes
    $sql_categorias = "SELECT DISTINCT nome_categoria FROM categorias_documentos";
    $result_categorias = $mysqli->query($sql_categorias);

    while ($categoria = $result_categorias->fetch_assoc()) {
        // Defina a categoria correta como selecionada
        $selected = ($filtro_categoria == $categoria["nome_categoria"]) ? "selected" : "";
        echo '<option value="' . $categoria["nome_categoria"] . '" ' . $selected . '>' . $categoria["nome_categoria"] . '</option>';
    }
    echo '  </select>
            </div>
        </form>';

    if ($result->num_rows > 0) {
        echo '<table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome do Documento</th>
                        <!-- <th>Tipo de Documento</th> -->
                        <th>Categoria</th>
                        <th>Data de Upload</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . $row["id"] . '</td>
                    <td>' . $row["nome_documento"] . '</td>
                    <!-- <td>' . $row["tipo_documento"] . '</td> -->
                    <td>' . $row["nome_categoria"] . '</td>
                    <td>' . $row["data_upload"] . '</td>
                    <td>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            Ações
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="' . $row["caminho_documento"] . '" target="_blank">Visualizar</a></li>
            <li><a class="dropdown-item" href="editar-documento.php?id=' . $row["id"] . '">Editar</a></li>
            <li><a class="dropdown-item" href="excluir-documento.php?id=' . $row["id"] . '">Excluir</a></li>
        </ul>
    </div>
</td>

                </tr>';
        }

        echo '</tbody></table>';
    } else {
        echo 'Nenhum documento cadastrado.';
    }

    $mysqli->close();
    ?>

</div>

<?php //include('includes/footer.php'); ?>

<!-- Link para o arquivo JavaScript do Bootstrap, se necessário -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> -->
<!-- Se preferir, pode baixar o arquivo JavaScript do Bootstrap e incluí-lo localmente -->
</body>
</html>
