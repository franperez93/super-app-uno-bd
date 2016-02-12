<?php

$datosconexion = array(
	"servername" => "40.115.25.86", 		// PON AQUI LA IP DE TU SERVIDOR
	"username" => "root", 			// PON AQUI EL USUARIO DE LA BD
	"password" => "", 			// PON AQUI LA CONTRASEÑA DE LA BD
	"dbname" => "log"
	);

/*

Para crear la BD en tu servidor:

CREATE SCHEMA `log` DEFAULT CHARACTER SET utf16 ;

CREATE TABLE `log`.`logs` (
  `idlog` INT NOT NULL AUTO_INCREMENT,
  `date` DATETIME NOT NULL,
  `logtext` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idlog`));
*/

setImageHeaders();
$icono = chooseRandomPic('icons');
grabarLog($icono); //Si falla el log no mostraremos el icono
returnImageData($icono);

function chooseRandomPic($dir = '.'){
    $files = glob($dir . '/*.*');
    $file = array_rand($files);
    return $files[$file];
}

function setImageHeaders(){
	header('Content-Type: image/png');
}

function returnImageData($image){
    readfile($image);
}

function grabarLog($nombreicono){
	global $datosconexion;
	$conn = conectaBD($datosconexion);
	$error = insertaRegistro($conn,$nombreicono);
	if($error!=""){
		die("No se ha podido insertar en el log: "+$error);
	}
	$conn = null;
	// La conexion se cierra sola cuando la variable conn muere
}

function insertaRegistro($conn,$texto){
	$sentence = 'INSERT INTO logs (date,logtext) VALUES (:date,:text)';

	try{
		$statement = $conn->prepare($sentence);
		$statement->bindValue(':date',date("Y-m-d H:i:s"));
		$statement->bindValue(':text',$texto);
		$statement->execute();
	}catch ( PDOException $exception ){
		return "PDO error :" . $exception->getMessage();
	}
	return "";
}

function conectaBD($datosconexion){
	try{
		$bd = obtenConexionBD($datosconexion);
	}catch(PDOException $e){
		die("Connection failed: " . $e->getMessage());
	}
	return $bd;
}

function obtenConexionBD($datosconexion){
    $conn = new PDO(obtenCadenaConexionBD($datosconexion), $datosconexion["username"], $datosconexion["password"]);
    // set the PDO error mode to exception, para poder hacer try/catch
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;   
}

function obtenCadenaConexionBD($datosconexion){
	return "mysql:host=".$datosconexion["servername"].";dbname=".$datosconexion["dbname"];
}

?>



