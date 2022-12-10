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
$trade_style="долгосрочная торговля";

function setParameters($conn, $answers){
    
    global $parameters;
    foreach($answers as $answer) {
        $temp=mysqli_fetch_assoc(mysqli_query($conn, "SELECT answers.id AS answer_id, questions.parameter_id, answers.parameter_value FROM answers LEFT JOIN questions ON answers.question_id=questions.id WHERE answers.id=".$answer["answer_id"].";"));
        $par_id=$temp["parameter_id"];
        $par_value=$temp["parameter_value"];
        $parameters[$par_id-1]["value"]=$par_value;
    }
}


function setWeights($rules){
    global $objects;
    global $parameters;
    global $trade_style;

    foreach($rules["easy-pete"] as $rule){
        $param_id=$rule["parameter"];
        $par_value=$rule["par_value"];

        if($rule["type"]=="weight"){
            switch ($rule["operator"]){
                case "=":
                    if($parameters[$param_id-1]["value"]==$par_value){
                        foreach($objects as &$item){
                            if(in_array($item[$rule["attribute"]], $rule["attr_values"])){
                                if($rule["weight_operator"]=="+"){
                                    $item["weight"]+=$rule["weight"];
                                }
                                else if($rule["weight_operator"]=="-"){
                                    $item["weight"]-=$rule["weight"];
                                }
                                else{
                                    $item["weight"]=$rule["weight"];
                                }
                            }
                        }
                    }

                    break;
                default: break;
            }
        }
        else{
            switch ($rule["operator"]){
                case "=":
                    if($parameters[$param_id-1]["value"]==$par_value){
                        $trade_style=$rule["style"];
                    }

                    break;
                default: break;
            }
        }
    }

    foreach($rules["difficult-pete"] as $rule){
        $param1_id=$rule["parameter1"];
        $par1_value=$rule["par_value1"];
        $param2_id=$rule["parameter2"];
        $par2_value=$rule["par_value2"];
        if (($rule["operator1"]=="=") && ($rule["operator2"]=="=")){
            if(($parameters[$param1_id-1]["value"]==$par1_value)&&($parameters[$param2_id-1]["value"]==$par2_value)){
                foreach($objects as &$item){
                    if(in_array($item[$rule["attribute"]], $rule["attr_values"])){
                        if($rule["weight_operator"]=="+"){
                            $item["weight"]+=$rule["weight"];
                        }
                        else if($rule["weight_operator"]=="-"){
                            $item["weight"]-=$rule["weight"];
                        }
                        else{
                            $item["weight"]=$rule["weight"];
                        }
                    }
                }
            }
        }
        else if (($rule["operator1"]=="=") && ($rule["operator2"]=="<")){
            if(($parameters[$param1_id-1]["value"]==$par1_value)&&($parameters[$param2_id-1]["value"]<$par2_value)){
                foreach($objects as &$item){
                    if(in_array($item[$rule["attribute"]], $rule["attr_values"])){
                        if($rule["weight_operator"]=="+"){
                            $item["weight"]+=$rule["weight"];
                        }
                        else if($rule["weight_operator"]=="-"){
                            $item["weight"]-=$rule["weight"];
                        }
                        else{
                            $item["weight"]=$rule["weight"];
                        }
                    }
                }
            }
        }
        else if (($rule["operator1"]=="=") && ($rule["operator2"]==">")){
            if(($parameters[$param1_id-1]["value"]==$par1_value)&&($parameters[$param2_id-1]["value"]>$par2_value)){
                foreach($objects as &$item){
                    if(in_array($item[$rule["attribute"]], $rule["attr_values"])){
                        if($rule["weight_operator"]=="+"){
                            $item["weight"]+=$rule["weight"];
                        }
                        else if($rule["weight_operator"]=="-"){
                            $item["weight"]-=$rule["weight"];
                        }
                        else{
                            $item["weight"]=$rule["weight"];
                        }
                    }
                }
            }
        }
        else if (($rule["operator1"]=="<") && ($rule["operator2"]=="<")){
            if(($parameters[$param1_id-1]["value"]<$par1_value)&&($parameters[$param2_id-1]["value"]<$par2_value)){
                foreach($objects as &$item){
                    if(in_array($item[$rule["attribute"]], $rule["attr_values"])){
                        if($rule["weight_operator"]=="+"){
                            $item["weight"]+=$rule["weight"];
                        }
                        else if($rule["weight_operator"]=="-"){
                            $item["weight"]-=$rule["weight"];
                        }
                        else{
                            $item["weight"]=$rule["weight"];
                        }
                    }
                }
            }
        }
        else if (($rule["operator1"]=="<") && ($rule["operator2"]==">")){
            if(($parameters[$param1_id-1]["value"]<$par1_value)&&($parameters[$param2_id-1]["value"]>$par2_value)){
                foreach($objects as &$item){
                    if(in_array($item[$rule["attribute"]], $rule["attr_values"])){
                        if($rule["weight_operator"]=="+"){
                            $item["weight"]+=$rule["weight"];
                        }
                        else if($rule["weight_operator"]=="-"){
                            $item["weight"]-=$rule["weight"];
                        }
                        else{
                            $item["weight"]=$rule["weight"];
                        }
                    }
                }
            }
        }
        else if (($rule["operator1"]==">") && ($rule["operator2"]==">")){
            if(($parameters[$param1_id-1]["value"]>$par1_value)&&($parameters[$param2_id-1]["value"]>$par2_value)){
                foreach($objects as &$item){
                    if(in_array($item[$rule["attribute"]], $rule["attr_values"])){
                        if($rule["weight_operator"]=="+"){
                            $item["weight"]+=$rule["weight"];
                        }
                        else if($rule["weight_operator"]=="-"){
                            $item["weight"]-=$rule["weight"];
                        }
                        else{
                            $item["weight"]=$rule["weight"];
                        }
                    }
                }
            }
        }
    }
}

function getObjects($conn, $answers, $rules, $parameters){
    global $objects;
    setParameters($conn, $answers);
    setWeights($rules);
    $result=array();
    $weight_sum=0;
    $sum=0;
    $deposit=intval($parameters[3]["value"]);
    

    foreach($objects as $object){
        if($object["weight"]>0) {
            $result[]=$object;
            $weight_sum+=$object["weight"];
            $sum+=$object["price"];
        }
    }


    foreach($result as &$item){
        $item["amount"]=round($item["weight"]/($weight_sum+$sum+$deposit)*2500)*floatval($item["min_amount"]);
    }
 
    if(count($result)==0)
    {
        ?>
        <img src="https://media.tenor.com/x8v1oNUOmg4AAAAM/rickroll-roll.gif">
        <?php
    }
    foreach($result as $item){
    ?>
        <li><?php echo "Актив: ".$item["name"]."; Стоимость: ".$item["price"]."$; Количество: ".$item["amount"];?></li>
    <?php
    }
     ?><p><?php //echo "Сумма активов: ".$sum."$" ?></p><?php
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
            <br>
            <ul>
                <?php getObjects($connection, $_SESSION["answers"], $rules, $parameters); ?>
            </ul>
            <p>Стиль торговли: <?php echo $trade_style; ?></p>
        </div>
    </div>
</body>

</html>