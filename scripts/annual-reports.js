document.addEventListener('DOMContentLoaded', function () {
    const reportSelect = document.getElementById('report-select');
    const reportDetailsBody = document.getElementById('report-details-body');
    const soldProductsBody = document.getElementById('sold-products-body');
    const reportSelectionForm = document.getElementById('report-selection-form');

    // Función para mostrar detalles del informe
    async function showReportDetails(reportId) {
        try {
            if (!reportId) {
                alert('Por favor selecciona un informe válido antes de continuar.');
                return;
            }

            const response = await fetch(`../sql/get_reports_details_annual.php?report_id=${reportId}`);
            const data = await response.json();

            if (data.success) {
                // Limpiar tablas antes de rellenarlas
                reportDetailsBody.innerHTML = '';
                soldProductsBody.innerHTML = '';

                // Agregar detalles del informe
                data.details.forEach(detail => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border p-2">${detail.concept}</td>
                        <td class="border p-2">${detail.value}</td>
                    `;
                    reportDetailsBody.appendChild(row);
                });

                // Agregar productos vendidos
                data.soldProducts.forEach(product => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border p-2">${product.name}</td>
                        <td class="border p-2">${product.quantity}</td>
                    `;
                    soldProductsBody.appendChild(row);
                });
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error al obtener los detalles del informe:', error);
            alert('Hubo un problema al cargar los detalles del informe');
        }
    }

    // Evento de selección del formulario
    reportSelectionForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const selectedReportId = reportSelect.value;
        showReportDetails(selectedReportId);
    });

    // Función para cargar informes en el select
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
            alert('Hubo un problema al cargar los informes');
        }
    }

    // Cargar informes al cargar la página
    loadMonthlyReports();
});