<?php //echo '<pre>';print_r($wishlist);die; ?>
<?php 
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($this->config->item('site_name'));

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
//processing excell rows
$excell_row     = 1;
					
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$this->config->item('site_name').' - Strength Report ('.date('d-M-y g:i A').')');
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
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,'Name : '.$student['us_name']);
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

$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,'Topic Name');
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row,'Strength');
$objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row,'Weak');
$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row,'Avg Time (MM:SS)');
$objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setSize(10);
$style = array(
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
$objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->applyFromArray($style);
$excell_row++;

foreach($progress as $progress_key => $t_progress){
    $strength_perntage          = 0;
    $weak_perntage              = 0;
    $t_progress['scored_mark']  = $t_progress['scored_mark']==null?0:$t_progress['scored_mark'];
    $strength_perntage          = ($t_progress['scored_mark']/$t_progress['total_mark'])*100;
    $strength_perntage          = round($strength_perntage,2);
    $weak_perntage              = 100 - $strength_perntage;

    $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$t_progress['qc_category_name']);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row,$strength_perntage.' %');
    $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row,$weak_perntage.' %');
    $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setSize(10);
    $style = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row,gmdate("i:s", (int)$t_progress['duration']));
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->applyFromArray($style);

    $excell_row++;
}


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Strength-'.$student['us_name'].'_'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>