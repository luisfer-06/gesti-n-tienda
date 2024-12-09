<?php
try {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "sistema_inventario";

    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $reportId = $_GET['report_id'] ?? null;

    if (!$reportId || !is_numeric($reportId)) {
        throw new Exception("ID de informe inválido.");
    }

    // Validar existencia del informe y que sea una cuenta mensual
    $stmtValidate = $pdo->prepare("SELECT * FROM cuentas 
                                   WHERE id = :report_id AND tipo_cuenta = 'mensual'");
    $stmtValidate->execute([':report_id' => $reportId]);
    $reportDetails = $stmtValidate->fetch(PDO::FETCH_ASSOC);

    if (!$reportDetails) {
        throw new Exception("Informe no encontrado o no es una cuenta mensual.");
    }

    // Obtener productos vendidos
    $stmtProducts = $pdo->prepare("
    SELECT 
        p.nombre_producto AS name,
        SUM(v.cantidad) AS quantity
    FROM ventas v
    INNER JOIN productos p ON v.codigo_producto = p.codigo_producto
    WHERE v.fecha_venta BETWEEN :fecha_inicio AND :fecha_fin
    GROUP BY p.codigo_producto, p.nombre_producto
    ");

    $stmtProducts->execute([
    ':fecha_inicio' => $reportDetails['fecha_creacion'],
    ':fecha_fin' => date('Y-m-d H:i:s')
    ]);

    $soldProducts = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
    'success' => true,
    'reportDetails' => [
        ['concept' => 'Total Ventas', 'value' => $reportDetails['total_ventas']],
        ['concept' => 'Total Gastos', 'value' => $reportDetails['total_gastos']],
        ['concept' => 'Ganancia Neta', 'value' => $reportDetails['ganancia_neta']]
    ],
    'soldProducts' => $soldProducts
    ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
?>