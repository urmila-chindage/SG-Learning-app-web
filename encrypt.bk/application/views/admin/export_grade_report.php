<?php 
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Grade Report');

//processing header name row
$excell_row     = 1;    
$total_lectures = count($lectures);
$total_column   = num2alpha($total_lectures+2);

// '  - '.$selected_course.' ('.date('d-M-y g:i A').')'
$report_main_title  = 'Grade Report'.PHP_EOL;
$report_main_title .= $selected_course.PHP_EOL;
$report_main_title .= 'Date - '.date('d-M-y g:i A');

$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $report_main_title);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.$total_column.$excell_row);

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$total_column.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$total_column.$excell_row)->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$total_column.$excell_row)->getAlignment()->setWrapText(true);

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$total_column.$excell_row)->applyFromArray(array(
                                                                                                            'alignment' => array(
                                                                                                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                                                                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                                                            )
                                                                                                        ));
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(100);
//end of processing header name row

//processing sub header row
$excell_row++; // value is 2
$total_subscribers = count($subscribers);
$student_text      = ( $total_subscribers > 1 )?'Students':'Student';
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $selected_course.' ('.$total_subscribers.' '.$student_text.')'.' / '.$selected_institution);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.$total_column.$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$total_column.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$total_column.$excell_row)->getFont()->setSize(10);

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$total_column.$excell_row)->applyFromArray(array(
                                                                                                            'alignment' => array(
                                                                                                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                                                                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                                                            )
                                                                                                        ));
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(22);
//end of processing sub header row


//processing header completion, grade and lecture names
$excell_row++;//value is 3
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(150);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, '');
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray(array(
                                                                            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                            )
                                                                        ));

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(3);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(3);
$objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row,'% Completed');
$objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row,'Grade');

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'C'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'C'.$excell_row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'C'.$excell_row)->getAlignment()->setTextRotation(90);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'C'.$excell_row)->applyFromArray(array(
                                                                            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                                                                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                                                                            )
                                                                        ));
$excell_cell = 'C';
foreach ($lectures as $lecture_key => $lecture)
{
    $excell_cell++;
    $objPHPExcel->getActiveSheet()->getColumnDimension($excell_cell)->setWidth(3);
    $objPHPExcel->getActiveSheet()->setCellValue($excell_cell.$excell_row,$lecture['cl_lecture_name']);
}

$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row.':'.$excell_cell.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row.':'.$excell_cell.$excell_row)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row.':'.$excell_cell.$excell_row)->getAlignment()->setTextRotation(90);
$objPHPExcel->getActiveSheet()->getStyle($excell_cell.$excell_row)->applyFromArray(array(
                                                                                    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                                                                                    )
                                                                                ));
//end of processing header completion, grade and lecture names




//Processing excell body 

$style_vertical = array(
    'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
    )
);

$style_center = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    )
);

foreach ($subscribers as $s_key => $subscriber)
{
    $lecture_log = (($subscriber['cs_lecture_log']!=NULL)||($subscriber['cs_lecture_log']!=''))?json_decode($subscriber['cs_lecture_log'], TRUE):array();
    $excell_row++; // value is 4 and increase on each loop
    $excell_cell = 'A';
    $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(18);
    $objPHPExcel->getActiveSheet()->setCellValue($excell_cell.$excell_row, $subscriber['cs_user_name']);
    $objPHPExcel->getActiveSheet()->getStyle($excell_cell.$excell_row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle($excell_cell.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle($excell_cell.$excell_row)->applyFromArray($style_vertical);

    $excell_cell++;//value is B
    $objPHPExcel->getActiveSheet()->setCellValue($excell_cell.$excell_row, ($subscriber['cs_percentage'] != '' && $subscriber['cs_percentage'] > 0)?$subscriber['cs_percentage'] :'-');
    $objPHPExcel->getActiveSheet()->getStyle($excell_cell.$excell_row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle($excell_cell.$excell_row)->applyFromArray($style_center);

    $excell_cell++;//value is C
    //$subscriber_grade    = ($subscriber['cs_auto_grade'] != '' && $subscriber['cs_auto_grade'] != '-' )?$subscriber['cs_auto_grade']:$subscriber['cs_manual_grade'];
    $subscriber_grade = ($subscriber['cs_manual_grade'] == '-')?(($subscriber['cs_auto_grade']!=null)?$subscriber['cs_auto_grade']:'-'):$subscriber['cs_manual_grade'];
    $objPHPExcel->getActiveSheet()->setCellValue($excell_cell.$excell_row, $subscriber_grade);
    $objPHPExcel->getActiveSheet()->getStyle($excell_cell.$excell_row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle($excell_cell.$excell_row)->applyFromArray($style_center);
    
    foreach($lectures as $lecture)
    {
        $grade       = isset($lecture_log[$lecture['id']])?$lecture_log[$lecture['id']]:'-';
        $grade       = isset($grade['grade'])?$grade['grade']:'-';
        $excell_cell++;// value is D and increase on each loop
        $objPHPExcel->getActiveSheet()->setCellValue($excell_cell.$excell_row,$grade);
    }
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row.':'.$excell_cell.$excell_row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row.':'.$excell_cell.$excell_row)->applyFromArray($style_center);
}
//End of Processing excell body

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$selected_course.'-'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

    function num2alpha($n)
    {
        for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n%26 + 0x41) . $r;
        return $r;
    }
?>