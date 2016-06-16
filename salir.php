<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Panel de control</title>
</head>
<body> 
    Has cerrado la sesión. pulse <a href="proxy.php" > aquí </a> para volver atrás.     
        
<!-- Codigo de javascript -->
<script src="js/salir.js"></script>
<?php
setcookie(session_name('inicio', '', time() -600000));
session_destroy();
?>
</body>
</html>