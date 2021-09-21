<?php // Do not put any HTML above this line
require_once "pdo.php";
session_start();

unset($_SESSION['name']);
unset($_SESSION['user_id']);


if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
// $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is php123
// $failure = false;  // If we have no POST data
// Check to see if we have some POST data, if we do process it

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
      if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ){ 
              $_SESSION['error']= "Email and password are required";//"User name and password are required";
              header("Location:login.php");
              return;
            } 
                    $check = hash('md5', $salt.$_POST['pass']);
                    $sql="SELECT user_id, name FROM users WHERE email = :em AND password = :pw";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ( $row !== false ) {
                                   $_SESSION['name'] = $row['name'];
                                   $_SESSION['user_id'] = $row['user_id'];
                                   // Redirect the browser to index.php
                                   header("Location: index.php");
                                   return;
                    }else{
                            $_SESSION['error']= "Incorrect password";
                            error_log("Login fail".$_POST['email']."$check");
                            header("Location:login.php");
                            return;
                     }
}
// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<title>Pedro Pablo Aruata Mamani</title>
<?php require_once "head.php"; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" 
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" 
        crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>

<?php 
            if(isset($_SESSION['error'])){
                echo '<p style="color:red">' . $_SESSION['error'] . '</p>';
                unset($_SESSION['error']);
            }
 ?>


<!-- 
<form method="POST">

<p>User Name: <input type="text" name="email"></p>
<p>Password:  <input type="text" name="pass"></p>

<p><input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
 -->

<form method="post" action="login.php">
  <label for="email">Email</label>
  <input type="text" name="email" id="email"><br/>
  <label for="id_1723">Password</label>
  <input type="password" name="pass" id="id_1723"></br>
  <input type="submit"  onclick="return  doValidate();" value="Log In">
  <input type="submit" name="cancel" value="Cancel">
</form>




<script>
  function doValidate(){
        console.log('Validating...');
        try {
            addr=document.getElementById('email').value;
            pw=document.getElementById('id_1723').value;
            console.log("Validating addr=" + addr + "pw="+pw);
            if(addr==null || addr=="" || pw==null || pw=="")
            {
              alert("Both fields must be filled out");
              return false;
            }

              if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
            }
            return true;
        }catch(e){
          return false;
        }

        return false;
  }
</script>



<!-- <script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script> -->


</div>
</body>


</html>
