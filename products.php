<?php
include('include/connect.php');

$product_query = "SELECT * FROM product_details";
$product_result = mysqli_query($con, $product_query);

if (isset($_POST['delete_all'])) {
    $delete_query = "DELETE FROM product_details";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('All products deleted successfully!'); window.location.href='products.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_query = "DELETE FROM product_details WHERE id = '$delete_id'";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
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
    <title>Product Details</title>
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
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete this product?')) {
                window.location.href = url;
            }
        }

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
                <a href="add_products.php" class="bg-green-500 text-white p-2 rounded">+ Add New Products</a>
            </div>
            <div class="text-right mb-4">
                <button onclick="showModal()" class="bg-red-500 text-white p-2 rounded">Delete All Products</button>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 xl:gap-12">
            <?php if (mysqli_num_rows($product_result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($product_result)): ?>
                    <div class="bg-white p-4 rounded shadow-lg">
                        <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($product['p_name']); ?></h3>
                        <p><strong>HSN Code:</strong> <?php echo htmlspecialchars($product['hsn_code']); ?></p>
                        <p><strong>Unit:</strong> <?php echo htmlspecialchars($product['unit']); ?></p>
                        <p><strong>GST %:</strong> <?php echo htmlspecialchars($product['gstpercent']); ?></p>
                        <p><strong>Total Price:</strong> ₹<?php echo htmlspecialchars($product['total_price']); ?></p>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($product['p_qty']); ?></p>

                        <div class="flex justify-between mt-2">
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="bg-blue-500 text-white p-2 rounded">Edit</a>
                            <a href="#" onclick="confirmDelete('?delete_id=<?php echo $product['id']; ?>');" class="bg-red-500 text-white p-2 rounded">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="confirmation-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="hideModal()">&times;</span>
            <h3 class="font-bold text-lg mb-2">Confirm Deletion</h3>
            <p>Are you sure you want to delete all Products?</p>
            <form action="" method="post" class="mt-4">
                <button type="submit" name="delete_all" class="bg-red-500 text-white p-2 rounded">Delete</button>
                <button type="button" onclick="hideModal()" class="bg-gray-500 text-white p-2 rounded">Cancel</button>
            </form>
        </div>
    </div>
</body>
<footer style="text-align: center; padding: 10px; width: 100%; background-color: #f1f1f1;">
    <p>All rights reserved &copy; 2025 Parth Gala</p>
</footer>

</html>