<?php


$connection = mysqli_connect('localhost','root','','invest');

if($connection==false){
    echo "error";
    echo mysqli_connect_error();
    exit();
}