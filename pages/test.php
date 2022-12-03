<?php
require "../config.php";
session_start();
// session_unset();

$answer = $_GET['answer'] ?? null;

if ($answer !== null) {
    add_user_answer($_GET['answer_question_id'], $answer);
}

$question = mysqli_query($connection, "SELECT * FROM questions WHERE id=".(int)$_GET['question_id'].";");

$questions_count = mysqli_query($connection, "SELECT * FROM questions;");
// $num_question=(int)$_GET['question'];


function print_answers($question_id, $conn, $count_questions)
{
    $answers = mysqli_query($conn, "SELECT * FROM answers WHERE answers.question_id=" . $question_id . ";");
    $item=0;
    while ($item = mysqli_fetch_assoc($answers)) {
?>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="question_id" id="answer_<?php echo $item["id"]; ?>" value=<?php echo $item["next_question_id"]; ?>>
            <input class="form-check-input" type="hidden" name="answer" value=<?php echo $item["id"]; ?>>
            <input class="form-check-input" type="hidden" name="answer_question_id" value=<?php echo $item["question_id"]; ?>>
            <label class="form-check-label" for="answer_<?php echo $item["id"]; ?>">
                <?php echo $item["text"]; ?>
            </label>
        </div>
<?php
    }
    
    if($question_id!=18){
        
        ?><button type="submit" class="btn btn-primary" >Далее</button><?php
    }
    else{
        ?><button type="button" onClick='location.href="results.php"' class="btn btn-primary">Готово</button><?php

        save_user_answers($_SESSION['answers']);
    }
}

function add_user_answer($question_id, $answer_id){
    $_SESSION['answers'][] = [
        'question_id' => $question_id,
        'answer_id' => $answer_id,
    ];
}

function save_user_answers($data){
    file_put_contents('user_answers.json', json_encode($data));
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

        <form class="questions_form" action="test.php" method="get">
            <?php 
            $item = mysqli_fetch_assoc($question) 

            ?><h3><?php echo $item["text"]; ?></h3><?php 
            
            print_answers($item["id"], $connection, mysqli_num_rows($questions_count));
            ?>
        </form>
    </div>
</body>
<script src="test.js"></script>
</html>