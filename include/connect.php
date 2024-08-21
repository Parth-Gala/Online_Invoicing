<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "yashgarment";

$con = new mysqli($servername, $username, $password);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$db_check_query = "CREATE DATABASE IF NOT EXISTS $db_name";
$db_created = false;
if ($con->query($db_check_query) === TRUE) {
    $db_created = $con->affected_rows > 0;
}

$con->select_db($db_name);
if (!$con) {
    die("Connection to database failed: " . mysqli_error($con));
}

// SQL to create `users` table if it doesn't exist
// $table_name = "users_details";
$table_check_query = "
CREATE TABLE IF NOT EXISTS user_details (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zipcode VARCHAR(10) NOT NULL,
    pan VARCHAR(20) NOT NULL,
    gst VARCHAR(20) NOT NULL,
    bank_name VARCHAR(255) NOT NULL,
    acc_number VARCHAR(30) NOT NULL,
    ifsc VARCHAR(15) NOT NULL,
    terms VARCHAR(30)
)";
$table_user_created = false;
if ($con->query($table_check_query) === TRUE) {
    $table_created = $con->affected_rows > 0;
}

$table_check_customer_query = "
CREATE TABLE IF NOT EXISTS customer_details (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    c_name VARCHAR(255) NOT NULL,
    c_address VARCHAR(255) NOT NULL,
    c_phone VARCHAR(20),
    c_email VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zipcode VARCHAR(10),
    pan VARCHAR(20),
    gst VARCHAR(20)
)";
$table_customer_created = false;
if ($con->query($table_check_customer_query) === TRUE) {
    $table_customer_created = $con->affected_rows > 0;
}


$table_check_product_query = "
CREATE TABLE IF NOT EXISTS product_details (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    p_name VARCHAR(255) NOT NULL,
    hsn_code VARCHAR(50) NOT NULL,
    unit VARCHAR(50) NOT NULL, 
    gstpercent DECIMAL(5,2),
    total_price DECIMAL(10,0) NOT NULL,
    p_qty INT(11)
)";
$table_product_created = false;
if ($con->query($table_check_product_query) === TRUE) {
    $table_product_created = $con->affected_rows > 0;
}

$table_check_invoice_query = "
CREATE TABLE IF NOT EXISTS invoice_details (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL,
    invoice_date DATE ,
    customer_id INT(11) ,
    customer_name VARCHAR(255) ,
    customer_address TEXT ,
    customer_city VARCHAR(100) ,
    customer_zipcode VARCHAR(20) ,
    customer_state VARCHAR(100) ,
    customer_phone VARCHAR(20) ,
    customer_gst VARCHAR(20) ,
    customer_pan VARCHAR(20) ,
    e_way_bill_no VARCHAR(50),
    delivery_note TEXT,
    terms_of_payment TEXT,
    supplier_reference TEXT,
    dispatch_doc_no VARCHAR(50),
    dispatch_through VARCHAR(100),
    delivery_date DATE,
    destination VARCHAR(255),
    delivery_terms TEXT,
    shipping_charges DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    products_name JSON ,
    products_price JSON ,
    products_qty JSON ,
    products_gst JSON ,
    products_hsn JSON ,
    products_unit JSON ,
    sub_total DECIMAL(10,2) ,
    total_tax DECIMAL(10,2) ,
    total DECIMAL(10,1) ,
    payment_received BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$table_invoice_created = false;
if ($con->query($table_check_invoice_query) === TRUE) {
    $table_invoice_created = $con->affected_rows > 0;
}

$message = "";
if ($db_created) {
    $message .= "Database `yashgarment` created.\n";
} else {
    $message .= "Database `yashgarment` already exists.\n";
}

if ($table_user_created) {
    $message .= "Table `user_details` created.";
} else {
    $message .= "Table `user_details` already exists.";
}

if ($table_product_created) {
    $message .= "Table `product_details` created.";
} else {
    $message .= "Table `product_details` already exists.";
}

if ($table_customer_created) {
    $message .= "Table `customer_details` created.";
} else {
    $message .= "Table `customer_details` already exists.";
}

if ($table_invoice_created) {
    $message .= "Table `invoice_details` created.";
} else {
    $message .= "Table `invoice_details` already exists.";
}

if (!empty($message)) {
    echo "<script>alert('$message');</script>";
}
?>
