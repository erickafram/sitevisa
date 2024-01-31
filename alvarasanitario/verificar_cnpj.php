<?php
include('../db_conection.php');

if ($mysqli->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
}

$cnpj = $_GET['cnpj'];

$query = "SELECT nome_fantasia FROM alvaras_sanitarias WHERE cnpj = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $cnpj);
$stmt->execute();
$result = $stmt->get_result();

$response = array();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['exists'] = true;
    $response['nomeFantasia'] = $row['nome_fantasia'];
} else {
    $response['exists'] = false;
    $response['nomeFantasia'] = '';
}

$stmt->close();
$mysqli->close();

echo json_encode($response);
?>
