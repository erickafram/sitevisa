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
    <title>Lista de Documentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Lista de Documentos</h5>

    <div class="mb-3">
        <input type="text" class="form-control" id="campo-pesquisa" placeholder="Pesquisar documentos">
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nome do Documento</th>
            <th>Tipo de Documento</th>
            <th>Visualizar</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $caminho_documentos = "uploads/";
        $formatos_permitidos = array(
            "pdf" => "PDF",
            "doc" => "DOC",
            "docx" => "DOCX",
            "png" => "PNG",
            "jpg" => "JPG",
            "jpeg" => "JPEG",
            "xls" => "XLS" // Adicione o formato XLS aqui
        );

        // Verificar e listar os arquivos na pasta "uploads"
        if ($handle = opendir($caminho_documentos)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $caminho_completo = $caminho_documentos . $entry;
                    $extensao_arquivo = strtolower(pathinfo($caminho_completo, PATHINFO_EXTENSION));

                    if (array_key_exists($extensao_arquivo, $formatos_permitidos)) {
                        echo '<tr>';
                        echo '<td>' . pathinfo($entry, PATHINFO_FILENAME) . '</td>';
                        echo '<td>' . $formatos_permitidos[$extensao_arquivo] . '</td>';
                        echo '<td><a href="' . $caminho_completo . '" target="_blank">Visualizar</a></td>';
                        echo '</tr>';
                    }
                }
            }
            closedir($handle);
        }
        ?>
        </tbody>
    </table>
</div>

<script>
    document.getElementById("campo-pesquisa").addEventListener("input", function() {
        var termo = this.value.toLowerCase();
        var rows = document.querySelectorAll("tbody tr");

        rows.forEach(function(row) {
            var nomeDocumento = row.querySelector("td:nth-child(1)").textContent.toLowerCase();

            if (nomeDocumento.includes(termo)) {
                row.style.display = "table-row";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>

<?php //include('includes/rodape.php'); ?>
</body>
</html>
