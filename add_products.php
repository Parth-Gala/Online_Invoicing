<?php
include('include/connect.php');

if (isset($_POST['submit_product'])) {
    $p_name = $_POST['p_name'];
    $hsn_code = $_POST['hsn_code'];
    $unit = $_POST['unit'];
    $gstpercent = $_POST['gstpercent'];
    $total_price = round($_POST['total_price']);
    $p_qty = $_POST['p_qty'];

    // Insert new product into the database
    $insert_product_query = "INSERT INTO product_details (p_name, hsn_code, unit, gstpercent, total_price, p_qty) 
                             VALUES ('$p_name', '$hsn_code', '$unit', '$gstpercent', '$total_price', '$p_qty')";

    if (mysqli_query($con, $insert_product_query)) {
        echo "<script>alert('Product added successfully!'); window.location.href='products.php';</script>";
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
    <title>Add Product</title>
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
    </style>
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
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white"
                    href="challan_form.php">+ New Challan</a></li>
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white"
                    href="debit_form.php">+ Debit Note</a></li>
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white"
                    href="credit_form.php">+ Credit Note</a></li>
            <li><a class="p-2 bg-green-500 text-center flex items-center justify-center rounded-md border border-black text-white"
                    href="invoice_form.php">+ New Invoice</a></li>
        </ul>
    </div>

    <div class="container mx-auto p-4">
        <div class="form-container">
            <h2 class="form-heading">Add New Product</h2>
            <form action="" method="post" enctype="multipart/form-data" class="form">
                <div class="form-group">
                    <label for="p_name">Product Name</label>
                    <input type="text" id="p_name" name="p_name" placeholder="Enter Product Name" required />
                </div>

                <div class="form-group">
                    <label for="hsn_code">HSN Code</label>
                    <input type="text" id="hsn_code" name="hsn_code" placeholder="Enter HSN Code" required />
                </div>

                <div class="form-group">
                    <label for="unit">Unit</label>
                    <select id="unit" name="unit" required>
                        <option value="pcs">Pcs</option>
                        <option value="mtr">Mtr</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="gstpercent">GST Percentage <span style="font-size: small;">(without '%' sign)</span></label>
                    <input type="number" id="gstpercent" name="gstpercent" placeholder="Enter GST %" step="0.01"
                        required />
                </div>

                <div class="form-group">
                    <label for="total_price">Price</label>
                    <input type="number" id="total_price" name="total_price" placeholder="Enter Total Price" step="0.01" />
                </div>

                <div class="form-group">
                    <label for="p_qty">Quantity</label>
                    <input type="number" id="p_qty" name="p_qty" placeholder="Enter Quantity" />
                </div>

                <button type="submit" class="form-button" name="submit_product">Add Product</button>
                <button type="button" class="form-button" onclick="window.location.href='products.php';">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>