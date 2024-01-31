<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Consulta</title>
</head>
<body>
<h2>Detalhes da Consulta do Alvará Sanitário</h2>

<?php
// Verifique se os parâmetros necessários estão definidos na URL
if (isset($_GET['alvara_id']) && isset($_GET['data_consulta'])) {
    // Conecte-se ao banco de dados ou inclua sua conexão aqui
    include('db_conection.php');

    // Obtenha o ID do Alvará e a data da consulta da URL
    $alvaraId = $_GET['alvara_id'];
    $dataConsulta = urldecode($_GET['data_consulta']);

    // Recupere os dados do Alvará com base no ID
    $query = "SELECT * FROM alvaras_sanitarias WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $alvaraId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $alvara = $result->fetch_assoc();

        // Adicione estilos de impressão para ocultar certos elementos da página ao imprimir
        echo '<style>
                @media print {
                    body * {
                        visibility: hidden;
                    }
                    #imprimir-detalhes, #imprimir-detalhes * {
                        visibility: visible;
                    }
                }
            </style>';

        // Inicie a impressão
        echo '<button onclick="window.print()">Imprimir Detalhes</button>';

        // Abra a div que será impressa
        echo '<div id="imprimir-detalhes">';

        // Aqui, você pode exibir os detalhes do Alvará e a data da consulta
        echo '<p>Número do Alvará: ' . $alvara['numero_alvara'] . '.' . date('Y', strtotime($alvara['data_vencimento'])) . '</p>';
        echo '<p>CNPJ: ' . $alvara['cnpj'] . '</p>';
        echo '<p>Nome Fantasia: ' . $alvara['nome_fantasia'] . '</p>';
        echo '<p>Razão Social: ' . $alvara['razao_social'] . '</p>';
        echo '<p>Status: ' . $alvara['status_alvara'] . '</p>';
        echo '<p>Data de Vencimento: ' . date('d/m/Y', strtotime($alvara['data_vencimento'])) . '</p>';
        echo '<br> . <p style="font-size:12px;">Data da Consulta: ' . $dataConsulta . '</p>';

        // Feche a div que será impressa
        echo '</div>';
    } else {
        echo '<p>Alvará não encontrado.</p>';
    }

    // Feche a conexão com o banco de dados
    $stmt->close();
    $mysqli->close();
} else {
    echo '<p>Parâmetros inválidos na URL.</p>';
}
?>

</body>
</html>
