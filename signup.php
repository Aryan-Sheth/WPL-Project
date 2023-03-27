<html>
    <body>
        <?php

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "wpl";

        $conn = new mysqli($servername,$username,$password,$dbname);

        if($conn->connect_error)
        {
            die("Connection failed: " . $conn->connect_error);
        }
        //MAIL SENDING
        $to_email = $_POST["email"];
        $subject = "Test email to send from XAMPP";
        $body = "Hi, This is test mail to check how to send mail from Localhost Using Gmail ";
        $headers = "From: doclabhospital@gmail.com";
 
        if (mail($to_email, $subject, $body, $headers))
 
        {
            echo '<script>prompt("Email sending success!")</script>';
        }
 
        else
 
        {
            echo '<script>alert("Email sending failed!")</script>';
        }

        //-------------------------------------

        $sql = "insert into users values('".$_POST["user"]."','".$_POST["email"]."','".$_POST["pass"]."');";

        if ($conn->query($sql) === TRUE) 
        {
            // echo "New record created successfully";
        } 
        else 
        {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
         
        
        $conn->close();

        header("Location: index.html");
        ?>
    </body>
</html> 
