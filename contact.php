<?php 
  require_once 'includes/bootstrap.php';
  require_once 'includes/dbh.php';

  $sql = "SELECT * FROM contacts;";
        $result = querySelect($sql,$conn);
        $row = mysqli_fetch_assoc($result);
        $contacts = [
            'tele' => $row['Telephone'],
            'email' => $row['Email'],
            'mobile' => $row['Mobile'],
            'address' => $row['Address'],
        ];
        
        echo $twig->render("contact.html",["contact"=>$contacts]);
