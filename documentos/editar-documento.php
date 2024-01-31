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
    <title>Editar Documento</title>
    <!-- Link para a biblioteca Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h1>Editar Documento</h1>

    <?php
    include('../db_conection.php');

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
        $documento_id = $_GET["id"];

        // Consulta para obter os detalhes do documento, incluindo a categoria e subcategoria
        $sql = "SELECT documentos.id, documentos.nome_documento, documentos.tipo_documento, documentos.caminho_documento, documentos.data_upload, categorias_documentos.id AS categoria_id, categorias_documentos.nome_categoria, subcategorias_documentos.id AS subcategoria_id, subcategorias_documentos.nome_subcategoria 
        FROM documentos 
        LEFT JOIN categorias_documentos ON documentos.categoria_id = categorias_documentos.id
        LEFT JOIN subcategorias_documentos ON documentos.subcategoria_id = subcategorias_documentos.id
        WHERE documentos.id = ?";
        $stmt = $mysqli->prepare($sql);

// Verifica se a consulta foi preparada corretamente
        if (false === $stmt) {
            die('Erro de preparação: ' . htmlspecialchars($mysqli->error));
        }

        $stmt->bind_param("i", $documento_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $documento = $result->fetch_assoc();

            if ($documento) {
                // Exibe os detalhes do documento, incluindo a categoria e subcategoria
                echo '<form action="processar-edicao-documento.php" method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="id" value="' . $documento["id"] . '">
                          <div class="mb-3">
                              <label for="novo_nome_documento" class="form-label">Novo Nome do Documento:</label>
                              <input type="text" class="form-control" name="novo_nome_documento" value="' . htmlspecialchars($documento["nome_documento"]) . '">
                          </div>
                          <div class="mb-3">
                              <label for="nova_categoria" class="form-label">Nova Categoria:</label>
                              <select class="form-control" name="nova_categoria">';

                // Consulta para obter todas as categorias existentes
                $sql_categorias = "SELECT id, nome_categoria FROM categorias_documentos";
                $result_categorias = $mysqli->query($sql_categorias);

                while ($categoria = $result_categorias->fetch_assoc()) {
                    $selected = ($categoria["id"] == $documento["categoria_id"]) ? "selected" : "";
                    echo '<option value="' . $categoria["id"] . '" ' . $selected . '>' . $categoria["nome_categoria"] . '</option>';
                }

                echo '      </select>
                          </div>
                          <div class="mb-3">
                              <label for="nova_subcategoria" class="form-label">Nova Subcategoria:</label>
                              <select class="form-control" name="nova_subcategoria">';

                // Consulta para obter todas as subcategorias existentes
                $sql_subcategorias = "SELECT id, nome_subcategoria FROM subcategorias_documentos";
                $result_subcategorias = $mysqli->query($sql_subcategorias);

                while ($subcategoria = $result_subcategorias->fetch_assoc()) {
                    $selected = ($subcategoria["id"] == $documento["subcategoria_id"]) ? "selected" : "";
                    echo '<option value="' . $subcategoria["id"] . '" ' . $selected . '>' . $subcategoria["nome_subcategoria"] . '</option>';
                }

                echo '      </select>
                          </div>
                          <div class="mb-3">
                              <label for="novo_documento" class="form-label">Novo Documento (opcional):</label>
                              <input type="file" class="form-control" name="novo_documento" accept=".pdf, .doc, .docx, .png, .jpg, .xls">
                          </div>
                          <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                      </form>';
            } else {
                echo 'Documento não encontrado.';
            }
        } else {
            echo 'Erro ao buscar o documento no banco de dados.';
        }

        $stmt->close();
    } else {
        echo 'ID do documento não especificado.';
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
