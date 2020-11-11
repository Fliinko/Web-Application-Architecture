<?php 
    require_once "includes/dbh.php";
    require_once "includes/bootstrap.php";

        $sql = "SELECT * FROM home;";
        $result = querySelect($sql,$conn);
        $row = mysqli_fetch_assoc($result);
        $home = [
            'name' => $row['r_name'],
            'story' => $row['r_story'],
            'opening1' => $row['r_opening1'],
            'closing1' => $row['r_closing1'],
            'opening2' => $row['r_opening2'],
            'closing2' => $row['r_closing2'],
            'desc' => $row['r_desc'],
            'address' => $row['r_address']
        ];
        
        echo $twig->render("index.html",["home"=>$home]);
?>
