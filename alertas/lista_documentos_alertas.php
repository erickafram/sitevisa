<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || ($_SESSION['nivel_acesso'] !== 'Administrador' && $_SESSION['nivel_acesso'] !== 'Gestor')) {
    header('Location: /sitevisa/404.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Documentos com Alertas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top: 50px;">
    <h5>Lista de Documentos com Alertas</h5>
    <!-- Exibir a lista de documentos com alertas aqui -->
    <?php
    include('../db_conection.php');

    // Consulta SQL para recuperar documentos com alertas e o nome do usuário que cadastrou
    $sql = "SELECT d.id, d.tipo_documento, d.numero_documento, d.data_alerta, u.nome_completo, d.numero_processo_documentos
        FROM documentos_alertas AS d
        LEFT JOIN usuarios AS u ON d.usuario_id = u.id";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        echo '<table class="table table-striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Tipo de Documento</th>';
        echo '<th>Número do Documento</th>';
        echo '<th>Data do Alerta</th>';
        echo '<th>Cadastrado por</th>'; // Nova coluna
        echo '<th>Dias Restantes</th>'; // Nova coluna
        echo '<th>Ações</th>'; // Adicionando a coluna de ações
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['tipo_documento']) . '</td>';
            echo '<td>' . htmlspecialchars($row['numero_documento']) . '</td>';
            $dataAlertaFormatada = date('d/m/Y', strtotime($row['data_alerta']));
            echo '<td>' . $dataAlertaFormatada . '</td>';
            echo '<td>' . htmlspecialchars($row['nome_completo']) . '</td>'; // Nome do usuário

            // Calcular os dias restantes até a data do alerta
            $dataAlerta = new DateTime($row['data_alerta']);
            $hoje = new DateTime();
            $hoje->setTime(0, 0, 0); // Zerar o horário para comparar apenas as datas
            $interval = $hoje->diff($dataAlerta);
            $diasRestantes = $interval->format('%r%a'); // Formatando para obter o sinal e o número de dias

            if ($diasRestantes < 0) {
                echo '<td>VENCIDO</td>';
            } elseif ($diasRestantes == 0) {
                echo '<td>Vence Hoje</td>';
            } else {
                echo '<td>' . $diasRestantes . ' Dias</td>';
            }

            echo '<td>';
            echo '<div class="dropdown">';
            echo '<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">';
            echo 'Ações';
            echo '</button>';
            echo '<div class="dropdown-menu" aria-labelledby="acoesDropdown">';
            echo '<a class="dropdown-item" href="editar.php?id=' . htmlspecialchars($row['id']) . '">Editar</a>';
            if (isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] === 'Administrador'):
                echo '<a class="dropdown-item" href="excluir.php?id=' . htmlspecialchars($row['id']) . '">Excluir</a>';
            endif;
            $urlVisualizarDocumento = 'https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=' . htmlspecialchars($row['numero_documento']);
            echo '<a class="dropdown-item" href="' . $urlVisualizarDocumento . '" target="_blank">Visualizar Documento</a>';

            // Adicione o link "Visualizar Processo" aqui
            $numeroProcessoMascarado = preg_replace('/(\d{4})(\d{2})(\d{10})/', '$1.$2.$3', $row['numero_processo_documentos']);
            $urlVisualizarProcesso = 'https://sistemas.saude.to.gov.br/Infovisacore/Manager/Procedure?identifier=' . $numeroProcessoMascarado . '&infoCompany=&procedureStatus=&procedureType=&procedureArea=';
            echo '<a class="dropdown-item" href="' . $urlVisualizarProcesso . '" target="_blank">Visualizar Processo</a>';

            echo '</div>';
            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'Nenhum documento com alerta encontrado.';
    }

    $mysqli->close();
    ?>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
