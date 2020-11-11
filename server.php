<?php

session_start();
require_once "includes/dbh.php";
require_once "includes/tools.php";
require_once "includes/bootstrap.php";


if (isset($_POST['reg_user'])) {

  $nameErr = $surnameErr = $teleErr = $emailErr = $passErr = "";
  $name = $surname = $tele = $email = $pass = "";

 
  $Name = mysqli_real_escape_string($conn, $_POST['Name']);
  $Surname = mysqli_real_escape_string($conn, $_POST['Surname']);
  $Telephone = mysqli_real_escape_string($conn, $_POST['Telephone']);
  $Email = mysqli_real_escape_string($conn, $_POST['Email']);
  $Password1 = mysqli_real_escape_string($conn,$_POST['Password1'] );
  $Password2 = mysqli_real_escape_string($conn,$_POST['Password2'] );

  if(empty($Name)){
    $nameErr = "Name is required";
    $valid['nameError'] = $nameErr;
  } else if(!preg_match("/^[a-zA-Z]*$/",$Name)){
    $nameErr = "Invalid Name inputted";
    $valid['nameError'] = $nameErr;
  } else {
    $name = clean_input($Name);
  }

  if(empty($Surname)){
    $surnameErr = "Surname is required";
    $valid['surnameError'] = $surnameErr;
  } else if(!preg_match("/^[a-zA-Z]*$/",$Surname)){
    $surnameErr = "Invalid Surname inputted";
    $valid['surnameError'] = $surnameErr;
  } else {
    $surname = clean_input($Surname);
  }

  if(empty($Telephone)){
    $teleErr = "Telephone is required";
    $valid['emailError'] = $teleErr;
  } else if(!preg_match("/^[0-9]*$/",$Telephone)){
    $teleErr = "Invalid telephone number inputted";
    $valid['teleError'] = $teleErr;
  } else {
    $tele = clean_input($Telephone);
  }

  if(empty($Email)){
    $emailErr = "Email is required";
    $valid['emailError'] = $emailErr;
  } else if(!filter_var($Email,FILTER_VALIDATE_EMAIL)){
    $emailErr = "Invalid email inputted";
    $valid['emailError'] = $emailErr;
  } else {
    $sql = "SELECT c_id FROM customer WHERE c_email='".$Email."';";
    $result = querySelect($sql,$conn);
    $exists = mysqli_num_rows($result);
    if($exists>0){
      $emailErr = "Email already exists";
      $valid['emailError'] = $emailErr;
    } else {
      $email = clean_input($Email);
    }
  }

  if(empty($Password1) && empty($Password2)){
    $passErr = "Passwords required";
    $valid['passError'] = $passErr;
  } else if(strcmp($Password1,$Password2)!=0){
    $passErr = "Passwords do not match";
    $valid['passError'] = $passErr;
  } else {
    $pass = clean_input($Password1);
    $pass = password_hash($pass,PASSWORD_DEFAULT);
  }

  if(empty($nameErr) && empty($surnameErr) && empty($teleErr) && empty($emailErr) && empty($passErr)){
      $sql = "INSERT INTO customer(c_name,c_surname,c_email,c_telephone,c_password) 
            VALUES(?,?,?,?,?);";
      $stmt = mysqli_stmt_init($conn);
      if(!mysqli_stmt_prepare($stmt,$sql)){
          echo "There was a problem";
      } else { 
        mysqli_stmt_bind_param($stmt,'sssss', $name, $surname, $email, $tele, $pass);   
        mysqli_stmt_execute($stmt);
      } 

      $formvalues = [];

      $_SESSION['email'] = $email;
      $valid['pagemessage'] = "You have just been registered successfully";
      $valid['color'] = "success";
     

      } else {
        $formvalues['name'] = $name;
        $formvalues['surname'] = $surname;
        $formvalues['tele'] = $tele;
        $formvalues['email'] = $email;

        $valid['pagemessage'] = "There are some invalid inputs";
        $valid['color'] = "error";
      }

      echo $twig->render('register.html',['valid' => $valid, 'formvalues' => $formvalues]);
  }

    if (isset($_POST['login_user'])) {

      $emailErr = $passErr = "";
      $email = $pass = "";

      $Email = mysqli_real_escape_string($conn, $_POST['Email']);
      $Password = mysqli_real_escape_string($conn, $_POST['Password']);

      if(empty($Email)){
        $emailErr = "Email is required";
        $validL['emailError'] = $emailErr;
      } else if(!filter_var($Email,FILTER_VALIDATE_EMAIL)){
        $emailErr = "Invalid email inputted";
        $validL['emailError'] = $emailErr;
      } else {
        $sql = "SELECT c_password FROM customer WHERE c_email='".$Email."';";
        $result = querySelect($sql,$conn);
        $exists = mysqli_num_rows($result);
        if($exists==0){
          $emailErr = "Email doesn't exist";
          $validL['emailError'] = $emailErr;
        } else {
          $email = clean_input($Email);
          $row = mysqli_fetch_array($result);
          $checkPass = $row['c_password'];
          if(empty($Password)){
            $passErr = "Password is required";
            $validL['passError'] = $passErr;
          } else if(!password_verify($Password,$checkPass)){
              $passErr = "Password is invalid";
              $validL['passError'] = $passErr;
          } 
        }
      }

      if(empty($emailErr) && empty($passErr)){
        $_SESSION['email'] = $email;
        $formvaluesL = [];
        $validL['pagemessage'] = "You have logged in successfully";
        $validL['color'] = "success";

      } else {
        $formvaluesL['email'] = $email;
        $validL['pagemessage'] = "There are some invalid inputs";
        $validL['color'] = "error";
      }

      echo $twig->render('login.html',['validL' => $validL, 'formvaluesL' => $formvaluesL]);
    }


    if(isset($_POST['feedback'])){

      $inputErr = $usernameErr = "";
      $input = $name = "";

      $Input = mysqli_real_escape_string($conn, $_POST['input']);
      $Name = mysqli_real_escape_string($conn, $_POST['username']);
  
      if(empty($Input)){
        $inputErr = "Input is required";
        $valid['inputError'] = $inputErr;
      } else {
        $input = clean_input($Input);
      }
    
      if(empty($Name)){
        $usernameErr = "Name is required";
        $valid['usernameError'] = $usernameErr;
      } else {
        $username = clean_input($Name);
      }  

      if(empty($usernameErr) && empty($inputErr)){
        $sql = "INSERT INTO feedback(input,name) 
              VALUES(?,?);";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$sql)){
            echo "There was a problem";
        } else { 
          mysqli_stmt_bind_param($stmt,'ss', $username, $input);   
          mysqli_stmt_execute($stmt);
        } 
  
        $formvalues = [];
  
       
        $valid['pagemessage'] = "You have sent feedback successfully. Thank you for you interest";
        $valid['color'] = "success";
       
  
        } else {
          $formvalues['username'] = $username;
          $formvalues['input'] = $input;
  
          $valid['pagemessage'] = "There are some invalid inputs";
          $valid['color'] = "error";
        }
  
        echo $twig->render('contact.html',['valid' => $valid, 'formvalues' => $formvalues]);
    }
