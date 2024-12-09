document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('salesForm');
    const makeAccountsButton = document.getElementById('makeAccounts');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('../sql/register_sale.php', {  // Ajusta esta ruta según tu estructura de directorios
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Venta registrada exitosamente');
                form.reset();
            } else {
                alert('Error al registrar la venta: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al procesar la solicitud');
        });
    });

    makeAccountsButton.addEventListener('click', function() {
        window.location.href = '../pages/contabilidad.html';
    });
    

  // Sales data storage
  let salesData = JSON.parse(localStorage.getItem('salesData')) || [];

  // Sales form submission
  document.getElementById('salesForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const nameProduct = document.getElementById('nameProduct').value;
      const productCode = document.getElementById('productCode').value;
      const quantity = parseFloat(document.getElementById('quantity').value);
      const salePrice = parseFloat(document.getElementById('salePrice').value);
      
      const saleEntry = {
          nameProduct,
          productCode,
          quantity,
          salePrice,
          total: quantity * salePrice,
          date: new Date().toISOString()
      };
      
      salesData.push(saleEntry);
      localStorage.setItem('salesData', JSON.stringify(salesData));
      
      // Reset form
      this.reset();
  });
});
