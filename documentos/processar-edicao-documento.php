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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $documento_id = $_POST["id"];
    $novo_nome_documento = $_POST["novo_nome_documento"];
    $nova_categoria_id = $_POST["nova_categoria"];
    $nova_subcategoria_id = $_POST["nova_subcategoria"]; // Nova subcategoria

    // Verifica se um novo documento foi enviado
    if (!empty($_FILES["novo_documento"]["name"])) {
        $caminho_destino = "uploads/";
        $novo_documento = $_FILES["novo_documento"];
        $novo_tipo_documento = $novo_documento["type"];
        $nome_arquivo = uniqid() . '_' . $novo_documento["name"];
        $caminho_arquivo = $caminho_destino . $nome_arquivo;

        // Verifica se o tipo de arquivo é permitido
        $formatos_permitidos = array(
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "image/png",
            "image/jpeg",
            "application/vnd.ms-excel" // Adicione o formato XLS aqui
        );

        if (in_array($novo_tipo_documento, $formatos_permitidos)) {
            if (move_uploaded_file($novo_documento["tmp_name"], $caminho_arquivo)) {
                // Atualiza o nome, a categoria, a subcategoria e o caminho do documento no banco de dados
                $sql = "UPDATE documentos SET nome_documento = ?, tipo_documento = ?, caminho_documento = ?, categoria_id = ?, subcategoria_id = ? WHERE id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("sssiii", $novo_nome_documento, $novo_tipo_documento, $caminho_arquivo, $nova_categoria_id, $nova_subcategoria_id, $documento_id);

                if ($stmt->execute()) {
                    echo "Documento atualizado com sucesso!";
                } else {
                    echo "Erro ao atualizar o documento no banco de dados.";
                }
                $stmt->close();
            } else {
                echo "Erro ao fazer o upload do novo documento.";
            }
        } else {
            echo "Tipo de arquivo não permitido: " . $novo_tipo_documento;
        }
    } else {
        // Se nenhum novo documento foi enviado, apenas atualiza o nome, a categoria e a subcategoria do documento no banco de dados
        $sql = "UPDATE documentos SET nome_documento = ?, categoria_id = ?, subcategoria_id = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("siii", $novo_nome_documento, $nova_categoria_id, $nova_subcategoria_id, $documento_id);

        if ($stmt->execute()) {
            echo "Nome, categoria e subcategoria do documento atualizados com sucesso!";
        } else {
            echo "Erro ao atualizar o nome, a categoria e a subcategoria do documento no banco de dados.";
        }
        $stmt->close();
    }
} else {
    echo "Requisição inválida.";
}

$mysqli->close();
?>
