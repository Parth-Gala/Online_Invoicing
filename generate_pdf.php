<?php
require_once('tcpdf/tcpdf.php');
include('include/connect.php');

// Ensure no output is sent before PDF generation
ob_start();

// Check if id is provided
if (!isset($_GET['id'])) {
    die("Invoice ID is missing.");
}

$invoice_id = $_GET['id'];

// Fetch invoice data
$sql = "SELECT * FROM invoice_details WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $invoice_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invoice not found.");
}

$invoice = $result->fetch_assoc();

$user_query = "SELECT * FROM user_details WHERE id = 1";
$user_result = $con->query($user_query);
$user = $user_result->fetch_assoc();

// Create new PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('dejavusans', '', 9);
$pdf->AddPage('P', 'A4');

// Function to convert numbers to words
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

// Create HTML content for the invoice
$html = <<<EOD
<h2 style="text-align:center;">Tax Invoice</h2>
<table align="left" border="1" cellpadding="6" cellspacing="0" style="width:100%;">
    <tr>
        <td rowspan="3" colspan="4">
            <strong>From:</strong>
            <strong>{$user['name']}</strong><br>
            {$user['address']},
            {$user['city']} - {$user['zipcode']}
            {$user['state']}<br>
            {$user['phone']}<br>
            GST No: {$user['gst']}<br>
            PAN No: {$user['pan']}
        </td>
        <td colspan="2"><strong>Invoice No: {$invoice['invoice_number']}</strong></td>
        <td colspan="2"><strong>Date:</strong> {$invoice['invoice_date']}</td>
    </tr>
    <tr>
        <td colspan="2"><strong>E-Way Bill No: </strong>{$invoice['e_way_bill_no']}</td>
        <td colspan="2"><strong>Terms of Payment: </strong>{$invoice['terms_of_payment']}</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Supplier Reference: </strong><br>{$invoice['supplier_reference']}</td>
        <td colspan="2"><strong>Delivery Note: </strong><br>{$invoice['delivery_note']}</td>
    </tr>
    <tr>
        <td rowspan="4" colspan="4">
            <strong>To: </strong>
            <strong>{$invoice['customer_name']}</strong><br>
            {$invoice['customer_address']},
            {$invoice['customer_city']} - {$invoice['customer_zipcode']}
            {$invoice['customer_state']}<br>
            {$invoice['customer_phone']}<br>
            GST No: {$invoice['customer_gst']}<br>
            PAN No: {$invoice['customer_pan']}
        </td>
        <td colspan="2"><strong>Dispatch Doc No: </strong>{$invoice['dispatch_doc_no']}</td>
        <td colspan="2"><strong>Delivery Date: </strong>{$invoice['delivery_date']}</td>
    </tr>
    <tr>
        <td colspan="2"><strong>Dispatch Through:</strong><br>{$invoice['dispatch_through']}</td>
        <td colspan="2"><strong>Destination: </strong>{$invoice['destination']}</td>
    </tr>
    <tr>
        <td colspan="8" rowspan="3"><strong>Delivery Terms: </strong><br>{$invoice['delivery_terms']}</td>
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

$products_name = json_decode($invoice['products_name']);
$products_price = json_decode($invoice['products_price']);
$products_qty = json_decode($invoice['products_qty']);
$products_gst = json_decode($invoice['products_gst']);
$products_hsn = json_decode($invoice['products_hsn']);
$products_unit = json_decode($invoice['products_unit']);

$serial_number = 1;
$sub_total = 0;
$total_tax = 0;

foreach ($products_name as $index => $product_name) {
    $amount = $products_price[$index] * $products_qty[$index];
    $gst = $products_gst[$index];
    $total_tax += $amount * $gst / 100;
    $sub_total += $amount;
    $cgst = $total_tax / 2;

    $html .= "<tr>
                <td align=\"center\" style=\"width:6%;\">{$serial_number}</td>
                <td align=\"left\">{$product_name}</td>
                <td align=\"center\">{$products_hsn[$index]}</td>
                <td align=\"center\">{$products_qty[$index]}</td>
                <td align=\"center\">{$products_unit[$index]}</td>
                <td align=\"right\">₹{$products_price[$index]}</td>
                <td align=\"right\">₹$amount</td>
              </tr>";
    $serial_number++;
}

$total = round($sub_total + $total_tax + (float)$invoice['shipping_charges'] - (float)$invoice['discount'], 0);
$total_in_words = convertNumberToIndianWords($total);

$html .= "<tr>
            <td colspan=\"2\" align=\"right\"><strong>Sub Total</strong></td>
            <td colspan=\"5\" align=\"right\">₹$sub_total</td>
          </tr>
          <tr>
            <td colspan=\"2\" align=\"right\"><strong>CGST</strong></td>
            <td colspan=\"5\" align=\"right\">₹$cgst</td>
          </tr>
          <tr>
            <td colspan=\"2\" align=\"right\"><strong>SGST</strong></td>
            <td colspan=\"5\" align=\"right\">₹$cgst</td>
          </tr>
          <tr>
            <td colspan=\"2\" align=\"right\"><strong>Shipping Charges</strong></td>
            <td colspan=\"5\" align=\"right\">₹{$invoice['shipping_charges']}</td>
          </tr>
          <tr>
            <td colspan=\"2\" align=\"right\"><strong>Discount</strong></td>
            <td colspan=\"5\" align=\"right\">₹{$invoice['discount']}</td>
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

foreach ($products_name as $index => $product_name) {
    $amount = $products_price[$index] * $products_qty[$index];
    $gst = $products_gst[$index];
    $total_tax += $amount * $gst / 100;
    $sub_total += $amount;
    $cgst = $total_tax / 2;
    $cgstper = $gst / 2;
    $cgstamt = $amount * $cgstper / 100;

    $html .= "<tr>
                  <td rowspan=\"1\" align=\"left\">{$products_hsn[$index]}</td>
                  <td rowspan=\"1\" align=\"right\">$amount</td>
                  <td>$cgstper%</td>
                  <td>$cgstamt</td>
                  <td>$cgstper%</td>
                  <td>$cgstamt</td>
                  <td>₹$total_tax</td>
              </tr>";
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

// Output PDF document
$customer_name = preg_replace('/[^A-Za-z0-9\-]/', '', $invoice['customer_name']);
$filename =  $customer_name . '_invoice.pdf';
$pdf->Output($filename, 'I');

// Save invoice details to database (optional, if you need to update something)
$update_query = "UPDATE invoice_details SET pdf_generated = 1 WHERE id = ?";
$update_stmt = $con->prepare($update_query);
$update_stmt->bind_param('i', $invoice_id);
$update_stmt->execute();

ob_end_flush();
