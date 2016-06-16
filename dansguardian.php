<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>WebProxy</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/simple-sidebar.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <img src="img/proxy.png" alt="WEBPROXY" style="width: 150px; height: 150px;" />
                <li class="sidebar-brand">
                    <a href="#">
                        WebProxy
                    </a>
                </li>
                <li>
                    <a href="proxy.php">SQUID</a>
                </li>
                <li>
                    <a href="#">DANSGUARDIAN</a>
                </li>
                <li>
                    <a href="salir.php">SALIR</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                         <a href="#menu-toggle" class="btn btn-default" id="menu-toggle">Ver/Ocultar menú</a>
                        <h1>DANSGUARDIAN</h1>
<div class="formulario">
<form action="" method="post">
Restricción: <select name="tipo">
    <option name="exteban">Extension</option>
    <option name="ipban">IP</option>
    <option name="aplicacionban">Aplicacion</option>
    <option name="palabrasban">Palabras en pagina</option>
    <option name="palabrasurlban">Palabras en URL</option>
    <option name="urlban">URL</option>
    <option name="sitiosban">Sitios</option>
    </select> Valor: <input type="text" name="valor"> <input type="submit" value="añadir" name="añadir"><br><br>
    <input type="submit" name="reiniciardansguardian" value="Aplicar cambios y reiniciar" onclick = "location='dansguardian.php'" /><br><br>
</form>
<?php
if(isset($_POST['añadir'])){
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);

// Recogemos los datos, para introducirlos en la base de datos
$valor=$_POST['valor'];
$tipo=$_POST['tipo'];

$consulta="insert into baneadas (tipo,valor) values ('$tipo','$valor')";
$resultado_consulta=mysql_query($consulta,$conexion);

if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados";
}
}
?>

<!-- Borrado de registro  -->
<?php
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);            
if (isset($_POST['borrarregistro'])) {
    foreach ($_POST['borrarregistro'] as $id){
    $consulta="delete from baneadas where valor='$id'";
    $resultado_consulta=mysql_query($consulta,$conexion);
}
if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n O consulta que no hay ninguna regla activa en este momento.";
}
}
?>  
    
<?php
if(isset($_POST['reiniciardansguardian'])){
// Registros
// Extensiones baneadas
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM baneadas where tipo='extension'");

if (!copy('/etc/dansguardian/lists/bannedextensionlist', '/etc/dansguardian/lists/bannedextensionlist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/bannedextensionlist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/bannedextensionlist.temp', '/etc/dansguardian/lists/bannedextensionlist');

// Extensiones permitidas
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM excepciones where tipo='extension'");

if (!copy('/etc/dansguardian/lists/exceptionextensionlist', '/etc/dansguardian/lists/exceptionextensionlist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/exceptionextensionlist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/exceptionextensionlist.temp', '/etc/dansguardian/lists/exceptionextensionlist');   
    
// IP BANEADAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM baneadas where tipo='ip'");

if (!copy('/etc/dansguardian/lists/bannediplist', '/etc/dansguardian/lists/bannediplist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/bannediplist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/bannediplist.temp', '/etc/dansguardian/lists/bannediplist');

// IP PERMITIDAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM excepciones where tipo='ip'");

if (!copy('/etc/dansguardian/lists/exceptioniplist', '/etc/dansguardian/lists/exceptioniplist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/exceptioniplist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/exceptioniplist.temp', '/etc/dansguardian/lists/exceptioniplist');    
    
// APLICACIONES BANEADAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM baneadas where tipo='aplicacion'");

if (!copy('/etc/dansguardian/lists/bannedmimetypelist', '/etc/dansguardian/lists/bannedmimetypelist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/bannedmimetypelist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/bannedmimetypelist.temp', '/etc/dansguardian/lists/bannedmimetypelist');

// APLICACIONES PERMITIDAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM excepciones where tipo='aplicacion'");

if (!copy('/etc/dansguardian/lists/exceptionmimetypelist', '/etc/dansguardian/lists/exceptionmimetypelist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/exceptionmimetypelist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/exceptionmimetypelist.temp', '/etc/dansguardian/lists/exceptionmimetypelist');     
    
// PALABRAS EN URL BANEADAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM baneadas where tipo='palabras en url'");

if (!copy('/etc/dansguardian/lists/bannedregexpurllist', '/etc/dansguardian/lists/bannedregexpurllist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/bannedregexpurllist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/bannedregexpurllist.temp', '/etc/dansguardian/lists/bannedregexpurllist');

// PALABRAS EN URL PERMITIDAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM excepciones where tipo='palabras en url'");

if (!copy('/etc/dansguardian/lists/exceptionregexpurllist', '/etc/dansguardian/lists/exceptionregexpurllist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/exceptionregexpurllist', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/exceptionregexpurllist.temp', '/etc/dansguardian/lists/exceptionregexpurllist');       

// PALABRAS EN PAGINA BANEADAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM baneadas where tipo='palabras en pagina'");

if (!copy('/etc/dansguardian/lists/bannedphraselist', '/etc/dansguardian/lists/bannedphraselist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/bannedphraselist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/bannedphraselist.temp', '/etc/dansguardian/lists/bannedphraselist');

// PALABRAS EN PAGINA PERMITIDAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM excepciones where tipo='palabras en pagina'");

if (!copy('/etc/dansguardian/lists/exceptionphraselist', '/etc/dansguardian/lists/exceptionphraselist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/exceptionphraselist', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/exceptionphraselist.temp', '/etc/dansguardian/lists/exceptionphraselist');      

// URL BANEADAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM baneadas where tipo='url'");

if (!copy('/etc/dansguardian/lists/bannedurllist', '/etc/dansguardian/lists/bannedurllist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/bannedurllist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/bannedurllist.temp', '/etc/dansguardian/lists/bannedurllist');

// URL PERMITIDAS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM excepciones where tipo='url'");

if (!copy('/etc/dansguardian/lists/exceptionurllist', '/etc/dansguardian/lists/exceptionurllist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/exceptionurllist', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/exceptionurllist.temp', '/etc/dansguardian/lists/exceptionurllist');     

// SITIOS BANEADOS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM baneadas where tipo='url'");

if (!copy('/etc/dansguardian/lists/bannedsitelist', '/etc/dansguardian/lists/bannedsitelist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/bannedsitelist.temp', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/bannedsitelist.temp', '/etc/dansguardian/lists/bannedsitelist');

// SITIOS PERMITIDOS
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("dansguardian");

$resultado = mysql_query("SELECT valor FROM excepciones where tipo='url'");

if (!copy('/etc/dansguardian/lists/exceptionsitelist', '/etc/dansguardian/lists/exceptionsitelist.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/dansguardian/lists/vacio.txt');  // array con las lineas del fichero

$ftmp = fopen('/etc/dansguardian/lists/exceptionsitelist', 'w');  // se crea el fichero y si existe se machaca

    while ($fila = mysql_fetch_array($resultado)) {
    $regla = "$fila[0]\n";
    $nueva = $regla;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
fclose($ftmp);  // cierra el fichero
rename('/etc/dansguardian/lists/exceptionsitelist.temp', '/etc/dansguardian/lists/exceptionsitelist');     
    
//MYSQL
// Conectar con la base de datos Mysql para insertar los datos de la tabla baneadas
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);

// baneadas
$consultadel="delete from dansguardian_baneadas";
$consulta="insert into dansguardian_baneadas select * from dansguardian.baneadas";
$resultado_consultadel=mysql_query($consultadel,$conexion);
$resultado_consulta=mysql_query($consulta,$conexion);

if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n";
}

// excepciones
$consultadel="delete from dansguardian_excepciones";
$consulta="insert into dansguardian_excepciones select * from dansguardian.excepciones";
$resultado_consultadel=mysql_query($consultadel,$conexion);
$resultado_consulta=mysql_query($consulta,$conexion);

if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n";
}   
    
$outPut = shell_exec("sudo service dansguardian restart");
echo $outPut;
}    

?>
</div>

<div id="tabla" class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
        <tr>
        <th>Extensiones</th>
        </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from baneadas where tipo='Extension'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistro[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table><br>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
        <tr>
        <th>Extensiones baneadas</th>
        </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_baneadas where tipo='Extension'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table><br>
    </div></div></div>
    <div id="tabla" class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
        <tr>
        <th>IP</th>
        </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from baneadas where tipo='IP'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistro[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table><br>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
        <tr>
        <th>IP baneadas</th>
        </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_baneadas where tipo='IP'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table><br>
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
        <tr>
        <th>Aplicaciones</th>
        </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from baneadas where tipo='Aplicacion'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistro[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table><br>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>Aplicaciones baneadas</th>    
    </tr>
    <tr>
        <th>Tipo</th>
        <th>Valor</th>        
    </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_baneadas where tipo='Aplicacion'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>     
    </table>
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>Palabras en páginas</th>
    </tr>
    <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
    </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from baneadas where tipo='Palabras en Pagina'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistro[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">                    
    <tr>
    <th>Palabras en páginas bloquedas</th>
    </tr>
    <tr>
        <th>Tipo</th>
        <th>Valor</th>        
    </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_baneadas where tipo='Palabras en Pagina'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
        <th>URL</th>
    </tr>
    <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
    </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from baneadas where tipo='URL'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistro[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>URL Baneadas</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_baneadas where tipo='URL'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>Sitios</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from baneadas where tipo='Sitios'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistro[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>Sitios baneados</th>
    </tr>  
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_baneadas where tipo='Sitios'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>

<br>
<h1>Excepciones</h1><br>
<div class="formulario">  
<form action="" method="post">
Restricción: <select name="tipoexc">
    <option name="exteexc">Extension</option>
    <option name="ipexc">IP</option>
    <option name="aplicacionesexc">Aplicacion</option>
    <option name="palabraspagexc">Palabras en Páginas</option>
    <option name="palabrasurlsexc">Palabras en URL</option>
    <option name="urlsexc">URL</option>
    <option name="sitiosexc">Sitios</option>
    </select> Valor: <input type="text" name="valorexc"> <input type="submit" value="añadir" name="añadir"><br><br>
</form>
</div>
<?php
if(isset($_POST['añadir'])){
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);

// Recogemos los datos, para introducirlos en la base de datos
$valor=$_POST['valorexc'];
$tipo=$_POST['tipoexc'];

$consulta="insert into excepciones (tipo,valor) values ('$tipo','$valor')";
$resultado_consulta=mysql_query($consulta,$conexion);

if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados";
}
}
?>
                        
<!-- Borrado de registro  -->
<?php
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);            
if (isset($_POST['borrar'])) {
    foreach ($_POST['borrarregistroex'] as $id){
    $consulta="delete from excepciones where valor='$id'";
    $resultado_consulta=mysql_query($consulta,$conexion);
}
if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n O consulta que no hay ninguna regla activa en este momento.";
}
}
?>  
                        
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>Extensiones</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from excepciones where tipo='Extension'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistroex[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>Extensiones permitidas</th>
    </tr>  
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_excepciones where tipo='Extension'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>IP</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from excepciones where tipo='IP'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistroex[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>IP permitidas</th>
    </tr>  
            <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_excepciones where tipo='IP'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>Aplicaciónes</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from excepciones where tipo='Aplicacion'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistroex[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>Aplicaciones permitidas</th>
    </tr>  
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_excepciones where tipo='Aplicacion'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>Palabras en página</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from excepciones where tipo='Palabras en Páginas'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistroex[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>Palabras en página permitidas</th>
    </tr>  
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_excepciones where tipo='Palabras en Páginas'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>Palabras en URL</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from excepciones where tipo='Palabras en URL'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistroex[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>Palabras en URL permitidas</th>
    </tr>  
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_excepciones where tipo='Palabras en URL'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>URL</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from excepciones where tipo='URL'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistroex[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>URL permitidas</th>
    </tr>  
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_excepciones where tipo='URL'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="tablasizq">
    <form action="dansguardian.php" method="post">
    <table class="table table-hover">
    <tr>
    <th>Sitios</th>
    </tr>
        <tr>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Seleccionar</th>
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('dansguardian',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from excepciones where tipo='Sitios'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistroex[]' value='$fila[valor]'></td>";
                echo "</tr>";
  }
?>
    </table>
    <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
    </form></div></div>                    
    <div class="col-lg-6">
    <div class="tablasder">
    <table class="table table-hover">
    <tr>
    <th>Sitios permitidos</th>
    </tr>  
        <tr>
        <th>Tipo</th>
        <th>Valor</th>        
        </tr>
<?php
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from dansguardian_excepciones where tipo='Sitios'";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
  }
?>
    </table>    
    </div></div></div>
                        
                </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>

</body>

</html>
