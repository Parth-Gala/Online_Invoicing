<?php
// Ensure no output is sent before PDF generation
ob_start();

// Include the TCPDF library
require_once('tcpdf/tcpdf.php');

// Database connection
$con = new mysqli("localhost", "root", "", "yashgarment");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$invoice_number = $_POST['invoice_number'];
$date = $_POST['invoice_date'];
$customer_id = $_POST['customer_id'];
$products = $_POST['products'];
$e_way_bill_no = $_POST['e_way_bill_no'];
$delivery_note = $_POST['delivery_note'];
$terms_of_payment = $_POST['terms_of_payment'];
$supplier_reference = $_POST['supplier_reference'];
$dispatch_doc_no = $_POST['dispatch_doc_no'];
$dispatch_through = $_POST['dispatch_through'];
$delivery_date = $_POST['delivery_date'];
$destination = $_POST['destination'];
$delivery_terms = $_POST['delivery_terms'];
$shipping_charges = isset($_POST['shipping']) && $_POST['shipping'] !== '' ? $_POST['shipping'] : 0;
$discount = isset($_POST['discount']) && $_POST['discount'] !== '' ? $_POST['discount'] : 0;

$customer_query = "SELECT * FROM customer_details WHERE id = $customer_id";
$customer_result = $con->query($customer_query);
$customer = $customer_result->fetch_assoc();

$sub_total = 0;
$total_tax = 0;

$products_name = [];
$products_price = [];
$products_qty = [];
$products_gst = [];
$products_hsn = [];
$products_unit = [];

$user_query = "SELECT * FROM user_details WHERE id = 1";
$user_result = $con->query($user_query);
$user = $user_result->fetch_assoc();

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('dejavusans', '', 9);
$pdf->AddPage('P', 'A4');

function convertNumberToIndianWords($number)
{
    $words = array(
        '0' => '',
        '1' => 'One',
        '2' => 'Two',
        '3' => 'Three',
        '4' => 'Four',
        '5' => 'Five',
        '6' => 'Six',
        '7' => 'Seven',
        '8' => 'Eight',
        '9' => 'Nine',
        '10' => 'Ten',
        '11' => 'Eleven',
        '12' => 'Twelve',
        '13' => 'Thirteen',
        '14' => 'Fourteen',
        '15' => 'Fifteen',
        '16' => 'Sixteen',
        '17' => 'Seventeen',
        '18' => 'Eighteen',
        '19' => 'Nineteen',
        '20' => 'Twenty',
        '30' => 'Thirty',
        '40' => 'Forty',
        '50' => 'Fifty',
        '60' => 'Sixty',
        '70' => 'Seventy',
        '80' => 'Eighty',
        '90' => 'Ninety'
    );

    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');

    if ($number == 0) {
        return 'Zero Rupees';
    }

    $no = floor($number);
    $decimal = round($number - $no, 2) * 100;
    $counter = 0;
    $str = array();

    while ($no > 0) {
        if ($counter == 0) {  // First group (units and tens)
            $divider = 100;
        } else if ($counter == 1) {  // Second group (hundreds)
            $divider = 10;
        } else {  // Remaining groups (thousands, lakhs, crores)
            $divider = 100;
        }

        $number = $no % $divider;
        $no = floor($no / $divider);

        if ($number) {
            $plural = (($counter > 2 && $number > 9)) ? '' : null;
            $hundred = ($counter == 1 && $str) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] . " " . $digits[$counter] . $plural . " " . $hundred
                : $words[floor($number / 10) * 10] . " " . $words[$number % 10] . " " . $digits[$counter] . $plural . " " . $hundred;
        } else {
            $str[] = null;
        }

        $counter++;
    }

    $str = array_reverse($str);
    $result = implode('', $str);

    if ($decimal) {
        $result .= " and " . $words[floor($decimal / 10)] . " " . $words[$decimal % 10] . " paise";
    }

    return $result . " Rupees";
}


// Create HTML content for the challan
$html = <<<EOD
<h2 style="text-align:center;">Tax Invoice</h2>
    <table align="left" border="1" cellpadding="6" cellspacing="0" style="width:100%;">
        <tr>
            <td rowspan="3" colspan="4" >
            <strong>From:</strong>
            <strong>{$user['name']}</strong><br>
            {$user['address']},
            {$user['city']} - {$user['zipcode']}
            {$user['state']}<br>
            {$user['phone']}<br>
            GST No: {$user['gst']}<br>
            PAN No: {$user['pan']}
            </td>
            <td colspan="2"><strong>Invoice No: $invoice_number</strong></td>
            <td colspan="2"><strong>Date:</strong>$date</td>
        </tr>
        <tr>
            <td colspan="2"><strong>E-Way Bill No: </strong>$e_way_bill_no</td>
            <td colspan="2"><strong>Terms of Payment: </strong>$terms_of_payment</td>
        </tr> 
        <tr>
            <td colspan="2"><strong>Supplier Reference: </strong><br>$supplier_reference</td>
            <td colspan="2"><strong>Delivery Note: </strong><br>$delivery_note</td>
        </tr> 

        <tr>
            <td rowspan="4" colspan="4" style="height: auto;">
            <strong>To: </strong>
            <strong>{$customer['c_name']}</strong><br>
            {$customer['c_address']},
            {$customer['city']} - {$customer['zipcode']}
            {$customer['state']}<br>
            {$customer['c_phone']}<br>
            GST No: {$customer['gst']}<br>
            PAN No: {$customer['pan']}
            </td>
            <td colspan="2"><strong>Dispatch Doc No: </strong>$dispatch_doc_no</td>
            <td colspan="2"><strong>Delivery Date: </strong>$delivery_date</td>  
        </tr>
        <tr>
            <td colspan="2"><strong>Dispatch Through:</strong><br>$dispatch_through </td>
            <td colspan="2"><strong>Destination: </strong>$destination</td>
        </tr> 
        <tr>
            <td colspan="8" rowspan="3"><strong>Delivery Terms: </strong><br>$delivery_terms </td>
        </tr> 
    </table>
    <table border="1" cellpadding="3" cellspacing="1" style="width:100%;">
    <tr>
        <td align="center" style="width:6%;">Sr No.</td>
        <td align="center" style="width:22%;">Items</td>
        <td align="center">HSN/SAC</td>
        <td align="center">Qty</td>
        <td align="center">Unit</td>
        <td align="center">Price</td>
        <td align="center" style="width:15%;">Amount</td>
    </tr>
EOD;

$serial_number = 1;
foreach ($products as $product_id => $product) {
    if (!empty($product['quantity'])) {
        $product_query = "SELECT * FROM product_details WHERE id = $product_id";
        $product_result = $con->query($product_query);
        $product_details = $product_result->fetch_assoc();

        $amount = $product_details['total_price'] * $product['quantity'];
        $gst = $product_details['gstpercent'];
        $total_tax += $amount * $gst / 100;
        $sub_total += $amount;
        $cgst = $total_tax / 2;

        $product_ids[] = $product_id;
        $products_name[] = $product_details['p_name'];
        $products_price[] = $product_details['total_price'];
        $products_qty[] = $product['quantity'];
        $products_gst[] = $gst;
        $products_hsn[] = $product_details['hsn_code'];
        $products_unit[] = $product_details['unit'];

        $html .= "<tr>
                    <td align=\"center\" style=\"width:6%;\">{$serial_number}</td>
                    <td align=\"left\">{$product_details['p_name']}</td>
                    <td align=\"center\">{$product_details['hsn_code']}</td>
                    <td align=\"center\">{$product['quantity']}</td>
                    <td align=\"center\">{$product_details['unit']}</td>
                    <td align=\"right\">₹{$product_details['total_price']}</td>
                    <td align=\"right\">₹$amount</td>
                </tr>";
    }
    $serial_number++;
}

$total = round($sub_total + $total_tax + (float)$shipping_charges - (float)$discount, 0);
$total_in_words = convertNumberToIndianWords($total);

$products_name_json = json_encode($products_name);
$products_price_json = json_encode($products_price);
$products_qty_json = json_encode($products_qty);
$products_gst_json = json_encode($products_gst);
$products_hsn_json = json_encode($products_hsn);
$products_unit_json = json_encode($products_unit);

$check_query = "SELECT * FROM invoice_details WHERE invoice_number = '$invoice_number'";
$check_result = $con->query($check_query);

if ($check_result->num_rows > 0) {
    echo "Error: Invoice number $invoice_number already exists.";
    exit;
}

$insert_query = "INSERT INTO invoice_details (
        invoice_number, invoice_date, customer_id, customer_name, customer_address, 
        customer_city, customer_zipcode, customer_state, customer_phone, customer_gst, customer_pan,
        e_way_bill_no, delivery_note, terms_of_payment, supplier_reference, dispatch_doc_no,
        dispatch_through, delivery_date, destination, delivery_terms, shipping_charges, discount,
        products_name, products_price, products_qty, products_gst, products_hsn, products_unit,
        sub_total, total_tax, total ) VALUES ('$invoice_number', '$date', $customer_id, '{$customer['c_name']}', '{$customer['c_address']}', '{$customer['city']}', '{$customer['zipcode']}', '{$customer['state']}', '{$customer['c_phone']}', '{$customer['gst']}', '{$customer['pan']}', '$e_way_bill_no', '$delivery_note', '$terms_of_payment', '$supplier_reference', '$dispatch_doc_no', '$dispatch_through', '$delivery_date', '$destination', '$delivery_terms', $shipping_charges, $discount, '$products_name_json', '$products_price_json', '$products_qty_json', '$products_gst_json', '$products_hsn_json', '$products_unit_json',$sub_total, $total_tax, $total
    )";

if ($con->query($insert_query) !== TRUE) {
    echo "Error: " . $insert_query . "<br>" . $con->error;
}

echo "Invoice saved successfully.";

$html .= "<tr>
            <td colspan=\"2\" align=\"right\"><strong>Sub Total</strong></td>
            <td colspan=\"5\" align=\"right\">₹$sub_total</td>
        </tr>
        <tr>
            <td colspan=\"2\" align=\"right\"><strong>CGST</strong></td>
            <td colspan=\"5\" align=\"right\">$cgst</td>
        </tr>
        <tr>
            <td colspan=\"2\" align=\"right\"><strong>SGST</strong></td>
            <td colspan=\"5\" align=\"right\">$cgst</td>
        </tr>
        <tr>
            <td colspan=\"2\" align=\"right\"><strong>Shipping Charges</strong></td>
            <td colspan=\"5\" align=\"right\">$shipping_charges</td>
        </tr>
        <tr>
            <td colspan=\"2\" align=\"right\"><strong>Discount</strong></td>
            <td colspan=\"5\" align=\"right\">$discount</td>
        </tr>
        <tr>
            <td colspan=\"2\" align=\"right\"><strong>Total</strong></td>
            <td colspan=\"5\" align=\"right\">₹$total</td>
        </tr>
        <tr>
            <td colspan=\"8\" align=\"left\">Amount in words <br><strong>$total_in_words Only</strong></td>
        </tr>
    </table>

    <table border=\"1\" cellpadding=\"5\" align=\"left\" style=\"width:100%;\">
        <tr>
            <td rowspan=\"2\" align=\"center\">HSN/SAC</td>
            <td rowspan=\"2\" align=\"center\">Taxable Val</td>
            <td colspan=\"2\" align=\"center\">Central Tax</td>
            <td colspan=\"2\" align=\"center\">State Tax</td>
            <td rowspan=\"2\" align=\"center\">Total</td>
        </tr>
        <tr>
            <td align=\"center\">Price </td>
            <td align=\"center\">Amount</td>
            <td align=\"center\">Price </td>
            <td align=\"center\">Amount</td>
        </tr>";

$sub_total = 0;
$cgstamt = 0;
$cgstper = 0;
$amount = 0;
$cgst = 0;
$total_tax = 0;
$serial_number = 1;
foreach ($products as $product_id => $product) {
    if (!empty($product['quantity'])) {
        $product_query = "SELECT * FROM product_details WHERE id = $product_id";
        $product_result = $con->query($product_query);
        $product_details = $product_result->fetch_assoc();

        $amount = $product_details['total_price'] * $product['quantity'];
        $gst = $product_details['gstpercent'];
        $total_tax += $amount * $gst / 100;
        $sub_total += $amount;
        $cgst = $total_tax / 2;
        $cgstper = $gst / 2;
        $cgstamt = $amount * $cgstper / 100;

        $html .= "<tr>
                    <td rowspan=\"1\" align=\"left\">{$product_details['hsn_code']}</td>
                    <td rowspan=\"1\" align=\"right\">$amount</td>
                    <td>$cgstper%</td>
                    <td>$cgstamt</td>
                    <td>$cgstper%</td>
                    <td>$cgstamt</td>
                    <td>₹$total_tax</td>
                  </tr>";
    }
}
$html .= "<tr>
            <td colspan=\"2\" >Total</td>
            <td colspan=\"1\" ></td>
            <td colspan=\"1\" >$cgst</td>
            <td colspan=\"1\" ></td>
            <td colspan=\"1\" >$cgst</td>
            <td colspan=\"1\" >$total_tax</td>
        </tr>
        <tr>
            <td colspan=\"3\" ><strong>Bank Detail</strong></td>
            <td colspan=\"4\" align=\"center\" ><strong>Authorised Signature</strong></td>
        </tr>
        <tr>
            <td colspan=\"3\">
            Bank Name:{$user['bank_name']}<br>
            Account No:{$user['acc_number']}<br>
            IFSCE Code:{$user['ifsc']}
            </td>
            <td colspan=\"4\" align=\"center\" >For.{$user['name']}</td>
        </tr>
        <tr>
            <td colspan=\"3\" ><strong>Terms and condition:</strong>{$user['terms']}</td>
            <td colspan=\"4\" align=\"center\" >This is computer generated Invoice</td>
        </tr>
</table>";

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean();
$customer_name = preg_replace('/[^A-Za-z0-9\-]/', '', $customer['c_name']);
$filename = $customer_name . '_invoice.pdf';
$pdf->Output($filename, 'I');


$con->close();
