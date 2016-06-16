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
                    <a href="#">SQUID</a>
                </li>
                <li>
                    <a href="dansguardian.php">DANSGUARDIAN</a>
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
                          <h1>SQUID</h1><br>
                            <h1>ACL</h1>
<!-- Formulario acl -->
        <div id="formulario">
        <form action="" method="post">
            <p>Nombre de la acl: <input type="text" name="nombreacl" size="10" maxlength="15"> Tipo: <select name="tipo">
                <option name="1">src</option>
                <option name="2">dst</option>
                <option name="3">dstdom_regex</option>
                <option name="4">time</option>
                <option name="5">url_regex</option>
                <option name="6">urlpath_regex</option>
                <option name="7">arp</option>
                <option name="8">port</option>
                <option name="9">req_mime</option>
                <option name="10">proxy_auth</option>
                <option name="11">proto</option>
                </select> Valor: <input type="text" name="valor"></p><br>
            <input type="submit" value="Añadir" name="añadiracl" />
            <input type="reset" value="Borrar" name="Reset" /><br><br>
            <input type="submit" name="reiniciarsquid" value="Aplicar cambios y reiniciar" />
        </form><br/><br/>
<!-- Comienzo del PHP acl -->
<?php
if(isset($_POST['añadiracl'])){           
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('proxy',$conexion);

// Recogemos los datos, para introducirlos en la base de datos
$nombre_acl=$_POST['nombreacl'];
$valor=$_POST['valor'];
$tipo=$_POST['tipo'];
$consulta="insert into acl (nombre,tipo,valor) values ('$nombre_acl','$tipo','$valor')";
$resultado_consulta=mysql_query($consulta,$conexion);

if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n";
}
}
?>

<!-- Borrado de registro  -->
<?php
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('proxy',$conexion);            
if (isset($_POST['borrarregistro'])) {
    foreach ($_POST['borrarregistro'] as $id){
    $consulta="delete from acl where nombre='$id'";
    $resultado_consulta=mysql_query($consulta,$conexion);
}
if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n O consulta que no hay ninguna regla activa en este momento.";
}
}
?>
        </div>
        <br>
<!-- Tablas que se muestran las activas y las que configuramos -->
        <div id="tablasacl">
<!-- Tabla que en la que vamos a poner los valores a configurar -->
        <div class="col-lg-6">
        <div class="tablaizq">
        <form action="proxy.php" method="post"><table class="table table-hover">
        <tr>
        <th>Nombre ACL</th>
        <th>Tipo</th>
        <th>Valores</th>
        <th>Seleccionar</th>
        </tr>
        <?php
            // TABLA DE IPS
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('proxy',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from acl where nombre !=''";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[nombre] </td><td> $fila[tipo] </td><td> $fila[valor] </td><td><input type='checkbox' name='borrarregistro[]' value='$fila[nombre]'></td>";
                echo "</tr>";
            }
// isset que nos dice que hasta que no se pulse el botón no se ejecutará el codigo que hemos escrito.
if(isset($_POST['reiniciarsquid'])){
// Nos conectamos a la base de datos
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("proxy");

$resultado = mysql_query("SELECT * FROM acl where nombre!=''");

if (!copy('/etc/squid3/squid.conf', '/etc/squid3/squid.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/squid3/squid.conf.vacio');  // array con las lineas del fichero

$ftmp = fopen('/etc/squid3/squid.tempo', 'w');  // se crea el fichero y si existe se machaca

$buscar = '#ACL'; // línea a buscar, detrás de la que se va a insertar la nueva línea

// Recorre array de lineas

foreach ($lineas as $linea) {
 
    fwrite($ftmp, $linea);  // escribe la línea


    if (strpos($linea, $buscar) !== false) {   // Si la línea contiene lo que se busca
    while ($fila = mysql_fetch_array($resultado)) {
    $acl = "acl $fila[0] $fila[1] $fila[2]\n";
    $nueva = $acl;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
}
}
fclose($ftmp);  // cierra el fichero
rename('/etc/squid3/squid.tempo', '/etc/squid3/squid.conf');

//MYSQL
// Conectar con la base de datos Mysql para insertar los datos de la tabla activado.acl_activadas
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);

// borramos la tabla y volvemos a insertar los datos.
$consultadel="delete from acl_activadas";
$consulta="insert into acl_activadas select * from proxy.acl where nombre !=''";
$resultado_consultadel=mysql_query($consultadel,$conexion);
$resultado_consulta=mysql_query($consulta,$conexion);

if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n";
}
}
?>
        </table><br>
        <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
        </form></div></div>
<!-- Tabla acl activas -->
        <div class="col-lg-6">
        <div id="tablader">
        <table class="table table-hover">
                <tr>
                <th>ACTIVAS</th>
                </tr>
                <tr>
                <th>NOMBRE</th>
                <th>TIPO</th>
                <th>VALORES</th>                
                </tr>
               <?php
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from acl_activadas where nombre!=''";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[nombre] </td><td> $fila[tipo] </td><td> $fila[valor] </td>";
                echo "</tr>";
            }
                ?>                
        </table>
        </div>
        </div></div></div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="col-lg-12">
        <h2>REGLAS</h2>
        <br>
<!-- Formulario para configurar las reglas que vamos a poner en los http_access -->
        <div id="formulariohttp"><form action="proxy.php" method="post">
            Prioridad (1-99): <input type="text" size="2" name="prioridad">
            ACL1: <select name="acl1">
            <?php
            // nombres ACLS PARA HTTP_ACCESS
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('proxy',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select nombre from acl";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<option name='$fila[nombre]'>$fila[nombre]</option>";
            }
            ?>
            </select>
            ACL2: <select name="acl2">
            <?php
            // nombres ACLS PARA HTTP_ACCESS
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('proxy',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select nombre from acl";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<option name='$fila[nombre]'>$fila[nombre]</option>";
            }
            ?>
            </select>
            ACL3: <select name="acl3">
            <?php
            // nombres ACLS PARA HTTP_ACCESS
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('proxy',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select nombre from acl";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<option name='$fila[nombre]'>$fila[nombre]</option>";
            }
            ?>
            </select>
            ACL4: <select name="acl4">
            <?php
            // nombres ACLS PARA HTTP_ACCESS
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('proxy',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select nombre from acl";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<option name='$fila[nombre]'>$fila[nombre]</option>";
            }
            ?>
            </select>
            ACL5: <select name="acl5">
            <?php
            // nombres ACLS PARA HTTP_ACCESS
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('proxy',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select nombre from acl";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<option name='$fila[nombre]'>$fila[nombre]</option>";
            }
            ?></select> Acción: <select name="accion"><option name="permitir">Permitir</option><option name="denegar">Denegar</option></select>
            <input type="submit" value="añadir" name="añadirhttp"></form></div>
            <br><br>
<div id="tablashttpa">
        <h1>HTTP_ACCESS</h1>
        <br>
        <div class="col-lg-6">
        <div class="tablaizq">
        <form method="post" action="proxy.php">
        <table class="table table-hover">
       <tr>
       <th>Prioridad</th>
       <th>acl1</th>
       <th>acl2</th>
       <th>acl3</th>
       <th>acl4</th>
       <th>acl5</th>
       <th>acción</th>
       <th>Seleccionar</th>
       </tr>
<?php 
if(isset($_POST['añadirhttp'])){
// Conectar con la base de datos Mysql
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('proxy',$conexion);

// Recogemos los datos, para introducirlos en la base de datos
$prioridad=$_POST['prioridad'];
$acl1=$_POST['acl1'];
$acl2=$_POST['acl2'];
$acl3=$_POST['acl3'];
$acl4=$_POST['acl4'];
$acl5=$_POST['acl5'];
$accionmysql=$_POST['accion'];
$consulta="insert into reglas values ('$acl1','$acl2','$acl3','$acl4','$acl5','$accionmysql','$prioridad')";
$resultado_consulta=mysql_query($consulta,$conexion);

if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados";
}
}

?>
<!-- Borrado de registro  -->
<?php
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('proxy',$conexion);            
if (isset($_POST['borrarregistrohttp'])) {
    foreach ($_POST['borrarregistrohttp'] as $id){
    echo $id;
   $consulta="delete from reglas where acl1='$id'";
   $resultado_consulta=mysql_query($consulta,$conexion);
}
if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n";
}
}
?>
<?php
            // TABLA DE http_access
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('proxy',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from reglas";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[prioridad] </td><td> $fila[acl1] </td><td> $fila[acl2] </td><td> $fila[acl3] </td><td> $fila[acl4] </td><td> $fila[acl5] </td><td> $fila[accion] </td><td><input type='checkbox' name='borrarregistrohttp[]' value='$fila[acl1]'></td>";
                echo "</tr>";
            }
?>
        </table><br>
<?php
if(isset($_POST['reiniciarsquid'])){
// Nos conectamos a la base de datos.
mysql_connect("localhost", "root", "root") or
    die("No se pudo conectar: " . mysql_error());
mysql_select_db("proxy");

$resultado = mysql_query("SELECT * FROM reglas where acl1!=''");

$filename = '/etc/squid3/squid.conf';

if (!copy('/etc/squid3/squid.conf', '/etc/squid3/squid.bk')) {
    echo "Error al copiar backup...\n";
    exit(1);    //finalizar el script
}
   
$lineas = file('/etc/squid3/squid.conf');  // array con las lineas del fichero

$ftmp = fopen('/etc/squid3/squid.temp', 'w');  // se crea el fichero y si existe se machaca

$buscar = '#HTTP_ACCESS'; // línea a buscar, detrás de la que se va a insertar la nueva línea

$nueva = $httpaccess;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line

// Recorre array de lineas

foreach ($lineas as $linea) {
 
    fwrite($ftmp, $linea);  // escribe la línea


    if (strpos($linea, $buscar) !== false) {   // Si la línea contiene lo que se busca
    while ($fila = mysql_fetch_array($resultado)) {
        $accion = "$fila[5]";
        if ($accion === "Permitir") {
        $httpaccess = "http_access allow $fila[0] $fila[1] $fila[2] $fila[3] $fila[4]\n";
        }
        else {
        $httpaccess = "http_access deny $fila[0] $fila[1] $fila[2] $fila[3] $fila[4]\n";
        }
    $nueva = $httpaccess;  // nueva línea a insertar. Se le concatena el fin de línea End Of Line
        fwrite($ftmp, $nueva);                   // se añade la nueva línea   
  } 
}
}
fclose($ftmp);  // cierra el fichero
rename('/etc/squid3/squid.temp', '/etc/squid3/squid.conf');
    
$conexion=mysql_connect('localhost','root','root');
mysql_select_db('activado',$conexion);
// httpaccess
$consultadel="delete from httpa_activadas";
$consulta="insert into httpa_activadas select * from proxy.reglas";
$resultado_consultadel=mysql_query($consultadel,$conexion);
$resultado_consulta=mysql_query($consulta,$conexion);

if($resultado_consulta==FALSE){
    echo "Se produjo un error al ingresar los datos. Por favor, revisar los datos ingresados\n";
}   
    
$outPut = shell_exec("sudo service squid3 restart");
echo $outPut;
}          
?> 
        <input type="submit" name="borrar" value="borrar registros seleccionados"><br><br>
        </form></div></div>
<!-- Tabla de las http_access activas -->
    <div class="col-lg-6">
    <div id="tablader">
    <table class="table table-hover">
    <tr>
    <th>ACTIVAS</th>
    </tr>
    <tr>
                <th>Prioridad</th>
                <th>acl1</th>
                <th>acl2</th>                
                <th>acl3</th>                
                <th>acl4</th>                
                <th>acl5</th>                
                <th>acción</th>
    </tr>
               <?php
            // Conectar con la base de datos Mysql
            $conexion=mysql_connect('localhost','root','root');
            mysql_select_db('activado',$conexion);
            // Creamos la consulta a la tabla que queremos
            $consulta="select * from httpa_activadas";
            $resultado=mysql_query($consulta);
            // Volcamos los datos a la tabla.
            while ($fila = mysql_fetch_array($resultado)) {
                echo "<tr>";
                echo "<td> $fila[prioridad] </td><td> $fila[acl1] </td><td> $fila[acl2] </td><td> $fila[acl3] </td><td> $fila[acl4] </td><td> $fila[acl5] </td><td> $fila[accion] </td>";
                echo "</tr>";
            }
                ?>
    </table></div></div>       
    </div></div>

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
