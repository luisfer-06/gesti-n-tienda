<?php
// Configuración de conexión
$servername = "localhost";
$username = "root";
$password = "";
$database = "sistema_inventario";

try {
    // Crear conexión con PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Leer datos enviados desde el cliente
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('No se recibieron datos válidos.');
    }

    // Validar datos
    if (
        empty($input['nombre_cuenta']) || 
        empty($input['tipo_cuenta']) || 
        !is_numeric($input['total_ventas']) || 
        !is_numeric($input['total_gastos']) || 
        !is_numeric($input['ganancia_neta'])
    ) {
        throw new Exception('Datos inválidos enviados al servidor.');
    }

    // Iniciar transacción
    $pdo->beginTransaction();

    // Insertar cuenta
    $stmt = $pdo->prepare('
        INSERT INTO cuentas 
        (nombre_cuenta, tipo_cuenta, total_ventas, total_gastos, ganancia_neta) 
        VALUES 
        (:nombre, :tipo, :total_ventas, :total_gastos, :ganancia_neta)
    ');

    $stmt->execute([
        ':nombre' => $input['nombre_cuenta'],
        ':tipo' => $input['tipo_cuenta'],
        ':total_ventas' => $input['total_ventas'],
        ':total_gastos' => $input['total_gastos'],
        ':ganancia_neta' => $input['ganancia_neta']
    ]);

    // Confirmar transacción
    $pdo->commit();

    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Cuenta guardada exitosamente'
    ]);
} catch (Exception $e) {
    // Deshacer cambios en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Log de error
    error_log('Error al guardar la cuenta: ' . $e->getMessage());
    error_log('Datos enviados: ' . json_encode($input));

    // Respuesta de error
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar la cuenta: ' . $e->getMessage()
    ]);
}
?>
