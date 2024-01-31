<?php
include('../db_conection.php');
require_once('../fpdf/fpdf.php');
require_once('../fpdi/src/autoload.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $numeroParecer = $_POST['numeroParecer'];
    $numeroProcesso = $_POST['numeroProcesso'];
    $dataAprovacao = $_POST['dataAprovacao'];

    // Salva os dados no banco de dados
    $stmt = $mysqli->prepare("INSERT INTO projeto_arquitetonico (status, numero_parecer, numero_processo, data_aprovacao) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $status, $numeroParecer, $numeroProcesso, $dataAprovacao);
    $stmt->execute();
    $stmt->close();

    if (isset($_FILES['arquivoPdf']) && $_FILES['arquivoPdf']['error'] == 0) {
        $arquivoTmp = $_FILES['arquivoPdf']['tmp_name'];

        // Carrega o PDF original e ajusta o tamanho da página para paisagem
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->setSourceFile($arquivoTmp);

        // Define o tamanho da página para 1.585 × 1.121 mm em paisagem
        $pdf->addPage('L', array(1585, 1121));
        $tplIdx = $pdf->importPage(1, '/MediaBox');
        $pdf->useTemplate($tplIdx);

        // Converte a data para o formato D/M/Y
        $dataFormatada = date("d/m/Y", strtotime($dataAprovacao));

        // Prepara o texto, convertendo para ISO-8859-1
        $texto = utf8_decode("Status: $status\nNúmero do Parecer: $numeroParecer\nNúmero do Processo: $numeroProcesso\nData da Aprovação: $dataFormatada");
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        // Posiciona o texto no canto direito da página
        $x = 1100;
        $y = 550;
        $pdf->SetXY($x, $y);
        $pdf->MultiCell(0, 10, $texto);

        // Envio do PDF para o navegador do usuário
        $pdf->Output('I', 'Projeto-Arquitetonico.pdf');
    }
}
?>
