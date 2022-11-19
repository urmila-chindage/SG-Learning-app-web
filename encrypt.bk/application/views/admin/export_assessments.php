<?php
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($this->config->item('site_name'));

//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
//processing excell rows
$excell_row = 1;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$this->config->item('site_name').' - Quiz Report ('.date('d-M-y g:i A').')');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.'D'.$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'D'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'D'.$excell_row)->getFont()->setSize(16);
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'D'.$excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);

$excell_row ++;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $course.' : '.$lecture);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.'D'.$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'D'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'D'.$excell_row)->getFont()->setSize(12);
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(28);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'D'.$excell_row)->applyFromArray($style);

$excell_row++;
/*$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,'Phone Number');
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);*/

$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,'Name');
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row,'Submitted Date');
$objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row,'Duration');
$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row,'Marks');
$objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row,'Grade');
$objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->applyFromArray($style);

$excell_row++;

foreach($assessments as $assessment_key => $assessment_value)
{
	$mark 	= '';
	$status = '';
    if($assessment_value['aa_valuated'] == "1")
    {
		$mark 	= $assessment_value['aa_mark_scored'];
        $status = ($assessment_value['aa_grade']!='')?$assessment_value['aa_grade']:'-';
    }
    else
    {
		$mark 	= 'Not Evaluated';
	}
	
	

	/*$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$assessment_value['us_phone']);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);*/

    $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$assessment_value['us_name']);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row,$assessment_value['aa_attempted_date']);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setSize(10);
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row,gmdate("H:i:s",$assessment_value['aa_duration']));
    $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row,$mark);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row,$status);
    $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->applyFromArray($style);

    $excell_row++;

}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Quiz-'.$lecture.'_'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

?>