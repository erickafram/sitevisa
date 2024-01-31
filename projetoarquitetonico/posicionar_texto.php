<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Posicionar Texto no PDF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        #pdfViewer {
            border: 1px solid #ccc;
            height: 500px;
            position: relative;
        }
        .draggable {
            position: absolute;
            cursor: move;
            border: 1px solid #333;
            background-color: #fff;
            padding: 5px;
        }
    </style>
</head>
<body>
<?php include('../includes/header.php'); ?>

<div class="container mt-5">
    <h2>Posicionar Texto no PDF</h2>
    <form id="formUploadPdf" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="arquivoPdf" class="form-label">Upload de PDF:</label>
            <input type="file" class="form-control" id="arquivoPdf" name="arquivoPdf" required>
        </div>
        <button type="submit" class="btn btn-primary">Carregar PDF</button>
    </form>

    <!-- Área para visualizar o PDF -->
    <div id="pdfViewer"></div>
</div>

<?php include('../includes/footer.php'); ?>

<!-- Bibliotecas e scripts necessários -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>

<script>
    let pdfDoc = null,
        pageNum = 1,
        pageRendering = false,
        pageNumPending = null,
        scale = 1,
        canvas = document.createElement("canvas"),
        ctx = canvas.getContext("2d"),
        pdfViewer = document.getElementById("pdfViewer");

    pdfViewer.appendChild(canvas);

    // Função para renderizar a página
    function renderPage(num) {
        pageRendering = true;
        // Usando promessa para buscar a página
        pdfDoc.getPage(num).then(function(page) {
            let viewport = page.getViewport({scale: scale});
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Renderizar PDF na página
            let renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            let renderTask = page.render(renderContext);

            // Aguardar o fim da renderização
            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    // Nova página requisitada enquanto renderizava
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });
    }

    // Função para carregar o PDF
    function loadPdf(file) {
        let fileReader = new FileReader();
        fileReader.onload = function() {
            let pdfData = new Uint8Array(this.result);
            pdfjsLib.getDocument({data: pdfData}).promise.then(function(pdfDoc_) {
                pdfDoc = pdfDoc_;
                renderPage(pageNum);
            });
        };
        fileReader.readAsArrayBuffer(file);
    }

    // Evento para carregar o PDF
    $("#formUploadPdf").on('submit', function(e) {
        e.preventDefault();
        let file = $("#arquivoPdf")[0].files[0];
        if (file) {
            loadPdf(file);
        }
    });

    // Criação da caixa de texto interativa
    let textBox = $('<div class="draggable">Arraste-me!</div>').appendTo('#pdfViewer');
    textBox.draggable({
        containment: "parent"
    });
</script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</body>
</html>
