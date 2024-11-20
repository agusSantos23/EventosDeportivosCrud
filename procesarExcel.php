<?php

$host = "localhost";
$user = "root";
$password = "";
$base_datos = "eventos_deportivos";

$conn = new mysqli($host, $user, $password, $base_datos);

if ($conn->connect_error) {
  die("Error de conexión: " . $conn->connect_error);
}


if( fopen("C:\wamp64\www\carpeta\EventosDeportivosCrud\dbExcel.csv","r")) { 
  $file = fopen("C:\wamp64\www\carpeta\EventosDeportivosCrud\dbExcel.csv","r");

  while(($data = fgetcsv($file, 1000, ","))) {

		$evento = $conn->real_escape_string($data[0]);
		$tipoDeporte = $conn->real_escape_string($data[1]);
		$fecha = $conn->real_escape_string($data[2]);
		$hora = $conn->real_escape_string($data[3]);
		$ubicacion = $conn->real_escape_string($data[4]);
		$organizador = $conn->real_escape_string($data[5]);

		$sql = "SELECT id FROM organizadores WHERE nombre = '$organizador'";
		$result = $conn->query($sql);

		if($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$idOrganizador = $row["id"];

			$sql = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador) VALUES ('$evento','$tipoDeporte','$fecha', '$hora','$ubicacion','$idOrganizador')";

			if($conn->query($sql)) {
				echo"Se a guadado en la bd";

			}else{
				echo "Se a producido un error al guardar en la bd:". $conn->error;
			}

		} else {
			echo "No se a encontrado ningun organizador";
		}
  }
} else {

	echo "No se a encontrado ningun archivo";

}