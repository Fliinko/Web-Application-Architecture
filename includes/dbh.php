<?php 

    $hostname = "localhost";
    $username = "taninu";
    $password = "1234";
    $database = "restaurant";

    $conn = mysqli_connect($hostname,$username,$password,$database);

    function querySelect($sql,$conn){
       $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$sql)){
            echo "There was a problem";
            exit();
        } else { 
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        }
        return $result;
    }
    
    function queryInsert($sql,$conn){
        $stmt = mysqli_stmt_init($conn);
         if(!mysqli_stmt_prepare($stmt,$sql)){
             echo "There was a problem";
             exit();
         } else { 
             mysqli_stmt_execute($stmt);
         } 
     }

     