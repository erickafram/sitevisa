<?php
include('../db_conection.php'); // Inclua o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar o formulário de edição de documento com alerta
    $id = $_POST['id'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];

    // Remover caracteres não numéricos do número do processo
    $numero_processo_documentos = preg_replace("/[^0-9]/", "", $_POST['numero_processo_documentos']);

    // Converter a data do formato "d/m/Y" para "Y-m-d"
    $data_alerta = date('Y-m-d', strtotime($_POST['data_alerta']));

    // Valide os dados conforme necessário

    // Atualize os dados na tabela documentos_alertas
    $sql = "UPDATE documentos_alertas SET tipo_documento = ?, numero_documento = ?, numero_processo_documentos = ?, data_alerta = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssi", $tipo_documento, $numero_documento, $numero_processo_documentos, $data_alerta, $id);

        if ($stmt->execute()) {
            // Redirecionar para a página de listagem após a edição bem-sucedida
            header('Location: lista_documentos_alertas.php');
            exit();
        } else {
            // Exibir mensagem de erro em caso de falha na edição
            $erro = "Erro ao editar documento com alerta: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    } else {
        // Exibir mensagem de erro em caso de falha na preparação da consulta
        $erro = "Erro de preparação: " . htmlspecialchars($mysqli->error);
    }

    $mysqli->close();
} else {
    // Redirecionar de volta para a página de listagem se o formulário não foi submetido
    header('Location: lista_documentos_alertas.php');
    exit();
}
?>
