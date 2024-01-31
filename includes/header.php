<?php
// Inicia a sessão se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título da Página</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/sitevisa/assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<header>
    <!-- Barra de navegação Bootstrap -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="font-size: 15px;">
        <div class="container">
            <!-- Logotipo -->
            <a class="navbar-brand" href="/sitevisa/index.php">
                <img src="/sitevisa/assets/img/logo.png" alt="Logotipo" width="70">
            </a>

            <!-- Botão de alternância para dispositivos móveis -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Itens de menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/sitevisa/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sitevisa/consultar_alvara.php">Consultar Alvará</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sitevisa/contato.php">Contato</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Infovisa
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="https://sistemas.saude.to.gov.br/infovisacore" target=_blank>Acessar Sistema</a></li>
                            <li><a class="dropdown-item" href="https://vigilancia-to.com.br/sitevisa/documentos/uploads/INSTRUTIVO-INFOVISA-2021.pdf" target=_blank>Instrutivo</a></li>
                            <!-- Adicione mais itens de menu para outras categorias aqui -->
                        </ul>
                    </li>

                    <!-- Dropdown "Documentos", visível apenas para Administradores -->
                    <?php if (isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] === 'Administrador'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Documentos
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/sitevisa/documentos/cadastrar_documento.php">Cadastrar Documentos</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/documentos/lista_documentos.php">Listar Documentos</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/documentos/uploads.php">Diretório Documentos</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categoria
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/sitevisa/categoria/cadastrar.php">Cadastrar Categoria</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/categoria/listar_categorias.php">Listar Categoria</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/subcategoria/cadastrar_subcategoria.php">Cadastrar Subcategoria</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/subcategoria/lista-de-subcategoria.php">Listar Subcategoria</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['nivel_acesso']) && ($_SESSION['nivel_acesso'] === 'Administrador' || $_SESSION['nivel_acesso'] === 'Gestor')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Alvará Sanitário
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/sitevisa/alvarasanitario/cadastrar_alvara.php">Cadastrar Alvará</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/alvarasanitario/lista.php">Lista Alvará</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/alvarasanitario/lista_filtrada.php">Buscar Alvará</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/alvarasanitario/lista_atualizar_status_alvaras.php">Atualizar Status Alvará</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- PARA ACESSO NIVEL GLR -->
                    <?php if ( isset($_SESSION['nivel_acesso']) && ($_SESSION['nivel_acesso'] === 'Administrador' || $_SESSION['nivel_acesso'] === 'Gestor') && isset($_SESSION['area']) && $_SESSION['area'] === 'GLR' ): ?>                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            GLR
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/sitevisa/GLR/documentos.php">Parecer Documentos</a></li>
                            <li><a class="dropdown-item" href="/sitevisa/GLR/consulta_cnpj_api.php">Consultar CNPJ</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- PARA ACESSO NIVEL AI E GLR -->
                    <?php if (isset($_SESSION['nivel_acesso']) && ($_SESSION['nivel_acesso'] === 'Administrador' || $_SESSION['nivel_acesso'] === 'Gestor') && isset($_SESSION['area']) && ($_SESSION['area'] === 'AI' || $_SESSION['area'] === 'GLR')): ?>
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            AI
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/sitevisa/projetoarquitetonico/cadastrar_projeto.php">Cadastrar Projeto</a></li>
                            <li><a class="dropdown-item" href="/sitevisa/projetoarquitetonico/listar_projetos.php">Listar Projetos</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- PARA ACESSO NIVEL GLR -->
                    <?php if ( isset($_SESSION['nivel_acesso']) && ($_SESSION['nivel_acesso'] === 'Administrador' || $_SESSION['nivel_acesso'] === 'Gestor') && isset($_SESSION['area']) && $_SESSION['area'] === 'GLR' ): ?>                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Alertas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/sitevisa/alertas/cadastro_documentos_alertas.php">Criar Alerta Documentos</a></li>
                            <li><a class="dropdown-item" href="/sitevisa/alertas/lista_documentos_alertas.php">Listar Alertas Documentos</a></li>
                            <li><a class="dropdown-item" href="/sitevisa/alertas/alertas_documentos.php">Ver Todos Alertas</a></li>
                            <li><a class="dropdown-item" href="/sitevisa/alertas/alerta_documentos_alvaras.php">Notificação Alerta E-mail</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>


                    <?php if (isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] === 'Administrador'): ?>

                    <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Usuários
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/sitevisa/admin/cadastro_usuario.php">Cadastrar</a></li>
                                <li><a class="dropdown-item" href="/sitevisa/admin/lista.php">Listar</a></li>
                            </ul>
                        </li>
                </ul>
                <?php endif; ?>
                <!-- Ícone "Login" para dispositivos móveis -->
                <ul class="navbar-nav ml-auto d-lg-none">
                    <?php if (isset($_SESSION['usuario_nome'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="/sitevisa/logout.php">Sair</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/sitevisa/login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>


            <!-- Ícone "Login" para desktop -->
            <ul class="navbar-nav ml-auto d-none d-lg-flex">
                <!-- Dropdown "ALERTAS", visível apenas para Administradores e Gestores -->
                <?php if (isset($_SESSION['nivel_acesso']) && ($_SESSION['nivel_acesso'] === 'Administrador' || $_SESSION['nivel_acesso'] === 'Gestor')): ?>
                    <li class="nav-item dropdown">
                        <a class="alertas nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell fa-fw"></i>
                            <span class="badge badge-danger badge-counter" id="notificationCounter">0</span>
                        </a>
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                            <h6 class="dropdown-header">Centro de Alertas</h6>
                            <div id="alertsContent">
                            </div>
                            <a class="dropdown-item text-center small text-gray-500" href="/sitevisa/alertas/alertas_documentos.php">Ver todos os alertas</a>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- PARA LOGIN DE USUARIO -->
                <?php if (isset($_SESSION['usuario_nome'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/sitevisa/logout.php">Sair</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/sitevisa/login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
<!-- Scripts JavaScript -->
<!-- Bootstrap Bundle JS que inclui Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Versão completa do jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tinymce@5.9.1/tinymce.min.js"></script>
<script>
    function updateAlerts() {
        $.get('/sitevisa/alertas/atualizar_alertas.php', function(data) {
            var notificationCounter = $('#notificationCounter');
            var alertsContent = $('#alertsContent');
            notificationCounter.text(data.length);
            alertsContent.empty();

            if (data.length > 0) {
                $.each(data, function(index, alert) {
                    if (alert.numero_processo) { // Se for um alvará
                        // Aplicar a máscara ao número do processo
                        var numeroProcesso = alert.numero_processo;
                        numeroProcesso = numeroProcesso.replace(/(\d{4})(\d{2})(\d{10})/, '$1.$2.$3');

                        // Adicionar alvará ao conteúdo do alerta
                        alertsContent.append('<a class="dropdown-item d-flex align-items-center" href="' +
                            'https://sistemas.saude.to.gov.br/infovisacore/Manager/Procedure?identifier=' + numeroProcesso +
                            '&infoCompany=&procedureStatus=&procedureType=&procedureArea=" target="_blank">' +
                            '<div class="mr-3">' +
                            '<div class="icon-circle bg-danger">' +
                            '</div>' +
                            '</div>' +
                            '<div>' +
                            '<div class="small text-gray-500" style="font-size: 11px;">Alvará vencido em: ' + alert.data_vencimento + '</div>' +
                            '<span class="font-weight-bold" style="font-size: 11px;">' + alert.nome_fantasia + '</span>' +
                            '</div>' +
                            '</a>');
                    } else if (alert.numero_documento) { // Se for um documento
                        // Formatar o link com o número do documento
                        var linkDocumento = 'https://sistemas.saude.to.gov.br/Infovisacore/Manager/DocumentAnswer/GetDocument?documentId=' + alert.numero_documento;

                        // Adicionar documento ao conteúdo do alerta
                        alertsContent.append('<a class="dropdown-item d-flex align-items-center" href="' +
                            linkDocumento + '" target="_blank">' +
                            '<div class="mr-3">' +
                            '<div class="icon-circle bg-warning">' +
                            '</div>' +
                            '</div>' +
                            '<div>' +
                            '<div>' +
                            '<div class="small text-gray-500" style="font-size: 11px;">Documento vencido em: ' + alert.data_alerta + '</div>' +
                            '<span class="font-weight-bold" style="font-size: 11px;">' + alert.tipo_documento + ' - ' + alert.numero_documento + '</span>' +
                            '</div>' +
                            '</a>');
                    }
                });
            } else {
                alertsContent.append('<a class="dropdown-item text-center small text-gray-500" href="#">Nenhum Documento vencido</a>');
            }
        });
    }
    updateAlerts();
</script>
</body>
</html>
