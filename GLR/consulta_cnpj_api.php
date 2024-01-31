<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de CNPJ</title>
</head>
<body>
<?php include('../includes/header.php'); ?>
<div class="container mt-5" style="padding-top:50px;">
    <h5>Consulta CNPJs de modo simples e rápido.</h5>
    <p class="mt-6 xl:max-w-lg xl:mx-auto">Aqui você pode consultar informações do Cadastro Nacional de Pessoas
        Jurídicas direto da base governamental.</p>
    <form method="post">
        <div class="form-group">
            <label for="cnpj">CNPJ:</label>
            <input type="text" class="form-control cnpj-mask" id="cnpj" name="cnpj" placeholder="99.999.999/9999-99" required>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom:10px; margin-top:5px;">Consultar</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cnpj = $_POST["cnpj"];
        $api_url = "https://minhareceita.org/$cnpj";
        $data = file_get_contents($api_url);
        $result = json_decode($data);

        if ($result) {
            // Exibir os dados em colunas lado a lado usando o Bootstrap
            echo "<h4>Dados do CNPJ: $cnpj</h4>";
            echo "<div class='row'>";

            echo "<div class='col-md-6'>";
            echo "<strong>Nome Fantasia:</strong> {$result->nome_fantasia}<br>";
            echo "<strong>Razão Social:</strong> {$result->razao_social}<br>";
            echo "<strong>CNPJ:</strong> {$result->cnpj}<br>";
            echo "<strong>Status:</strong> {$result->descricao_situacao_cadastral}<br>";
            echo "<strong>CEP:</strong> {$result->cep}<br>";
            echo "<strong>Data de Abertura:</strong> " . date('d/m/Y', strtotime($result->data_inicio_atividade)) . "<br>";
            echo "<strong>DDD:</strong> (" . substr($result->ddd_telefone_1, 0, 2) . ") " . substr($result->ddd_telefone_1, 2, 4) . "-" . substr($result->ddd_telefone_1, 6, 4) . "<br>";
            echo "<strong>Email:</strong> {$result->email}<br>";
            echo "</div>";

            echo "<div class='col-md-6'>";
            echo "<strong>Tipo de Logradouro:</strong> {$result->descricao_tipo_de_logradouro}<br>";
            echo "<strong>Logradouro:</strong> {$result->logradouro}<br>";
            echo "<strong>Número:</strong> {$result->numero}<br>";
            echo "<strong>Complemento:</strong> {$result->complemento}<br>";
            echo "<strong>Bairro:</strong> {$result->bairro}<br>";
            echo "<strong>Município:</strong> {$result->municipio}<br>";
            echo "<strong>UF:</strong> {$result->uf}<br>";
            $cnaePrincipal = substr($result->cnae_fiscal, 0, 4) . "-" . substr($result->cnae_fiscal, 4, 1) . "/" . substr($result->cnae_fiscal, 5) . " - {$result->cnae_fiscal_descricao}";
            echo "<strong>CNAE Principal:</strong> <span id='cnaePrincipal'>$cnaePrincipal</span> <button class='btn btn-sm btn-primary' onclick='copiarCNAEPrincipal()'>Copiar</button><br>";
            echo "</div>";

            echo "</div>";

            if (!empty($result->cnaes_secundarios)) {
                echo "<h2>CNAEs Secundários:</h2>";
                foreach ($result->cnaes_secundarios as $cnae_secundario) {
                    echo "{$cnae_secundario->codigo} - {$cnae_secundario->descricao}<br>";
                }
            }

            if (!empty($result->qsa)) {
                echo "<h2>Sócios:</h2>";
                foreach ($result->qsa as $socio) {
                    echo "<strong>Nome do Sócio:</strong> {$socio->nome_socio}<br>";
                    echo "<strong>CNPJ/CPF do Sócio:</strong> {$socio->cnpj_cpf_do_socio}<br>";
                    echo "<strong>Qualificação do Sócio:</strong> {$socio->qualificacao_socio}<br>";
                    echo "<strong>Data de Entrada na Sociedade:</strong> {$socio->data_entrada_sociedade}<br>";
                    echo "<strong>Idade do Sócio:</strong> {$socio->faixa_etaria}<br>";
                }
            }
        } else {
            echo "<div class='alert alert-danger mt-3'>CNPJ não encontrado.</div>";
        }
    }
    ?>

    <!-- Inclua os arquivos JavaScript e CSS do Inputmask -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>
    <script>
        function copiarCNAEPrincipal() {
            var cnaePrincipal = document.getElementById("cnaePrincipal");
            var textArea = document.createElement("textarea");
            textArea.value = cnaePrincipal.innerText;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("copy");
            document.body.removeChild(textArea);
            alert("CNAE Principal copiado para a área de transferência!");
        }
        $(document).ready(function () {
            $('.cnpj-mask').inputmask('99.999.999/9999-99');
        });
    </script>
</div>
</body>
</html>
