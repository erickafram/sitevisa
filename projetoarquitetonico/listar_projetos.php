<?php
include('../db_conection.php');

// Query para buscar os projetos arquitetônicos
$query = "SELECT * FROM projeto_arquitetonico";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Listar Projetos Arquitetônicos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
        <h2>Projetos Arquitetônicos Cadastrados</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Status</th>
                <th>Número do Parecer</th>
                <th>Número do Processo</th>
                <th>Data da Aprovação</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php
                        // Extrai apenas o número do parecer, sem o ano
                        $numeroParecer = explode('.', $row['numero_parecer'])[0];
                        ?>
                        <a href="https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=<?php echo htmlspecialchars($numeroParecer); ?>" target="_blank">
                            <?php echo htmlspecialchars($row['numero_parecer']); ?>
                        </a>
                    </td>
                    <td>
                        <a href="https://sistemas.saude.to.gov.br/infovisacore/Manager/Procedure?identifier=<?php echo htmlspecialchars($row['numero_processo']); ?>&infoCompany=&procedureStatus=&procedureType=&procedureArea=" target="_blank">
                            <?php echo htmlspecialchars($row['numero_processo']); ?>
                        </a>
                    </td>
                    <td>
                        <?php
                        // Formata a data para d/m/Y
                        echo date('d/m/Y', strtotime($row['data_aprovacao']));
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>