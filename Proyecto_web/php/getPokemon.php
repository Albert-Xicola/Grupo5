<?php
//conexion a la base de datos
$link = mysqli_connect('localhost','grupo5-','grupo5-','pokewebapp');

if($link == false){
    die("ERROR: Could not connect ".mysqli_connect_error());
}

$pokemonId = $_POST['pokemonId'];

// Query para obtener los datos del pokemon
$sql = "SELECT p.id, p.img_id,p.especie,p.nombre,p.peso,p.altura,p.baxp FROM pokemon p WHERE p.id ='$pokemonId'";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0){
    while ($row = $result->fetch_assoc())
    {
        echo json_encode($row);
    }
}else{
	echo json_encode(array('error' => 'No se encontró el pokemon'));
}
mysqli_close($link);
?>
