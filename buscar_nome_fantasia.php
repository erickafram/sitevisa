<?php
// Conecte-se ao banco de dados
include('db_conection.php');

if(isset($_POST["nome_fantasia"])){
    $output = '';
    $query = "SELECT nome_fantasia FROM alvaras_sanitarias WHERE nome_fantasia LIKE '%".$_POST["nome_fantasia"]."%' GROUP BY nome_fantasia";
    $result = $mysqli->query($query);

    $output = '<ul class="lista-sugestoes">';
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_array($result)){
            $output .= '<li class="sugestao">'.$row["nome_fantasia"].'</li>';
        }
    } else {
        $output .= '<li>Sem sugestões</li>';
    }
    $output .= '</ul>';
    echo $output;
}
?>
