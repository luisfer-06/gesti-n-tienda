document.addEventListener('DOMContentLoaded', function() {
    // Expense categories
    const expenseCategories = [
        'Arriendo', 'Celular', 'Fletes', 'Refrigerios', 'Aseo', 
        'Alcira', 'ElementosDeAseo', 'Agua', 'Luz', 'Internet', 
        'Lavandería', 'ArreglosRopa', 'ArreglosDonnaAna', 
        'Transportes', 'Almuerzos', 'OtroGasto1', 'OtroGasto2', 'OtroGasto3'
    ];

    // Dynamically generate expense inputs
    const expensesContainer = document.getElementById('expensesContainer');
    expenseCategories.forEach(category => {
        const expenseRow = document.createElement('div');
        expenseRow.classList.add('expense-row');
        expenseRow.innerHTML = `
            <label>${category}:</label>
            <input type="number" 
                   class="expense-input" 
                   id="expense-${category}" 
                   placeholder="0.00" 
                   step="000.1">
        `;
        expensesContainer.appendChild(expenseRow);
    });

    // Fetch and display sales data
    function loadSalesData() {
        fetch('../sql/get_account.php')
            .then(response => response.json())
            .then(sales => {
                const tableBody = document.getElementById('salesTableBody');
                tableBody.innerHTML = ''; // Clear existing rows

                let totalSales = 0;
                sales.forEach(sale => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${sale.codigo_producto}</td>
                        <td>${sale.cantidad}</td>
                        <td>$${parseFloat(sale.precio_venta).toFixed(0)}</td>
                    `;
                    tableBody.appendChild(row);
                    
                    // Calculate total sales
                    totalSales += parseFloat(sale.precio_venta);
                });

                // Update total sales
                document.getElementById('totalSales').textContent = `$${totalSales.toFixed(0)}`;
                updateNetProfit();
            })
            .catch(error => {
                console.error('Error loading sales:', error);
            });
    }

    // Calcular y actualizar gastos totales
    function calculateTotalExpenses() {
        let totalExpenses = 0;
        expenseCategories.forEach(category => {
            const expenseInput = document.getElementById(`expense-${category}`);
            const expenseValue = parseFloat(expenseInput.value) || 0;
            totalExpenses += expenseValue;
        });
        
        document.getElementById('totalExpenses').textContent = `$${totalExpenses.toFixed(0)}`;
        return totalExpenses;
    }

    // Actualizar beneficio neto
    function updateNetProfit() {
        const totalSales = parseFloat(document.getElementById('totalSales').textContent.replace('$', ''));
        const totalExpenses = calculateTotalExpenses();
        const netProfit = totalSales - totalExpenses;
        
        const netProfitElement = document.getElementById('netProfit');
        netProfitElement.textContent = `$${netProfit.toFixed(0)}`;
        
        // código de color beneficio neto
        netProfitElement.classList.remove('total-sales', 'total-expenses');
        netProfitElement.classList.add(netProfit >= 0 ? 'total-sales' : 'total-expenses');
    }

    // Agregar detectores de eventos a las entradas de gastos
    expenseCategories.forEach(category => {
        const expenseInput = document.getElementById(`expense-${category}`);
        expenseInput.addEventListener('input', updateNetProfit);
    });

    // Finalizar el controlador del botón de cuenta
    const finishAccountBtn = document.getElementById('finishAccountBtn');
    finishAccountBtn.addEventListener('click', function() {
        const accountName = prompt('Ingrese el nombre de la cuenta:');
        const accountType = confirm('¿Es una cuenta mensual? (Cancelar para cuenta anual)') ? 'mensual' : 'anual';

        if (accountName) {
            const totalSales = parseFloat(document.getElementById('totalSales').textContent.replace('$', ''));
            const totalExpenses = calculateTotalExpenses();
            const netProfit = totalSales - totalExpenses;

            //Preparar datos de gastos
            const expenses = {};
            expenseCategories.forEach(category => {
                const expenseInput = document.getElementById(`expense-${category}`);
                expenses[category] = parseFloat(expenseInput.value) || 0;
            });

            //Enviar datos al servidor
            fetch('../sql/save_account.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nombre_cuenta: accountName,
                    tipo_cuenta: accountType,
                    total_ventas: totalSales,
                    total_gastos: totalExpenses,
                    ganancia_neta: netProfit
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cuenta guardada exitosamente');
                    window.location.href = '../pages/index.html'
                } else {
                    alert('Error al guardar la cuenta: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la solicitud');
            });
        }
    });

    // Initial load of sales data
    loadSalesData();
});