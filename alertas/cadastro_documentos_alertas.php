<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || ($_SESSION['nivel_acesso'] !== 'Administrador' && $_SESSION['nivel_acesso'] !== 'Gestor')) {
    header('Location: /sitevisa/404.php');
    exit;
}

// Inicialize a variável $numero_processo_documentos com um valor padrão
$numero_processo_documentos = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero_processo_documentos'])) {
    $numero_processo_documentos = $_POST['numero_processo_documentos'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Documentos com Alertas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top: 50px;">
    <h5>Cadastro de Documentos com Alertas</h5>

    <form action="processar_cadastro_documentos_alertas.php" method="POST">
        <div class="form-group mb-3">
            <label for="tipo_documento">Tipo de Documento:</label>
            <select class="form-control" name="tipo_documento" required>
                <option value="Parecer de Licenciamento">Parecer de Licenciamento</option>
                <option value="Notificação Sanitária">Notificação Sanitária</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="numero_documento">Número do Documento no INFOVISA:</label>
            <input type="text" class="form-control" name="numero_documento" required>
        </div>

        <div class="form-group">
            <label for="numero_processo_documentos">Número de Processo:</label>
            <input type="text" class="form-control numero-processo" name="numero_processo_documentos" value="<?php echo htmlspecialchars($numero_processo_documentos); ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="data_alerta">Prazo:</label>
            <input type="date" class="form-control" name="data_alerta" required>
        </div>

        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>
<?php include('../includes/footer.php'); ?>
<script>
    $(document).ready(function() {
        $('.numero-processo').on('input', function() {
            // Remove qualquer caractere não numérico
            var rawValue = $(this).val().replace(/\D/g, '');

            // Adiciona a máscara desejada
            var maskedValue = rawValue.replace(/(\d{4})(\d{2})(\d{10})/, '$1.$2.$3');

            // Define o valor formatado de volta no campo
            $(this).val(maskedValue);
        });
    });
</script>

</body>
</html>
