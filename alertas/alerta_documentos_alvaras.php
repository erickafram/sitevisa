<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<?php

include('../db_conection.php');
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Consulta alvarás sanitários vencidos
$sqlAlvaras = "SELECT * FROM alvaras_sanitarias WHERE status_alvara = 'VENCIDO'";
$resultAlvaras = $mysqli->query($sqlAlvaras);

// Consulta documentos com data de alerta menor que a data atual
$currentDate = date("Y-m-d");
$sqlDocumentos = "SELECT * FROM documentos_alertas WHERE data_alerta < '$currentDate'";
$resultDocumentos = $mysqli->query($sqlDocumentos);

if ($resultAlvaras->num_rows > 0 || $resultDocumentos->num_rows > 0) {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtppro.zoho.com'; // Substitua pelo seu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'alertas@vigilancia-to.com.br'; // Substitua pelo seu e-mail
        $mail->Password = '@@2024@@Ekb'; // Substitua pela sua senha
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinatários
        $mail->setFrom('alertas@vigilancia-to.com.br', 'Vigiância Sanitária Tocantins'); // Substitua pelo seu e-mail e nome de remetente
        $mail->addAddress('erickafram08@gmail.com'); // E-mail do primeiro destinatário (erickafram08@gmail.com)
        $mail->addAddress('licenciamento.visato@gmail.com'); // E-mail do segundo destinatário (licenciamento.visato@gmail.com)

        // Conteúdo do E-mail
        $mail->isHTML(true);
        $mail->Subject = 'Notificação de Documentos Vencidos';
        $mail->CharSet = 'UTF-8'; // Definindo a codificação de caracteres como UTF-8

        $message = "Aviso de documentos vencidos:<br>";

// Alvarás Sanitários Vencidos
        if ($resultAlvaras->num_rows > 0) {
            $message .= "<br><strong>Alvarás Sanitários Vencidos:</strong><br><ul>";
            while ($row = $resultAlvaras->fetch_assoc()) {
                $message .= "<li>Número de Alvará: {$row['numero_alvara']}</li>";
                $message .= "<li>Link do Processo do Alvará Sanitário: <a href='https://sistemas.saude.to.gov.br/infovisacore/Manager/Procedure?identifier={$row['numero_processo']}'>{$row['numero_processo']}</a></li>";
            }
            $message .= "</ul>";
        }

// Documentos com Data de Alerta Expirada
        if ($resultDocumentos->num_rows > 0) {
            $message .= "<br><strong>Documentos com Data de Alerta Expirada:</strong><br><ul>";
            while ($row = $resultDocumentos->fetch_assoc()) {
                $message .= "<li>Tipo de Documento: {$row['tipo_documento']}</li>";
                $message .= "<li>Número do Documento: {$row['numero_documento']}</li>";
                $message .= "<li>Link do Processo do Documento: <a href='https://sistemas.saude.to.gov.br/infovisacore/Manager/Procedure?identifier={$row['numero_processo_documentos']}'>{$row['numero_processo_documentos']}</a></li>";
            }
            $message .= "</ul>";
        }

        $mail->Body = $message;


        $mail->send();
        echo "E-mail de notificação de documentos vencidos enviado com sucesso para erickafram08@gmail.com<br>";

    } catch (Exception $e) {
        echo "Erro ao enviar a notificação de documentos vencidos. Erro: {$mail->ErrorInfo}<br>";
    }
} else {
    echo "Nenhum documento vencido ou com data de alerta expirada encontrado.<br>";
}

$mysqli->close();
?>
