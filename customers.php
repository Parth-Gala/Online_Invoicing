<?php
include('include/connect.php');

// Fetch all customer details from the database
$customer_query = "SELECT * FROM customer_details";
$customer_result = mysqli_query($con, $customer_query);

// Check for deletion request
if (isset($_POST['delete_all'])) {
    $delete_query = "DELETE FROM customer_details";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('All customers deleted successfully!'); window.location.href='customers.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}

// Check for specific customer deletion request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM customer_details WHERE id = '$delete_id'";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Customer deleted successfully!'); window.location.href='customers.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon_v2.ico">
    <link href="./output.css" rel="stylesheet">
    <link href="./login.css" rel="stylesheet">
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
            border: 1px solid #ddd;
        }

        table th {
            background-color: #ff6b6b;
            color: white;
            text-align: left;
        }

        table td {
            background-color: #fff;
            color: #333;
        }

        .bg-orange-300 {
            background-color: #ff6b6b;
        }

        .bg-green-500 {
            background-color: #38b2ac;
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
    </style>
    <script>
        function showModal() {
            document.getElementById('confirmation-modal').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('confirmation-modal').style.display = 'none';
        }
    </script>
</head>

<body class="bg-gray-100">
    <div class="dashboard-header">
        <ul class="flex items-center">
            <li><a class=" underline" href="index.php">Invoice History</a></li>
            <li><a class=" underline" href="customers.php">Customers</a></li>
            <li><a class=" underline" href="products.php">Products</a></li>
            <li><a class=" underline" href="profile.php">My Business</a></li>
            <li><a class=" underline" href="analysis.php">Analysis</a></li>
        </ul>
        <ul class="flex items-center">
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white" href="challan_form.php">+ New Challan</a></li>
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white" href="debit_form.php">+ Debit Note</a></li>
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white" href="credit_form.php">+ Credit Note</a></li>
            <li><a class="p-2 bg-green-500 text-center flex items-center justify-center rounded-md border border-black text-white" href="invoice_form.php">+ New Invoice</a></li>
        </ul>
    </div>


    <div class="container mx-auto p-4">
        <div class="flex justify-end items-center gap-1">
            <div class="text-right mb-4">
                <a href="add_customers.php" class="bg-green-500 text-white p-2 rounded">+ Add New Customer</a>
            </div>
            <div class="text-right mb-4">
                <button onclick="showModal()" class="bg-red-500 text-white p-2 rounded">Delete All Customers</button>
            </div>
        </div>

        <!-- Display customer details in cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 xl:gap-12">
            <?php if (mysqli_num_rows($customer_result) > 0): ?>
                <?php while ($customer = mysqli_fetch_assoc($customer_result)): ?>
                    <div class="bg-white p-4 rounded-2xl shadow-lg">
                        <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($customer['c_name']); ?></h3>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['c_address']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['c_phone']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['c_email']); ?></p>
                        <p><strong>City:</strong> <?php echo htmlspecialchars($customer['city']); ?></p>
                        <p><strong>State:</strong> <?php echo htmlspecialchars($customer['state']); ?></p>
                        <p><strong>Zipcode:</strong> <?php echo htmlspecialchars($customer['zipcode']); ?></p>
                        <p><strong>PAN:</strong> <?php echo htmlspecialchars($customer['pan']); ?></p>
                        <p><strong>GST:</strong> <?php echo htmlspecialchars($customer['gst']); ?></p>
                        <!-- Edit and Delete buttons -->
                        <div class="flex justify-between mt-2">
                            <a href="edit_customer.php?id=<?php echo $customer['id']; ?>" class="bg-blue-500 text-white p-2 rounded">Edit</a>
                            <a href="?delete_id=<?php echo $customer['id']; ?>" onclick="return confirm('Are you sure you want to delete this customer?');" class="bg-red-500 text-white p-2 rounded">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No customers found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmation-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="hideModal()">&times;</span>
            <h3 class="font-bold text-lg mb-2">Confirm Deletion</h3>
            <p>Are you sure you want to delete all customers?</p>
            <form action="" method="post" class="mt-4">
                <button type="submit" name="delete_all" class="bg-red-500 text-white p-2 rounded">Delete</button>
                <button type="button" onclick="hideModal()" class="bg-gray-500 text-white p-2 rounded">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>