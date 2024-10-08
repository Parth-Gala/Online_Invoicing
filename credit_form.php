<?php
include('include/connect.php');
// Fetch business details (your business)
$user_query = "SELECT * FROM user_details WHERE id = 1"; // Assuming only one business user
$user_result = $con->query($user_query);
$user = $user_result->fetch_assoc();

// Fetch customers
$customer_query = "SELECT * FROM customer_details";
$customer_result = $con->query($customer_query);

// Fetch products
$product_query = "SELECT * FROM product_details";
$product_result = $con->query($product_query);

// Get current date
$date = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Credit Note</title>
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

<body>
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


    <div class="container mx-auto py-6 my-2">
        <div class="grid grid-cols-2 gap-6 mx-1">
            <div class="form-container shadow-lg">
                <h2 class="form-heading text-center text-2xl">Create Credit Note</h2>
                <form action="creditnote.php" method="post">
                    <h3>Date: <?php echo $date; ?></h3>

                    <div class="form-group">
                        <label for="credit_note">Credit Note Number</label>
                        <input type="text" id="credit_note" name="credit_note" placeholder="Enter Credit Note number"
                            required />
                    </div>

                    <input type="hidden" name="date" value="<?php echo $date; ?>">

                    <div class="form-group">
                        <h2><strong>Business Details:</strong></h2>
                        <?php if ($user): ?>
                            <p class=" flex-col items-start justify-center"><?php echo $user['name']; ?><br>
                                <?php echo nl2br($user['address']); ?><br>
                                GST No: <?php echo $user['gst']; ?></p>
                        <?php else: ?>
                            <p>No details found for the specified user.</p>
                        <?php endif; ?>
                    </div>

                    <div class="separator"></div>

                    <div class="form-group">
                        <label for="customer_id">Select Customer</label>
                        <select name="customer_id" id="customer_id" required onchange="updateCustomerDetails()">
                            <?php while ($customer = $customer_result->fetch_assoc()) { ?>
                                <option value="<?php echo $customer['id']; ?>" data-name="<?php echo $customer['c_name']; ?>"
                                    data-phone="<?php echo $customer['c_phone']; ?>"
                                    data-address="<?php echo $customer['c_address']; ?>"
                                    data-city="<?php echo $customer['city']; ?>" data-state="<?php echo $customer['state']; ?>">
                                    <?php echo $customer['c_name']; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <div class="border-2 rounded-md  border-black py-1">
                            <p id="customer_name">Name:</p>
                            <p id="customer_phone">Phone: </p>
                            <p id="customer_address">Address: </p>
                            <span id="customer_city">City: </span> ,
                            <span id="customer_state">State: </span>
                        </div>
                    </div>
                    <script>
                        function updateCustomerDetails() {
                            var selectedOption = document.getElementById('customer_id').options[document.getElementById('customer_id').selectedIndex];

                            var customerName = selectedOption.getAttribute('data-name');
                            var customerPhone = selectedOption.getAttribute('data-phone');
                            var customerAddress = selectedOption.getAttribute('data-address');
                            var customerCity = selectedOption.getAttribute('data-city');
                            var customerState = selectedOption.getAttribute('data-state');

                            document.getElementById('customer_name').innerHTML = "<strong>Name: </strong>" + customerName;
                            document.getElementById('customer_phone').innerHTML = "<strong>Phone: </strong>" + customerPhone;
                            document.getElementById('customer_address').innerHTML = "<strong>Address: </strong>" + customerAddress;
                            document.getElementById('customer_city').innerHTML = customerCity;
                            document.getElementById('customer_state').innerHTML = customerState;
                        }

                        updateCustomerDetails();
                    </script>

            </div>

            <div class="form-container shadow-lg">
                <div class="w-full">
                    <div class="flex justify-between items-center text-center form-group">
                        <h3 class=" text-start align-top"><strong>Select Products</strong></h3>
                        <div class="text-right mb-4">
                            <a href="add_products.php" class="bg-green-500 text-white p-2 rounded">+ Add Products</a>
                        </div>
                    </div>
                    <table>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>GST(%)</th>
                        </tr>
                        <?php while ($product = $product_result->fetch_assoc()) { ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="products[<?php echo $product['id']; ?>][name]"
                                        value="<?php echo $product['p_name']; ?>">
                                    <?php echo $product['p_name']; ?>
                                </td>
                                <td>
                                    ₹<?php echo $product['total_price']; ?>
                                </td>
                                <td>
                                    <input type="number" name="products[<?php echo $product['id']; ?>][quantity]" min="1"
                                        placeholder="Enter Quantity">
                                </td>
                                <td>
                                    <?php echo $product['unit']; ?>
                                </td>
                                <td>
                                    <?php echo $product['gstpercent']; ?>%
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                    <div class="separator"></div>
                    <div class="text-center mb-4">
                        <input type="submit" value="Generate Credit Note" class="bg-green-500 text-white p-2 rounded cursor-pointer">
                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php
$con->close();
?>