<?php
// Inclua o arquivo de conexão com o banco de dados
include('../db_conection.php');

// Consulte os alvarás que estão vencidos
$queryAlvara = "SELECT * FROM alvaras_sanitarias WHERE data_vencimento < CURDATE() AND status_alvara = 'VENCIDO'";
$resultAlvara = $mysqli->query($queryAlvara);

// Consulte os documentos que estão vencidos
$queryDocumento = "SELECT * FROM documentos_alertas WHERE data_alerta < CURDATE()";
$resultDocumento = $mysqli->query($queryDocumento);

$alerts = array();

// Alvarás
if ($resultAlvara) {
    while ($row = $resultAlvara->fetch_assoc()) {
        // Formate a data para o formato "d/m/Y"
        $row['data_vencimento'] = date('d/m/Y', strtotime($row['data_vencimento']));
        $alerts[] = $row;
    }
    $resultAlvara->free();
}

// Documentos
if ($resultDocumento) {
    while ($row = $resultDocumento->fetch_assoc()) {
        // Formate a data para o formato "d/m/Y"
        $row['data_alerta'] = date('d/m/Y', strtotime($row['data_alerta']));
        $alerts[] = $row;
    }
    $resultDocumento->free();
}

$mysqli->close();

// Saída em formato JSON
header('Content-Type: application/json');
echo json_encode($alerts);
?>
