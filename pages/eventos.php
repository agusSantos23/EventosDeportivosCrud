<?php
  include "../procesar.php";

  $errors = isset($_SESSION['errors']) ? $_SESSION['errors']: null ;
  unset($_SESSION['errors']);

	$nume = isset($_GET['nume']) ? $_GET['nume'] : 1;
	$nume = $nume == "" ? 1 : $nume;
	$registrosPermitidos = 5;
	$inicio = ($nume - 1) * $registrosPermitidos;

	$todosEventos = count(get("eventos")); 
	$paginas = ceil($todosEventos / $registrosPermitidos);

	if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
    $nombreBuscado = isset($_GET['nombreBuscado']) ? $_GET['nombreBuscado'] : null; 
    $col = isset($_GET['col']) ? $_GET['col'] : null; 
    $ordenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : ""; 
    $eventosEncontrados = get("eventos", $nombreBuscado, $col, $ordenar, $nume, $registrosPermitidos); 
	}


	$isEdit = isset($_GET['id']);
	$eventData = null;

	if ($isEdit) {
		$eventId = $_GET['id'];
		$events = get("eventos");
		foreach ($events as $event) {
			if ($event['id'] == $eventId) {
				$eventData = $event;
				break;
			}
		}
	}

	function generatePaginationUrl($page) {
    $queryParams = $_GET;
    $queryParams['nume'] = $page;
    return $_SERVER['PHP_SELF'] . '?' . http_build_query($queryParams);
	}


	function generateUrl($baseUrl, $columnName) { 
    $queryParams = $_GET;
    $queryParams['col'] = $columnName;
    $queryParams['ordenar'] = (isset($_GET['col']) && $_GET['col'] === $columnName && $_GET['ordenar'] === 'ASC') ? 'DESC' : 'ASC';
    
    // Asegúrate de que el parámetro 'nume' no se duplica
    $queryParams['nume'] = isset($queryParams['nume']) ? (int)$queryParams['nume'] : 1;

    return $baseUrl . '?' . http_build_query($queryParams);
}




	function generarEmoji($columnName) { 
		$col = isset($_GET['col']) ? $_GET['col'] : ''; 
		$ordenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : '';
		if ($col === $columnName) { 
			return $ordenar === 'ASC' ? '⬇️' : '⬆️'; 
		} 
		return ''; 
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Eventos</title>
	<link rel="stylesheet" href="../styles/crudStyle.css">
</head>
<body>
	<header>
		<h1>Eventos</h1>
	</header>
	<main>
		<div>
			<div>
				<form method="GET" id="Buscador">
					<input type="text" name="nombreBuscado" id="nombreBuscado" placeholder="Buscar por Nombre">
					<button type="submit">Buscar</button>
				</form>
				<table>
					<thead>
						<th>
							<a href="<?php echo generateUrl('eventos.php', 'id'); ?>">Id <?php echo generarEmoji('id'); ?></a>
						</th>

						<th> 
							<a href="<?php echo generateUrl('eventos.php', 'nombre_evento'); ?>">Nombre Evento <?php echo generarEmoji('nombre_evento'); ?></a>
						</th> 
								
						<th> 
							<a href="<?php echo generateUrl('eventos.php', 'tipo_deporte'); ?>">Tipo Deporte <?php echo generarEmoji('tipo_deporte'); ?></a>
						</th> 
									
						<th> 
							<a href="<?php echo generateUrl('eventos.php', 'fecha'); ?>">Fecha <?php echo generarEmoji('fecha'); ?></a>
						</th>
						
						<th> 
							<a href="<?php echo generateUrl('eventos.php', 'hora'); ?>">Hora <?php echo generarEmoji('hora'); ?></a>
						</th> 
							
						<th> 
							<a href="<?php echo generateUrl('eventos.php', 'ubicacion'); ?>">Ubicación <?php echo generarEmoji('ubicacion'); ?></a>
						</th> 
							
						<th> 
							<a href="<?php echo generateUrl('eventos.php', 'id_organizador'); ?>">Organizador <?php echo generarEmoji('id_organizador'); ?></a>
						</th>
					</thead>

					<tbody>
						<?php 
							if (!empty($eventosEncontrados)) { 
								foreach ($eventosEncontrados as $event): 
						?> 
							<tr> 
								<td><?php echo $event['id']; ?></td> 
								<td><?php echo $event['nombre_evento']; ?></td> 
								<td><?php echo $event['tipo_deporte']; ?></td> 
								<td><?php echo $event['fecha']; ?></td> 
								<td><?php echo $event['hora']; ?></td> 
								<td><?php echo $event['ubicacion']; ?></td> 
								<td><?php echo $event['id_organizador']; ?></td> 
								<td> 
									<a href='eventos.php?id=<?php echo $event['id']; ?>'>Editar</a> 
									<form action='../procesar.php' method='POST'> 
										<input type='hidden' name='accion' value='DELTeventos'> 
										<input type='hidden' name='id' value='<?php echo $event['id']; ?>'> 
										<button type='submit' onclick='return confirm("¿Estás seguro de que deseas eliminar este evento?")'>Eliminar</button> 
									</form> 
								</td> 
							</tr> 
						<?php 
								endforeach; 
							} else {
							echo "<tr><td colspan='8'>No se ha encontrado ningún evento</td></tr>"; 
							} 
						?>
					</tbody>
				</table>

			</div>
			
			<div id="paginacion">
				
				<?php for ($i = 1; $i <= $paginas; $i++): ?> 
					<a href="<?php echo generatePaginationUrl($i); ?>"><?php echo $i; ?></a>
				<?php endfor; ?>
				
			</div>
		</div>

		<form action="../procesar.php" method="post">
			
			<?php if ($errors == true): ?>
        <ul id="errorList">
          <?php 
            foreach ($errors as $error) {
              echo "<li>$error</li>";
            }
          ?>
        </ul>
      <?php endif; ?>

			<div>
				<h2><span id="action"><?php echo $isEdit ? "Editar" : "Crear"; ?></span> Evento</h2>

				<input type="hidden" name="accion" value="<?php echo $isEdit ? 'UPDAeventos' : 'POSTeventos'; ?>">

				<?php echo $isEdit ? '<input type="hidden" name="id" value="' . $eventData['id'] . '">' : ''; ?>

				<input type="text" name="nombre_evento" id="nombre_evento" placeholder="Nombre del Evento" value="<?php echo $isEdit ? $eventData['nombre_evento'] : ''; ?>">
				<input type="text" name="deporte" id="deporte" placeholder="Deporte" value="<?php echo $isEdit ? $eventData['tipo_deporte'] : ''; ?>">
				
				<div>
					<input type="datetime-local" name="fecha" id="fecha" value="<?php echo $isEdit ? $eventData['fecha'] . 'T' . $eventData['hora'] : ''; ?>">

					<select name="idOrganizador" id="idOrganizador">
						<option selected disabled>Selecciona un Organizador</option>
						<?php
							$managers = get("organizadores");
							foreach ($managers as $manager) {
								$selected = ($isEdit && $eventData['id_organizador'] == $manager['id']) ? 'selected' : '';
								echo "<option value='{$manager['id']}' $selected>{$manager['nombre']}</option>";
							}
						?>
					</select>
				</div>

				<input type="text" name="ubicacion" id="ubicacion" placeholder="Ubicacion" value="<?php echo $isEdit ? $eventData['ubicacion'] : ''; ?>">
		</div>

			<button type="submit"><?php echo $isEdit ? "Actualizar" : "Crear"; ?></button>
		</form>
	</main>
</body>

</html>