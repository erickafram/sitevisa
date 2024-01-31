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
// Inclua a verificação de sessão e controle de acesso aqui, se necessário

// Conecte-se ao banco de dados (substitua as informações de conexão)
include('../db_conection.php');

// Verifique se houve algum erro na conexão
if ($mysqli->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
}

// Inicialize variáveis para armazenar os valores dos filtros
$status = '';
$tipo = '';
$dataVencimentoInicio = '';
$dataVencimentoFim = '';
$dataCadastroInicio = '';
$dataCadastroFim = '';

// Processar o formulário de filtragem se for submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];
    $tipo = $_POST['tipo'];
    $dataVencimentoInicio = $_POST['data_vencimento_inicio'];
    $dataVencimentoFim = $_POST['data_vencimento_fim'];
    $dataCadastroInicio = $_POST['data_cadastro_inicio'];
    $dataCadastroFim = $_POST['data_cadastro_fim'];

    // Construa a consulta SQL com base nos filtros selecionados
    $query = "SELECT * FROM alvaras_sanitarias WHERE 1 = 1";

    if (!empty($status)) {
        $query .= " AND status_alvara = '$status'";
    }

    if (!empty($tipo)) {
        $query .= " AND tipo_alvara = '$tipo'";
    }

    if (!empty($dataVencimentoInicio)) {
        $query .= " AND data_vencimento >= '$dataVencimentoInicio'";
    }

    if (!empty($dataVencimentoFim)) {
        $query .= " AND data_vencimento <= '$dataVencimentoFim'";
    }

    if (!empty($dataCadastroInicio)) {
        $query .= " AND data_cadastro >= '$dataCadastroInicio'";
    }

    if (!empty($dataCadastroFim)) {
        $query .= " AND data_cadastro <= '$dataCadastroFim'";
    }

    $result = $mysqli->query($query);

    // Contar o número de resultados encontrados
    $num_results = $result->num_rows;
} else {
    // Consulta ao banco de dados para obter a lista completa de Alvarás Sanitários
    $query = "SELECT * FROM alvaras_sanitarias";
    $result = $mysqli->query($query);
    // Contar o número de resultados encontrados
    $num_results = $result->num_rows;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Alvarás Sanitários (Filtrada)</title>
    <!-- Adicione o link para o Bootstrap CSS, se necessário -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top: 55px;">
    <h5>Lista de Alvarás Sanitários (Filtrada)</h5>

    <form method="POST">
        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select class="form-select" name="status" id="status">
                <option value="">Todos</option>
                <option value="ATIVO" <?php echo ($status === 'ATIVO') ? 'selected' : ''; ?>>ATIVO</option>
                <option value="VENCIDO" <?php echo ($status === 'VENCIDO') ? 'selected' : ''; ?>>VENCIDO</option>
                <option value="CANCELADO" <?php echo ($status === 'CANCELADO') ? 'selected' : ''; ?>>CANCELADO</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo:</label>
            <select class="form-select" name="tipo" id="tipo">
                <option value="">Todos</option>
                <option value="Administrativo" <?php echo ($tipo === 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                <option value="Definitivo" <?php echo ($tipo === 'Definitivo') ? 'selected' : ''; ?>>Definitivo</option>
                <option value="Provisório" <?php echo ($tipo === 'Provisório') ? 'selected' : ''; ?>>Provisório</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Data de Vencimento (Início) e Data de Vencimento (Fim):</label>
            <div class="row">
                <div class="col">
                    <input type="date" class="form-control" name="data_vencimento_inicio" id="data_vencimento_inicio" value="<?php echo $dataVencimentoInicio; ?>">
                </div>
                <div class="col">
                    <input type="date" class="form-control" name="data_vencimento_fim" id="data_vencimento_fim" value="<?php echo $dataVencimentoFim; ?>">
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Data de Cadastro (Início) e Data de Cadastro (Fim):</label>
            <div class="row">
                <div class="col">
                    <input type="date" class="form-control" name="data_cadastro_inicio" id="data_cadastro_inicio" value="<?php echo $dataCadastroInicio; ?>">
                </div>
                <div class="col">
                    <input type="date" class="form-control" name="data_cadastro_fim" id="data_cadastro_fim" value="<?php echo $dataCadastroFim; ?>">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <?php if ($num_results > 0): ?>
        <!-- Mostrar a quantidade de resultados encontrados -->
        <div style="padding-top:10px;"></div>
        <p><?php echo "Encontrados $num_results resultados." ?></p>
        <table class="table">
            <thead>
            <tr>
                <th>Data de Cadastro</th>
                <th>Número</th>
                <th>Fantasia</th>
                <th>CNPJ</th>
                <th>Status</th>
                <th>Data de Vencimento</th>
                <th>Tipo de Alvará</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['data_cadastro'])); ?></td>
                    <td><?php echo $row['numero_alvara'] . '.' . date('Y'); ?></td>
                    <td><?php echo $row['nome_fantasia']; ?></td>
                    <td><?php echo $row['cnpj']; ?></td>
                    <td><?php echo $row['status_alvara']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['data_vencimento'])); ?></td>
                    <td><?php echo $row['tipo_alvara']; ?></td>
                    <td>
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownAcoes<?php echo $row['id']; ?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Ações
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownAcoes<?php echo $row['id']; ?>">
                                    <a class="dropdown-item" href="<?php echo 'https://sistemas.saude.to.gov.br/infovisacore/Manager/Company?filter=' . urlencode($row['nome_fantasia']); ?>" target="_blank">Ver Estabelecimento</a>
                                    <a class="dropdown-item" href="<?php echo 'https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=' . $row['numero_alvara']; ?>" target="_blank">Visualizar Alvará</a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum Alvará Sanitário encontrado com os filtros selecionados.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
<!-- Link para o arquivo JavaScript do Bootstrap, se necessário -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> -->
<!-- Se preferir, pode baixar o arquivo JavaScript do Bootstrap e incluí-lo localmente -->
</body>
</html>

<?php
// Feche a conexão com o banco de dados
$mysqli->close();
?>
