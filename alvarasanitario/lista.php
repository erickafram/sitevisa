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
include('../db_conection.php');
if ($mysqli->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifique se o formulário de edição foi enviado
    if (isset($_POST['numero_alvara_edit'])) {
        $id = $_POST['id']; // ID do Alvará Sanitário
        $numero_alvara_edit = $_POST['numero_alvara_edit'];
        $numero_processo_edit = $_POST['numero_processo_edit']; // Manter o número do processo com a máscara
        $status_alvara_edit = $_POST['status_alvara_edit'];
        $nome_fantasia_edit = $_POST['nome_fantasia_edit'];
        $cnpj_edit = $_POST['cnpj_edit'];
        $data_vencimento_edit = $_POST['data_vencimento_edit'];
        $tipo_alvara_edit = $_POST['tipo_alvara_edit'];

        $query = "UPDATE alvaras_sanitarias SET numero_alvara = ?, status_alvara = ?, nome_fantasia = ?, cnpj = ?, data_vencimento = ?, tipo_alvara = ?, numero_processo = ? WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssssssi", $numero_alvara_edit, $status_alvara_edit, $nome_fantasia_edit, $cnpj_edit, $data_vencimento_edit, $tipo_alvara_edit, $numero_processo_edit, $id);
        if ($stmt->execute()) {
            // Atualização bem-sucedida, redirecione para a página de lista novamente ou exiba uma mensagem de sucesso
            header('Location: lista.php');
            exit;
        } else {
            // Erro na atualização, exiba uma mensagem de erro
            echo '<div class="alert alert-danger">Erro ao atualizar os dados. Por favor, tente novamente.</div>';
        }
    }
}
$query = "SELECT * FROM alvaras_sanitarias";
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    // Adicione as condições de pesquisa à consulta SQL
    $query .= " WHERE numero_alvara LIKE '%$searchTerm%' OR status_alvara LIKE '%$searchTerm%' OR nome_fantasia LIKE '%$searchTerm%' OR cnpj LIKE '%$searchTerm%' OR tipo_alvara LIKE '%$searchTerm%'";
}

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
<div class="container mt-5" style="padding-top: 55px;">
    <h5>Lista de Alvarás Sanitários Cadastrados</h5>
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Pesquisar" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button class="btn btn-primary" type="submit">Pesquisar</button>
        </div>
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
                <th>Ações</th>
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
                    <td>
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownAcoes<?php echo $row['id']; ?>" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Ações
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownAcoes<?php echo $row['id']; ?>">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editarModal<?php echo $row['id']; ?>">Editar</a>
                                    <?php if (isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] === 'Administrador'): ?>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#excluirModal<?php echo $row['id']; ?>">Excluir</a>
                                    <?php endif; ?>
                                    <a class="dropdown-item" href="<?php echo 'https://sistemas.saude.to.gov.br/infovisacore/Manager/Procedure?identifier=' . $row['numero_processo'] . '&infoCompany=&procedureStatus=&procedureType=&procedureArea='; ?>" target="_blank">Ver Processo</a>
                                    <a class="dropdown-item" href="<?php echo 'https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=' . $row['numero_alvara']; ?>" target="_blank">Visualizar Alvará</a>
                                    <a class="dropdown-item" href="<?php echo 'https://sistemas.saude.to.gov.br/infovisacore/Manager/Company?filter=' . urlencode($row['nome_fantasia']); ?>" target="_blank">Ver Estabelecimento</a>
                                    <a class="dropdown-item" href="<?php echo 'qrcodes/' . $row['numero_alvara'] . '.png'; ?>" download>Baixar QRCode</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#copiarQRCodeModal<?php echo $row['id']; ?>">Copiar QRCode</a>
                                    <a class="dropdown-item" href="https://vigilancia-to.com.br/sitevisa/alvarasanitario/consultar_alvara.php?numero_alvara=<?php echo $row['numero_alvara']; ?>" target="_blank">Informações Alvará</a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Modal para Copiar QRCode -->
                <div class="modal fade" id="copiarQRCodeModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="copiarQRCodeModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="copiarQRCodeModalLabel<?php echo $row['id']; ?>">Copiar QRCode para inserir INFOVISA</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <div>
                                    <span style="font-size: 8px">QRCode do Alvará:</span>
                                    <img id="qrCodeImage<?php echo $row['id']; ?>" alt="" src="qrcodes/<?php echo $row['numero_alvara']; ?>.png" style="height: 120px; width: 120px" />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" onclick="copiarQRCode(<?php echo $row['id']; ?>)">Copiar QRCode</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editarModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editarModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarModalLabel<?php echo $row['id']; ?>">Editar Alvará</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <form action="#" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <div class="form-group">
                                        <label for="numero_alvara_edit">Novo Número do Alvará:</label>
                                        <input type="text" class="form-control" name="numero_alvara_edit" id="numero_alvara_edit" value="<?php echo $row['numero_alvara']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="numero_processo_edit">Novo Número do Processo:</label>
                                        <input type="text" class="form-control" name="numero_processo_edit" id="numero_processo_edit" value="<?php echo $row['numero_processo']; ?>" required>
                                        <small style="font-size: 12px; color:#757575; margin-top: 5px;">Digite o número do Processo com a máscara (exemplo: 2023.01.0000000382)</small>
                                    </div>


                                    <div class="form-group">
                                        <label for="status_alvara_edit">Novo Status:</label>
                                        <select class="form-control" name="status_alvara_edit" id="status_alvara_edit">
                                            <option value="ATIVO" <?php echo ($row['status_alvara'] === 'ATIVO') ? 'selected' : ''; ?>>ATIVO</option>
                                            <option value="VENCIDO" <?php echo ($row['status_alvara'] === 'VENCIDO') ? 'selected' : ''; ?>>VENCIDO</option>
                                            <option value="CANCELADO" <?php echo ($row['status_alvara'] === 'CANCELADO') ? 'selected' : ''; ?>>CANCELADO</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome_fantasia_edit">Novo Nome Fantasia:</label>
                                        <input type="text" class="form-control" name="nome_fantasia_edit" id="nome_fantasia_edit" value="<?php echo $row['nome_fantasia']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="cnpj_edit">Novo CNPJ:</label>
                                        <input type="text" class="form-control" name="cnpj_edit" id="cnpj_edit" value="<?php echo $row['cnpj']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="data_vencimento_edit">Nova Data de Vencimento:</label>
                                        <input type="date" class="form-control" name="data_vencimento_edit" id="data_vencimento_edit" value="<?php echo $row['data_vencimento']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="tipo_alvara_edit">Novo Tipo de Alvará:</label>
                                        <select class="form-control" name="tipo_alvara_edit" id="tipo_alvara_edit">
                                            <option value="Administrativo" <?php echo ($row['tipo_alvara'] === 'Administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                                            <option value="Definitivo" <?php echo ($row['tipo_alvara'] === 'Definitivo') ? 'selected' : ''; ?>>Definitivo</option>
                                            <option value="Provisório" <?php echo ($row['tipo_alvara'] === 'Provisório') ? 'selected' : ''; ?>>Provisório</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="margin-top:15px;">Salvar</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="excluirModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="excluirModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="excluirModalLabel<?php echo $row['id']; ?>">Confirmar Exclusão</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                Tem certeza de que deseja excluir o Alvará com número <?php echo $row['numero_alvara']; ?>?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <form method="POST" action="excluir_alvara.php">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum Alvará Sanitário cadastrado no sistema.</p>
    <?php endif; ?>
</div>
<?php include('../includes/footer.php'); ?>
<!-- Include inputmask library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
<script>
    $(document).ready(function() {
        // Apply input mask to the "Número do Processo" field
        //$('#numero_processo_edit').inputmask('9999.99.9999999999');
    });

    function copiarQRCode(rowId) {
        var qrCodeImage = document.querySelector('#qrCodeImage' + rowId);

        if (qrCodeImage) {
            var range = document.createRange();
            range.selectNode(qrCodeImage);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            document.execCommand('copy');
            window.getSelection().removeAllRanges();

            alert('QRCode copiado para a área de transferência!');
        } else {
            alert('QRCode não encontrado.');
        }
    }
</script>
</body>
</html>
<?php
$mysqli->close();
?>
