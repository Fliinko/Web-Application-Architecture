<?php 
    require_once "includes/dbh.php";
    require_once "includes/bootstrap.php";

    $sql = "SELECT * FROM staff;";
    $result = querySelect($sql,$conn);
   
    echo $twig->render('staff.html',['members' => $result]);
?>
    


