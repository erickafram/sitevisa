<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] !== 'Administrador') {
    header('Location: /sitevisa/404.php');
    exit;
}
?>
<?php
include('../db_conection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria_id = $_POST["categoria"];
    $subcategoria_id = $_POST["subcategoria"]; // Adicionado para receber o ID da subcategoria
    $caminho_destino = "uploads/";

    $uploads = $_FILES["documento"];
    $num_files = count($uploads['name']);

    $formatos_permitidos = array(
        "pdf" => "application/pdf",
        "doc" => "application/msword",
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "png" => "image/png",
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "xls" => "application/vnd.ms-excel" // Adicione o formato XLS aqui
    );


    $erro = false;

    for ($i = 0; $i < $num_files; $i++) {
        $extensao_arquivo = strtolower(pathinfo($uploads["name"][$i], PATHINFO_EXTENSION));

        if (array_key_exists($extensao_arquivo, $formatos_permitidos)) {
            $nome_documento = !empty($_POST["nome_documento"]) ? $_POST["nome_documento"] : pathinfo($uploads["name"][$i], PATHINFO_FILENAME);
            $caminho_arquivo = $caminho_destino . $uploads["name"][$i];

            if (move_uploaded_file($uploads["tmp_name"][$i], $caminho_arquivo)) {
                // Modificado para incluir subcategoria_id na consulta SQL
                $sql = "INSERT INTO documentos (nome_documento, categoria_id, subcategoria_id, tipo_documento, caminho_documento) VALUES (?, ?, ?, ?, ?)";
                $stmt = $mysqli->prepare($sql);
                if (false === $stmt) {
                    echo 'Erro de preparação: ' . htmlspecialchars($mysqli->error);
                    $erro = true;
                    break;
                }

                $bind = $stmt->bind_param("siiss", $nome_documento, $categoria_id, $subcategoria_id, $formatos_permitidos[$extensao_arquivo], $caminho_arquivo);
                if (false === $bind) {
                    echo 'Erro ao vincular parâmetros: ' . htmlspecialchars($stmt->error);
                    $erro = true;
                    break;
                }

                if (!$stmt->execute()) {
                    echo "Erro ao cadastrar o documento no banco de dados: " . htmlspecialchars($stmt->error);
                    $erro = true;
                    break;
                }
                $stmt->close();
            } else {
                echo "Erro ao fazer o upload do documento.";
                $erro = true;
                break;
            }
        } else {
            echo "Tipo de arquivo não permitido: " . htmlspecialchars($extensao_arquivo);
            $erro = true;
            break;
        }
    }

    if (!$erro) {
        echo "Documentos cadastrados com sucesso!";
    }

    $mysqli->close();
}
?>
