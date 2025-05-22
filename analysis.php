<?php
include('include/connect.php');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Total number of customers
$customer_count_query = "SELECT COUNT(*) AS customer_count FROM customer_details";
$customer_count_result = $con->query($customer_count_query);
$customer_count = $customer_count_result->fetch_assoc()['customer_count'];

// Total number of products
$product_count_query = "SELECT COUNT(*) AS product_count FROM product_details";
$product_count_result = $con->query($product_count_query);
$product_count = $product_count_result->fetch_assoc()['product_count'];

// Total sales amount
$total_sales_query = "SELECT SUM(total) AS total_sales FROM invoice_details";
$total_sales_result = $con->query($total_sales_query);
$total_sales = $total_sales_result->fetch_assoc()['total_sales'];

// Total received payments
$total_received_query = "SELECT SUM(total) AS total_received FROM invoice_details WHERE payment_received = 1";
$total_received_result = $con->query($total_received_query);
$total_received = $total_received_result->fetch_assoc()['total_received'];

// Total pending payments
$total_pending_query = "SELECT SUM(total) AS total_pending FROM invoice_details WHERE payment_received = 0";
$total_pending_result = $con->query($total_pending_query);
$total_pending = $total_pending_result->fetch_assoc()['total_pending'];

// Top 5 customers based on total sales
$top_customers_query = "
    SELECT customer_name, SUM(total) AS total_spent 
    FROM invoice_details 
    GROUP BY customer_id 
    ORDER BY total_spent DESC 
    LIMIT 5";
$top_customers_result = $con->query($top_customers_query);

$top_customers = [];
if ($top_customers_result->num_rows > 0) {
    while ($row = $top_customers_result->fetch_assoc()) {
        $top_customers[] = $row;
    }
}

$sql = "SELECT SUM(total) as total_sales, SUM(CASE WHEN payment_received = 1 THEN total ELSE 0 END) as total_received, SUM(CASE WHEN payment_received = 0 THEN total ELSE 0 END) as total_not_received, COUNT(id) as total_invoices FROM invoice_details";
$result = $con->query($sql);
$data = $result->fetch_assoc();

$monthlySalesQuery = "SELECT 
                        DATE_FORMAT(invoice_date, '%Y-%m') as month, 
                        SUM(total) as monthly_total 
                      FROM invoice_details 
                      GROUP BY DATE_FORMAT(invoice_date, '%Y-%m')";
$monthlySalesResult = $con->query($monthlySalesQuery);

$monthlySales = [];
if ($monthlySalesResult) {
    while ($row = $monthlySalesResult->fetch_assoc()) {
        $monthlySales[] = $row;
    }
}

$invoiceQuery = "
    SELECT products_name, products_price, products_qty 
    FROM invoice_details;
";

$invoiceResult = $con->query($invoiceQuery);

$topProducts = [];

if ($invoiceResult) {
    while ($row = $invoiceResult->fetch_assoc()) {
        $productNames = json_decode($row['products_name'], true);
        $productPrices = json_decode($row['products_price'], true);
        $productQtys = json_decode($row['products_qty'], true);

        foreach ($productNames as $index => $productName) {
            if (!isset($topProducts[$productName])) {
                $topProducts[$productName] = 0;
            }
            $topProducts[$productName] += $productPrices[$index] * $productQtys[$index];
        }
    }
}

arsort($topProducts);

// Limit to top 5 products
$topProducts = array_slice($topProducts, 0, 5, true);

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analysis Report</title>
    <link href="./output.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Add your styles here */
        body {
            background-color: #f4f7fa;
        }

        .dashboard-header {
            background-color: #4caf50;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;
        }

        .dashboard-header ul {
            display: flex;
            gap: 20px;
        }

        .dashboard-header a {
            color: white;
            font-weight: bold;
        }

        .analysis-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            margin-left: 12px;
            margin-right: 12px;
        }

        .analysis-heading {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
        }

        .analysis-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .analysis-table th,
        .analysis-table td {
            padding: 10px;
            border: 1px solid #C2B7B7;
        }

        .analysis-table th {
            background-color: #4caf50;
            color: white;
        }

        .text-center {
            text-align: center;
        }

        .text-red-500 {
            color: #f44336;
        }

        .text-green-500 {
            color: #4caf50;
        }

        .text-blue-500 {
            color: #2196f3;
        }

        .w-\[50\%\] {
            width: 50%;
        }

        .flex {
            display: flex;
        }

        .justify-between {
            justify-content: space-between;
        }

        .m-2 {
            margin: 0.5rem
                /* 8px */
            ;
        }
    </style>
</head>

<body>
    <div class="dashboard-header">
        <ul class="flex items-center">
            <li><a class="underline" href="index.php">Invoice History</a></li>
            <li><a class="underline" href="customers.php">Customers</a></li>
            <li><a class="underline" href="products.php">Products</a></li>
            <li><a class="underline" href="profile.php">My Business</a></li>
        </ul>
        <ul class="flex items-center">
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white"
                    href="challan_form.php">+ New Challan</a></li>
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white"
                    href="debit_form.php">+ Debit Note</a></li>
            <li><a class="p-2 bg-orange-600 text-center flex items-center justify-center rounded-md border border-black text-white"
                    href="credit_form.php">+ Credit Note</a></li>
            <li><a class="p-2 bg-blue-500 text-center flex items-center justify-center rounded-md border border-black text-white"
                    href="invoice_form.php">+ New Invoice</a></li>
        </ul>
    </div>

    <div class="analysis-container">
        <h2 class="analysis-heading">Overview</h2>
        <table class="analysis-table">
            <tr>
                <th>Total Customers</th>
                <td class="text-center"><?php echo $customer_count; ?></td>
            </tr>
            <tr>
                <th>Total Products</th>
                <td class="text-center"><?php echo $product_count; ?></td>
            </tr>
            <tr>
                <th>Total Payments Received</th>
                <td class="text-center text-green-500">₹<?php echo number_format($total_received, 2); ?></td>
            </tr>
            <tr>
                <th>Total Payments Pending</th>
                <td class="text-center text-red-500">₹<?php echo number_format($total_pending, 2); ?></td>
            </tr>
            <tr>
                <th>Total Sales</th>
                <td class="text-center text-blue-500">₹<?php echo number_format($total_sales, 2); ?></td>
            </tr>
            <tr>
                <th>Total Invoices</th>
                <td class="text-center"><?php echo $data['total_invoices']; ?></td>
            </tr>
        </table>
    </div>

    <div class="analysis-container text-center">
        <div class="flex justify-between items-center">
            <div class="w-[50%] border border-black m-2">
                <h2 class="form-heading">Monthly Sales</h2>
                <div class="chart-container">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>

            <div class="w-[50%] border border-black m-2">
                <h2 class="form-heading">Top 5 Products by Sales</h2>
                <div class="chart-container">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="analysis-container">
        <h2 class="analysis-heading">Top 5 Customers</h2>
        <table class="analysis-table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Total Spent</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($top_customers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['customer_name']); ?></td>
                        <td class="text-center">₹<?php echo number_format($customer['total_spent'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Monthly Sales Chart
        const monthlySalesData = {
            labels: <?php echo json_encode(array_column($monthlySales, 'month')); ?>,
            datasets: [{
                label: 'Monthly Sales (₹)',
                data: <?php echo json_encode(array_column($monthlySales, 'monthly_total')); ?>,
                backgroundColor: '#38b2ac',
                borderColor: '#38b2ac',
                fill: false,
                tension: 0.1
            }]
        };

        const monthlySalesConfig = {
            type: 'line',
            data: monthlySalesData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const monthlySalesChart = new Chart(
            document.getElementById('monthlySalesChart'),
            monthlySalesConfig
        );

        //Top products by sales
        // Top Products by Sales Chart
        const topProductsData = {
            labels: <?php echo json_encode(array_keys($topProducts)); ?>,
            datasets: [{
                label: 'Total Sales (₹)',
                data: <?php echo json_encode(array_values($topProducts)); ?>,
                backgroundColor: '#ff6b6b',
            }]
        };

        const topProductsConfig = {
            type: 'bar',
            data: topProductsData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const topProductsChart = new Chart(
            document.getElementById('topProductsChart'),
            topProductsConfig
        );
    </script>
</body>
<footer style="text-align: center; padding: 10px; width: 100%; background-color: #f1f1f1;">
    <p>All rights reserved &copy; 2025 Parth Gala</p>
</footer>

</html>