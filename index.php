<?php
include('include/connect.php');

// Check database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch invoices data
$sql = "SELECT * FROM invoice_details";
$result = $con->query($sql);

// Initialize totals
$totalReceived = 0;
$totalNotReceived = 0;
$totalAmount = 0;

$invoices = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Check if payment is received or not
            if ($row['payment_received'] == 0) {
                $totalNotReceived += $row['total'];
            } else {
                $totalReceived += $row['total'];
            }
            $invoices[] = $row;
        }
        $totalAmount = $totalReceived + $totalNotReceived;
    }
} else {
    echo "Error: " . $con->error;
}

if (isset($_POST['delete_invoice_id'])) {
    $delete_id = $_POST['delete_invoice_id'];
    $delete_query = "DELETE FROM invoice_details WHERE id = '$delete_id'";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Invoice deleted successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}

if (isset($_POST['invoice_id']) && isset($_POST['payment_status'])) {
    $invoice_id = $_POST['invoice_id'];
    $payment_status = $_POST['payment_status'];

    // Update payment status in the database
    $update_query = "UPDATE invoice_details SET payment_received = ? WHERE id = ?";
    $stmt = $con->prepare($update_query);
    $stmt->bind_param('ii', $payment_status, $invoice_id);

    if ($stmt->execute()) {
        echo "<script>alert('Payment status updated successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error: " . $con->error . "');</script>";
    }

    $stmt->close();
}

if (isset($_POST['delete_all'])) {
    $delete_query = "DELETE FROM invoice_details";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('All invoices deleted successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}

$serial_num = 1;
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice History</title>
    <link href="./output.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
        }

        .dashboard-header {
            background-color: #ff6b6b;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-header ul {
            display: flex;
            gap: 20px;
        }

        .dashboard-header a {
            color: white;
            font-weight: bold;
        }

        .form-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            margin-left: 12px;
            margin-right: 12px;
        }

        .form-heading {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .separator {
            height: 2px;
            background-color: #ccc;
            margin: 20px 0;
        }

        .form-group label {
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            background-color: #f9f9f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #C2B7B7;
        }

        table th {
            background-color: #ff6b6b;
            color: white;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 1;
            border: 2px solid #232121;
        }

        table td {
            background-color: #ffffff;
            color: #333;
        }

        .bg-orange-300 {
            background-color: #ff6b6b;
        }

        .bg-green-500 {
            background-color: #38b2ac;
        }

        .text-red-500 {
            color: #D52121;
        }

        .text-green-500 {
            color: #38b2ac;
        }

        .text-blue-500 {
            color: #4299e1;
        }

        .text-center {
            text-align: center;
        }

        .flex {
            display: flex;
        }

        .justify-around {
            justify-content: space-around;
        }

        .items-start {
            align-items: flex-start;
        }

        .align-top {
            vertical-align: top;
        }

        .rounded {
            border-radius: 0.5rem;
        }

        .border {
            border: 1px solid #ccc;
        }

        .border-black {
            border-color: #333;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .form-container h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 1rem;
        }

        .form-container p,
        .form-container label {
            font-size: 1rem;
            color: #555;
        }

        .bg-white {
            background-color: white;
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .gap-6 {
            gap: 1.5rem;
        }

        .text-xl {
            font-size: 1.25rem;
        }

        .form-container .separator {
            margin: 1rem 0;
            border-bottom: 1px solid #ddd;
        }

        .table-container {
            overflow-x: auto;
            max-width: 100%;
            margin-bottom: 20px;

            overflow-y: auto;
            max-height: 400px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .form-container .text-center button {
            background-color: #38b2ac;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .form-container .text-center button:hover {
            background-color: #2c7a7b;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            padding-top: 60px;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .mx-20 {
            margin-left: 5rem
                /* 80px */
            ;
            margin-right: 5rem
                /* 80px */
            ;
        }
    </style>
    <script>
        function showModal(invoiceId) {
            document.getElementById('delete_invoice_id').value = invoiceId;
            document.getElementById('confirmation-modal').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('confirmation-modal').style.display = 'none';
        }


        function showModaldeleteall() {
            document.getElementById('confirmationall-modal').style.display = 'block';
        }

        function hideModalall() {
            document.getElementById('confirmationall-modal').style.display = 'none';
        }
    </script>
</head>

<body class="bg-gray-100">
    <div class="dashboard-header">
        <ul class="flex items-center">
            <li><a class="underline" href="index.php">Invoice History</a></li>
            <li><a class="underline" href="customers.php">Customers</a></li>
            <li><a class="underline" href="products.php">Products</a></li>
            <li><a class="underline" href="profile.php">My Business</a></li>
            <li><a class=" underline" href="analysis.php">Analysis</a></li>
        </ul>
        <ul class="flex items-center">
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white" href="challan_form.php">+ New Challan</a></li>
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white" href="debit_form.php">+ Debit Note</a></li>
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white" href="credit_form.php">+ Credit Note</a></li>
            <li><a class="p-2 bg-green-500 text-center flex items-center justify-center rounded-md border border-black text-white" href="invoice_form.php">+ New Invoice</a></li>
        </ul>
    </div>

    <div class="form-container m-2 mt-4 mx-20">
        <h2 class="form-heading text-center">Payment Status</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr class="text-center">
                        <th class="text-center">Received</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="text-center text-xl font-bold">
                        <td class=" text-green-500 ">₹<?php echo $totalReceived; ?></td>
                        <td class=" text-red-500">₹<?php echo $totalNotReceived; ?></td>
                        <td class=" text-blue-500">₹<?php echo $totalAmount; ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

    <div class="form-container text-center">
        <h2 class="form-heading">Invoice History</h2>
        <div class="text-right mb-4">
            <button onclick="showModaldeleteall()" class="bg-red-500 text-white p-2 rounded">Delete All Invoices</button>
        </div>
        <div class="table-container">
            <table>
                <thead class=" static">
                    <tr>
                        <th>Sr.No</th>
                        <th>Invoice Number</th>
                        <th>Customer Name</th>
                        <th>Invoice Date</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $row): ?>
                            <tr>
                                <td><?php echo $serial_num ?></td>
                                <td><?php echo htmlspecialchars($row['invoice_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['invoice_date']))); ?></td>
                                <td><?php echo htmlspecialchars(number_format($row['total'], 2)); ?></td>
                                <td>
                                    <form action="" method="post" style="display:inline;">
                                        <input type="hidden" name="invoice_id" class=" border border-black" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <select name="payment_status" onchange="this.form.submit()">
                                            <option value="0" <?php echo $row['payment_received'] == 0 ? 'selected' : ''; ?>>Not Received</option>
                                            <option value="1" <?php echo $row['payment_received'] == 1 ? 'selected' : ''; ?>>Received</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="flex items-center">
                                    <a href="generate_pdf.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="mx-1 text-center flex items-center justify-center rounded-md text-white"><img src="./images/download.png" alt="delete" width="30" height="30" /></a>
                                    <button onclick="showModal('<?php echo htmlspecialchars($row['id']); ?>')" class="mx-1 text-center flex items-center justify-center rounded-md text-white"><img src="./images/delete.png" alt="delete" width="30" height="30" /></button>
                                </td>
                            </tr>
                        <?php $serial_num++;
                        endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No invoices found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="confirmation-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal()">&times;</span>
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this invoice? This action cannot be undone.</p>
            <form method="post" action="">
                <input type="hidden" name="delete_invoice_id" id="delete_invoice_id">
                <button type="submit" class="p-2 bg-red-500 text-white rounded">Yes, Delete</button>
                <button type="button" class="p-2 bg-gray-500 text-white rounded" onclick="hideModal()">Cancel</button>
            </form>
        </div>
    </div>

    <div id="confirmationall-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModalall()">&times;</span>
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete all the invoices? This action is critical. Be carefull !!!</p>
            <form method="post" action="">
                <button type="submit" name="delete_all" class="p-2 bg-red-500 text-white rounded">Yes, Delete</button>
                <button type="button" class="p-2 bg-gray-500 text-white rounded" onclick="hideModalall()">Cancel</button>
            </form>
        </div>
    </div>
</body>
<footer style="text-align: center; padding: 10px; position: fixed; bottom: 0; width: 100%; background-color: #f1f1f1;">
    <p>All rights reserved &copy; 2024 Parth Gala</p>
</footer>

</html>