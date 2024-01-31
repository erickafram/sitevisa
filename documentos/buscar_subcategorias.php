<?php
include('../db_conection.php');

if (isset($_GET['categoria_id'])) {
    $categoria_id = $_GET['categoria_id'];

    $sql = "SELECT id, nome_subcategoria FROM subcategorias_documentos WHERE categoria_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $subcategorias = [];
    while ($row = $result->fetch_assoc()) {
        $subcategorias[] = $row;
    }

    echo json_encode($subcategorias);
}
?>
