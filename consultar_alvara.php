<?php
include('db_conection.php'); // Inclua sua conexão com o banco de dados aqui
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Alvará</title>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="container mt-5" style="padding-top: 55px;">
    <h5>Consultar Alvará</h5>
    <p>Para consultar um Alvará Sanitário emitido em 2024, escolha o tipo de pesquisa:</p>
    <p class="alert alert-info">A busca é somente para os Alvarás Sanitários emitidos em 2024.</p>

    <form action="" method="GET">
        <div class="form-group">
            <input type="radio" name="tipo_pesquisa" id="pesquisa_numero" value="numero" checked>
            <label for="pesquisa_numero">Número do Alvará Sanitário</label>

            <input type="radio" name="tipo_pesquisa" id="pesquisa_nome" value="nome">
            <label for="pesquisa_nome">Nome Fantasia</label>

            <input type="radio" name="tipo_pesquisa" id="pesquisa_cnpj" value="cnpj">
            <label for="pesquisa_cnpj">CNPJ</label>
        </div>


        <div class="form-group" id="campo_numero_alvara">
            <label for="numero_alvara" style="padding-top:10px;">Digite o Número do Alvará Sanitário:</label>
            <input type="text" class="form-control" name="numero_alvara" id="numero_alvara" placeholder="Digite o número do alvará">
        </div>

        <div class="form-group" id="campo_nome_fantasia" style="display: none;">
            <label for="nome_fantasia" style="padding-top:10px;"> Digite o Nome Fantasia:</label>
            <input type="text" class="form-control" name="nome_fantasia" id="nome_fantasia" placeholder="Digite o nome fantasia">
            <div id="sugestoes_nome_fantasia" class="sugestoes"></div>
        </div>

        <div class="form-group" id="campo_cnpj" style="display: none;">
            <label for="cnpj" style="padding-top:10px;">Digite o CNPJ:</label>
            <input type="text" class="form-control" name="cnpj" id="cnpj" placeholder="99.999.999/9999-99">
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top:15px; margin-bottom: 15px;">Consultar</button>
    </form>

    <?php
    date_default_timezone_set('America/Sao_Paulo');
    $dataConsulta = date('d/m/Y H:i:s');
    if (isset($_GET['numero_alvara']) || isset($_GET['nome_fantasia']) || isset($_GET['razao_social']) || isset($_GET['cnpj'])) {
        if (!empty($_GET['numero_alvara'])) {
            $numero_alvara = $_GET['numero_alvara'];
            $query = "SELECT * FROM alvaras_sanitarias WHERE numero_alvara = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $numero_alvara);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($alvara = $result->fetch_assoc()) {
                    // Define a cor com base no status do alvará
                    switch ($alvara['status_alvara']) {
                        case 'ATIVO':
                            $status_color = 'green'; // Verde para "ATIVO"
                            break;
                        case 'VENCIDO':
                        case 'CANCELADO':
                            $status_color = 'red'; // Vermelho para "VENCIDO" e "CANCELADO"
                            break;
                        default:
                            $status_color = 'black'; // Preto ou outra cor para outros estados
                            break;
                    }
                    echo '<div class="container mt-4">';
                    echo '<div class="card">';
                    echo '<div class="card-header bg-primary text-white">';
                    echo '<h5 class="card-title">Informações do Alvará Sanitário</h5>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h6 class="card-subtitle mb-2">Número do Alvará: ' . $alvara['numero_alvara'] . '.' . date('Y', strtotime($alvara['data_vencimento'])) . '</h6>';
                    echo '<p class="card-text">CNPJ: ' . $alvara['cnpj'] . '</p>';
                    echo '<p class="card-text">Nome Fantasia: ' . $alvara['nome_fantasia'] . '</p>';
                    echo '<p class="card-text">Razão Social: ' . $alvara['razao_social'] . '</p>';
                    echo '<p class="card-text">Status: <b><span style="color: ' . $status_color . ';">' . $alvara['status_alvara'] . '</span></b></p>';
                    echo '<p class="card-text">Data de Vencimento: ' . date('d/m/Y', strtotime($alvara['data_vencimento'])) . '</p>';
                    echo '</div>';
                    echo '<div class="card-footer">';
                    $link_alvara = 'https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=' . $alvara['numero_alvara'];
                    echo '<a href="' . $link_alvara . '" class="card-link" target="_blank">Visualizar Alvará Sanitário</a>';
                    echo '<a href="#" class="card-link imprimir-consulta" data-alvara-id="' . $alvara['id'] . '">Imprimir Consulta</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '<p class="card-text" style="font-size:12px; padding-top:8px;">Consultado em: ' . $dataConsulta . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<h5>Consultar Alvará</h5>';
                echo '<p>O número do Alvará Sanitário ' . $numero_alvara . ' não está cadastrado no sistema.</p>';
            }
            $stmt->close();
        } elseif (!empty($_GET['nome_fantasia'])) {
            // Busca por Nome Fantasia
            $nome_fantasia = $_GET['nome_fantasia'];
            $query = "SELECT * FROM alvaras_sanitarias WHERE nome_fantasia LIKE ?";
            $stmt = $mysqli->prepare($query);
            $likeNomeFantasia = "%" . $nome_fantasia . "%";
            $stmt->bind_param("s", $likeNomeFantasia);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($alvara = $result->fetch_assoc()) {
                    // Define a cor com base no status do alvará
                    switch ($alvara['status_alvara']) {
                        case 'ATIVO':
                            $status_color = 'green'; // Verde para "ATIVO"
                            break;
                        case 'VENCIDO':
                        case 'CANCELADO':
                            $status_color = 'red'; // Vermelho para "VENCIDO" e "CANCELADO"
                            break;
                        default:
                            $status_color = 'black'; // Preto ou outra cor para outros estados
                            break;
                    }
                    echo '<div class="container mt-4">';
                    echo '<div class="card">';
                    echo '<div class="card-header bg-primary text-white">';
                    echo '<h5 class="card-title">Informações do Alvará Sanitário</h5>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h6 class="card-subtitle mb-2">Número do Alvará: ' . $alvara['numero_alvara'] . '.' . date('Y', strtotime($alvara['data_vencimento'])) . '</h6>';
                    echo '<p class="card-text">CNPJ: ' . $alvara['cnpj'] . '</p>';
                    echo '<p class="card-text">Nome Fantasia: ' . $alvara['nome_fantasia'] . '</p>';
                    echo '<p class="card-text">Razão Social: ' . $alvara['razao_social'] . '</p>';
                    echo '<p class="card-text">Status: <b><span style="color: ' . $status_color . ';">' . $alvara['status_alvara'] . '</span></b></p>';
                    echo '<p class="card-text">Data de Vencimento: ' . date('d/m/Y', strtotime($alvara['data_vencimento'])) . '</p>';
                    echo '</div>';
                    echo '<div class="card-footer">';
                    $link_alvara = 'https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=' . $alvara['numero_alvara'];
                    echo '<a href="' . $link_alvara . '" class="card-link" target="_blank">Visualizar Alvará Sanitário</a>';
                    echo '<a href="#" class="card-link imprimir-consulta" data-alvara-id="' . $alvara['id'] . '">Imprimir Consulta</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '<p class="card-text" style="font-size:12px; padding-top:8px;">Consultado em: ' . $dataConsulta . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<h5>Consultar Alvará</h5>';
                echo '<p>Nenhum alvará encontrado para o nome fantasia informado: ' . $nome_fantasia . '.</p>';
            }

            $stmt->close();
        } elseif (!empty($_GET['cnpj'])) {
            // Busca por CNPJ
            $cnpj = $_GET['cnpj'];
            $query = "SELECT * FROM alvaras_sanitarias WHERE cnpj = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $cnpj);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($alvara = $result->fetch_assoc()) {
                    // Define a cor com base no status do alvará
                    switch ($alvara['status_alvara']) {
                        case 'ATIVO':
                            $status_color = 'green'; // Verde para "ATIVO"
                            break;
                        case 'VENCIDO':
                        case 'CANCELADO':
                            $status_color = 'red'; // Vermelho para "VENCIDO" e "CANCELADO"
                            break;
                        default:
                            $status_color = 'black'; // Preto ou outra cor para outros estados
                            break;
                    }
                    echo '<div class="container mt-4">';
                    echo '<div class="card">';
                    echo '<div class="card-header bg-primary text-white">';
                    echo '<h5 class="card-title">Informações do Alvará Sanitário</h5>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h6 class="card-subtitle mb-2">Número do Alvará: ' . $alvara['numero_alvara'] . '.' . date('Y', strtotime($alvara['data_vencimento'])) . '</h6>';
                    echo '<p class="card-text">CNPJ: ' . $alvara['cnpj'] . '</p>';
                    echo '<p class="card-text">Nome Fantasia: ' . $alvara['nome_fantasia'] . '</p>';
                    echo '<p class="card-text">Razão Social: ' . $alvara['razao_social'] . '</p>';
                    echo '<p class="card-text">Status: <b><span style="color: ' . $status_color . ';">' . $alvara['status_alvara'] . '</span></b></p>';
                    echo '<p class="card-text">Data de Vencimento: ' . date('d/m/Y', strtotime($alvara['data_vencimento'])) . '</p>';
                    echo '</div>';
                    echo '<div class="card-footer">';
                    $link_alvara = 'https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=' . $alvara['numero_alvara'];
                    echo '<a href="' . $link_alvara . '" class="card-link" target="_blank">Visualizar Alvará Sanitário</a>';
                    echo '<a href="#" class="card-link imprimir-consulta" data-alvara-id="' . $alvara['id'] . '">Imprimir Consulta</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '<p class="card-text" style="font-size:12px; padding-top:8px;">Consultado em: ' . $dataConsulta . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<h5>Consultar Alvará</h5>';
                echo '<p>Nenhum alvará encontrado para o CNPJ informado: ' . $cnpj . '.</p>';
            }

            $stmt->close();
        }
        $mysqli->close();
    }
    ?>

</div>
<div style="padding-top:50px;"></div>
<?php include('includes/footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    $(document).ready(function() {
        $('#cnpj').mask('00.000.000/0000-00');
    });
    $(document).on('click', '.imprimir-consulta', function(e) {
        e.preventDefault();

        // Obtenha o ID do Alvará a partir do atributo data-alvara-id
        var alvaraId = $(this).data('alvara-id');

        // Obtenha a data da consulta do PHP e formate-a para ser incluída no link de impressão
        var dataConsulta = '<?php echo $dataConsulta; ?>';
        dataConsulta = dataConsulta.replace(/ /g, '%20'); // Substitua espaços por %20 na data

        // Crie um link de impressão personalizado com os dados do Alvará e a data da consulta
        var linkImpressao = 'imprimir_consulta.php?alvara_id=' + alvaraId + '&data_consulta=' + dataConsulta;

        // Abra uma nova janela ou guia para a página de impressão
        window.open(linkImpressao, '_blank');
    });

</script>
<script>
    $(document).ready(function(){
        $("#nome_fantasia").keyup(function(){
            var nome_fantasia = $(this).val();
            if(nome_fantasia != ''){
                $.ajax({
                    url: 'buscar_nome_fantasia.php',
                    method: 'POST',
                    data: {nome_fantasia: nome_fantasia},
                    success: function(data){
                        $('#sugestoes_nome_fantasia').fadeIn();
                        $('#sugestoes_nome_fantasia').html(data);
                    }
                });
            } else {
                $('#sugestoes_nome_fantasia').fadeOut();
                $('#sugestoes_nome_fantasia').html("");
            }
        });

        $('input[type=radio][name=tipo_pesquisa]').change(function() {
            if (this.value == 'numero') {
                $('#campo_nome_fantasia').hide();
                $('#campo_numero_alvara').show();
                $('#campo_cnpj').hide();
            } else if (this.value == 'nome') {
                $('#campo_numero_alvara').hide();
                $('#campo_nome_fantasia').show();
                $('#campo_cnpj').hide();
            } else if (this.value == 'cnpj') {
                $('#campo_numero_alvara').hide();
                $('#campo_nome_fantasia').hide();
                $('#campo_cnpj').show();
            }
        });

        $(document).on('click', '.sugestao', function(){
            $('#nome_fantasia').val($(this).text());
            $('#sugestoes_nome_fantasia').fadeOut();
        });
    });
</script>
</body>
</html>
