<?php  ob_start(); 
// Include the main TCPDF library (search for installation path).
require_once('TCPDF/tcpdf_include.php');
// echo '<pre>'; print_r($order);die;
$user       = json_decode($order['ph_user_details'], true);
$taxes      = json_decode($order['ph_tax_objects'], true);
$promocode  = json_decode($order['ph_promocode'], true);
$site_contact_settings  = $this->settings->setting('website');
$site_contact_details   = $site_contact_settings['as_setting_value'];
$contacts               = $site_contact_details['setting_value'];
//$itemBasePrice          = ($order['ph_item_base_price'] > $order['ph_item_discount_price'])?$order['ph_item_discount_price']:$order['ph_item_base_price'];
$item_price             = ($order['ph_item_discount_price'] > 0) ? $order['ph_item_discount_price']:$order['ph_item_base_price'];

$ph_tax_objects = json_decode($order['ph_tax_objects']);

//print_r($ph_tax_objects);
//print_R($order['ph_tax_objects']);die;

$sgst_tax               = $ph_tax_objects->sgst->percentage;
$cgst_tax               = $ph_tax_objects->cgst->percentage;

$sgst_tax_amount        = $ph_tax_objects->sgst->amount;
$cgst_tax_amount        = $ph_tax_objects->cgst->amount;
$total_tax_amount       = $sgst_tax_amount + $cgst_tax_amount;
$total_tax              = $sgst_tax + $cgst_tax;

$sgst                   = floatval($sgst_tax);
$cgst                   = floatval($cgst_tax);
$item_price             = floatval($item_price);

//do not use any where starts
$coursePriceWithoutPromo = $item_price;
$ph_item_amount_received = $order['ph_item_amount_received'];
//do not use any where ends

if(count($promocode) > 0)
{
    $item_price         = $item_price - $promocode['discount_rate'];
    //$ph_item_amount_received = $ph_item_amount_received - $item_price;
}

//echo $item_price;die;
if($order['ph_tax_type'] == '1')
{
    $taxedAmount    = $item_price;
    $totalAmountPid = $item_price + $total_tax_amount;
}
else 
{
     $taxedAmount        = $item_price / (100 + $total_tax) * 100;
     $total_tax_amount   = $item_price / (100 + $total_tax) * $total_tax;
     $totalAmountPid     = $total_tax_amount + $taxedAmount;
}
//$subtotal = $taxedAmount - $ph_item_amount_received;
//echo $taxedAmount;die;
//echo $taxedAmount;die;
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($this->config->item('site_name'));
$pdf->SetTitle('INVOICE for Order '.$order['ph_order_id']);
//echo PDF_HEADER_STRING;die;
// set default header data
$site_url = site_url();
$site_url = explode('/',$site_url);
$pdf->SetHeaderData(
                    logo_upload_path().$this->config->item('site_logo'), 
                    20, 
                    'INVOICE for Order '.$order['ph_order_id'], ' by '.$this->config->item('site_name').' - '.$site_url[2]
                );

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();
$html = '
        <table style="font-family:arial;" cellpadding="3">
            <tr>
                <td colspan="3"><p style="text-align: left;color:#7c56d1; font-size:20px;">'.config_item('site_name').'</p></td>
            </tr>
            <tr>
                <td colspan="3"></td>
            </tr>
            <tr style="text-align: left; font-weight:bold; line-height:25px;">
                <th>Adress</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
            <tr style="color:#6a6868">
                <td style="line-height:14px;">'.$contacts->site_address.'</td>
                <td style="line-height:25px;">'.$contacts->site_email.'</td>
                <td style="line-height:25px;">'.$contacts->site_phone.'</td>
            </tr>
        </table>

        <br>
        <br>
        <br>
        <table style="font-family:arial; border-bottom:1px solid #ccc;" cellpadding="3">
            <tr>
                <td colspan="3"><p style="font-size:35px; font-weight:bold; text-align:left; color:#173289;">Invoice</p></td>
            </tr>
            <tr>
                <td colspan="3"><p style="text-align: left;color:#e01b84;font-size:17px;">Submitted on '.$order['payment_date'].'</h3></td>
            </tr>
            <tr>
                <td colspan="3"></td>
            </tr>
            <tr style="text-align: left; font-weight:bold; line-height:25px;">
                <th>Invoice for</th>
                <th>Payable to</th>
                <th>Invoice #</th>
            </tr>
            <tr style="color:#6a6868">
                <td>'.$user['name'].'</td>
                <td>'.config_item('site_name').'</td>
                <td>'.$order['ph_order_id'].'</td>
            </tr>
        <tr>
            <td style="color:#6a6868">'.$user['email'].'</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td style="color:#6a6868">'.$user['phone'].'</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </table>
        <table style="font-family:arial;border-bottom:3px solid #ccc;" cellpadding="5">
            <tr style="color:#284191; font-weight:bold; font-size:14px; line-height:30px;">
                <td align="left">
                    <p>Item Name</p>
                </td>
                <td align="right">
                    <p>Item price</p>
                </td>
            </tr>
            <tr style="background-color: #eee;">
                <td align="left" style="color:#6a6868;">'.$order['ph_item_code'].' - '.$order['ph_item_name'].'</td>
                <td align="right" style="color:#6a6868;">&#8377; '.$coursePriceWithoutPromo.'</td>
            </tr>';

        if(count($promocode) > 0)
            {
            $html .=  '
                <tr>
                    <td align="left" style="color:#284191;">Discounts applied</td>
                    <td style="font-weight:bold;color:#6a6868;" align="right">-&#8377; '.round($promocode['discount_rate'], 2).' </td>
                </tr>';
            }

        $html .= '<tr style="background-color: #fff; color:#6a6868;">
                <td></td>
                <td align="right"></td>
            </tr>
            <tr style="background-color: #fff; color:#6a6868;">
                <td></td>
                <td align="right"></td>
            </tr>
        </table>
        <table style="font-family:arial;" cellpadding="5">';
        if($order['ph_tax_type'] =='1')
        {
        $html .= '  
                <tr>
                    <td style="color:#6a6868;"></td>
                    <td></td>
                    <td align="right" style="color:#284191;">Subtotal</td>
                    <td style="font-weight:bold;" align="right">&#8377; '.round($item_price, 2).'</td>
                </tr>';
        }
        

        if($order['ph_tax_type'] =='1')
        {
                $html .= ' 
                <tr>
                    <td></td>
                    <td></td>
                    <td align="right" style="color:#284191;">GST @ (<i>'.$total_tax.'%)</i></td>
                    <td style="font-weight:bold;color:#6a6868;" align="right">'.round($total_tax_amount, 2).'</td>
                </tr>';
        }
        $html .= '<tr>
                    <td></td>
                    <td></td>
                    <td align="right" style="color:#284191;">Total</td>
                    <td style="font-weight:bold; font-size:18px; color: #e01b84;" align="right">&#8377; '.round($ph_item_amount_received, 2).'</td>
                </tr>
        

        </table>
        
        <br>
        <br>
        
        <table style="font-family:arial;" cellpadding="5">
        
            <tr style="">
                <td align="left" style="color:#6a6868;"><b>Taxable amount</b></td>
                <td align="" style="color:#6a6868;"><b>GST(<i>'.$total_tax.'%</i>)</b></td>
                <td align="right" style="color:#6a6868;"><b>Total</b></td>
            </tr>

            <tr style="background-color: #eee;">
                <td align="left" style="color:#6a6868;">&#8377; '.round($taxedAmount, 2).'</td>
                <td align="" style="color:#6a6868;">&#8377; '.round($total_tax_amount, 2).'</td>
                <td align="right" style="color:#6a6868;">&#8377; '.$totalAmountPid.'</td>
            </tr>
  
        </table>
        
        ';
//border-bottom:3px solid #ccc;
// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
/*
// create some HTML content
$html = '<h1>Invoice for the payment </h1>'
        . '<table>'
        . ' <tbody>'
        . ' <tr><td>Order ID</td><td>'.$order['ph_order_id'].'</td></tr>'
        . ' <tr><td>Payment Date</td><td>'.$order['ph_payment_date'].'</td></tr>'
        . '</tbody>'
        . '</table><br /><br />'
        . '<table border="1" cellpadding="6">'
        . '<thead><tr align="center"><th colspan="2"><b>Particulars</b></th></tr></thead>'
        . '<tbody>'
        . ' <tr><td>Order Number</td><td>'.$order['ph_order_id'].'</td></tr>'
        . ' <tr><td>Invoice Date</td><td>'.$order['ph_payment_date'].'</td></tr>'
        . ' <tr><td>Purchased By</td><td>'.$user['name'].'</td></tr>'
        . ' <tr><td>Item Name</td><td>'.$order['ph_item_name'].'</td></tr>'
        
        . ' <tr><td>Item Base Price</td><td >&#8377; '.$order['ph_item_base_price'].'/-</td></tr>'
       
        . '</tbody>'
        . '</table>'
        . '<br /><br />'
        . '<table border="1" cellpadding="6">'
        . '<thead><tr align="center"><th colspan="2"><b>Payment Details</b></th></tr></thead>'
        . '<tbody>';
        if(!empty($order['ph_item_discount_price'])){
            $html .=  ' <tr><td>Item Discount Price</td><td>&#8377; '.$order['ph_item_discount_price'].' /-</td></tr>';
        }
        if(count($promocode) > 0){
            $html .=  ' <tr><td>Promocode Discount Rate</td><td>&#8377; '.$promocode['discount_rate'].' /-</td></tr>';
        }
        if($order['ph_tax_type'] == '1'){
            $total_tax          = $taxes['sgst']['percentage']+$taxes['cgst']['percentage'];
            $total_tax_amount   = $taxes['cgst']['amount'] + $taxes['sgst']['amount'];
            if(($total_tax_amount) > 0){
                
                $html .=  ' <tr><td>GST @ (<i>'.$total_tax.'%)</i></td><td>&#8377; '.$total_tax_amount.'/-</td></tr>';
                if(($taxes['sgst']['amount']) > 0){
                    $html .=  ' <tr><td style="text-indent: 20px;">-SGST (<i>'.$taxes['sgst']['percentage'].'%</i>)</td><td>&#8377; '.$taxes['sgst']['amount'].' /-</td></tr>';
                }
                if(($taxes['cgst']['amount']) > 0){
                    $html .=  ' <tr><td style="text-indent: 20px;">-CGST (<i>'.$taxes['cgst']['percentage'].'%</i>)</td><td>&#8377; '.$taxes['cgst']['amount'].' /-</td></tr>';
                }
            }
            
        }
        $html .=' <tr><td>Total Amount Paid <br><small>(inclusive of all taxes)</small></td><td>&#8377; '.$order['ph_item_amount_received'].' /-</td></tr>';
        
        
        
        $html .= '</tbody></table>';

*/
// output the HTML content
// echo $html;die;
$pdf->writeHTML($html, true, false, true, false, '');


//Close and output PDF document
$pdf->Output($order['ph_order_id'].'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+