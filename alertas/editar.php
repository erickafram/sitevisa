<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || ($_SESSION['nivel_acesso'] !== 'Administrador' && $_SESSION['nivel_acesso'] !== 'Gestor')) {
    header('Location: /sitevisa/404.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Documento com Alerta</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top: 50px;">
    <h5>Editar Documento com Alerta</h5>

    <?php
    // Verifique se o ID do documento a ser editado foi passado por meio de um parâmetro na URL
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];

        include('../db_conection.php');

        // Consulta SQL para recuperar os dados do documento com base no ID
        $sql = "SELECT tipo_documento, numero_documento, numero_processo_documentos, data_alerta FROM documentos_alertas WHERE id = ?";
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($tipo_documento, $numero_documento, $numero_processo_documentos, $data_alerta);
                $stmt->fetch();
                $dataAlertaFormatada = date('d/m/Y', strtotime($data_alerta));
                $numero_processo_documentos_formatado = preg_replace('/(\d{4})(\d{2})(\d{10})/', '$1.$2.$3', $numero_processo_documentos);

                // Exiba um formulário preenchido com os dados do documento para edição
                echo '<form action="processar_edicao.php" method="POST">';
                echo '<input type="hidden" name="id" value="' . $id . '">';
                echo '<div class="form-group mb-3">';
                echo '<label for="tipo_documento">Tipo de Documento:</label>';
                echo '<select class="form-control" name="tipo_documento" required>';
                echo '<option value="Parecer de Licenciamento" ' . ($tipo_documento == 'Parecer de Licenciamento' ? 'selected' : '') . '>Parecer de Licenciamento</option>';
                echo '<option value="Notificação Sanitária" ' . ($tipo_documento == 'Notificação Sanitária' ? 'selected' : '') . '>Notificação Sanitária</option>';
                echo '</select>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label for="numero_documento">Número do Documento:</label>';
                echo '<input type="text" class="form-control" name="numero_documento" value="' . htmlspecialchars($numero_documento) . '" required>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label for="numero_processo_documentos">Número de Processo:</label>';
                echo '<input type="text" class="form-control numero-processo" name="numero_processo_documentos" value="' . htmlspecialchars($numero_processo_documentos_formatado) . '" required>';
                echo '</div>';
                echo '<div class="form-group mb-3">';
                echo '<label for="data_alerta">Data do Alerta:</label>';
                echo '<input type="date" class="form-control" name="data_alerta" value="' . $data_alerta . '" required>';
                echo '</div>';
                echo '<button type="submit" class="btn btn-primary">Salvar Alterações</button>';
                echo '</form>';
                echo '<script>
                    $(document).ready(function() {
                        $(".numero-processo").on("input", function() {
                            // Remove qualquer caractere não numérico
                            var rawValue = $(this).val().replace(/\\D/g, "");
                            
                            // Adiciona a máscara desejada
                            var maskedValue = rawValue.replace(/(\\d{4})(\\d{2})(\\d{10})/, "$1.$2.$3");
                            
                            // Define o valor formatado de volta no campo
                            $(this).val(maskedValue);
                        });
                    });
                </script>';
            } else {
                echo 'Documento não encontrado.';
            }

            $stmt->close();
        }

        $mysqli->close();
    } else {
        echo 'ID do documento não especificado.';
    }
    ?>

</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
