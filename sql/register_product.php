<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_inventario";

$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    $response = [
        'success' => false,
        'message' => 'Error de conexión: ' . $conn->connect_error
    ];
    echo json_encode($response);
    exit;
}

$validAdminCode = "000";

// Procesar el formulario cuando se envíe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminCode = $_POST["adminCode"];
    $productCode = $_POST["productCode"];
    $productName = $_POST["productName"];
    $quantity = $_POST["quantity"];
    $purchasePrice = $_POST["purchasePrice"];

    // Validar código de administrador
    if (empty($adminCode) || $adminCode !== $validAdminCode) {
        $response = [
            'success' => false,
            'message' => 'Código de administrador inválido.'
        ];
        echo json_encode($response);
        exit;
    }

    // Insertar el nuevo producto en la tabla de productos
    $insertSQL = "INSERT INTO productos (codigo_producto, nombre_producto, precio_compra, cantidad) VALUES (?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSQL);
    $insertStmt->bind_param("ssdi", $productCode, $productName, $purchasePrice, $quantity);

    if ($insertStmt->execute()) {
        $response = [
            'success' => true,
            'message' => 'Producto registrado exitosamente.'
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Error al registrar el producto: ' . $insertStmt->error
        ];
    }

    echo json_encode($response);

    $insertStmt->close();
}

$conn->close();
?>