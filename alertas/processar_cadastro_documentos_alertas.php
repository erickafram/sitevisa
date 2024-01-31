<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../db_conection.php'); // Inclua o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar o formulário de cadastro de documentos com alertas
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $data_alerta = $_POST['data_alerta'];

    // Novo campo: Número de Processo
    $numero_processo_documentos = $_POST['numero_processo_documentos'];

    // Remover caracteres não numéricos do número de processo
    $numero_processo_documentos = preg_replace("/[^0-9]/", "", $numero_processo_documentos);

    // Obtenha o ID do usuário autenticado, por exemplo, a partir da sessão
    $usuario_id = $_SESSION['usuario_id']; // Certifique-se de que a chave 'usuario_id' corresponda ao seu sistema de autenticação

    // Valide os dados conforme necessário

    $sql = "INSERT INTO documentos_alertas (tipo_documento, numero_documento, data_alerta, usuario_id, numero_processo_documentos)
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssii", $tipo_documento, $numero_documento, $data_alerta, $usuario_id, $numero_processo_documentos);

        if ($stmt->execute()) {
            // Redirecionar para a página de listagem após o cadastro bem-sucedido
            header('Location: lista_documentos_alertas.php');
            exit();
        } else {
            // Exibir mensagem de erro em caso de falha no cadastro
            $erro = "Erro ao cadastrar documento com alerta: " . htmlspecialchars($stmt->error);
        }

        $stmt->close();
    } else {
        // Exibir mensagem de erro em caso de falha na preparação da consulta
        $erro = "Erro de preparação: " . htmlspecialchars($mysqli->error);
    }

    $mysqli->close();
}

// Inclua o cabeçalho, rodapé e mensagens de erro conforme necessário
?>
