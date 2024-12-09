<?php
header('Content-Type: application/json');  // Asegura respuesta JSON

$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_inventario";

$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode([
        'success' => false, 
        'message' => "Error de conexión: " . $conn->connect_error
    ]));
}

// Si se envía el formulario de venta
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $nameProduct = $_POST["nameProduct"];  // Corregido
        $productCode = $_POST["productCode"];
        $quantity = intval($_POST["quantity"]);
        $salePrice = floatval($_POST["salePrice"]);

        // Obtener información del producto
        $sql = "SELECT nombre_producto, codigo_producto, cantidad FROM productos WHERE codigo_producto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $productCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            // Verificar si hay suficiente cantidad
            if ($product["cantidad"] < $quantity) {
                throw new Exception("Cantidad insuficiente en inventario");
            }

            // Iniciar transacción
            $conn->begin_transaction();

            // Actualizar cantidad de producto
            $newQuantity = $product["cantidad"] - $quantity;
            $updateSQL = "UPDATE productos SET cantidad = ? WHERE codigo_producto = ?";
            $updateStmt = $conn->prepare($updateSQL);
            $updateStmt->bind_param("is", $newQuantity, $productCode);
            $updateStmt->execute();

            // Registrar venta
            $insertSQL = "INSERT INTO ventas (nombre_producto, codigo_producto, cantidad, precio_venta, fecha_venta) 
                          VALUES (?, ?, ?, ?, NOW())";
            $insertStmt = $conn->prepare($insertSQL);
            $insertStmt->bind_param("ssid", $nameProduct, $productCode, $quantity, $salePrice);  // Corregido

            if ($insertStmt->execute()) {
                // Confirmar transacción
                $conn->commit();
                echo json_encode([
                    'success' => true, 
                    'message' => 'Venta registrada exitosamente'
                ]);
            } else {
                // Revertir transacción en caso de error
                $conn->rollback();
                throw new Exception("Error al registrar la venta");
            }
        } else {
            throw new Exception("Producto no encontrado");
        }
    } catch (Exception $e) {
        // Asegurarse de hacer rollback en caso de excepción
        if ($conn->in_transaction()) {
            $conn->rollback();
        }
        echo json_encode([
            'success' => false, 
            'message' => $e->getMessage()
        ]);
    }
    exit;  // Terminar ejecución después de la respuesta POST
}

$conn->close();
?>