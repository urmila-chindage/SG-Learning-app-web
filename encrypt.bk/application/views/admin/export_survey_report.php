<?php 
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

// echo "<pre>";print_r($tutor_name);exit;
//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Survey Report');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
//processing excell rows
$excell_row     = 1;
      

$report_name    = 'Survey Report';
$report_main_title  = 'Survey Name - '.$survey_name.PHP_EOL;
if($tutor_report == true)
{
    $report_name    = 'Tutor Performance Report';
    $report_main_title .= 'Tutor Name - '.$tutor_name.PHP_EOL;
}
$report_main_title .= 'Date - '.date('d-M-y g:i A');
$report_main_title = $report_name.' '.$report_main_title;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $report_main_title);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.num2alpha(count($questions)+2).$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($questions)+2).$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($questions)+2).$excell_row)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($questions)+2).$excell_row)->getAlignment()->setWrapText(true);
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($questions)+2).$excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(100);

// next row
$excell_row ++;

$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(150);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,'Student Name / Question');
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);



$excell_cell =1;
foreach ($questions as $question){
    $objPHPExcel->getActiveSheet()->getColumnDimension(num2alpha($excell_cell))->setWidth(3);
    $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($excell_cell).$excell_row,$question);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getAlignment()->setWrapText(true);
    //$objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getAlignment()->setTextRotation(90);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->applyFromArray($style);
    $excell_cell++;
}


// next row 

$excell_row++;

foreach ($reports as $student_name => $answers){
    $excell_cell =0;

    $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(18);
    $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($excell_cell).$excell_row,$student_name);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setBold(true);
    $style = array(
            'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->applyFromArray($style);

    foreach($answers as $answer){
        $excell_cell++;
            // $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->applyFromArray(
            //     array(  )
            // );
            $objPHPExcel->getActiveSheet()->getColumnDimension(num2alpha($excell_cell))->setWidth(20);
            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($excell_cell).$excell_row,$answer);
    }


    $excell_row++;
}


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$survey_name.'-'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

    function num2alpha($n)
    {
        for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n%26 + 0x41) . $r;
        return $r;
    }
