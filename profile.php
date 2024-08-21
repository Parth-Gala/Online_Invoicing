<?php
include('include/connect.php');

if (isset($_POST['submit_form'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipcode = $_POST['zipcode'];
    $pan = $_POST['pan'];
    $gst = $_POST['gst'];
    $bank_name = $_POST['bank_name'];
    $acc_number = $_POST['acc_number'];
    $ifsc = $_POST['ifsc'];
    $terms = $_POST['terms'];

    $check_user = "SELECT * FROM user_details WHERE email = '$email'";
    $result = mysqli_query($con, $check_user);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $update_query = "UPDATE user_details SET 
                            name = IF('$name' != name AND '$name' != '', '$name', name), 
                            phone = IF('$phone' != phone AND '$phone' != '', '$phone', phone), 
                            address = IF('$address' != address AND '$address' != '', '$address', address), 
                            city = IF('$city' != city AND '$city' != '', '$city', city), 
                            state = IF('$state' != state AND '$state' != '', '$state', state), 
                            zipcode = IF('$zipcode' != zipcode AND '$zipcode' != '', '$zipcode', zipcode), 
                            pan = IF('$pan' != pan AND '$pan' != '', '$pan', pan), 
                            gst = IF('$gst' != gst AND '$gst' != '', '$gst', gst), 
                            bank_name = IF('$bank_name' != bank_name AND '$bank_name' != '', '$bank_name', bank_name), 
                            acc_number = IF('$acc_number' != acc_number AND '$acc_number' != '', '$acc_number', acc_number), 
                            ifsc = IF('$ifsc' != ifsc AND '$ifsc' != '', '$ifsc', ifsc), 
                            terms = IF('$terms' != terms AND '$terms' != '', '$terms', terms)
                        WHERE email = '$email'";

        $message = "Details updated successfully!";
    } else {
        $insert_query = "INSERT INTO user_details (name, phone, email, address, city, state, zipcode, pan, gst, bank_name, acc_number, ifsc, terms) 
                         VALUES ('$name', '$phone', '$email', '$address', '$city', '$state', '$zipcode', '$pan', '$gst', '$bank_name', '$acc_number', '$ifsc', '$terms')";
        $message = "Details added successfully!";
    }

    if (mysqli_query($con, isset($update_query) ? $update_query : $insert_query)) {
        echo "<script>alert('$message');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
    }
}

$user_query = "SELECT * FROM user_details WHERE id = 1";
$user_result = mysqli_query($con, $user_query);

if (mysqli_num_rows($user_result) > 0) {
    $user = mysqli_fetch_assoc($user_result);
} else {
    $user = null;
}


$sales_query = "SELECT invoice_date, SUM(total) AS sales_amount FROM invoice_details GROUP BY invoice_date ORDER BY invoice_date";
$sales_result = mysqli_query($con, $sales_query);

if (!$sales_result) {
    echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Business</title>
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

    <div class="w-full h-auto flex justify-around items-start align-top">
        <div class="w-full">
            <div class="form-container">
                <h2 class="form-heading">Edit Details</h2>
                <form action="" method="post" enctype="multipart/form-data" class="form">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter Your Name" />
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" placeholder="+9198196XXXXX" />
                    </div>

                    <div class="form-group">
                        <label for="email">*Email</label>
                        <input type="email" id="email" name="email" placeholder="example@email.com" required />
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" placeholder="Enter Your Address" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" placeholder="Enter Your City" />
                    </div>

                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" placeholder="Enter Your State" />
                    </div>

                    <div class="form-group">
                        <label for="zipcode">Zipcode</label>
                        <input type="text" id="zipcode" name="zipcode" placeholder="Enter Your Zipcode" />
                    </div>

                    <div class="form-group">
                        <label for="pan">PAN Number</label>
                        <input type="text" id="pan" name="pan" placeholder="Enter Your PAN Number" />
                    </div>

                    <div class="form-group">
                        <label for="gst">GST Number</label>
                        <input type="text" id="gst" name="gst" placeholder="Enter Your GST Number" />
                    </div>

                    <!-- Separator Line -->
                    <div class="separator"></div>

                    <!-- Bank Information -->
                    <div class="form-group">
                        <label for="bank_name">Bank Name</label>
                        <input type="text" id="bank_name" name="bank_name" placeholder="Enter Your Bank Name" />
                    </div>

                    <div class="form-group">
                        <label for="acc_number">Account Number</label>
                        <input type="text" id="acc_number" name="acc_number" placeholder="Enter Your Account Number" />
                    </div>

                    <div class="form-group">
                        <label for="ifsc">IFSC Code</label>
                        <input type="text" id="ifsc" name="ifsc" placeholder="Enter IFSC Code" />
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-group">
                        <label for="terms">Terms</label>
                        <input type="text" id="terms" name="terms" placeholder="Terms and Conditions" />
                    </div>

                    <!-- Submit and Clear Buttons -->
                    <button type="submit" class="form-button" name="submit_form">Submit</button>
                    <button type="button" class="form-button" onclick="window.location.reload();">Clear</button>
                </form>
            </div>
        </div>

        <div class=" w-full">
            <div class="form-container">
                <h2 class="form-heading">My Current Details</h2>
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
                        <p><strong>Terms:</strong> <?php echo nl2br(htmlspecialchars($user['terms'])); ?></p>
                    </div>
                <?php else: ?>
                    <p>No details found for the specified user.</p>
                <?php endif; ?>
            </div>

            <div class="form-container">
                <h2 class="form-heading">Sales Analysis</h2>
                <div class="table-container">
                    <?php if (mysqli_num_rows($sales_result) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Invoice Date</th>
                                    <th>Sales Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($sales_result)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['invoice_date']); ?></td>
                                        <td><?php echo number_format($row['sales_amount'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No sales data available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>



</body>

</html>