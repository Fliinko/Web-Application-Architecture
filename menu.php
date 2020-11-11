<?php 
    require_once "includes/dbh.php";
    require_once "includes/bootstrap.php";

    $sqlS = "SELECT s_id,s_name, s_img, s_cost FROM menu WHERE s_category='Starters' ORDER BY s_type;";
    $resultS = querySelect($sqlS,$conn);   
 
    $sqlM = "SELECT s_id,s_name, s_img, s_cost FROM menu WHERE s_category='Main' ORDER BY s_type;";
    $resultM = querySelect($sqlM,$conn);
   
    $sqlD = "SELECT s_id,s_name, s_img, s_cost FROM menu WHERE s_category='Dessert' ORDER BY s_type;";
    $resultD = querySelect($sqlD,$conn);
    
    echo $twig->render('menu.html',['starters' => $resultS, 'mains' => $resultM, 'desserts' => $resultD]);

?>
