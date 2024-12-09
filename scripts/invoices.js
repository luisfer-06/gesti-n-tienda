document.addEventListener('DOMContentLoaded', function () {
    fetchInvoices();
});

function fetchInvoices() {
    fetch('../sql/get_invoices.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayInvoices(data.invoices);
            } else {
                alert('Error al obtener las facturas: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al procesar la solicitud');
        });
}

function displayInvoices(invoices) {
    const tableBody = document.getElementById('invoicesTableBody');
    tableBody.innerHTML = '';

    invoices.forEach(invoice => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">${invoice.product_name}</td>
            <td class="px-6 py-4 whitespace-nowrap">${invoice.quantity.toLocaleString('es-ES')}</td>
            <td class="px-6 py-4 whitespace-nowrap">$${parseFloat(invoice.unit_price).toLocaleString('es-ES', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</td>
            <td class="px-6 py-4 whitespace-nowrap">$${(invoice.quantity * invoice.unit_price).toLocaleString('es-ES', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</td>
            <td class="px-6 py-4 whitespace-nowrap">${new Date(invoice.sale_date).toLocaleString('es-ES')}</td>
        `;
        tableBody.appendChild(row);
    });
}
