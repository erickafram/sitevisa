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

// Handle database connection error
if ($mysqli->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
}

// Função para atualizar o status dos alvarás
function atualizarStatusAlvaras() {
    global $mysqli;

    // Consulta todos os alvarás
    $query = "SELECT * FROM alvaras_sanitarias";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataVencimento = strtotime($row['data_vencimento']);
            $dataAtual = strtotime(date('Y-m-d'));

            // Verifique se a data de vencimento expirou
            if ($dataVencimento < $dataAtual && $row['status_alvara'] !== 'CANCELADO') {
                // Atualize o status para 'VENCIDO'
                $id = $row['id'];
                $queryUpdate = "UPDATE alvaras_sanitarias SET status_alvara = 'VENCIDO' WHERE id = ?";
                $stmt = $mysqli->prepare($queryUpdate);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
            }
        }
        echo '<div class="alert alert-success">Status dos alvarás atualizados com sucesso.</div>';
    } else {
        echo '<div class="alert alert-warning">Nenhum alvará cadastrado no sistema.</div>';
    }
}


// Verifique se o botão de atualização foi clicado
if (isset($_POST['atualizar_status'])) {
    atualizarStatusAlvaras();
}

// Consulta todos os alvarás
$query = "SELECT * FROM alvaras_sanitarias";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Alvarás Sanitários</title>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top: 50px;">
    <h5>Lista de Alvarás Sanitários Cadastrados</h5>
    <form method="POST">
        <button class="btn btn-primary" type="submit" name="atualizar_status">Atualizar Status</button>
    </form>
    <?php if ($result->num_rows > 0): ?>
        <table class="table">
            <thead>
            <tr>
                <th>Data de Cadastro</th>
                <th>Número</th>
                <th>Status</th>
                <th>Fantasia</th>
                <th>CNPJ</th>
                <th>Data de Vencimento</th>
                <th>Tipo de Alvará</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['data_cadastro'])); ?></td>
                    <td><?php echo $row['numero_alvara'] . '.' . date('Y'); ?></td>
                    <td><?php echo $row['status_alvara']; ?></td>
                    <td><?php echo $row['nome_fantasia']; ?></td>
                    <td><?php echo $row['cnpj']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['data_vencimento'])); ?></td>
                    <td><?php echo $row['tipo_alvara']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum Alvará Sanitário cadastrado no sistema.</p>
    <?php endif; ?>
</div>
</body>
</html>
