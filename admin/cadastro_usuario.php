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
include('../db_conection.php'); // Inclua o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar o formulário de cadastro
    $nome_completo = $_POST['nome_completo'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $data_nascimento = $_POST['data_nascimento'];
    $data_nascimento_formatada = date('Y-m-d', strtotime(str_replace('/', '-', $data_nascimento)));

    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Armazene a senha de forma segura
    $nivel_acesso = $_POST['nivel_acesso'];
    $area = isset($_POST['area']) ? $_POST['area'] : null;

    // Inserir os dados do usuário no banco de dados
    $sql = "INSERT INTO usuarios (nome_completo, cpf, email, telefone, data_nascimento, senha, nivel_acesso, area)
    VALUES (?, ?, ?, ?, STR_TO_DATE(?, '%Y-%m-%d'), ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    if (false === $stmt) {
        die('Erro de preparação: ' . htmlspecialchars($mysqli->error));
    }

    $bind = $stmt->bind_param("ssssssss", $nome_completo, $cpf, $email, $telefone, $data_nascimento_formatada, $senha, $nivel_acesso, $area);

    if (false === $bind) {
        die('Erro ao vincular parâmetros: ' . htmlspecialchars($stmt->error));
    }

    if ($stmt->execute()) {
        // Redirecionar para a página de login após o cadastro bem-sucedido
        header('Location: lista.php.php');
        exit();
    } else {
        // Exibir uma mensagem de erro detalhada em caso de falha no cadastro
        $erro = "Erro ao cadastrar usuário: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Adicionar após os links do Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:50px;">
    <h5>Cadastro de Usuário</h5>

    <form action="cadastro_usuario.php" method="POST">
        <div class="form-group mb-3">
            <label for="nome_completo">Nome Completo:</label>
            <input type="text" class="form-control" name="nome_completo" required>
        </div>

        <div class="form-group mb-3">
            <label for="cpf">CPF:</label>
            <input type="text" class="form-control" name="cpf" id="cpf" required>
        </div>

        <div class="form-group mb-3">
            <label for="email">Email:</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="form-group mb-3">
            <label for="telefone">Telefone:</label>
            <input type="text" class="form-control" name="telefone" id="telefone" required>
        </div>

        <div class="form-group mb-3">
            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="text" class="form-control date-input" name="data_nascimento" required>
        </div>

        <!-- Linha para os campos de Senha e Confirmação de Senha -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="senha">Senha:</label>
                    <input type="password" class="form-control" name="senha" id="senha" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="confirm_senha">Confirmar Senha:</label>
                    <input type="password" class="form-control" name="confirm_senha" id="confirm_senha" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="area">Área:</label>
                    <select class="form-control" name="area">
                        <option value="">Selecione</option>
                        <option value="GIM">GIM</option>
                        <option value="GLR">GLR</option>
                        <option value="AASVS">AASVS</option>
                        <option value="ACAS">ACAS</option>
                        <option value="AI">AI</option>
                        <option value="DVISA">DVISA</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="nivel_acesso">Nível de Acesso:</label>
                    <select class="form-control" name="nivel_acesso" required>
                        <option value="Administrador">Administrador</option>
                        <option value="Gestor">Gestor</option>
                        <option value="Técnico">Técnico</option>
                        <option value="Usuário">Usuário</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Cadastrar</button>
    </form>

    <?php
    if (isset($erro)) {
        echo '<div class="alert alert-danger mt-3">' . $erro . '</div>';
    }
    ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(document).ready(function(){
        // Função para validar as senhas
        function validarSenhas() {
            var senha = $('#senha').val();
            var confirmSenha = $('#confirm_senha').val();
            return senha === confirmSenha;
        }

        // Evento de submissão do formulário
        $('form').submit(function(event) {
            if (!validarSenhas()) {
                alert('As senhas não coincidem!');
                event.preventDefault(); // Impede o envio do formulário
            }
        });

        // Inicialize o Bootstrap Datepicker
        $('.date-input').datepicker({
            format: 'yyyy-mm-dd', // Formato da data
            autoclose: true,
            todayHighlight: true,
            clearBtn: true,
            endDate: '0d' // Desativa datas futuras
        });

        $('#cpf').mask('000.000.000-00');
        $('#telefone').mask('(00) 00000-0000');

        // Verificar se o campo de data de nascimento está vazio
        $('.date-input').each(function() {
            var $this = $(this);
            if (!$this.val()) {
                $this.datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeYear: true,
                    yearRange: "-100:+0",
                    changeMonth: true
                });
            }
        });
    });
</script>

</body>
</html>
