<html>
    <head>
        <link rel="stylesheet" href="./assets/css/otpstyle.css">
    </head>
    <body onload = "loadSite()">
        
        <div class="container">
            <h3 class="title">OTP Verification</h3>
            <p class="sub-title">
              Enter the OTP you received to
              <span id="email"></span>
            </p>
            <div class="wrapper">
              <input type="text" class="field 1" id="otpField" maxlength="6">
            </div>
            <button style="padding-top: 10px; height: 50px; width: 100%; border: none; background-color: white; color: #0090e4; font-size: large;" onclick = "verifyOTP()" >Confirm</button>
        </div>

        <?php

        function sendOTP()
        {
            $to_email = $_POST["email"];
            $subject = "Doclab Login OTP";
            $otp = rand(100000,999999);
            $body = "Hey, $to_email \n $otp is your One-Time Password to login to Doclab\n If you did not request to login, please ignore this message";
            $headers = "From: doclabhospital@gmail.com";
 
            if (mail($to_email, $subject, $body, $headers))
            {
                echo $otp;
            }
 
            else
            {
                echo 0;
            }
        }

        function updateDB()
        {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "wpl";
            
            $conn = new mysqli($servername,$username,$password,$dbname);
    
            if($conn->connect_error)
            {
                die("Connection failed: " . $conn->connect_error);
            }
    
            $sql = "insert into users values('".$_POST["user"]."','".$_POST["email"]."','".$_POST["pass"]."');";
    
            if ($conn->query($sql) === TRUE) 
            {
                echo "true";
            } 
            else 
            {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
             
            
            $conn->close();
        }

        function getmail()
        {
            echo($_POST["email"]);
        }
        
        function redirect()
        {
            header("Location: index.html");
        }
        ?>
    </body>

    <script>

        var password = 0;

        function loadSite()
        {
            var email = "<?php getmail()?>";
            document.getElementById("email").innerHTML = email;
            var sentEmail = "<?php sendOTP()?>";
            sentEmail = parseInt(sentEmail);
            if(sentEmail != 0)
            {
                password = sentEmail;
            }
            else
            {
                alert("We were unable to send an email to the provided address");
                window.location.replace("index.html");
            }
        }

        function verifyOTP()
        {
            password = parseInt(password);
            if(password != 0)
            {
                if(document.getElementById("otpField").value == password)
                {
                    alert("Verification Successful");
                    var update = "<?php updateDB()?>";
                    if(update!="true")
                    {
                        alert(update);
                    }
                    window.location.replace("index.html");
                }
                else
                {
                    alert("Verification Failed - Invalid OTP");
                    window.location.replace("index.html");
                }

            }
            else
            {
                alert("There was an error generating your OTP\n Please Try Again");
                window.location.replace("index.html");
            }
        }
    </script>
</html> 
