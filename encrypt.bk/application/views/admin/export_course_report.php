<?php //echo '<pre>';print_r($wishlist);die; ?>
<?php 
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($this->config->item('site_name').' - Course Report');

//processing excell rows
$excell_row     = 1;
					
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$this->config->item('site_name').' - Course Report ('.date('d-M-y g:i A').')');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.num2alpha(count($lectures)+2).$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($lectures)+2).$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($lectures)+2).$excell_row)->getFont()->setSize(16);
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($lectures)+2).$excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);

$excell_row ++;
$student_text = (count($subscribers)>1)?'Students':'Student';
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$selected_course.' ('.count($subscribers).' '.$student_text.')');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.num2alpha(count($lectures)+2).$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($lectures)+2).$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($lectures)+2).$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($lectures)+2).$excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(22);

$excell_row ++;

$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(150);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,'');
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->getColumnDimension(num2alpha(1))->setWidth(5);
$objPHPExcel->getActiveSheet()->setCellValue(num2alpha(1).$excell_row,'% Completed');
$objPHPExcel->getActiveSheet()->getStyle(num2alpha(1).$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle(num2alpha(1).$excell_row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle(num2alpha(1).$excell_row)->getAlignment()->setTextRotation(90);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle(num2alpha(1).$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->getColumnDimension(num2alpha(2))->setWidth(3);
$objPHPExcel->getActiveSheet()->setCellValue(num2alpha(2).$excell_row,'Grade');
$objPHPExcel->getActiveSheet()->getStyle(num2alpha(2).$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle(num2alpha(2).$excell_row)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle(num2alpha(2).$excell_row)->getAlignment()->setTextRotation(90);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle(num2alpha(2).$excell_row)->applyFromArray($style);


$excell_cell =3;
foreach ($lectures as $lecture_key => $lecture){
    $objPHPExcel->getActiveSheet()->getColumnDimension(num2alpha($excell_cell))->setWidth(3);
    $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($excell_cell).$excell_row,$lecture['cl_lecture_name']);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getAlignment()->setTextRotation(90);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->applyFromArray($style);
    $excell_cell++;
}

$excell_row++;

foreach ($subscribers as $s_key => $subscriber){
    $excell_cell =0;

    $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(18);
    $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($excell_cell).$excell_row,$subscriber['us_name']);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setSize(10);
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setBold(true);
    $style = array(
            'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->applyFromArray($style);

    $excell_cell++;
    $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($excell_cell).$excell_row,$subscriber['completed_percentage'] != ''?$subscriber['completed_percentage'].'%':'-');
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->applyFromArray($style);

    $excell_cell++;

    $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($excell_cell).$excell_row,$subscriber['marks_percentage'] != ''?convert_to_grade($subscriber['marks_percentage']):'-');
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->applyFromArray($style);

    foreach($subscriber['lectures'] as $ulecture){
        $excell_cell++;
        if($ulecture['ll_percentage_new']>96){
            $objPHPExcel->getActiveSheet()->getStyle(num2alpha($excell_cell).$excell_row)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '33ff42')
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => 'DDDDDD')
                        )
                    )
                )
            );
        }
    }


    $excell_row++;
}



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

    function grade()
    {
        $grade    = array();
        $grade[0] = 'E';
        $grade[1] = 'E';
        $grade[2] = 'D';
        $grade[3] = 'D+';
        $grade[4] = 'C';
        $grade[5] = 'C+';
        $grade[6] = 'B';
        $grade[7] = 'B+';
        $grade[8] = 'A';
        $grade[9] = 'A+';
        $grade[10] = 'A+';
        return $grade;
    }
    function convert_to_grade($grade_percentage)
    {
        $grade_percentage = floor($grade_percentage/10);
        $grade = grade();
        return (isset($grade[$grade_percentage])?$grade[$grade_percentage]:'-');
    }
?>