<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Documentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container mt-5" style="padding-top:55px;">
    <?php
    include('db_conection.php');

    $categoria_id = isset($_GET["categoria_id"]) ? $_GET["categoria_id"] : null;
    $termo_pesquisa = isset($_GET["q"]) ? $_GET["q"] : "";

    if ($categoria_id !== null) {
        // Consulta para buscar o nome da categoria e a descrição
        $sql_categoria = "SELECT nome_categoria, descricao FROM categorias_documentos WHERE id = ?";
        $stmt_categoria = $mysqli->prepare($sql_categoria);
        $stmt_categoria->bind_param("i", $categoria_id);

        if ($stmt_categoria->execute()) {
            $stmt_categoria->bind_result($categoria_nome, $categoria_descricao);
            $stmt_categoria->fetch();

            // Verifique se a descrição está vazia antes de exibir o alerta
            if (!empty($categoria_descricao)) {
                echo '<div class="alert alert-info">';
                //echo '<center><h5>Aviso: ' . htmlspecialchars($categoria_nome) . '</h5></center>';
                echo '<p>' . htmlspecialchars_decode($categoria_descricao) . '</p>';
                echo '</div>';
            }

            if (!empty($categoria_nome)) {
                echo '<h6>Relação de documentos na Categoria ' . htmlspecialchars($categoria_nome) . '</h6>';
            } else {
                echo '<h5>Documentos nesta Categoria</h5>';
            }

            $stmt_categoria->close();
        } else {
            echo 'Erro ao buscar o nome da categoria: ' . htmlspecialchars($mysqli->error);
        }

        $sql_documentos = "SELECT id, nome_documento, tipo_documento, caminho_documento FROM documentos WHERE categoria_id = ?";
        $tipoParametro = 'i'; // Tipo do parâmetro (i = integer)

        if (!empty($termo_pesquisa)) {
            $sql_documentos .= " AND nome_documento LIKE ?";
            $tipoParametro .= 's'; // Adiciona outro tipo de parâmetro 's' para pesquisa
            $termo_pesquisa = '%' . $termo_pesquisa . '%'; // Adicione % antes e depois do termo de pesquisa
        }

        $stmt = $mysqli->prepare($sql_documentos);

        if (!empty($termo_pesquisa)) {
            $stmt->bind_param($tipoParametro, $categoria_id, $termo_pesquisa);
        } else {
            $stmt->bind_param($tipoParametro, $categoria_id);
        }

        if ($stmt->execute()) {
            $result_documentos = $stmt->get_result();

            // Exibir o formulário de pesquisa
            echo '<form method="GET" action="">';
            echo '<div class="input-group mb-3">';
            echo '<input type="text" class="form-control" placeholder="Pesquisar por nome de documento" name="q" value="' . htmlspecialchars($termo_pesquisa) . '">';
            echo '<input type="hidden" name="categoria_id" value="' . htmlspecialchars($categoria_id) . '">';
            echo '<button class="btn btn-outline-secondary" type="submit">Pesquisar</button>';
            echo '</div>';
            echo '</form>';

            // Consulta para buscar documentos e suas respectivas subcategorias
            $sql_documentos = "SELECT documentos.id, documentos.nome_documento, documentos.tipo_documento, documentos.caminho_documento, subcategorias_documentos.nome_subcategoria 
                           FROM documentos 
                           LEFT JOIN subcategorias_documentos ON documentos.subcategoria_id = subcategorias_documentos.id
                           WHERE documentos.categoria_id = ?";

            if (!empty($termo_pesquisa)) {
                $sql_documentos .= " AND documentos.nome_documento LIKE ?";
                $termo_pesquisa = '%' . $termo_pesquisa . '%';
            }

            $stmt = $mysqli->prepare($sql_documentos);
            if (!empty($termo_pesquisa)) {
                $stmt->bind_param("is", $categoria_id, $termo_pesquisa);
            } else {
                $stmt->bind_param("i", $categoria_id);
            }

            if ($stmt->execute()) {
                $result_documentos = $stmt->get_result();

                if ($result_documentos->num_rows > 0) {
                    // Agrupar documentos por subcategoria
                    $documentosPorSubcategoria = [];
                    while ($documento = $result_documentos->fetch_assoc()) {
                        $documentosPorSubcategoria[$documento["nome_subcategoria"]][] = $documento;
                    }

                    foreach ($documentosPorSubcategoria as $subcategoria => $documentos) {
                        echo '<div class="card mt-3" style="">';
                        echo '<div class="card-header">' . htmlspecialchars($subcategoria) . '</div>';
                        echo '<div class="card-body">';
                        echo '<table class="table table-striped">';
                        echo '<thead><tr><th>Nome do Documento</th><th>Extensão</th></tr></thead>';
                        echo '<tbody>';
                        foreach ($documentos as $doc) {
                            $extensao = strtoupper(pathinfo($doc["caminho_documento"], PATHINFO_EXTENSION));
                            echo '<tr><td><a href="documentos/' . htmlspecialchars($doc["caminho_documento"]) . '" target="_blank">' . htmlspecialchars($doc["nome_documento"]) . '</a></td>';
                            echo '<td>' . $extensao . '</td></tr>';
                        }
                        echo '</tbody></table>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo 'Nenhum documento encontrado.';
                }
            } else {
                echo 'Erro ao buscar documentos: ' . htmlspecialchars($mysqli->error);
            }

            $stmt->close();
        } else {
            echo 'Categoria inválida.';
        }
    }

    $mysqli->close();
    ?>

</div>
<div style="padding-bottom:40px;"
<?php include('includes/footer.php'); ?>
</body>
</html>
