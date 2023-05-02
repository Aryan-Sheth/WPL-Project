<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Appintment Confirmation | DocLab</title>
        <!-- ========================CSS Libraries============================= -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- ===========================Custom CSS============================ -->
        <link rel="stylesheet" href="assets/css/neurology.css">
    </head>
    <body>
        <?php

        

        function addToDatabase()
        {
            $fname = $_POST["firstname"];
            $mname = $_POST["middlename"];
            $lname = $_POST["lastname"];
            $gender = $_POST["gender"];
            $bGroup = $_POST["blood"];
            $age = $_POST["age"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $date = $_POST["date"]; 
            $time = $_POST["time"]; 
            $dept = $_POST["dept"]; 
            $purpose = $_POST["purpose"];
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "wpl";
            
            $conn = new mysqli($servername,$username,$password,$dbname);

            if($conn->connect_error)
            {
                die("Connection failed: " . $conn->connect_error);
            }

            

            $deptcsv = "";

            foreach($dept as $i)
            {
                $deptcsv = $deptcsv.$i.",";    
            }

            $deptcsv = substr($deptcsv,0,-1);
        
            $sql = "insert into medical values('$fname','$mname','$lname','$gender','$bGroup',$age,'$email',$phone,'$date','$time','$deptcsv','$purpose');";

            if ($conn->query($sql) === TRUE) 
            {
                // echo "true";
            } 
            else 
            {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            
            
            $conn->close();
        
        }

        function fileUpload()
        {
            $file = $_FILES['medicFile'];
            $targetDir = 'Uploads/';
            $targetFile = $targetDir . basename($file['name']);
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check file size (max 10 MB)
            if($file['size'] > 10000000)
            {
                return false;
            }

            // Move file to target directory
            if(!file_exists($targetFile))
            {
                if(move_uploaded_file($file['tmp_name'], $targetFile))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }

        }

        function sendConfirmation($emails, $doctor)
        {
            $fname = $_POST["firstname"];
            $mname = $_POST["middlename"];
            $lname = $_POST["lastname"];
            $gender = $_POST["gender"];
            $bGroup = $_POST["blood"];
            $age = $_POST["age"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $date = $_POST["date"]; 
            $time = $_POST["time"]; 
            $dept = $_POST["dept"]; 
            $purpose = $_POST["purpose"];

            $deptcsv = "";

            foreach($dept as $i)
            {
                $deptcsv = $deptcsv.$i.",";    
            }

            $deptcsv = substr($deptcsv,0,-1);

            $from_email         = 'doclabwebsite@gmail.com'; //from mail, sender email address

            
            //Load POST data from HTML form
            $sender_name = "Doclab"; //sender name
            $reply_to_email = 'doclabwebsite@gmail.com'; //sender email, it will be used in "reply-to" header
            $subject     = "Appointment Booking Confirmation"; //subject for the email
            

            if($doctor)
            {
                $recipient_email = $emails;
                $message     = "New Booking at $date $time\n\n
                                Patient Details:\n
                                Name: $fname $mname $lname\n
                                Gender: $gender\n
                                Blood Group: $bGroup\n
                                Age: $age\n
                                Phone No.: $phone\n
                                Purpose of Checkup: $purpose\n
                                Medical Record Attached
                                "; 

                $tmp_name = $_FILES['medicFile']['tmp_name']; // get the temporary file name of the file on the server
                $name     = $_FILES['medicFile']['name']; // get the name of the file
                $size     = $_FILES['medicFile']['size']; // get size of the file for size validation
                $type     = $_FILES['medicFile']['type']; // get type of the file
                $error     = $_FILES['medicFile']['error']; // get the error (if any)

                //validate form field for attaching the file
                if($error > 0)
                {
                    die('Upload error or No files uploaded');
                }

                $handle = fopen($tmp_name, "r"); // set the file handle only for reading the file
                $content = fread($handle, $size); // reading the file
                fclose($handle);                 // close upon completion
                $encoded_content = chunk_split(base64_encode($content));

                $boundary = md5("random"); // define boundary with a md5 hashed value
                //header
                $headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version
                $headers .= "From:".$from_email."\r\n"; // Sender Email
                $headers .= "Reply-To: ".$reply_to_email."\r\n"; // Email address to reach back
                $headers .= "Content-Type: multipart/mixed;"; // Defining Content-Type
                $headers .= "boundary = $boundary\r\n"; //Defining the Boundary
                    
                //plain text
                $body = "--$boundary\r\n";
                $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $body .= chunk_split(base64_encode($message));

                $body .= "--$boundary\r\n";
                $body .="Content-Type: $type; name=".$name."\r\n";
                $body .="Content-Disposition: attachment; filename=".$name."\r\n";
                $body .="Content-Transfer-Encoding: base64\r\n";
                $body .="X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n";
                $body .= $encoded_content; // Attaching the encoded file with email
            }
            else
            {
                $recipient_email = $email;
                $body = "Booking Confirmed For $fname $mname $lname at $date $time for $deptcsv departments";
                $headers = "From: doclabwebsite@gmail.com";
            }
            
            $sentMailResult = mail($recipient_email, $subject, $body, $headers);
        
            if($sentMailResult )
            {
                echo "Mail Confirmation Sent";
            }
            else
            {
                echo false;
            }

        }

        set_time_limit(300);
        addToDatabase();
        fileUpload();
        sleep(10);
        sendConfirmation("",false);
        sendConfirmation("aryan.sheth@somaiya.edu",true);
        

        ?>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" 
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" 
        integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
        
        <header>
            <!-- ====================NAVIGATION===================== -->
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <div class="navbar-brand ps-5">
                        <img src="assets/images/logo.svg" alt="doclab homepage">
                    </div>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                    aria-controls="navbarSupportedContent" aria-expanded="false">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse pe-5" id = "navbarSupportedContent">
                        <ul class="navbar-nav ms-auto align-items-center">
                            <li class="nav-item pe-4">
                                <a href="index.html" class="nav-link" aria-current="page">Home</a>
                            </li>
                            <li class="nav-item pe-4">
                                <a href="aboutus.html" class="nav-link">About Us</a>
                            </li>
                            <li class="nav-item pe-4">
                                <a href="doctors.html" class="nav-link">Our Doctors</a>
                            </li>
                            <li class="nav-item pe-4">
                                <a href="#" class="nav-link">Departments</a>
                            </li>
                            <li class="nav-item pe-4">
                                <a href="appointment.html" class="nav-link"><button class="appointment-button"> 
                                    <div class="fa fa-phone" style="color:white; font-size: larger;"></div><i class="pe-2"></i>
                                    Make an Appointment </button></a>
                            </li>
                            <li class="nav-item pe-4">
                                <a href="loginsignup.html" class="nav-link"><button class="appointment-button"><i class="p-2">
                                </i>Login/Signup<i class="p-2"></i> </button></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- =================END OF NAVIGATION====================== -->
        </header>

        <!-- ========================HOME BANNER ==========================-->

        <div class="container-fluid home">
            <div class="row pt-5 pb-5 align-items-center gx-5">
                <div class="col-xxl-4 offset-2 d-none d-sm-block">
                    <img src="assets/images/hero-banner.png" alt="" class="img-fluid hero-animation" style = "--order: 0">
                </div>
                <div class="col-xxl-3 hero-animation" style = "--order: 1">
                    <div class="body-white-text-strong hero-text">Appointment Confirmation</div>
                </div>
            </div>
        </div>
        <br>

        <div class="container">
            <div class="row pt-5 pb-5 align-items-center justify-content-center">
                   <div class="row">
                    <h1>Your Appointment Has Been Confirmed!!</h1>
                    <h5>Kindly check your email to verify</h5>
                </div>
            </div>
        </div>
        <!-- ==========================FOOTER========================= -->

        <footer>
            <div class = "container">
                <div class="p-5"></div>
                <div class="row align-items-center">
                    <div class="col-xl-2">
                        <img src="assets/images/logo.svg" alt="">
                    </div> 
                    <div class="col-xl-5 email-info body-white-text-strong">
                        <a href="mailto:doclabhospital@gmail.com" class="fa fa-envelope-open-o pe-2"></a>
                        doclabhospital@gmail.com <br>
                    </div>
                    <div class="col-xl-5 email-info body-white-text-strong">
                        <a href="tel:9920467976" class="fa fa-phone"></a>
                        +91- 9920467976<br>
                    </div>  
                </div>
                <div class="row pt-5 gx-5 justify-content-around">
                    <div class="col-xl-3">
                        <h3 class="body-white-text-strong">About Us</h3>
                        <p class="footer-subtext">
                            At Doclab, we provide the best treatment to our patients!
                            The different departments which Doclab provides are: Pulmonology, Cardiology, Neurology and Psychiary!
                        </p>
                        <br>
                        <div class="row align-items-center gx-5">
                            <div class="col-xl-2"><div class="fa fa-map-o" style="font-size: 2.6em;"></div></div>
                            <div class="col-xl-10"><div class="footer-subtext">
                                Doclab, Near Suresh Colony, Vile Parle West, Mumbai, Maharashtra 400056
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        
                        <ul type="none">
                            <li><h3 class="body-white-text-strong">Departments</h3></li>
                            <li><a href="pulmonology.html" class="footer-subtext s-1">Pulmonology</a></li>
                            <li><a href="cardiology.html" class="footer-subtext s-1">Cardiology</a></li>
                            <li><a href="neurology.html" class="footer-subtext s-1">Neurology</a></li>
                            <li><a href="psychiatry.html" class="footer-subtext s-1">Psychiatry</a></li>  
                        </ul>
                    </div>
                    <div class="col-xl-3">
                        <ul type="none">
                            <li><h3 class="body-white-text-strong">Useful Links</h3></li>
                            <li><a href="index.html" class="footer-subtext s-1">Home</a></li>
                            <li><a href="aboutus.html" class="footer-subtext s-1">About Us</a></li>
                            <li><a href="ourdoctors.html" class="footer-subtext s-1">Our Doctors</a></li>
                            <li><a href="departments.html" class="footer-subtext s-1">Departments</a></li>
                            <li><a href="appointment.html" class="footer-subtext s-1">Make An Appointment</a></li> 
                            <li><a href="loginsignup.html" class="footer-subtext s-1">Login/Signup</a></li>  
                        </ul>
                    </div>
                </div>
                <div class="p-5"></div>
            </div>
        </footer>
    </body>
</html>