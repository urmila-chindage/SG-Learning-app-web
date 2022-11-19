 <?php  ob_start(); 
//echo '<pre>'; print_r($share);die;

// Include the main TCPDF library (search for installation path).
require_once('TCPDF/tcpdf_include.php');
require_once('TCPDF/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($this->config->item('site_name'));
$pdf->SetTitle('INVOICE for payment id '.$share['ph_order_id']);
//echo PDF_HEADER_STRING;die;
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, 20, 'INVOICE for payment '.$share['ph_order_id'], ' by '.$this->config->item('site_name').' - www.onlineprofesor.com ');

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

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content
$html = '<h1>Invoice for the payment </h1>'
        . '<table>'
        . ' <tbody>'
        . ' <tr><td>Payment ID</td><td>'.$share['ph_order_id'].'</td></tr>'
        . ' <tr><td>Payment Date</td><td>'.$share['ph_payment_date'].'</td></tr>'
        . '</tbody>'
        . '</table><br /><br />'
        . '<table border="1" cellpadding="6">'
        . '<thead><tr align="center"><th colspan="2"><b>Particulars</b></th></tr></thead>'
        . '<tbody>'
        . ' <tr><td>Invoice Number</td><td>'.$share['ph_order_id'].'</td></tr>'
        . ' <tr><td>Invoice Date</td><td>'.$share['ph_payment_date'].'</td></tr>'
        . ' <tr><td>Purchased By</td><td>'.$share['student_name'].'</td></tr>'
        . ' <tr><td>Course Name</td><td>'.$share['cb_title'].'</td></tr>'
        . ' <tr><td>Amount Paid</td><td>'.$share['payed_amount'].'</td></tr>'
        . ' <tr><td>Course Price</td><td>'.$share['ph_course_price'].'</td></tr>'
        . ' <tr><td>Discount Price</td><td>'.$share['ph_course_discount'].'</td></tr>'
        . ' <tr><td>Teacher Share</td><td>'.round($share['teacher_share'], 2).'</td></tr>'
        . '</tbody>'
        . '</table><br /><br />';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


//Close and output PDF document
$pdf->Output($share['ph_order_id'].'.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
