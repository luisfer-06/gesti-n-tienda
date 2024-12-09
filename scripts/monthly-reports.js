document.addEventListener('DOMContentLoaded', function () {
    const reportSelect = document.getElementById('report-select');
    const reportDetailsBody = document.getElementById('report-details-body');
    const soldProductsBody = document.getElementById('sold-products-body');
    const reportSelectionForm = document.getElementById('report-selection-form');

    // Cargar informes desde la base de datos
    async function loadMonthlyReports() {
        try {
            const response = await fetch('../sql/get_reports_annuals.php');
            const data = await response.json();

            if (data.success) {
                data.reports.forEach(report => {
                    const option = document.createElement('option');
                    option.value = report.id;
                    option.textContent = `${report.name} (${report.month} ${report.year})`;
                    reportSelect.appendChild(option);
                });
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error al cargar informes:', error);
        }
    }

    // Mostrar detalles del informe
    async function showReportDetails(reportId) {
        try {
            const response = await fetch(`../sql/get_report_details_annuals.php?report_id=${reportId}`);
            const data = await response.json();

            if (data.success) {
                reportDetailsBody.innerHTML = '';
                soldProductsBody.innerHTML = '';

                // Llenar tabla de detalles
                data.reportDetails.forEach(detail => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border p-2">${detail.concept}</td>
                        <td class="border p-2 text-right">${detail.value}</td>
                    `;
                    reportDetailsBody.appendChild(row);
                });

                // Llenar tabla de productos vendidos
                data.soldProducts.forEach(product => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border p-2">${product.name}</td>
                        <td class="border p-2 text-right">${product.quantity}</td>
                    `;
                    soldProductsBody.appendChild(row);
                });
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error al mostrar detalles del informe:', error);
        }
    }

    // Evento de selección
    reportSelectionForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const selectedReportId = reportSelect.value;
        if (selectedReportId) {
            showReportDetails(selectedReportId);
        }
    });

    // Cargar informes al iniciar
    loadMonthlyReports();
});
