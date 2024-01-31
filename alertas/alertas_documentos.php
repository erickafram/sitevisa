<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || ($_SESSION['nivel_acesso'] !== 'Administrador' && $_SESSION['nivel_acesso'] !== 'Gestor')) {
    header('Location: /sitevisa/404.php');
    exit;
}
?>
<?php
// Inclui o cabeçalho
include('../includes/header.php');
include('../db_conection.php');

// Consulta para alvarás vencidos
$queryAlvara = "SELECT * FROM alvaras_sanitarias WHERE data_vencimento < CURDATE() AND status_alvara = 'VENCIDO'";
$resultAlvara = $mysqli->query($queryAlvara);

// Consulta para documentos vencidos
$queryDocumento = "SELECT * FROM documentos_alertas WHERE data_alerta < CURDATE()";
$resultDocumento = $mysqli->query($queryDocumento);
?>

<div class="container mt-5" style="padding-top:50px;">

    <!-- Seção de Alvarás Vencidos -->
    <h4>Alvarás Vencidos</h4>
    <?php if ($resultAlvara->num_rows > 0): ?>
        <table class="table">
            <thead>
            <tr>
                <th>Número do Alvará</th>
                <th>Nome Fantasia</th>
                <th>Data de Vencimento</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $resultAlvara->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['numero_alvara']); ?></td>
                    <td><?php echo htmlspecialchars($row['nome_fantasia']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['data_vencimento'])); ?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownAlvara<?php echo $row['id']; ?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Ações
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownAlvara<?php echo $row['id']; ?>">
                                <a class="dropdown-item" href="https://sistemas.saude.to.gov.br/infovisacore/Manager/Procedure?identifier=<?php echo isset($row['numero_processo']) ? htmlspecialchars($row['numero_processo']) : ''; ?>&infoCompany=&procedureStatus=&procedureType=&procedureArea=" target="_blank">Ver Alvará</a>
                                <!-- Outras ações como editar ou excluir podem ser adicionadas aqui -->
                            </div>
                        </div>
                    </td>
                </tr>

            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum alvará vencido encontrado.</p>
    <?php endif; ?>

    <!-- Seção de Documentos Vencidos -->
    <h4>Documentos Vencidos</h4>
    <?php if ($resultDocumento->num_rows > 0): ?>
        <table class="table">
            <thead>
            <tr>
                <th>Tipo de Documento</th>
                <th>Número do Documento</th>
                <th>Data Vencimento</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $resultDocumento->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['tipo_documento']); ?></td>
                    <td><?php echo htmlspecialchars($row['numero_documento']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['data_alerta'])); ?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownDocumento<?php echo $row['id']; ?>" data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                Ações
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownDocumento<?php echo $row['id']; ?>">
                                <a class="dropdown-item" href="https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=<?php echo htmlspecialchars($row['numero_documento']); ?>" target="_blank">Ver Documento</a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum documento vencido encontrado.</p>
    <?php endif; ?>

</div>

<?php
// Fecha a conexão com o banco de dados
$mysqli->close();

// Inclui o rodapé
include('../includes/footer.php');
?>
