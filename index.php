<!--
Portal VISA ESTADUAL 2024
Desenvolvedor: Erick Vinicius
Versão: 1.0
Desenvolvido em PHP, portal completo para cadastros de alvará sanitário e relação de documentos.
-->

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Categorias de Documentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* CSS PARA CARDS POR ERICK VINCIIUS */
        .card-title i:hover{
            transform: scale(1.25) rotate(100deg);
            color:#18d4ca;

        }
        .card:hover{
            transform: scale(1.05);
            box-shadow: 10px 10px 15px rgba(0,0,0,0.3);
        }
        .card-text{
            height:80px;
        }

        .card::before, .card::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            transform: scale3d(0, 0, 1);
            transition: transform .3s ease-out 0s;
            background: rgba(255, 255, 255, 0.1);
            content: '';
            pointer-events: none;
        }
        .card::before {
            transform-origin: left top;
        }
        .card::after {
            transform-origin: right bottom;
        }
        .card:hover::before, .card:hover::after, .card:focus::before, .card:focus::after {
            transform: scale3d(1, 1, 1);
        }
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>


<div class="container mt-5" style="padding-top: 55px;">
    <!-- Lista de Documentos de Acesso Rápido -->
    <div class="documentos-rapidos">
        <h3>Documentos de Acesso Rápido</h3>
        <ol>
            <li>
                <a href="/sitevisa/documentos/uploads/Portaria-Prorrogacao-alvara-sanitario.pdf" target="_blank">PORTARIA PRORROGAÇÃO ALVARÁ SANITÁRIO</a>
            </li>
            <li>
                <a href="/sitevisa/documentos/uploads/659cbfdc96b47_relacao.pdf" target="_blank">RELACAO DE DOCUMENTOS PARA LICENCIAMENTO 2024</a>
            </li>
            <li>
                <a href="/sitevisa/documentos/uploads/INSTRUTIVO-INFOVISA-2021.pdf" target="_blank">INSTRUTIVO INFOVISA</a>
            </li>
        </ol>
    </div>

    <!-- Barra de Pesquisa -->
    <div class="search-container mb-3">
        <form action="" method="GET">
            <div class="input-group">
                <input type="text" class="form-control" id="pesquisa" placeholder="Pesquisar Categoria..." name="search">
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </div>
        </form>
    </div>

    <div class="row" id="categorias-container">
        <?php
        include('db_conection.php');

        // Consulta SQL inicial para carregar todas as categorias
        $sql = "SELECT id, nome_categoria, subtitulo FROM categorias_documentos";
        $result = $mysqli->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categoria_id = $row["id"];
                $categoria_nome = $row["nome_categoria"];
                $categoria_subtitulo = $row["subtitulo"];

                echo '<div class="col-md-3 inicio">';
                echo '<a href="lista-documentos.php?categoria_id=' . $categoria_id . '" style="text-decoration: none; color: inherit;">';
                echo '<div class="card mb-3">';
                echo '<div class="card-header">' . $categoria_nome . '</div>';
                echo '<div class="card-body">';
                if (!empty($categoria_subtitulo)) {
                    echo '<p class="categoria-subtitulo">' . $categoria_subtitulo . '</p>';
                }
                echo '<div class="text-center mt-auto">';
                echo '<button class="btn btn-primary">Ver Relação de Documentos</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo 'Nenhuma categoria de documento encontrada.';
        }

        $mysqli->close();
        ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<script>
    $(document).ready(function () {
        // Função para atualizar os resultados de pesquisa
        function atualizarResultadosPesquisa() {
            var termoPesquisa = $('#pesquisa').val().toLowerCase();

            $('#categorias-container .inicio').each(function () {
                var categoriaNome = $(this).find('.card-header').text().toLowerCase();
                var categoriaSubtitulo = $(this).find('.categoria-subtitulo').text().toLowerCase();
                var correspondencia = categoriaNome.includes(termoPesquisa) || categoriaSubtitulo.includes(termoPesquisa);

                $(this).css('display', correspondencia ? 'block' : 'none');
            });
        }

        // Adicionar um evento de input à barra de pesquisa
        $('#pesquisa').on('input', function () {
            atualizarResultadosPesquisa();
        });

        // Inicialmente, mostre todas as categorias
        atualizarResultadosPesquisa();
    });
</script>
