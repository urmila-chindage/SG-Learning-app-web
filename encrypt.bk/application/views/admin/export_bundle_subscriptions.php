<?php

include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Bundle subscription report');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
//$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);

//processing excell rows
$excell_row = 1;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, $report_title.' (' . date('d-M-y g:i A') . ')');
$objPHPExcel->getActiveSheet()->mergeCells('A' . $excell_row . ':' . 'G' . $excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'G' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'G' . $excell_row)->getFont()->setSize(16);
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'G' . $excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);

$excell_row ++;
$num = 1;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, 'Total Bundle subscriptions '.' : '.count($enrolled_users));
$objPHPExcel->getActiveSheet()->mergeCells('A' . $excell_row . ':' . 'G' . $excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'G' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'G' . $excell_row)->getFont()->setSize(12);
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(28);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'G' . $excell_row)->applyFromArray($style);

$excell_row++;

$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, 'Student Name');
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, 'Phone');
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, 'Email');
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, 'Amount received');
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, 'Payment mode');
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('F' . $excell_row, 'Subscription Date');
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('G' . $excell_row, 'Enrolled by');
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->applyFromArray($style);


$excell_row++;
foreach ($enrolled_users as $enrolled_user) {


    $cb_details                 = json_decode($enrolled_user['bs_payment_details'], true);

    $ph_user_details            = json_decode($cb_details['ph_user_details'], true);

    $enrolled_by                = isset($ph_user_details['enrolled_by']['us_name']) ? $ph_user_details['enrolled_by']['us_name'] : 'Self';

    $ph_payment_mode            = isset($cb_details['ph_payment_mode']) ? $cb_details['ph_payment_mode'] : '';

    $payment_mode               = '';//$ph_payment_mode == '1' ? 'Online' : $ph_payment_mode == '2' ? 'Free' : 'Offline';
    if($ph_payment_mode == '1')
    {
        $payment_mode       = 'Online';
    }
    else if($ph_payment_mode == '2')
    {
        $payment_mode       = 'Free';
    }
    else
    {
        $payment_mode       = 'Offline';
    }
    $received_amount = $cb_details['ph_item_amount_received'];//isset($payment_details->ph_item_amount_received) ? $payment_details->ph_item_amount_received : '0';
    //'7CFC00' : 'FF8C00';
    
    $num++;
    //echo '<pre>';print_r($report);die;
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, $enrolled_user['us_name'] );
    $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, $enrolled_user['us_phone']);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, $enrolled_user['us_email']);
    $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, $received_amount);
    $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, $payment_mode);
    $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('F' . $excell_row, $enrolled_user['bs_subscription_date']);
    $objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        // ),
        // 'fill' => array(
        //     'type' => PHPExcel_Style_Fill::FILL_SOLID,
        //     'color' => array('rgb' => $status_color)
         )
    );
    $objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('G' . $excell_row, $enrolled_by);
    $objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->applyFromArray($style);


   


    $excell_row++;
}
//print_r($objPHPExcel);
//die;
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$cb_title.'_subscription_report_'.date('d_M_y-h_i_s') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>