<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || ($_SESSION['nivel_acesso'] !== 'Administrador' && $_SESSION['nivel_acesso'] !== 'Gestor')) {
    header('Location: /sitevisa/404.php');
    exit;
}

// Verifica se existe uma mensagem na sessão
if (isset($_SESSION['mensagem']) && !empty($_SESSION['mensagem'])) {
    echo '<div class="alert alert-info">' . $_SESSION['mensagem'] . '</div>';
    // Limpa a mensagem após exibição
    unset($_SESSION['mensagem']);
}

include('../db_conection.php');
require_once('../fpdf/fpdf.php');
require_once('../fpdi/src/autoload.php');

$mensagem = "";
$pdfGerado = false;

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $numeroParecer = $_POST['numeroParecer'];
    $numeroProcesso = $_POST['numeroProcesso'];
    $dataAprovacao = $_POST['dataAprovacao'];

    // Salva os dados no banco de dados
    $stmt = $mysqli->prepare("INSERT INTO projeto_arquitetonico (status, numero_parecer, numero_processo, data_aprovacao) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "Erro na preparação do statement: " . $mysqli->error;
        exit;
    }
    $stmt->bind_param("ssss", $status, $numeroParecer, $numeroProcesso, $dataAprovacao);

    if (!$stmt->execute()) {
        echo "Erro ao executar o statement: " . $stmt->error;
        exit;
    } else {
        $mensagem = "Projeto arquitetônico cadastrado com sucesso!";
        $pdfGerado = true;
    }

    $stmt->close();

    // Gera o PDF apenas se os dados foram salvos com sucesso
    if ($pdfGerado && isset($_FILES['arquivoPdf']) && $_FILES['arquivoPdf']['error'] == 0) {
        $arquivoTmp = $_FILES['arquivoPdf']['tmp_name'];
        $nomeArquivo = uniqid() . '-' . $_FILES['arquivoPdf']['name'];

        // Carrega o PDF original e ajusta o tamanho da página para paisagem
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($arquivoTmp);

        // Define o tamanho da página para 1.585 × 1.121 mm em paisagem
        $pdf->addPage('L', array(1585, 1121));
        $tplIdx = $pdf->importPage(1, '/MediaBox');
        $pdf->useTemplate($tplIdx);

        // Converte a data para o formato D/M/Y
        $dataFormatada = date("d/m/Y", strtotime($dataAprovacao));

        // Prepara o texto, convertendo para ISO-8859-1
        $texto = utf8_decode("Status: $status\nNúmero do Parecer: $numeroParecer\nNúmero do Processo: $numeroProcesso\nData da Aprovação: $dataFormatada");
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);

        // Posiciona o texto no canto direito da página
        $x = 1080; // Coordenada X (horizontal)
        $y = 550;  // Coordenada Y (vertical)
        $pdf->SetXY($x, $y);
        $pdf->MultiCell(0, 10, $texto);

        // Envio do PDF para o navegador do usuário
        $pdf->Output('I', $nomeArquivo);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Projeto Arquitetônico</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Inclua o jQuery antes do inputmask -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:40px;">
    <h2>Cadastrar Projeto Arquitetônico</h2>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-info">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <form action="gerar_pdf.php" method="POST" enctype="multipart/form-data" target="_blank" id="formCadastro">

    <div class="mb-3">
            <label for="arquivoPdf" class="form-label">Upload de PDF:</label>
            <input type="file" class="form-control" id="arquivoPdf" name="arquivoPdf" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Deferido">Deferido</option>
                <option value="Indeferido">Indeferido</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="numeroParecer" class="form-label">Número do Parecer:</label>
            <input type="text" class="form-control" id="numeroParecer" name="numeroParecer" required data-mask="99999.9999">
        </div>
        <div class="mb-3">
            <label for="numeroProcesso" class="form-label">Número do Processo:</label>
            <input type="text" class="form-control" id="numeroProcesso" name="numeroProcesso" required data-mask="9999.99.0000000000">
        </div>
        <div class="mb-3">
            <label for="dataAprovacao" class="form-label">Data da Aprovação:</label>
            <input type="date" class="form-control" id="dataAprovacao" name="dataAprovacao" required>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>
<!-- Inclua a versão correta do inputmask após o jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
<script>
    $(document).ready(function () {
        // Aplica a máscara de entrada
        $('#numeroParecer').inputmask('99999.9999');
        $('#numeroProcesso').inputmask('9999.99.9999999999');

        // Adiciona um ouvinte de eventos para limpar o formulário após o envio
        $('#formCadastro').on('submit', function() {
            setTimeout(function() {
                document.getElementById('formCadastro').reset();
            }, 1000);
        });
    });
</script>
</body>
</html>