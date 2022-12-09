<?php
require "../config.php";
session_start();

# получаем параметры
$temp=mysqli_query($connection, "SELECT * FROM parameters;");
$parameters=array();
while(($row =  mysqli_fetch_assoc($temp))) {
    $parameters[] = $row;
}

# получаем список активов
$temp=mysqli_query($connection, "SELECT * FROM objects;");
$objects=array();
while(($row =  mysqli_fetch_assoc($temp))) {
    $objects[] = $row;
}

# получаем правила
$temp = file_get_contents("../rules.json");
$rules = json_decode($temp, true);

# атрибуты
$attributes=array();
$trade_style="";

function setParameters($conn, $answers){
    
    global $parameters;
    foreach($answers as $answer) {
        $temp=mysqli_fetch_assoc(mysqli_query($conn, "SELECT answers.id AS answer_id, questions.parameter_id, answers.parameter_value FROM answers LEFT JOIN questions ON answers.question_id=questions.id WHERE answers.id=".$answer["answer_id"].";"));
        $par_id=$temp["parameter_id"];
        $par_value=$temp["parameter_value"];
        $parameters[$par_id-1]["value"]=$par_value;
    }
}

function setWeights($conn, $rules){

}

function getObjects($conn, $answers){
    setParameters($conn, $answers);
    
    ?>
    <li>cock</li>
    <?php
}

?>



<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Экспертная система по подбору инвестиционного портфеля</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i|Montserrat:400,500,700|Playfair+Display:400,400i,700,700i&amp;subset=cyrillic" rel="stylesheet">
    <link href="../style.css" type="text/css" rel="stylesheet">
</head>

<body>
    <h1>Экспертная система по подбору инвестиционного портфеля</h1>
    <div class="main-block">

        <button type="button" class="btn btn-primary btn-lg" onClick='location.href="../index.php"'>Начать заново</button>

        <h2>Результаты</h2>

        <div class="result">
            <p class="titleResult">Исходя из ответов, Вам больше подойдёт:</p>


            <img src="https://media.tenor.com/x8v1oNUOmg4AAAAM/rickroll-roll.gif">
            <br>
            <ul>
                <?php getObjects($connection, $_SESSION["answers"]); ?>
            </ul>
            <?php print_r($parameters); ?>
        </div>
    </div>
</body>

</html>