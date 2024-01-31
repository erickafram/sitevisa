<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['nivel_acesso']) || ($_SESSION['nivel_acesso'] !== 'Administrador' && $_SESSION['nivel_acesso'] !== 'Gestor')) {
    header('Location: /sitevisa/404.php');
    exit;
}
$mensagemCadastro = ''; // Inicializa a mensagem vazia
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../db_conection.php');
    if ($mysqli->connect_error) {
        die('Erro na conexão com o banco de dados: ' . $mysqli->connect_error);
    }
    $numero_alvara = $_POST['numero_alvara'];
    $numero_processo = $_POST['numero_processo']; // Add this line to retrieve the "Número do Processo" value
    $cnpj = $_POST['cnpj'];
    $nome_fantasia = $_POST['nome_fantasia'];
    $status_alvara = $_POST['status_alvara'];
    $tipo_alvara = $_POST['tipo_alvara'];
    $data_vencimento = $_POST['data_vencimento'];
    $razao_social = $_POST['razao_social']; // Novo campo: Razão Social
    $telefone_com_ddd = $_POST['telefone_com_ddd']; // Novo campo: Telefone com DDD
    $email = $_POST['email']; // Novo campo: Email
    $municipio = $_POST['municipio']; // Novo campo: Município
    $query = "SELECT * FROM alvaras_sanitarias WHERE numero_alvara = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $numero_alvara);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $mensagemCadastro = '<p>O número do Alvará Sanitário ' . $numero_alvara . ' já está cadastrado no sistema.</p>';
    } else {
        $query = "INSERT INTO alvaras_sanitarias (numero_alvara, cnpj, nome_fantasia, status_alvara, tipo_alvara, data_vencimento, numero_processo, razao_social, telefone_com_ddd, email, municipio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssssssssss", $numero_alvara, $cnpj, $nome_fantasia, $status_alvara, $tipo_alvara, $data_vencimento, $numero_processo, $razao_social, $telefone_com_ddd, $email, $municipio);
        if ($stmt->execute()) {
            $mensagemCadastro = '<p>O número do Alvará Sanitário ' . $numero_alvara . ' foi cadastrado com sucesso no sistema.</p>';
        } else {
            $mensagemCadastro = '<p>Ocorreu um erro ao cadastrar o número do Alvará Sanitário. Por favor, tente novamente.</p>';
        }
    }
    //$linkAlvara = 'https://34.150.156.86/sitevisa/consultar_alvara.php?tipo_pesquisa=numero&numero_alvara=' . $numero_alvara . '&nome_fantasia=';
    $linkAlvara = 'https://vigilancia-to.com.br/sitevisa/alvarasanitario/consultar_alvara.php?numero_alvara=' . $numero_alvara;

    // Gere o QRCode
    include('qrcode/qrlib.php');
    QRcode::png($linkAlvara, 'qrcodes/' . $numero_alvara . '.png', QR_ECLEVEL_L, 4);

    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Alvará</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/imask/6.0.6/imask.min.js"></script>
</head>
<body>
<?php include('../includes/header.php'); ?>
<div class="container mt-5" style="padding-top: 55px;">
    <h5>Cadastrar Alvará</h5>
    <form action="" method="POST">
        <div class="card">
            <div class="card-body">
                <p>Preencher informações do alvará sanitário</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero_alvara">N° Alvará Sanitário:</label>
                            <p style="font-size: 12px; color:#757575; margin-bottom: 5px;">Digite o número do Alvará Sanitário, cadastrado no Infovisa</p>
                            <?php
                            $ano_atual = date('Y');
                            echo '<input type="text" class="form-control" name="numero_alvara" id="numero_alvara" placeholder="Digite o número do alvará sem o ano ' . $ano_atual . '" required>';
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero_processo">N° Processo:</label>
                            <p style="font-size: 12px; color:#757575; margin-bottom: 5px;">Digite o número do Processo com a máscara (exemplo: 2023.01.0000000382)</p>
                            <input type="text" class="form-control" name="numero_processo" id="numero_processo" placeholder="Digite o número do processo" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status_alvara">Status:</label>
                            <select class="form-control" name="status_alvara" id="status_alvara">
                                <option value="ATIVO">ATIVO</option>
                                <option value="VENCIDO">VENCIDO</option>
                                <option value="CANCELADO">CANCELADO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_alvara">Tipo de Alvará:</label>
                            <select class="form-control" name="tipo_alvara" id="tipo_alvara">
                                <option value="Definitivo">Definitivo</option>
                                <option value="Administrativo">Administrativo</option>
                                <option value="Provisório">Provisório</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="data_vencimento">Data de Validade do Alvará:</label>
                            <input type="date" class="form-control" name="data_vencimento" id="data_vencimento" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:20px;">
            <div class="card-body">
                <p>Preencher informações da empresa</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cnpj">CNPJ:</label>
                            <input type="text" class="form-control" name="cnpj" id="cnpj" placeholder="99.999.999/9999-99" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome_fantasia">Nome Fantasia:</label>
                            <input type="text" class="form-control" name="nome_fantasia" id="nome_fantasia" placeholder="Digite o Nome Fantasia">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6" style="margin-top:5px;">
                        <button type="button" class="btn btn-primary btn-sm" onclick="consultarCNPJ()">Consultar CNPJ</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="limparCampos()">Limpar Campos</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="razao_social">Razão Social:</label>
                            <input type="text" class="form-control" name="razao_social" id="razao_social" placeholder="Digite a Razão Social" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefone_com_ddd">Telefone com DDD:</label>
                            <input type="text" class="form-control" name="telefone_com_ddd" id="telefone_com_ddd" placeholder="(99) 99999-9999" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Digite o Email" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="municipio">Município:</label>
                            <input type="text" class="form-control" name="municipio" id="municipio" placeholder="Digite o Município" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:15px;">Cadastrar</button>
    </form>
</div>

<!-- Modal para QRCode -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QRCode do Alvará</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo $mensagemCadastro; ?>
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                    <p>
                        <a href="qrcodes/<?php echo $numero_alvara; ?>.png" download>Download QRCode</a>
                        <button id="copiarQRCode" onclick="copiarQRCode()">Copiar QRCode para INFOVISA</button>
                    </p>
                    <div>
                        <span style="font-size:8px">QRCode do Alvará:</span>
                        <img id="qrCodeImage" alt="" src="qrcodes/<?php echo $numero_alvara; ?>.png" style="height:120px; width:120px" />
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
    // FUNÇÃO PARA PREENCHER OS CAMPOS SEM PRECISAR DE BOTÃO PARA CONSULTAR
    function preencherDadosEmpresa() {
        var cnpjInput = document.getElementById('cnpj');
        var nomeFantasiaInput = document.getElementById('nome_fantasia');
        var razaoSocialInput = document.getElementById('razao_social');
        var telefoneComDDDInput = document.getElementById('telefone_com_ddd');
        var emailInput = document.getElementById('email');
        var municipioInput = document.getElementById('municipio');
        var cnpj = cnpjInput.value.replace(/[^\d]+/g, ''); // Remover caracteres não numéricos
        // Use AJAX para fazer a solicitação para a API
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log(xhr.responseText); // Adicione este console.log para depuração
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response['RAZAO SOCIAL']) {
                        razaoSocialInput.value = response['RAZAO SOCIAL'];
                        nomeFantasiaInput.value = response['NOME FANTASIA']; // Adicione esta linha
                        telefoneComDDDInput.value = response['DDD'] + ' ' + response['TELEFONE'];
                        emailInput.value = response['EMAIL'];
                        municipioInput.value = response['MUNICIPIO'];
                    } else {
                        // Limpe os campos se não houver valor da API
                        razaoSocialInput.value = '';
                        nomeFantasiaInput.value = ''; // Adicione esta linha
                        telefoneComDDDInput.value = '';
                        emailInput.value = '';
                        municipioInput.value = '';
                    }
                } else {
                    console.error('Erro na solicitação para a API');
                }
            }
        };
        xhr.open('GET', 'https://api-publica.speedio.com.br/buscarcnpj?cnpj=' + cnpj, true);
        xhr.send();
    }

    // FUNÇÃO PARA PREENCHER OS CAMPOS QUE PRECISA DO BOTÃO PARA CONSULTAR
    function consultarCNPJ() {
        var cnpjInput = document.getElementById('cnpj');
        var nomeFantasiaInput = document.getElementById('nome_fantasia');
        var razaoSocialInput = document.getElementById('razao_social');
        var telefoneComDDDInput = document.getElementById('telefone_com_ddd');
        var emailInput = document.getElementById('email');
        var municipioInput = document.getElementById('municipio');
        var cnpj = cnpjInput.value.replace(/[^\d]+/g, ''); // Remover caracteres não numéricos

        // Use AJAX para fazer a solicitação para a API
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log(xhr.responseText); // Adicione este console.log para depuração
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response['RAZAO SOCIAL']) {
                        razaoSocialInput.value = response['RAZAO SOCIAL'];
                        nomeFantasiaInput.value = response['NOME FANTASIA']; // Adicione esta linha
                        telefoneComDDDInput.value = response['DDD'] + ' ' + response['TELEFONE'];
                        emailInput.value = response['EMAIL'];
                        municipioInput.value = response['MUNICIPIO'];
                    } else {
                        // Limpe os campos se não houver valor da API
                        razaoSocialInput.value = '';
                        nomeFantasiaInput.value = ''; // Adicione esta linha
                        telefoneComDDDInput.value = '';
                        emailInput.value = '';
                        municipioInput.value = '';
                    }
                } else {
                    console.error('Erro na solicitação para a API');
                }
            }
        };
        xhr.open('GET', 'https://api-publica.speedio.com.br/buscarcnpj?cnpj=' + cnpj, true);
        xhr.send();
    }

    function limparCampos() {
        var cnpjInput = document.getElementById('cnpj');
        var nomeFantasiaInput = document.getElementById('nome_fantasia');
        var razaoSocialInput = document.getElementById('razao_social');
        var telefoneComDDDInput = document.getElementById('telefone_com_ddd');
        var emailInput = document.getElementById('email');
        var municipioInput = document.getElementById('municipio');

        // Limpe os campos
        cnpjInput.value = '';
        nomeFantasiaInput.value = '';
        razaoSocialInput.value = '';
        telefoneComDDDInput.value = '';
        emailInput.value = '';
        municipioInput.value = '';
    }


    function verificarCNPJ() {
        var cnpjInput = document.getElementById('cnpj');
        var nomeFantasiaInput = document.getElementById('nome_fantasia');
        var cnpj = cnpjInput.value;

        // Verifique se o CNPJ possui um formato válido aqui, se necessário

        // Use AJAX para verificar o CNPJ no servidor
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.exists) {
                    // O CNPJ existe no banco de dados, preencha o campo "Nome Fantasia"
                    nomeFantasiaInput.value = response.nomeFantasia;
                } else {
                    // O CNPJ não existe no banco de dados, deixe o campo "Nome Fantasia" vazio
                    nomeFantasiaInput.value = '';
                }
            }
        };
        xhr.open('GET', 'verificar_cnpj.php?cnpj=' + cnpj, true);
        xhr.send();
    }
    function copiarQRCode() {
        var qrCodeImage = document.querySelector('#qrCodeImage');

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
    const numeroProcessoInput = document.getElementById('numero_processo');
    const numeroProcessoMask = IMask(numeroProcessoInput, {
        mask: '0000.00.0000000000'
    });
    const cnpjInput = document.getElementById('cnpj');
    const cnpjMask = IMask(cnpjInput, {
        mask: '00.000.000/0000-00'
    });

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $mensagemCadastro): ?>
    var qrCodeModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
    qrCodeModal.show();
    <?php endif; ?>
</script>
</body>
</html>
