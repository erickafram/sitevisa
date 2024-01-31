<?php
include('../db_conection.php');

// Handle database connection error
if ($mysqli->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
}

// Obtém a data atual
$currentDate = date('Y-m-d');

// Seleciona todos os alvarás com status não igual a 'CANCELADO'
$query = "SELECT * FROM alvaras_sanitarias WHERE status_alvara != 'CANCELADO'";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Atualiza o status dos alvarás com base na data de vencimento
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $vencimento = $row['data_vencimento'];

        // Converter a data de vencimento para o formato Y-m-d para comparação
        $dataVencimentoFormatada = date('Y-m-d', strtotime($vencimento));

        if ($currentDate < $dataVencimentoFormatada) {
            // Se a data atual for menor que a data de vencimento, definir o status como "ATIVO"
            $status = 'ATIVO';
        } else {
            // Caso contrário, definir o status como "VENCIDO"
            $status = 'VENCIDO';
        }

        // Atualiza o status do Alvará
        $updateQuery = "UPDATE alvaras_sanitarias SET status_alvara = ? WHERE id = ?";
        $updateStmt = $mysqli->prepare($updateQuery);
        $updateStmt->bind_param("si", $status, $id);
        $updateStmt->execute();
    }
}


$mysqli->close();
?>
