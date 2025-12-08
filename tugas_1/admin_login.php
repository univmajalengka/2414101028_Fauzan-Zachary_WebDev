<?php
session_start();
$error='';
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username==='admin' && $password==='123456'){
        $_SESSION['admin_logged_in']=true;
        header("Location: admin_dashboard.php");
        exit;
    }else{
        $error='Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<link rel="stylesheet" href="style.css">
<style>
/* Background */
body{
    margin:0;
    font-family:sans-serif;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg,#f5a623,#f76b1c);
}

/* Card login */
.login-container{
    background:white;
    padding:40px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    width:350px;
    text-align:center;
    animation:fadeIn 1s ease;
}

/* Animasi fadeIn */
@keyframes fadeIn{
    from {opacity:0; transform: translateY(-20px);}
    to {opacity:1; transform: translateY(0);}
}

h2{margin-bottom:20px;color:#f76b1c;}

input[type=text], input[type=password]{
    width:100%;
    padding:12px 15px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:10px;
    outline:none;
    transition:0.3s;
}

input[type=text]:focus, input[type=password]:focus{
    border-color:#f5a623;
    box-shadow:0 0 8px rgba(245,166,35,0.5);
}

button{
    width:100%;
    padding:12px;
    background:#f76b1c;
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-size:16px;
    transition:0.3s;
    margin-top:10px;
}

button:hover{
    background:#f5a623;
}

.error-msg{
    color:red;
    margin-bottom:10px;
}
</style>
</head>
<body>
<div class="login-container">
    <h2>Login Admin</h2>
    <?php if($error) echo "<p class='error-msg'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>

