<?php
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet 1
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Institutes');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);

//processing excell header rows
$excell_row     = 1;			
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $this->config->item('site_name').' - INSTITUTES ('.date('d-M-y g:i A').')');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.'L'.$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'L'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'L'.$excell_row)->getFont()->setSize(16);

$style_center = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    )
);
$style_left = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);


$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'L'.$excell_row)->applyFromArray($style_left);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);
$excell_row ++;
//End of processing excell header rows



$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, 'Code');
$objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row, 'Name');
$objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row, 'Phone Number');
$objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row, 'Address');
$objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row, 'Head Name');
$objPHPExcel->getActiveSheet()->setCellValue('F'.$excell_row, 'Head Email');
$objPHPExcel->getActiveSheet()->setCellValue('G'.$excell_row, 'Head Phone');
$objPHPExcel->getActiveSheet()->setCellValue('H'.$excell_row, 'Officer Name');
$objPHPExcel->getActiveSheet()->setCellValue('I'.$excell_row, 'Officer Email');
$objPHPExcel->getActiveSheet()->setCellValue('J'.$excell_row, 'Officer Phone');
$objPHPExcel->getActiveSheet()->setCellValue('K'.$excell_row, 'Classroom Code');
$objPHPExcel->getActiveSheet()->setCellValue('L'.$excell_row, 'Room Strength');

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'L'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'L'.$excell_row)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'L'.$excell_row)->applyFromArray($style_center);
$excell_row++;

$objPHPExcel->getActiveSheet()->getStyle($excell_row)->getAlignment()->setWrapText(true);

if(!empty($institutes))
{
    foreach($institutes as $institute)
    {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $institute['ib_institute_code']);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row, $institute['ib_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row, $institute['ib_phone']);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row, $institute['ib_address']);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row, $institute['ib_head_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$excell_row, $institute['ib_head_email']);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$excell_row, $institute['ib_head_phone']);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$excell_row, $institute['ib_officer_name']);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$excell_row, $institute['ib_officer_email']);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$excell_row, $institute['ib_officer_phone']);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$excell_row, $institute['ib_class_code']);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$excell_row, $institute['ib_class_strength']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'K'.$excell_row)->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(75);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style_center);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row.':'.'L'.$excell_row)->applyFromArray($style_left);
        $objPHPExcel->getActiveSheet()->getStyle('J'.$excell_row.':'.'L'.$excell_row)->applyFromArray($style_center);
        $excell_row++;
    }
}

// echo '<pre>'; print_r($institutes);die;
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Institutes_'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

?>