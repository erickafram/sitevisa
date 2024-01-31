<?php
include('../db_conection.php');

// Handle database connection error
if ($mysqli->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
}

$numero_alvara = '';
$status_alvara = '';
$nome_fantasia = '';
$razao_social = '';
$cnpj = '';
$data_vencimento = '';

// Processa solicitação se o número do alvará for fornecido
if (isset($_GET['numero_alvara'])) {
    $numero_alvara = $_GET['numero_alvara'];
    $query = "SELECT * FROM alvaras_sanitarias WHERE numero_alvara = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $numero_alvara);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Extrai dados do alvará
        $numero_alvara = $row['numero_alvara'];
        $status_alvara = $row['status_alvara'];
        $nome_fantasia = $row['nome_fantasia'];
        $razao_social = $row['razao_social'];
        $cnpj = $row['cnpj'];
        $data_vencimento = date('d/m/Y', strtotime($row['data_vencimento']));
    } else {
        $alvaraNotFound = true;
    }
} else {
    $alvaraNotProvided = true;
}

// Obtenha a data da consulta
date_default_timezone_set('America/Sao_Paulo');
$dataConsulta = date('d/m/Y H:i:s');

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informações do Alvará Sanitário</title>
    <style>
        @media print {
            header, nav, aside, footer, .btn {
                display: none;
            }

            .btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <center><h5 class="mb-4">Informações do Alvará Sanitário</h5></center>
    <?php if (isset($numero_alvara)): ?>
        <table class="table table-bordered table-striped">
            <tbody>
            <tr><th>Número do Alvará:</th><td><?= $numero_alvara . '.' . date('Y'); ?></td></tr>
            <tr>
                <th>Status do Alvará:</th>
                <td class="<?= 'status-' . strtolower($status_alvara); ?>"><?= $status_alvara; ?></td>
            </tr>
            <tr><th>Nome Fantasia:</th><td><?= $nome_fantasia; ?></td></tr>
            <tr><th>Razão Social:</th><td><?= $razao_social; ?></td></tr>
            <tr><th>CNPJ:</th><td><?= $cnpj; ?></td></tr>
            <tr><th>Data de Validade:</th><td><?= $data_vencimento; ?></td></tr>
            </tbody>
        </table>
        <div style="font-size: 15px; padding: 0px 5px 10px;">Data da Consulta: <?= $dataConsulta; ?></div>
        <?php if (strtolower($status_alvara) === 'vencido'): ?>
            <p class="alert alert-danger">O alvará está vencido!</p>
        <?php endif; ?>
        <a class="btn btn-primary" href="https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=<?= $numero_alvara; ?>" target="_blank">Visualizar Alvará</a>
        <button class="btn btn-success btn-print" onclick="window.print()">Imprimir Consulta</button>
    <?php elseif (isset($alvaraNotFound)): ?>
        <p class="alert alert-warning">Alvará não encontrado.</p>
    <?php elseif (isset($alvaraNotProvided)): ?>
        <p class="alert alert-info">Número de alvará não fornecido.</p>
    <?php endif; ?>
</div>
</body>
</html>
