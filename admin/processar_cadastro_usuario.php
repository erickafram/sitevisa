<?php
include '../db_conection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_completo = $_POST["nome_completo"];
    $cpf = $_POST["cpf"];
    $email = $_POST["email"];
    $telefone = $_POST["telefone"];
    $data_nascimento = $_POST["data_nascimento"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);

    // Insira os dados do usuário na tabela
    $sql = "INSERT INTO usuarios (nome_completo, cpf, email, telefone, data_nascimento, senha) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("Erro de preparação: " . $mysqli->error);
    }
    if (!$stmt->bind_param("ssssss", $nome_completo, $cpf, $email, $telefone, $data_nascimento, $senha)) {
        die("Erro ao vincular parâmetros: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        echo "Erro ao cadastrar o usuário: " . $stmt->error;
    } else {
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $mysqli->close();
}
?>
