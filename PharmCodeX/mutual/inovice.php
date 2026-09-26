<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Sales - Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .invoice-box { border: 1px solid #ccc; padding: 20px; border-radius: 10px; }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4">Pharmacy Sales - Create Invoice</h2>
    <div class="invoice-box">
        <form id="invoiceForm">
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Customer Name:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="customerName" required>
                </div>
                <label class="col-sm-2 col-form-label">Date:</label>
                <div class="col-sm-4">
                    <input type="date" class="form-control" id="invoiceDate" required>
                </div>
            </div>

            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Medicine</th>
                        <th>Qty</th>
                        <th>Price (per unit)</th>
                        <th>Discount (%)</th>
                        <th>Tax (%)</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="invoiceItems">
                    <tr>
                        <td><input type="text" class="form-control medicine" required></td>
                        <td><input type="number" class="form-control qty" value="1" min="1" required></td>
                        <td><input type="number" class="form-control price" value="0" min="0" required></td>
                        <td><input type="number" class="form-control discount" value="0" min="0"></td>
                        <td><input type="number" class="form-control tax" value="0" min="0"></td>
                        <td class="total">0</td>
                        <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-primary mb-3" id="addRow">Add Medicine</button>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Grand Total:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="grandTotal" readonly>
                </div>
                <div class="col-sm-6 text-end">
                    <button type="button" class="btn btn-success" onclick="printInvoice()">Print Invoice</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateRow(row) {
        let qty = parseFloat(row.querySelector('.qty').value) || 0;
        let price = parseFloat(row.querySelector('.price').value) || 0;
        let discount = parseFloat(row.querySelector('.discount').value) || 0;
        let tax = parseFloat(row.querySelector('.tax').value) || 0;

        let total = qty * price;
        total -= (total * discount / 100);
        total += (total * tax / 100);

        row.querySelector('.total').innerText = total.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let totals = document.querySelectorAll('.total');
        let grandTotal = 0;
        totals.forEach(td => grandTotal += parseFloat(td.innerText) || 0);
        document.getElementById('grandTotal').value = grandTotal.toFixed(2);
    }

    document.getElementById('invoiceItems').addEventListener('input', function(e) {
        if(e.target.closest('tr')) calculateRow(e.target.closest('tr'));
    });

    document.getElementById('addRow').addEventListener('click', function() {
        let tbody = document.getElementById('invoiceItems');
        let row = tbody.rows[0].cloneNode(true);
        row.querySelectorAll('input').forEach(input => input.value = input.classList.contains('qty') ? '1' : '0');
        row.querySelector('.medicine').value = '';
        row.querySelector('.total').innerText = '0';
        tbody.appendChild(row);
    });

    document.getElementById('invoiceItems').addEventListener('click', function(e) {
        if(e.target.classList.contains('removeRow')) {
            if(document.querySelectorAll('#invoiceItems tr').length > 1) {
                e.target.closest('tr').remove();
                calculateGrandTotal();
            } else alert('At least one medicine required.');
        }
    });

    function printInvoice() {
        let content = document.querySelector('.invoice-box').innerHTML;
        let myWindow = window.open('', '', 'width=800,height=600');
        myWindow.document.write('<html><head><title>Invoice</title>');
        myWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">');
        myWindow.document.write('</head><body>');
        myWindow.document.write(content);
        myWindow.document.write('</body></html>');
        myWindow.document.close();
        myWindow.print();
    }
</script>

<?php include("../includes/footer.php"); ?>
</body>
</html>
