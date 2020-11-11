<?php 
    session_start();
    require_once "includes/dbh.php";
    require_once "includes/bootstrap.php";
    require_once "includes/tools.php";



    if(isset($_GET['book_submit'])){
        $sqlTABLESEAN = "SELECT * from tables;";
        $result2 = querySelect($sqlTABLESEAN,$conn);
        $total = mysqli_num_rows($result2);

        $timeErr = $dateErr = $seatsErr = "";
        $time = $date = $seats = "";
      
        $Date = mysqli_real_escape_string($conn, $_GET['date']);
        $Time = mysqli_real_escape_string($conn, $_GET['time']);
        $Seats = mysqli_real_escape_string($conn, $_GET['seats']);

        
        if(empty($Date)){
            $dateErr = "Date is required";
            $valid['dateError'] = $dateErr;
        }else{
            $today_date=date('Y-m-d H:i:s');
            $current_date=strtotime($today_date);
            $booking_date=strtotime($Date);
            if($current_date==$booking_date){
                $dateErr = "Same day bookings cannot be accepted";
                $valid['dateError'] = $dateErr;
            }else if($current_date > $booking_date){
                $dateErr = "Date entered is in the past";
                $valid['dateError'] = $dateErr;
            } else {
                $date = clean_input($Date);
                if(empty($Time)){
                    $timeErr = "Time is required";
                    $valid['timeError'] = $timeErr;
                }else{
                    $time = clean_input($Time);
                    $sql = "SELECT r_opening1, r_closing1 FROM home;";
                    $result = querySelect($sql,$conn);
                    $row = mysqli_fetch_assoc($result);
                    $startT = strtotime($row['r_opening1']);    //opening hours
                    $bookT = strtotime($Time);
                    $endT = strtotime($row['r_closing1']) - 60*60;
                    if($startT <= $bookT && $endT > $bookT){
                        $sql = "SELECT b_number, t_number FROM booking
                                WHERE b_date = '$date' AND b_time = '$time';";
                        $result = querySelect($sql,$conn);
                        
                        $totalbooked = mysqli_num_rows($result);
                        
                        if($totalbooked == $total){
                            $timeErr = "There are no tables available at this time.";
                            $valid['timeError'] = $timeErr;        
                        }else{
                            $booked = array();
                            $available = array();
                            while($row == mysqli_fetch_assoc($result)){
                                array_push($booked, $row['t_number']);
                                    //array of available tables at that time
                            }
                            for($i = 1;$i <= $total;$i++){
                                if(in_array($i,$booked)){
                                    continue;
                                }else{
                                    array_push($available,$i);
                                }
                            }

                            if(empty($Seats)){
                                $seatErr = "Seats number is required";
                                $valid['seatError'] = $seatErr;
                            }else{
                                    $seats = clean_input($Seats);
                                    $found = FALSE;
                                    $seatmax = $seats+3;
                                    foreach($available as $value){
                                        $sqlSeatCheck = "SELECT t_number FROM tables
                                        WHERE t_seats >= $seats AND t_seats <=$seatmax AND t_number = $value;";
                                        $seatresult = querySelect($sqlSeatCheck,$conn);
                                        
                                        if( mysqli_num_rows($seatresult) == 1){
                    
                                            $customersql = "SELECT c_id from customer
                                                            WHERE c_email ='".$_SESSION['email']."'; ";
                                            $customerresult = querySelect($customersql,$conn);
                                            
                                            $row = mysqli_fetch_assoc($customerresult);
                                            $customerid = $row['c_id'];
                                            
                                            $sqlbook = "INSERT INTO booking(b_date,b_time,t_number,c_id) 
                                            VALUES(?,?,?,?);";
                                            $stmt = mysqli_stmt_init($conn);
                                            if(!mysqli_stmt_prepare($stmt,$sqlbook)){
                                                echo "There was a problem";
                                            } else { 
                                                $date = date("Y-m-d",strtotime($date));
                                                $time = date("H:i",strtotime($time));
                                                mysqli_stmt_bind_param($stmt,"ssss", $date, $time, $value,$customerid);   
                                                mysqli_stmt_execute($stmt);
                                         }
                                        $tablenumber = $value;
                    
                                        $found = TRUE;
                                        break;
                                      }  
                    
                                        
                                    }
                                    
                                    if(!$found){
                                        $seatErr = "Not enough Seats.";
                                        $valid['seatError'] = $seatErr;
                                    }
                                    
                    
                            }
                        } 
                                    
                    }else{
                        $timeErr = "Time must be between ".$row['r_opening1']." and ".$row['r_closing1'];
                        $valid['timeError'] = $timeErr;
                    }
                    
                }
            }
        }
        if(empty($dateErr) && empty($timeErr) && empty($seatErr)){
            $valid['pageMessage'] = "Table $tablenumber Booked at $time on $date";
            $valid['color'] = "success";
        } else {
            $valid['pageMessage'] = "There were some issues with your input";
            $valid['color'] = "error";
        }
        echo $twig->render("bookatable.html",["valid"=>$valid]);
        

    }else{
    
        if(!isset($_SESSION['email'])){

            echo $twig->render("mustsignin.html");
        }else{
            $sql = "SELECT * FROM tables;";
            $results = querySelect($sql,$conn);

            echo $twig->render("bookatable.html",["tables"=>$results]);
        } 

    }
