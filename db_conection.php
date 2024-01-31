<?php
$hostname = "localhost"; // Normalmente é "localhost" se estiver executando localmente
$username = "root"; // Seu nome de usuário do MySQL
$password = ""; // Sua senha do MySQL
$database = "sitevisa"; // Nome do banco de dados que você está usando

// Conectando ao banco de dados MySQL
$mysqli = new mysqli($hostname, $username, $password, $database);

// Verificando a conexão
if ($mysqli->connect_error) {
    die("Erro na conexão com o banco de dados: " . $mysqli->connect_error);
}
?>