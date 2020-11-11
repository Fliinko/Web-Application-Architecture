<?php 
    require_once "includes/dbh.php";
    require_once "includes/bootstrap.php";

    $id = mysqli_real_escape_string($conn,$_GET['id']);

    $sql = "SELECT * FROM menu WHERE s_id='$id';";
    $result = querySelect($sql,$conn);
    $numRow = mysqli_num_rows($result);
    if($numRow > 0){
        $error =    "No Dish Found!";
    }

    $row = mysqli_fetch_assoc($result);

    $dish = [
        'id' => $row['s_id'],
        'category' => $row['s_category'],
        'type' => $row['s_type'],
        'name' => $row['s_name'],
        'desc' => $row['s_description'],
        'img' => $row['s_img'],
        'cost' => $row['s_cost'],
    ];

    echo $twig->render("dish.html",["dish"=>$dish]);
