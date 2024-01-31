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
    <title>Lista de Subcategorias</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:40px;">
    <h5>Lista de Subcategorias</h5>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nome da Subcategoria</th>
            <th>Categoria</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php
        include('../db_conection.php');
        $sql = "SELECT subcategorias_documentos.id, subcategorias_documentos.nome_subcategoria, categorias_documentos.nome_categoria 
                FROM subcategorias_documentos 
                INNER JOIN categorias_documentos ON subcategorias_documentos.categoria_id = categorias_documentos.id";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr data-id="' . $row["id"] . '">';
                echo '<td>' . $row["nome_subcategoria"] . '</td>';
                echo '<td>' . $row["nome_categoria"] . '</td>';
                echo '<td>';
                echo '<a href="javascript:void(0);" class="editar-subcategoria" data-id="' . $row["id"] . '">Editar</a> | ';
                echo '<a href="javascript:void(0);" class="excluir-subcategoria" data-id="' . $row["id"] . '">Excluir</a>';
                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="3">Nenhuma subcategoria encontrada.</td></tr>';
        }
        $mysqli->close();
        ?>
        </tbody>
    </table>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Subcategoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário de Edição -->
                <form id="form-edicao" method="POST">
                    <input type="hidden" id="editar-id" name="editar_id">
                    <div class="form-group">
                        <label for="editar-nome-subcategoria">Novo Nome da Subcategoria:</label>
                        <input type="text" class="form-control" id="editar-nome-subcategoria" name="editar_nome_subcategoria">
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="excluirModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Tem certeza de que deseja excluir esta subcategoria?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger" id="confirmar-exclusao">Excluir</a>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Abrir modal de edição ao clicar em "Editar"
        $('.editar-subcategoria').click(function () {
            var subcategoriaId = $(this).data('id');
            $('#editar-id').val(subcategoriaId);
            $('#editarModal').modal('show');
        });

        // Abrir modal de exclusão ao clicar em "Excluir"
        $('.excluir-subcategoria').click(function () {
            var subcategoriaId = $(this).data('id');
            $('#confirmar-exclusao').attr('data-id', subcategoriaId); // Adiciona o ID à modal de exclusão
            $('#excluirModal').modal('show');
        });

        // Excluir subcategoria após confirmação
        $('#confirmar-exclusao').click(function () {
            var subcategoriaId = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "excluir_subcategoria.php",
                data: { id: subcategoriaId },
                success: function (response) {
                    if (response == 'success') {
                        // Atualize a tabela após a exclusão
                        $('tr[data-id="' + subcategoriaId + '"]').remove();
                        $('#excluirModal').modal('hide');
                    } else {
                        alert("Erro ao excluir subcategoria.");
                    }
                }
            });
        });

        // Atualizar subcategoria após edição
        $('#form-edicao').submit(function (e) {
            e.preventDefault();
            var subcategoriaId = $('#editar-id').val();
            var novoNomeSubcategoria = $('#editar-nome-subcategoria').val();

            $.ajax({
                type: "POST",
                url: "editar_subcategoria.php",
                data: {
                    id: subcategoriaId,
                    novoNomeSubcategoria: novoNomeSubcategoria
                },
                success: function (response) {
                    if (response == 'success') {
                        // Feche o modal de edição após a conclusão
                        $('#editarModal').modal('hide');
                        // Atualize a tabela com o novo nome
                        $('tr[data-id="' + subcategoriaId + '"] td:first-child').text(novoNomeSubcategoria);
                    } else {
                        alert("Erro ao editar subcategoria.");
                    }
                }
            });
        });
    });
</script>

</body>
</html>
