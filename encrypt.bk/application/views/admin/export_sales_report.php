<?php //echo '<pre>';print_r($wishlist);die;  ?>
<?php

include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Sales Report');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Order Id
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25); // Payee Name
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12); // Payee Phone
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25); // Item
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9); // Type
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30); // itmes included
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(24); // Base Price
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12); // Promocode
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14); // Discout Type
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18); // Discount Percentage
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14); // Discount Rate
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(18); // item_net_amount
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(9); // tax_type
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(16); // tax_percentage
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10); // tax_amount
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12); // Paid Amount
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(12); // payment_mode
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(18); // transaction_id
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(16); // Payment Status
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(16); // payment_gateway_used
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20); // payment_gateway_used



//processing excell rows
$excell_row = 1;

$report_title       = ucfirst($status);
$report_type        = '';
if($status == 'all')
{
    $report_title   = '';
}
if($type != 'all')
{
    $report_type   = ' '.ucfirst($type). ' wise ';
}
$date_filter = '';

if($startdate && $enddate)
{
    $date_filter = ' ( '.$startdate.' To '.$enddate.' )';
}
elseif ($startdate) 
{
    $date_filter = ' ( '.$startdate.' To '.date('Y-m-d').' )';
}
elseif ($enddate) 
{
    $date_filter = ' ( Till '.$enddate.' )';
}

$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, $report_title.$report_type.' Sales Report '.'(' . date('d-M-y g:i A') . ')');
$objPHPExcel->getActiveSheet()->mergeCells('A' . $excell_row . ':' . 'T' . $excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'T' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'T' . $excell_row)->getFont()->setSize(16);
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'T' . $excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);

$excell_row ++;
$num = 1;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, 'Sales Report '.' : '.count($orders).'. '.$date_filter);
$objPHPExcel->getActiveSheet()->mergeCells('A' . $excell_row . ':' . 'T' . $excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'T' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'T' . $excell_row)->getFont()->setSize(12);
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(28);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'T' . $excell_row)->applyFromArray($style);

$excell_row++;

$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, 'Order Id');
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, 'Payee Name');
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, 'Payee Phone');			
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, 'Item');
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);



$objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, 'Type');
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);


//----------
$objPHPExcel->getActiveSheet()->setCellValue('F' . $excell_row, 'Items Included');
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->applyFromArray($style);
//----------

$objPHPExcel->getActiveSheet()->setCellValue('G' . $excell_row, 'Base Price after Discount');
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('H' . $excell_row, 'Promocode');	
$objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->applyFromArray($style);





//------------------------------//


$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('I' . $excell_row, 'Discount Type');
$objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('J' . $excell_row, 'Discount Percentage');			
$objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('K' . $excell_row, 'Discount Rate');
$objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('L' . $excell_row, 'Item Net Amount');			
$objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('M' . $excell_row, 'Tax Type');
$objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->applyFromArray($style);




$objPHPExcel->getActiveSheet()->setCellValue('N' . $excell_row, 'Tax Percentage');
$objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('O' . $excell_row, 'Tax Amount');			
$objPHPExcel->getActiveSheet()->getStyle('O' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('O' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('O' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('P' . $excell_row, 'Paid Amount');
$objPHPExcel->getActiveSheet()->getStyle('P' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('P' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('P' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('Q' . $excell_row, 'Payment Mode');
$objPHPExcel->getActiveSheet()->getStyle('Q' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('Q' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('Q' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('R' . $excell_row, 'Transaction Id');		
$objPHPExcel->getActiveSheet()->getStyle('R' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('R' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('R' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('S' . $excell_row, 'Payment Status');
$objPHPExcel->getActiveSheet()->getStyle('S' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('S' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('S' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('T' . $excell_row, 'Payment Gateway');
$objPHPExcel->getActiveSheet()->getStyle('T' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('T' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('T' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('U' . $excell_row, 'Date & Time');
$objPHPExcel->getActiveSheet()->getStyle('U' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('U' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('U' . $excell_row)->applyFromArray($style);

//----//



//------------------------------//
$excell_row++;

foreach ($orders as $order) { //ph_item_type

    $items_included                 = '';
    if($order['ph_item_type'] == '2')
    {
        $items_include              = json_decode($order['ph_item_other_details'], true);
    
        $items_include              = isset($items_include['c_courses']) ? $items_include['c_courses'] : array(); 
        
        if(!is_array($items_include)){
            $items_include          = json_decode($items_include, true);
        }

        $items                      = array();
        if(!empty($items_include))
        {
            foreach($items_include as $item)
            {
                array_push($items, $item['course_name']);
            }
            $items_included             = implode(', ', $items);
        }
    }

    $ph_status              = $order['ph_status'] == '1' ? lang('complete') : lang('incomplete');
    $status_color           = $order['ph_status'] == '1' ? '7CFC00' : 'FF8C00';
    $base_price             = $order['ph_item_discount_price'] ? $order['ph_item_discount_price'] : $order['ph_item_base_price'];
    
    $ph_user_details        = json_decode($order['ph_user_details']);
    $phone                  = isset($ph_user_details->phone) ? $ph_user_details->phone : '';

    $ph_promocode           = json_decode($order['ph_promocode']);
    //print_r($ph_promocode);
    $promocode              = isset($ph_promocode->promocode) ? $ph_promocode->promocode : '';
    $discout_type           = isset($ph_promocode->discout_type) ? 'Percentage' : 'Flat Rate';
    $discount_percentage    = isset($ph_promocode->discount_percentage) ? $ph_promocode->discount_percentage : 0;
    $discount_rate          = isset($ph_promocode->discount_rate) ? $ph_promocode->discount_rate : 0;
    $item_net_amount        = isset($ph_promocode->item_net_amount) ? $ph_promocode->item_net_amount : 0;
    
    $ph_tax_objects         = json_decode($order['ph_tax_objects']);
    $sgst_percentage        = isset($ph_tax_objects->sgst->percentage) ? $ph_tax_objects->sgst->percentage : 0;
    $gst_amount             = isset($ph_tax_objects->sgst->amount) ? round($ph_tax_objects->sgst->amount) : 0;
    $cgst_percentage        = isset($ph_tax_objects->cgst->percentage) ? $ph_tax_objects->cgst->percentage : 0;
    $cgst_amount            = isset($ph_tax_objects->cgst->amount) ? round($ph_tax_objects->cgst->amount) : 0;
    $tax_percentage         = $sgst_percentage + $cgst_percentage;
    $tax_amount             = $gst_amount + $cgst_amount;
    
    if($item_net_amount == 0)
    {
        if($order['ph_item_amount_received'] > 0)
        {
            $item_net_amount        = $order['ph_item_amount_received'] - $tax_amount;
        }
        
    }
    $tax_type               = $order['ph_tax_type'] ? 'Exclusive' : 'Inclusive';

    $payment_mode           = '';//$order['ph_payment_mode'] == '1' ? 'Online'  : $order['ph_payment_mode'] == '2' ? 'Free' : 'Offline';
    if($order['ph_payment_mode'] == '1')
    {
        $payment_mode       = 'Online';
    }
    else if($order['ph_payment_mode'] == '2')
    {
        $payment_mode       = 'Free';
    }
    else
    {
        $payment_mode       = 'Offline';
    }
    $transaction_id         = $order['ph_transaction_id'];
    $payment_gateway_used   = $order['ph_payment_gateway_used']; 
    $ph_item_type           = $order['ph_item_type'] == '1' ? lang('course') : lang('bundle'); 
    
    $transaction_details    = json_decode($order['ph_transaction_details']);
    $payment_bank           = isset($transaction_details->bank) ? $transaction_details->bank : '';
    
    $num++;
    //echo '<pre>';print_r($report);die;
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, $order['ph_order_id'] );
    $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, $order['us_name']);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, $phone);
    $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, $order['ph_item_name']);	
    $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);


    $objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, $ph_item_type);	
    $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);



    $objPHPExcel->getActiveSheet()->setCellValue('F' . $excell_row, $items_included);
    $objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('G' . $excell_row, '₹ '.$base_price);	
    $objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('H' . $excell_row, $promocode); 	
    $objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->applyFromArray($style);



    //-----------------------------------//

    $objPHPExcel->getActiveSheet()->setCellValue('I' . $excell_row, $discout_type);
    $objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('J' . $excell_row, $discount_percentage.'%'); 
    $objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('K' . $excell_row, $discount_rate);
    $objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('L' . $excell_row, '₹ '.$item_net_amount);
    $objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('M' . $excell_row, $tax_type);
    $objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->applyFromArray($style);


    $objPHPExcel->getActiveSheet()->setCellValue('N' . $excell_row, $tax_percentage.'%');
    $objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->applyFromArray($style);


    $objPHPExcel->getActiveSheet()->setCellValue('O' . $excell_row, '₹ '.$tax_amount);
    $objPHPExcel->getActiveSheet()->getStyle('O' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('O' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('P' . $excell_row, '₹ '.$order['ph_item_amount_received']);
    $objPHPExcel->getActiveSheet()->getStyle('P' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('P' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('Q' . $excell_row, $payment_mode);
    $objPHPExcel->getActiveSheet()->getStyle('Q' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('Q' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('R' . $excell_row, $transaction_id);
    $objPHPExcel->getActiveSheet()->getStyle('R' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('R' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('S' . $excell_row, $ph_status);	
    $objPHPExcel->getActiveSheet()->getStyle('S' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => $status_color)
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('S' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('T' . $excell_row, $payment_gateway_used);
    $objPHPExcel->getActiveSheet()->getStyle('T' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('T' . $excell_row)->applyFromArray($style);



    $objPHPExcel->getActiveSheet()->setCellValue('U' . $excell_row, $order['ph_payment_date']);
    $objPHPExcel->getActiveSheet()->getStyle('U' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('U' . $excell_row)->applyFromArray($style);



    //------------------------------------//


    $excell_row++;
}


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Sales_report_' . date('d_M_y-h_i_s') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>