<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Atividades e Documentos</title>
    <style>
        .checkbox-with-margin {
            margin-right: 5px; /* Ajuste o valor da margem conforme necessário */
        }
    </style>

</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5" style="padding-top:45px;">
    <div class="alert alert-primary" role="alert">
        Marque os documentos que a empresa protocolou, selecione a atividade do estabelecimento e clique no botão "Verificar Documentos".
    </div>

    <div class="container mt-5" style="margin-top: 0rem!important;">
        <div class="form-group mt-4">
            <label for="nomeSelect">Selecione o Nome:</label>
            <select class="form-control" id="nomeSelect" style="margin-bottom: 10px;">
                <option value="Erick Vinicius Rodrigues">Erick Vinicius Rodrigues</option>
                <option value="Isabelly Aparecida Ribeiro de Sousa">Isabelly Aparecida Ribeiro de Sousa</option>
                <option value="Alexandre Mattiello">Alexandre Mattiello</option>
            </select>
        </div>

        <div id="tipoEmpresa">
            <label for="tipoEmpresaSelect">Tipo de Empresa:</label>
            <select class="form-control" id="tipoEmpresaSelect" style="margin-bottom: 10px;">
                <option value="">Selecione</option>
                <option value="privada">Empresa Privada</option>
                <option value="publica">Empresa Pública</option>
            </select>
        </div>


    <div id="documentosContainerTodas">
        <h5>Todos os Documentos</h5>
        <ul id="todosDocumentos">
            <li data-tipo="ambos"><input type="checkbox" id="cnpj"> CNPJ - com data de emissão de até 30 dias;</li>
            <li data-tipo="privada"><input type="checkbox" id="contratoSocial"> CONTRATO SOCIAL – versão que identifica o responsável legal pela empresa. APENAS PARA EMPRESAS PRIVADAS;</li>
            <li data-tipo="privada"><input type="checkbox" id="dare"> DARE - Documento de Arrecadação Estadual - Isento para estabelecimentos públicos;</li>
            <li data-tipo="privada"><input type="checkbox" id="compPagamento"> COMPROVANTE DE PAGAMENTO DO DARE - Isento para estabelecimentos públicos;</li>
            <li data-tipo="ambos"><input type="checkbox" id="parecerProjeto"> PARECER PROJETO - Parecer técnico de análise do projeto arquitetônico do estabelecimento ou declaração que o projeto está em análise.</li>
        </ul>
    </div>


    <div class="form-group mt-4">
        <label for="atividadeSelect">Selecione a Atividade:</label>
        <select class="form-control" id="atividadeSelect" style="margin-bottom:10px;">
            <option value="">Selecione</option>
            <option value="HOSPITAL">HOSPITAL, AMBULATÓRIO DE ESPECIALIDADES, CLÍNICA E POLICLÍNICA COM INTERNAÇÃO, PRONTO-ATENDIMENTO, DIAGNÓSTICO POR IMAGEM (EXCETO MEDICINA NUCLEAR)</option>
            <option value="DISTRIBUIDORA">DISTRIBUIDORA DE PRODUTOS</option>
            <option value="DIALISE">DIALISE</option>
            <option value="FARMACIA">FARMACIA DE MANIPULACAO</option>
            <option value="GASES">GASES MEDICINAIS</option>
            <option value="HEMOTERAPIA">HEMOTERAPIA</option>
            <option value="INDUSTRIADEALIMENTOS">INDÚSTRIA DE ALIMENTOS DISPENSADOS DE REGISTRO</option>
            <option value="INDUSTRIAMEDICAMENTOS">INDÚSTRIA DE MEDICAMENTOS</option>
            <option value="INDUSTRIASANEANTES">INDÚSTRIA DE SANEANTES E COSMÉTICOS</option>
            <option value="LAVANDERIA">LAVANDERIA HOSPITALAR</option>
            <option value="MEDICINA">MEDICINA NUCLEAR</option>
            <option value="QUIMIOTERAPIA">QUIMIOTERAPIA – ONCOLOGIA CLÍNICA</option>
            <option value="RADIO">RADIOTERAPIA E BRAQUITERAPIA </option>
            <option value="UTI">UNIDADE DE TERAPIA INTENSIVA (UTI) </option>
            <option value="LABCLINICO">LABORATÓRIO CLÍNICO E ANALÍTICO </option>
            <option value="ESV">ESTABELECIMENTO SUJEITO À VISA E NÃO CONTEMPLADO NAS RELAÇÕES ANTERIORES </option>
        </select>
    </div>

    <div id="documentosContainer">
    </div>

    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="marcarTodosCheckbox">
        <label class="form-check-label" for="marcarTodosCheckbox">Marcar todos os documentos</label>
    </div>

    <!-- Botão de verificação -->
    <button id="verificarDocumentos" class="btn btn-primary btn-sm mt-3" style="margin-bottom:10px;">Verificar Documentos</button>
    <button id="limparCheckboxes" class="btn btn-danger btn-sm mt-3" style="margin-bottom:10px;">Limpar</button>
    <div id="documentosNaoMarcados">
        <h6 style="padding-top:15px;">O estabelecimento não protocolou os seguintes documentos:</h6>
        <ul id="listaDocumentosNaoMarcados">
        </ul>
    </div>
    <center><button id="copiarDocumentosFaltando" class="btn btn-primary btn-sm mt-3" style="margin-bottom:25px;">Gerar Parecer de Documentos</button></center>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const limparCheckboxesButton = document.getElementById("limparCheckboxes");
        limparCheckboxesButton.addEventListener("click", function () {
            const checkboxes = document.querySelectorAll("input[type='checkbox']");
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            location.reload();
        });

        const tipoEmpresaSelect = document.getElementById("tipoEmpresaSelect");
        const todosDocumentos = document.getElementById("todosDocumentos");
        const documentosContainerTodas = document.getElementById("documentosContainerTodas");

        // Função para atualizar a lista de documentos com base no tipo de empresa
        function atualizarListaDocumentos() {
            const tipoEmpresa = tipoEmpresaSelect.value;
            todosDocumentos.innerHTML = ""; // Limpa a lista de documentos

            if (tipoEmpresa === "publica") {
                // Adicione os documentos relevantes para empresas públicas
                const documentosPublicos = [
                    "CNPJ - com data de emissão de até 30 dias;",
                    "PARECER PROJETO - Parecer técnico de análise do projeto arquitetônico do estabelecimento ou declaração que o projeto está em análise."
                ];

                documentosPublicos.forEach(function (documento) {
                    const li = document.createElement("li");
                    li.innerHTML = `<input type="checkbox"> ${documento}`;
                    todosDocumentos.appendChild(li);
                });
            } else if (tipoEmpresa === "privada") {
                // Adicione os documentos relevantes para empresas privadas
                const documentosPrivados = [
                    "CNPJ - com data de emissão de até 30 dias;",
                    "CONTRATO SOCIAL – versão que identifica o responsável legal pela empresa.;",
                    "DARE - Documento de Arrecadação Estadual;",
                    "COMPROVANTE DE PAGAMENTO DO DARE;",
                    "PARECER PROJETO - Parecer técnico de análise do projeto arquitetônico do estabelecimento ou declaração que o projeto está em análise."
                ];

                documentosPrivados.forEach(function (documento) {
                    const li = document.createElement("li");
                    li.innerHTML = `<input type="checkbox"> ${documento}`;
                    todosDocumentos.appendChild(li);
                });
            }

            // Exiba ou oculte a lista de documentos com base na seleção do tipo de empresa
            if (tipoEmpresa === "") {
                documentosContainerTodas.style.display = "none";
            } else {
                documentosContainerTodas.style.display = "block";
            }
        }

        // Adicione um ouvinte de evento para atualizar a lista quando o tipo de empresa é alterado
        tipoEmpresaSelect.addEventListener("change", atualizarListaDocumentos);

        // Chame a função inicialmente para exibir a lista correta com base na seleção padrão
        atualizarListaDocumentos();
    });


    // Função para verificar se todos os checkboxes estão marcados
    function verificarTodosMarcados() {
        var todosDocumentosCheckboxes = document.querySelectorAll('#todosDocumentos input[type="checkbox"]');
        var atividadeDocumentosCheckboxes = document.querySelectorAll('#documentosContainer ul input[type="checkbox"]');
        var marcarTodosCheckbox = document.getElementById('marcarTodosCheckbox');

        var todosMarcados = true;

        todosDocumentosCheckboxes.forEach(function (checkbox) {
            if (!checkbox.checked) {
                todosMarcados = false;
            }
        });

        atividadeDocumentosCheckboxes.forEach(function (checkbox) {
            if (!checkbox.checked) {
                todosMarcados = false;
            }
        });

        // Defina o estado do checkbox "Marcar todos os documentos" com base na verificação
        marcarTodosCheckbox.checked = todosMarcados;
    }

    // Adicione um evento de clique ao botão "Verificar Documentos" para verificar documentos não marcados
    document.getElementById('verificarDocumentos').addEventListener('click', function () {
        var documentosNaoMarcadosContainer = document.getElementById('documentosNaoMarcados');
        documentosNaoMarcadosContainer.style.display = 'block'; // Mostrar a área de documentos não marcados
        verificarDocumentosNaoMarcados();
        verificarTodosMarcados(); // Verificar se todos os documentos estão marcados após a verificação
    });

    // Adicione um evento de clique ao checkbox "Marcar todos os documentos"
    document.getElementById('marcarTodosCheckbox').addEventListener('change', function () {
        var todosDocumentosCheckboxes = document.querySelectorAll('#todosDocumentos input[type="checkbox"]');
        var atividadeDocumentosCheckboxes = document.querySelectorAll('#documentosContainer ul input[type="checkbox"]');
        var marcarTodosChecked = this.checked;

        todosDocumentosCheckboxes.forEach(function (checkbox) {
            checkbox.checked = marcarTodosChecked;
        });

        atividadeDocumentosCheckboxes.forEach(function (checkbox) {
            checkbox.checked = marcarTodosChecked;
        });

        // Verificar novamente se todos os documentos estão marcados após a mudança
        verificarTodosMarcados();
    });


    document.getElementById('copiarDocumentosFaltando').addEventListener('click', function () {
        var listaDocumentosNaoMarcados = document.getElementById('listaDocumentosNaoMarcados');
        var documentosFaltandoTexto = '';
        var isCadastroCompleto = true; // Inicialmente, assumimos que o cadastro está completo

        // Iterar sobre os itens da lista de documentos não marcados e construir o texto
        for (var i = 0; i < listaDocumentosNaoMarcados.children.length; i++) {
            var item = listaDocumentosNaoMarcados.children[i];
            documentosFaltandoTexto += item.textContent + '\n';

            // Verificar se o documento é "Responsável Legal pelo estabelecimento" ou "Responsável Técnico"
            if (item.textContent.includes("Responsável Legal pelo estabelecimento") || item.textContent.includes("Responsável Técnico")) {
                isCadastroCompleto = false; // Se algum dos dois documentos estiver faltando, o cadastro está incompleto
            }
        }

        // Verifique se o checkbox "Marcar todos os documentos" está marcado
        var marcarTodosCheckbox = document.getElementById('marcarTodosCheckbox');
        var marcarTodosChecked = marcarTodosCheckbox.checked;

        // Verifique se existem documentos faltando ou se o checkbox "Marcar todos os documentos" está marcado
        if (documentosFaltandoTexto.trim() !== '' || marcarTodosChecked) {
            // Obtenha a data atual
            var dataAtual = new Date();
            var mes = dataAtual.toLocaleString('pt-BR', { month: 'long' });
            var ano = dataAtual.getFullYear();
            var dataFormatada = `Palmas ${dataAtual.getDate()} de ${mes} de ${ano}`;

            var nomeSelecionado = document.getElementById('nomeSelect').value;

            // Determine o texto apropriado para o status do cadastro
            var cadastroStatus = isCadastroCompleto ? 'COMPLETO' : 'INCOMPLETO';

            // Se todos os documentos estiverem marcados ou o checkbox "Marcar todos os documentos" estiver marcado
            if (documentosFaltandoTexto.trim() === '' || marcarTodosChecked) {
                var textoCompleto = `
Após avaliação do cadastro do estabelecimento e documentos protocolados temos o seguinte:

O cadastro do estabelecimento está ${cadastroStatus}.

A documentação do estabelecimento está COMPLETA.

É o parecer, SMJ.
${dataFormatada}
${nomeSelecionado}
Gerência de Licenciamento e Regulação Sanitária
`;
            } else {
                // Se houver documentos faltando, crie o texto completo com base nos documentos faltantes
                textoCompleto = `
Após avaliação do cadastro do estabelecimento e documentos protocolados temos o seguinte:

O cadastro do estabelecimento está ${cadastroStatus}.

A documentação do estabelecimento está INCOMPLETA e devem ser protocolados os seguintes documentos:

${documentosFaltandoTexto}
PROTOCOLE APENAS OS DOCUMENTOS SOLICITADOS.

É o parecer, SMJ.
${dataFormatada}
${nomeSelecionado}
Gerência de Licenciamento e Regulação Sanitária
`;
            }

            // Copiar o texto para a área de transferência
            var textArea = document.createElement('textarea');
            textArea.value = textoCompleto;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            // Exibir uma mensagem de sucesso (opcional)
            alert('Documentos Faltando copiados para a área de transferência.');
        } else {
            // Se não houver documentos faltando e o checkbox "Marcar todos os documentos" não estiver marcado, exiba uma mensagem
            alert('Todos os documentos estão marcados.');
        }
    });



    // Função para verificar documentos não marcados
    function verificarDocumentosNaoMarcados() {
        var todosDocumentosCheckboxes = document.querySelectorAll('#todosDocumentos input[type="checkbox"]');
        var atividadeSelect = document.getElementById('atividadeSelect');
        var atividadeSelecionada = atividadeSelect.value;
        var documentosNaoMarcados = [];

        todosDocumentosCheckboxes.forEach(function(checkbox) {
            if (!checkbox.checked) {
                documentosNaoMarcados.push(checkbox.parentElement.textContent.trim());
            }
        });

        // Se uma atividade estiver selecionada, verifique os documentos correspondentes à atividade
        if (atividadeSelecionada) {
            var documentosAtividade = document.querySelectorAll('#documentosContainer ul input[type="checkbox"]');

            documentosAtividade.forEach(function(checkbox) {
                if (!checkbox.checked) {
                    documentosNaoMarcados.push(checkbox.parentElement.textContent.trim());
                }
            });
        }

        var listaDocumentosNaoMarcados = document.getElementById('listaDocumentosNaoMarcados');
        listaDocumentosNaoMarcados.innerHTML = '';

        if (documentosNaoMarcados.length > 0) {
            documentosNaoMarcados.forEach(function(documento) {
                var li = document.createElement('li');
                li.textContent = documento;
                listaDocumentosNaoMarcados.appendChild(li);
            });
        } else {
            // Se nenhum documento estiver marcado, exiba uma mensagem indicando que não há documentos não marcados.
            var li = document.createElement('li');
            li.textContent = "Documentos Completo.";
            listaDocumentosNaoMarcados.appendChild(li);
        }

        // Scroll até a lista de documentos não marcados
        var documentosNaoMarcadosContainer = document.getElementById('documentosNaoMarcados');
        documentosNaoMarcadosContainer.scrollIntoView();
    }

    // Quando o valor do select muda, atualize a lista de documentos correspondentes à atividade
    document.getElementById('atividadeSelect').addEventListener('change', function() {
        var atividadeSelecionada = this.value;
        var documentosContainer = document.getElementById('documentosContainer');

        // Limpando o conteúdo anterior
        documentosContainer.innerHTML = '';

        // Adicione os documentos correspondentes à atividade selecionada
        if (atividadeSelecionada === 'HOSPITAL') {
            // Documentos para a Primeira Atividade
            var documentosHospitalAtividade = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o médico responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRM.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Regional de Medicina);",
                "CADASTRO NO SISTEMA NOTIVISA – print da página de cadastro no notivisa;",
                "REL DE EQUIPAMENTOS – Se possuir algum equipamento que emita radiação ionizante (Rx, mamografia, tomografia, hemodinâmica, densitometria óssea etc.) *Se não possuir faça uma declaração informando que não possui equipamento que emita radiação ionizante."
            ];

            var ul = document.createElement('ul');
            documentosHospitalAtividade.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'DISTRIBUIDORA') {
            // Documentos para Distribuidora de Produtos
            var documentosDistribuidora = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRF.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional);",
                "REL DE PRODUTOS - Relação sucinta da natureza e espécies dos produtos que a empresa irá comercializar assinada pelo responsável técnico e responsável legal.",
                "AFE – ANVISA - Autorização de funcionamento de empresa emitida pela Anvisa (P/ cada tipo de produto distribuído);",
                "AE – ANVISA - Autorização especial atualizada emitida pela Anvisa, se comercializar medicamentos a partir de substâncias sujeitas a controle especial;"
            ];

            var ul = document.createElement('ul');
            documentosDistribuidora.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'DIALISE') {
            var documentosDistribuidoraVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o médico responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRM.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Regional de Medicina);",
                "CADASTRO NO SISTEMA NOTIVISA;",
                "TERMO RT ENFERMAGEM - Termo de responsabilidade técnica pelo serviço de enfermagem (expedido pelo Conselho Regional de Enfermagem).",
                "CERT RT SUBSTITUTO MÉDICO - Certidão de Responsabilidade Técnica do substituto do RT médico"
            ];

            var ul = document.createElement('ul');
            documentosDistribuidoraVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'FARMACIA') {
            var documentosFarmaciaVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRF.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional);",
                "REL DE PRODUTOS - Relação sucinta da natureza e espécies dos produtos com que a empresa irá trabalhar.",
                "AFE – ANVISA - Autorização de funcionamento de empresa emitida pela Anvisa;",
                "AE – ANVISA - Autorização especial atualizada emitida pela Anvisa, se fabricar medicamentos a partir de substâncias sujeitas a controle especial;"
            ];

            var ul = document.createElement('ul');
            documentosFarmaciaVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'GASES') {
            var documentosGasesVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do Conselho Profissional.",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional);",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "REL DE PRODUTOS - Relação sucinta da natureza e espécies dos produtos que a empresa produz;",
                "AFE – ANVISA - Autorização de funcionamento de empresa emitida pela Anvisa (P/ cada tipo de produto distribuído);",
                "AE – ANVISA - Autorização especial atualizada emitida pela Anvisa, se comercializar medicamentos a partir de substâncias sujeitas a controle especial;"
            ];

            var ul = document.createElement('ul');
            documentosGasesVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'HEMOTERAPIA') {
            var documentosHemoterapiaVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o médico responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRM.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Regional de Medicina);",
                "CADASTRO NO SISTEMA NOTIVISA – print da página de cadastro no NOTIVISA;"
            ];

            var ul = document.createElement('ul');
            documentosHemoterapiaVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'INDUSTRIADEALIMENTOS') {
            var documentosIndustriadealimentosVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico (se houver) - cadastrar o responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do conselho de profissional.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "REL ALIMENTOS - Relação dos tipos de alimentos que vai produzir."
            ];

            var ul = document.createElement('ul');
            documentosIndustriadealimentosVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'INDUSTRIAMEDICAMENTOS') {
            var documentosIndustriadeMedicamentosVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRF.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional);",
                "REL DE PRODUTOS - Relação sucinta da natureza e espécies dos produtos com que a empresa irá trabalhar;",
                "AFE – ANVISA - Autorização de funcionamento de empresa emitida pela Anvisa;",
                "AE – ANVISA - Autorização especial atualizada emitida pela Anvisa, se fabricar medicamentos a partir de substâncias sujeitas a controle especial;",
                "LICENCA AMBIENTAL - Licença do órgão ambiental;"
            ];

            var ul = document.createElement('ul');
            documentosIndustriadeMedicamentosVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'INDUSTRIASANEANTES') {
            var documentosIndustriadeSaneantesVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do conselho regional.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional);",
                "REL DE PRODUTOS - Relação sucinta da natureza e espécies dos produtos com que a empresa produz;",
                "AFE – ANVISA - Autorização de funcionamento de empresa emitida pela Anvisa;",
                "LICENCA AMBIENTAL - Licença do órgão ambiental;"
            ];

            var ul = document.createElement('ul');
            documentosIndustriadeSaneantesVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'LAVANDERIA') {
            var documentosLavanderiaVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "REL EQUIPAMENTOS - Relação dos equipamentos."
            ];

            var ul = document.createElement('ul');
            documentosLavanderiaVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'MEDICINA') {
            var documentosMedicinaVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o médico responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRM.",
                "Físico – cadastrar como “funcionário” o físico responsável e adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal.",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional).",
                "DECLARACAO RT FISICO - Declaração de responsabilidade técnica pelo setor de proteção radiológica (físico) e seu substituto.",
                "TITULO FISICO - Título de especialista em física médica pela ABFM.",
                "REL EXAMES - Relação dos exames realizados.",
                "CNEN - Autorização de operação (funcionamento) emitida pelo CNEN.",
                "REL DE EQUIPAMENTOS – preencher a tabela com os equipamentos que emitam radiação ionizante."
            ];

            var ul = document.createElement('ul');
            documentosMedicinaVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'QUIMIOTERAPIA') {
            var documentosQuimioterapiaVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o médico responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRM.",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional).",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal."
            ];

            var ul = document.createElement('ul');
            documentosQuimioterapiaVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'RADIO') {
            var documentosRadioVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o médico responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRM.",
                "Físico – cadastrar como “funcionário” o físico responsável e adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional);",
                "CNEN - Autorização de operação (funcionamento) emitida pelo CNEN.",
                "DECLARACAO RT FISICO - Declaração de responsabilidade técnica pelo setor de proteção radiológica (físico);",
                "DOC ACELERADOR - Documentos do aparelho de radioterapia: cópia da nota fiscal ou outro documento comprobatório da procedência, contemplando marca, modelo e número de série e registro do produto na ANVISA;",
                "DOC BRAQUI - Documentos do aparelho de braquiterapia: cópia da nota fiscal ou outro documento comprobatório da procedência, contemplando marca, modelo e número de série e registro do produto na ANVISA;",
                "REL DE EQUIPAMENTOS – Relação de equipamentos que emitam radiação ionizante",
                "CERTIDÃO DE RT SUBSTITUTO DO MÉDICO.",
                "CERTIDÃO DE RT SUBSTITUTO DO FÍSICO."
            ];

            var ul = document.createElement('ul');
            documentosRadioVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'UTI') {
            var documentosUtiVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o médico responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRM.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Regional de Medicina);",
                "REL DE EQUIPAMENTOS – Relação de equipamentos que emitam radiação ionizante (Rx) *Se não possuir faça uma declaração informando que não possui equipamento que emita radiação ionizante."
            ];

            var ul = document.createElement('ul');
            documentosUtiVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'LABCLINICO') {
            var documentosLabCVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico - cadastrar o responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do CRF, CRBM ou CRM.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "CERT RT - Certificado de responsabilidade técnica pelo estabelecimento (emitido pelo Conselho Profissional);",
                "DEC LACEN - declaração de inscrição no programa de controle de qualidade emitido pelo LACEN.",
                "REL EXAMES - Relação dos exames realizados.",
                "REL POSTO COLETA - Relação de posto(s) de coleta vinculado(s) ao laboratório contendo nome, CNPJ, município e endereço."
            ];

            var ul = document.createElement('ul');
            documentosLabCVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        } else if (atividadeSelecionada === 'ESV') {
            var documentosESVVigilanciaSanitaria = [
                "Responsável Legal pelo estabelecimento – cadastrar o responsável legal (consta no contrato social, ata de eleição, designação no diário oficial ou procuração pública com poderes para administrar o estabelecimento. E adicionar o documento de identidade (RG, Habilitação ou Carteirinha do conselho profissional ou órgão similar).",
                "Responsável Técnico (se a legislação obrigar) - cadastrar o responsável técnico pelo estabelecimento e adicionar em seu cadastro a carteirinha do conselho de profissional.",
                "ALVARA LOC - Alvará de localização emitido pela prefeitura municipal;",
                "REL ATIVIDADE - Relação das atividades que a empresa efetivamente irá exercer."
            ];

            var ul = document.createElement('ul');
            documentosESVVigilanciaSanitaria.forEach(function(documento) {
                var li = document.createElement('li');
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'checkbox-with-margin'; // Adicione a classe CSS aqui
                li.appendChild(checkbox);
                li.appendChild(document.createTextNode(documento));
                ul.appendChild(li);
            });

            documentosContainer.appendChild(ul);
        }

        // Scroll até a lista de documentos não marcados
        var documentosNaoMarcadosContainer = document.getElementById('documentosNaoMarcados');
        documentosNaoMarcadosContainer.scrollIntoView();
    });

    // Adicione um evento de clique ao botão "Verificar Documentos" para mostrar a lista de documentos não marcados
    document.getElementById('verificarDocumentos').addEventListener('click', function() {
        var documentosNaoMarcadosContainer = document.getElementById('documentosNaoMarcados');
        documentosNaoMarcadosContainer.style.display = 'block'; // Mostrar a área de documentos não marcados
        verificarDocumentosNaoMarcados();
    });

</script>

</body>
</html>