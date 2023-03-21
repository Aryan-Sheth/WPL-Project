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

        $sql = "select password from users where username = ".$_POST("user")."";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) 
        {
            $flag = 0;
            while($row = $result->fetch_assoc()) 
            {
                if($_POST("pass") == $row["password"])
                {
                    $flag = 1;
                    echo("login success");
                }
            }
            if($flag == 0)
            {
                echo("login fail");
            }
        } 
        else 
        {
            echo("No such username");
        }
         
        
        $conn->close();
        ?>
    </body>
</html> 
