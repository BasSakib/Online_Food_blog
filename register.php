<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

$message = [];

if(isset($_POST['submit'])){

   $name = htmlspecialchars($_POST['name']);
   $email = htmlspecialchars($_POST['email']);
   $number = htmlspecialchars($_POST['number']);

   $pass = sha1($_POST['pass']);
   $cpass = sha1($_POST['cpass']);

   // check existing user
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? OR number = ?");
   $select_user->execute([$email, $number]);

   if($select_user->rowCount() > 0){

      $message[] = 'Email or number already exists!';

   }else{

      if($pass != $cpass){

         $message[] = 'Confirm password does not match!';

      }else{

         // insert user
         $insert_user = $conn->prepare("INSERT INTO `users`(name, email, number, password, address) VALUES(?,?,?,?,?)");

         $insert_user->execute([
            $name,
            $email,
            $number,
            $pass,
            ''
         ]);

         // login user
         $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
         $select_user->execute([$email, $pass]);

         if($select_user->rowCount() > 0){

            $row = $select_user->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user_id'] = $row['id'];

            header('location:home.php');
            exit();

         }else{
            $message[] = 'Registration failed!';
         }

      }

   }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>Register</title>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- CSS -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      body{
         font-family: Arial, sans-serif;
      }

      .message{
         background: #ffdddd;
         color: #b30000;
         padding: 10px;
         margin: 10px auto;
         width: 50%;
         border-radius: 5px;
         text-align: center;
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message">'.$msg.'</div>';
   }
}
?>

<section class="form-container">

   <form action="" method="post">

      <h3>Register Now</h3>

      <input type="text" 
             name="name" 
             required 
             placeholder="Enter your name" 
             class="box" 
             maxlength="50">

      <input type="email" 
             name="email" 
             required 
             placeholder="Enter your email" 
             class="box" 
             maxlength="50">

      <input type="number" 
             name="number" 
             required 
             placeholder="Enter your number" 
             class="box">

      <input type="password" 
             name="pass" 
             required 
             placeholder="Enter your password" 
             class="box" 
             maxlength="50">

      <input type="password" 
             name="cpass" 
             required 
             placeholder="Confirm your password" 
             class="box" 
             maxlength="50">

      <input type="submit" 
             value="Register Now" 
             name="submit" 
             class="btn">

      <p>Already have an account? 
         <a href="login.php">Login now</a>
      </p>

   </form>

</section>

</body>
</html>