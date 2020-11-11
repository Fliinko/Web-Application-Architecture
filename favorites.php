<?php 

    session_start();
    require_once "includes/dbh.php";
    require_once "includes/bootstrap.php";
    require_once "includes/tools.php";

    use PHPMailer\PHPMailer\PHPMailer;

    if(!isset($_SESSION['email'])){

        echo $twig->render("mustsignin.html");

    }else{

        $customersql = "SELECT c_id,c_name from customer
        WHERE c_email ='".$_SESSION['email']."'; ";
        $customerresult = querySelect($customersql,$conn);
        $row = mysqli_fetch_assoc($customerresult);
        $customerid = $row['c_id'];
        $customername = $row['c_name'];

        if(isset($_GET['add'])){

            $dish = $_GET['dish'];
            $sqlcheck = "SELECT * from favorites WHERE c_id = $customerid AND s_id = $dish";
            $result = querySelect($sqlcheck,$conn);

            if(mysqli_num_rows($result) > 0){
                header("Location: dish.php?id=$dish");
            }

            else{
            
            $sqlfave = "INSERT INTO favorites(c_id, s_id) 
            VALUES(?,?);";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt,$sqlfave)){
            echo "There was a problem";
            } else { 

             mysqli_stmt_bind_param($stmt,"ss", $customerid, $dish);   
             mysqli_stmt_execute($stmt);

        }
      }

        header("Location: dish.php?id=$dish");
    }

    if(isset($_GET['delete'])){
        $dish = $_GET['dish'];

        $sqlcheck = "SELECT * from favorites WHERE c_id = $customerid AND s_id = $dish;";
        $result = querySelect($sqlcheck,$conn);

        if(mysqli_num_rows($result) == 0){
            header("Location: dish.php?id=$dish");
        } else {
            $sqlremove = "DELETE FROM favorites WHERE c_id = $customerid AND s_id = $dish;";
            querySelect($sqlremove,$conn);
        }
        header("Location: dish.php?id=$dish");
    }

        $valid = array();

        $sqlnewpage = "SELECT s_id from favorites WHERE c_id = $customerid";
        $result = querySelect($sqlnewpage, $conn);
        $favorites = array();
        while($row = mysqli_fetch_assoc($result)){
            $dishid = $row['s_id'];
            $sqldish = "SELECT * from menu WHERE s_id = $dishid";
            $resultdish = querySelect($sqldish, $conn);
            $disharray = mysqli_fetch_assoc($resultdish);
            array_push($favorites, $disharray);
        }

        if(isset($_GET['send'])){
            $email = $emailErr = "";
    
            $Email = mysqli_real_escape_string($conn, $_GET['email']);
    
            if(empty($Email)){
                $emailErr = "Email is required";
                $valid['emailError'] = $emailErr;
              } else if(!filter_var($Email,FILTER_VALIDATE_EMAIL)){
                $emailErr = "Invalid email inputted";
                $valid['emailError'] = $emailErr;
              } else {
                  $email = clean_input($Email);
              }
    
              if(empty($emailErr)){
                // sending the email
                $subject = "Favorites List";
                $header = "From: $customername";
                $message = "This is the favourites list of ".$customername.":\n";
                foreach($favorites as $fav){
                    $message .= $fav['s_name']."\n";
                }
                $mail = new PHPMailer();
                
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Username = 'taninurestaurant@outlook.com';
                $mail->Password = 'ninu1234restaurant';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->Host = 'smtp-mail.outlook.com';

                $mail->setFrom('taninurestaurant@outlook.com', "Ta' Ninu Restaurant");
                $mail->addAddress($email, 'Customer');

                $mail->Subject = $subject;
                $mail->Body = $message;

                if($mail->send()){
                    $formvalues = [];
                    $valid['pageMessage'] = "Email was sent successfully to $email";
                    $valid['color'] = "success";
                }else{
                    $valid['sendError'] = "There was a problem sending the email\n".$mail->ErrorInfo;
                    $valid['pageMessage'] = "There are some invalid inputs";
                    $valid['color'] = "error";
                }
                
              } else {
                $valid['pageMessage'] = "There are some invalid inputs";
                $valid['color'] = "error";
              }
        }
        echo $twig->render("favorites.html",["favorites"=>$favorites, "valid"=>$valid]);
 } 

