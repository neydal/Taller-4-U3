<?php
// === CORS: Permite acceso desde la app móvil ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// === Tipo de respuesta ===
header('Content-Type: application/json');

include 'db.php';

$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        $resultado = $conexion->query("SELECT * FROM libros");
        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        echo json_encode($datos);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $titulo = $conexion->real_escape_string($data['titulo'] ?? '');
        $autor = $conexion->real_escape_string($data['autor'] ?? '');
        $anio = $data['anio'] ?? '';
        $categoria = $conexion->real_escape_string($data['categoria'] ?? '');
        $imagen = $data['imagen'] ?? null; // URL de ImgBB

        $sql = "INSERT INTO libros (titulo, autor, anio, categoria, imagen) 
                VALUES ('$titulo', '$autor', '$anio', '$categoria', '$imagen')";
        
        if ($conexion->query($sql)) {
            echo json_encode([
                "mensaje" => "Libro agregado correctamente",
                "id" => $conexion->insert_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al agregar libro"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;
        $titulo = $conexion->real_escape_string($data['titulo'] ?? '');
        $autor = $conexion->real_escape_string($data['autor'] ?? '');
        $anio = $data['anio'] ?? '';
        $categoria = $conexion->real_escape_string($data['categoria'] ?? '');
        $imagen = $data['imagen'] ?? null;

        $sql = "UPDATE libros SET 
                titulo='$titulo', 
                autor='$autor', 
                anio='$anio', 
                categoria='$categoria', 
                imagen='$imagen' 
                WHERE id=$id";
        
        if ($conexion->query($sql)) {
            echo json_encode(["mensaje" => "Libro actualizado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al actualizar"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? 0;

        $sql = "DELETE FROM libros WHERE id=$id";
        if ($conexion->query($sql)) {
            echo json_encode(["mensaje" => "Libro eliminado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>