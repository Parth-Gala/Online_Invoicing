<?php
// Ensure no output is sent before PDF generation
ob_start();

// Include the TCPDF library
require_once('tcpdf/tcpdf.php');

include('include/connect.php');

$challan_number = $_POST['debit_note'];
$date = $_POST['date'];
$customer_id = $_POST['customer_id'];
$products = $_POST['products'];

$customer_query = "SELECT * FROM customer_details WHERE id = $customer_id";
$customer_result = $con->query($customer_query);
$customer = $customer_result->fetch_assoc();

$user_query = "SELECT * FROM user_details WHERE id = 1";
$user_result = $con->query($user_query);
$user = $user_result->fetch_assoc();

$pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('dejavusans', '', 10);
$pdf->AddPage('P', 'A5');

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


$html = <<<EOD
<h2 style="text-align:center;">Debit Note</h2>
    <table align = "left" border="1" cellpadding="4">
        <tr>
            <td>Debit Note Number: $challan_number </td>
            <td>Date: $date</td>
        </tr>
    </table>

<table border="1" cellpadding="5">
    <tr>
        <td>
            <strong>From:</strong><br>
            {$user['name']}<br>
            {$user['address']}<br>
            {$customer['city']} - {$customer['zipcode']}<br>
            {$customer['state']}<br>
            GST No: {$user['gst']}<br>
        </td>
        <td>
            <strong>To:</strong><br>
            {$customer['c_name']}<br>
            {$customer['c_address']}<br>
            {$customer['city']} - {$customer['zipcode']}<br>
            {$customer['state']}<br>
            GST No: {$customer['gst']}
        </td>
    </tr>
</table>
<br><br>
<table border="1" cellpadding="2">
    <tr style="background-color:#eee;">
        <th>Sr.No </th>
        <th>Product Name</th>
        <th>HSN/SAC</th>
        <th>Qty</th>
        <th>Unit</th>
        <th>Price</th>
        <th>Amount</th>
    </tr>
EOD;

$total = 0;
$serial_number = 1;
foreach ($products as $product_id => $product) {
    if (!empty($product['quantity'])) {
        $product_query = "SELECT * FROM product_details WHERE id = $product_id";
        $product_result = $con->query($product_query);
        $product_details = $product_result->fetch_assoc();

        $amount = $product_details['total_price'] * $product['quantity'];
        $total += $amount;

        $html .= "<tr>
                    <td>{$serial_number}</td>
                    <td>{$product_details['p_name']}</td>
                    <td>{$product_details['hsn_code']}</td>
                    <td>{$product['quantity']}</td>
                    <td>{$product_details['unit']}</td>
                    <td>₹{$product_details['total_price']}</td>
                    <td>₹$amount</td>
                </tr>";
    }
}

$total_in_words = convertNumberToIndianWords($total);
$html .= "<tr>
            <td colspan=\"5\" align=\"right\"><strong>Total</strong></td>
            <td>₹$total</td>
        </tr>
        <tr>
            <td colspan=\"6\" align=\"left\"><span><strong>In Words:</strong></span> <span> $total_in_words Only</span></td>
        </tr>
    </table>
    <table border=\"1\" cellpadding=\"5\">
         <tr>
             <td>This receipt should be signed by the person having authority and return it to bearer</td>
             <td align=\"right\"><strong>Authorised Signature</strong></td>
         </tr>
    </table>";

$pdf->writeHTML($html, true, false, true, false, '');

ob_end_clean();
$customer_name = preg_replace('/[^A-Za-z0-9\-]/', '', $customer['c_name']);
$filename = $customer_name . '_debitnote.pdf';
$pdf->Output($filename, 'I');

$con->close();
