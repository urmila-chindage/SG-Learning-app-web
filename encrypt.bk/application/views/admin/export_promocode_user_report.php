<?php //echo '<pre>';print_r($wishlist);die;  ?>
<?php

include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Discount Coupon Usage Report');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);


//processing excell rows
$excell_row = 1;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, strtoupper($promocode_name).' - Discount Coupon Usage Report (' . date('d-M-y g:i A') . ')');
$objPHPExcel->getActiveSheet()->mergeCells('A' . $excell_row . ':' . 'F' . $excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'F' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'F' . $excell_row)->getFont()->setSize(16);
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'F' . $excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);

$excell_row ++;
$num = 1;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, ' Total Usage '.' : '.count($user_reports));
$objPHPExcel->getActiveSheet()->mergeCells('A' . $excell_row . ':' . 'F' . $excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'F' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'F' . $excell_row)->getFont()->setSize(12);
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(28);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'F' . $excell_row)->applyFromArray($style);

$excell_row++;

$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, 'Name');
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, 'Email');
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, 'Mobile');
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, 'Item Type');
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, 'Item Name');
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('F' . $excell_row, 'Applied On');
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->applyFromArray($style);
$excell_row++;


foreach ($user_reports as $user_report) {
    
    $num++;
    //echo '<pre>';print_r($report);die;
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, isset($user_report['name'])?$user_report['name']:'');
    $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, isset($user_report['email'])?$user_report['email']:'');
    $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, isset($user_report['phone'])?$user_report['phone']:'');
    $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, isset($user_report['itemType'])?$user_report['itemType']:'');
    $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, isset($user_report['itemName'])?$user_report['itemName']:'');
    $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('F' . $excell_row, isset($user_report['applied_on'])?$user_report['applied_on']:'');
    $objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setSize(10);
    $style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->applyFromArray($style);

    $excell_row++;
}


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Discount_Coupon_Users_' . date('d_M_y-h_i_s') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>