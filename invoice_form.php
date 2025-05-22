<?php
include('include/connect.php');

$user_query = "SELECT * FROM user_details ORDER BY id DESC LIMIT 1";
$user_result = $con->query($user_query);
$user = $user_result->fetch_assoc();

// Fetch customers
$customer_query = "SELECT * FROM customer_details";
$customer_result = $con->query($customer_query);

// Fetch products
$product_query = "SELECT * FROM product_details";
$product_result = $con->query($product_query);

// Get current date
$display_date = date('d-F-Y');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Invoice</title>
    <link href="./output.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
        }

        .large-checkbox {
            width: 20px;
            height: 20px;
            background-color: #C51616FF;
            transform: scale(1.5);
            -webkit-transform: scale(1.5);
            -moz-transform: scale(1.5);
            -ms-transform: scale(1.5);
            -o-transform: scale(1.5);
            margin: 5px;
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
                <h2 class="form-heading text-center text-2xl">Create Invoice</h2>
                <form action="invoice.php" method="post" target="_blank">
                    <h3>Creation Date: <?php echo $display_date; ?></h3>

                    <div class="form-group">
                        <label for="invoice_number">Invoice Number</label>
                        <input type="text" id="invoice_number" name="invoice_number" placeholder="Enter Invoice number"
                            required />
                    </div>

                    <div class="form-group">
                        <label for="invoice_date">Invoice Date:</label>
                        <input type="date" id="invoice_date" name="invoice_date">
                    </div>
                    <?php
                    $terms = isset($user['terms']) ? $user['terms'] : '';
                    ?>

                    <div class="form-group">
                        <h2 class="text-center"><strong>Business Details</strong></h2>
                        <?php if ($user): ?>
                            <div class="flex flex-col items-start">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($user['address'])); ?></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($user['city']); ?></p>
                                <p><strong>State:</strong> <?php echo htmlspecialchars($user['state']); ?></p>
                                <p><strong>Zipcode:</strong> <?php echo htmlspecialchars($user['zipcode']); ?></p>
                                <p><strong>PAN:</strong> <?php echo htmlspecialchars($user['pan']); ?></p>
                                <p><strong>GST:</strong> <?php echo htmlspecialchars($user['gst']); ?></p>
                                <p><strong>Bank Name:</strong> <?php echo htmlspecialchars($user['bank_name']); ?></p>
                                <p><strong>Account Number:</strong> <?php echo htmlspecialchars($user['acc_number']); ?></p>
                                <p><strong>IFSC Code:</strong> <?php echo htmlspecialchars($user['ifsc']); ?></p>
                            </div>
                        <?php else: ?>
                            <p>No details found for the specified user.</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="terms">Terms and Conditions</label>
                        <textarea id="terms" name="terms" rows="2"
                            placeholder="Enter Terms and Conditions"><?php echo htmlspecialchars($terms); ?></textarea>
                    </div>

                    <div class="separator"></div>
            </div>

            <div class="form-container shadow-lg">
                <div class="form-group">
                    <label for="customer_id">Select Customer</label>
                    <select name="customer_id" id="customer_id" required onchange="updateCustomerDetails()">
                        <?php while ($customer = $customer_result->fetch_assoc()) { ?>
                            <option value="<?php echo $customer['id']; ?>"
                                data-name="<?php echo $customer['c_name']; ?>"
                                data-phone="<?php echo $customer['c_phone']; ?>"
                                data-address="<?php echo $customer['c_address']; ?>"
                                data-city="<?php echo $customer['city']; ?>"
                                data-state="<?php echo $customer['state']; ?>">
                                <?php echo $customer['c_name']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <div class="border-2 rounded-md border-black p-2 px-4 mt-2">
                        <p id="customer_name">Name:</p>
                        <p id="customer_phone">Phone:</p>
                        <p id="customer_address">Address:</p>
                        <span id="customer_city">City: </span>,
                        <span id="customer_state">State: </span>
                    </div>

                    <!-- Edit Customer Button -->
                    <div class="text-right mt-4">
                        <a id="edit_customer" class="bg-blue-500 text-white p-2 rounded cursor-pointer">Edit Customer</a>
                    </div>
                </div>

                <script>
                    function updateCustomerDetails() {
                        var selectedOption = document.getElementById('customer_id').options[document.getElementById('customer_id').selectedIndex];

                        var customerId = selectedOption.value;
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

                        document.getElementById('edit_customer').href = "edit_customer.php?id=" + customerId;
                    }
                    updateCustomerDetails();
                </script>

                <div class="separator"></div>

                <div class="form-group">
                    <label for="e_way_bill_no">E-Way Bill No:</label>
                    <input type="text" id="e_way_bill_no" name="e_way_bill_no" placeholder="Enter E-Way Bill No">
                </div>

                <div class="form-group">
                    <label for="delivery_note">Delivery Note:</label>
                    <input type="text" id="delivery_note" name="delivery_note" placeholder="Enter Delivery Note">
                </div>

                <div class="form-group">
                    <label for="terms_of_payment">Terms of Payment:</label>
                    <input type="text" id="terms_of_payment" name="terms_of_payment" placeholder="Enter Terms of Payment">
                </div>

                <div class="form-group">
                    <label for="supplier_reference">Supplier Reference:</label>
                    <input type="text" id="supplier_reference" name="supplier_reference" placeholder="Enter Supplier Reference">
                </div>

                <div class="form-group">
                    <label for="dispatch_doc_no">Dispatch Doc No:</label>
                    <input type="text" id="dispatch_doc_no" name="dispatch_doc_no" placeholder="Enter Dispatch Doc No">
                </div>

                <div class="form-group">
                    <label for="dispatch_through">Dispatch Through:</label>
                    <input type="text" id="dispatch_through" name="dispatch_through" placeholder="Enter Dispatch Method">
                </div>

                <div class="form-group">
                    <label for="delivery_date">Delivery Date:</label>
                    <input type="date" id="delivery_date" name="delivery_date">
                </div>

                <div class="form-group">
                    <label for="destination">Destination:</label>
                    <input type="text" id="destination" name="destination" placeholder="Enter Destination">
                </div>

                <div class="form-group">
                    <label for="delivery_terms">Delivery Terms:</label>
                    <textarea id="delivery_terms" name="delivery_terms" placeholder="Enter Delivery Terms"></textarea>
                </div>
            </div>

            <div class="form-container shadow-lg">
                <div class="w-full">
                    <div class="flex justify-between items-center text-center form-group">
                        <h3 class="text-start"><strong>Select Products</strong></h3>
                        <div class="text-right mb-4">
                            <a href="products.php" class="bg-green-500 text-white p-2 rounded">+ Add Products</a>
                        </div>
                    </div>
                    <div class="table-container">
                        <table>
                            <?php if (mysqli_num_rows($product_result) > 0): ?>
                                <tr>
                                    <th>Select</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price (₹)</th>
                                    <th>Unit</th>
                                    <th>GST(%)</th>
                                </tr>
                                <?php while ($product = $product_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="form-group">
                                            <input type="checkbox" name="products[<?php echo $product['id']; ?>][name]" class="large-checkbox">
                                        </td>
                                        <td class="form-group">
                                            <!-- <input type="checkbox" name="products[<?php echo $product['id']; ?>][name]"> -->
                                            <input type="text" name="products[<?php echo $product['id']; ?>][name]"
                                                value="<?php echo $product['p_name']; ?>">
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" name="products[<?php echo $product['id']; ?>][quantity]" min="1"
                                                    placeholder="Enter Quantity">
                                            </div>
                                        </td>
                                        <td class="form-group">
                                            <input type="text" name="products[<?php echo $product['id']; ?>][price]"
                                                value="<?php echo $product['total_price']; ?>">
                                        </td>
                                        <td>
                                            <?php echo $product['unit']; ?>
                                        </td>
                                        <td>
                                            <?php echo $product['gstpercent']; ?>%
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No products found.</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <label for="shipping">Shipping Charges:</label>
                    <input type="number" id="shipping" name="shipping" placeholder="Enter Shipping Charges">
                </div>

                <div class="form-group">
                    <label for="discount">Discount:</label>
                    <input type="number" id="discount" name="discount" placeholder="Enter Discount Amount">
                </div>

                <div class="separator"></div>
                <div class="text-center">
                    <input type="submit" value="Create Invoice" class="bg-green-500 text-white p-2 rounded cursor-pointer">
                </div>
                </form>
            </div>
        </div>
    </div>
</body>
<footer style="text-align: center; padding: 10px; width: 100%; background-color: #f1f1f1;">
    <p>All rights reserved &copy; 2025 Parth Gala</p>
</footer>

</html>

<?php
$con->close();
?>