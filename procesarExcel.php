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
	$contador = 0;
	$contadorExito = 0;
	$errores = [];

  while(($data = fgetcsv($file, 1000, ","))) {
		$filaErrores = [];
		$contador ++;

		$evento = $conn->real_escape_string($data[0]);
		$tipoDeporte = $conn->real_escape_string($data[1]);
		$fecha = $conn->real_escape_string($data[2]);
		$hora = $conn->real_escape_string($data[3]);
		$ubicacion = $conn->real_escape_string($data[4]);
		$organizador = $conn->real_escape_string($data[5]);

		if (empty($evento)) { 
			$filaErrores['evento'][] = 'Es obligatorio el campo del Evento'; 
		} else if (strlen($evento) <= 3 && strlen($evento) >= 15) {
			$filaErrores['evento'][] = 'Tiene que ser entre 3 y 15 caracteres'; 
		}

		if (empty($tipoDeporte)) { 
			$filaErrores['tipo_deporte'][] = 'Es obligatorio el campo del Tipo de Deporte'; 
		} else if (!in_array($tipoDeporte, ['futbol', 'baloncesto', 'tenis'])) { 
			$filaErrores['tipo_deporte'][] = 'Solo se permite los valores futbol, baloncesto, tenis'; 
		}

		if (empty($fecha)) { 
			$filaErrores['fecha'][] = 'La fecha es un campo requerido'; 
		} else if (!preg_match("/\d{2}-\d{2}-\d{4}/", $fecha)) { 
			$filaErrores['fecha'][] = 'El formato de la fecha no es valido, DD-MM-YYYY'; 
		}

		if (empty($hora)) { 
			$filaErrores['hora'][] = 'La hora es requerida'; 
		} else if (!preg_match("/\d{2}:\d{2}/", $hora)) { 
			$filaErrores['hora'][] = 'El formato de la hora no es valida'; 
		}

		if (empty($ubicacion)) { 
			$filaErrores['ubicacion'][] = 'La Ubicacion es requerida'; 
		} else if (strlen($ubicacion) > 255) { 
			$filaErrores['ubicacion'][] = 'La Ubicacion no puede superar los 255 caracteres'; 
		} 
		
		if (empty($organizador)) { 
			$filaErrores['organizador'][] = 'error_Organizador'; 
		} 

		if(!empty($filaErrores)){
			$errores[] = $filaErrores;
			continue;
		}


		$sql = "SELECT id FROM organizadores WHERE nombre = '$organizador'";
		$result = $conn->query($sql);

		if($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$idOrganizador = $row["id"];

			$sql = "INSERT INTO eventos (nombre_evento, tipo_deporte, fecha, hora, ubicacion, id_organizador) VALUES ('$evento','$tipoDeporte','$fecha', '$hora','$ubicacion','$idOrganizador')";

			if($conn->query($sql)) {
				$contadorExito ++;

				echo"Se a guadado en la bd";

			}else{
				echo "Se a producido un error al guardar en la bd:". $conn->error;
			}

		} else {
			echo "No se a encontrado ningun organizador";
		}
  }

	echo "Valores procesados: $contador .\n";
	echo "Valores procesados con exito: $contadorExito .\n";
	echo "Valores no procesados con exito:" . count($errores) .".\n";

	foreach ($errores as $index => $filaErrores) { 
		$erroresFila = []; 
		foreach ($filaErrores as $campo => $contenido) { 
			foreach ($contenido as $error) { $erroresFila[] = "$campo: $error"; }
		} 
		echo $index . ": " . implode("; ", $erroresFila) . ".\n";
	}


} else {

	echo "No se a encontrado ningun archivo";

}