<?php
include('../db_conection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_subcategoria = $_POST['nome_subcategoria'];
    $categoria_id = $_POST['categoria_id'];

    $sql = "INSERT INTO subcategorias_documentos (nome_subcategoria, categoria_id) VALUES (?, ?)";
    $stmt = $mysqli->prepare($sql);

    if ($stmt === false) {
        die('Erro de preparação: ' . htmlspecialchars($mysqli->error));
    }

    $stmt->bind_param('si', $nome_subcategoria, $categoria_id);

    if ($stmt->execute()) {
        echo "Subcategoria cadastrada com sucesso!";
    } else {
        echo "Erro ao cadastrar subcategoria: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $mysqli->close();
} else {
    echo "Método de requisição inválido.";
}
?>
