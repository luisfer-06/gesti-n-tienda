<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "sistema_inventario");

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener datos del JSON enviado
$data = json_decode(file_get_contents("php://input"), true);
$code = $data['code'];
$quantity = $data['quantity'];

// Verificar si el producto existe
$sql = "SELECT cantidad FROM productos WHERE codigo_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Actualizar la cantidad
    $row = $result->fetch_assoc();
    $newQuantity = $row['cantidad'] + $quantity;

    $updateSql = "UPDATE productos SET cantidad = ? WHERE codigo_producto = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("is", $newQuantity, $code);

    if ($updateStmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }

    $updateStmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Producto no encontrado"]);
}

$stmt->close();
$conn->close();
?>
