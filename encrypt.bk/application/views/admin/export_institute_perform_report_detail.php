<?php //echo '<pre>';print_r($wishlist);die;  ?>
<?php

include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($this->config->item('site_name'));

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

//processing excell rows
$excell_row = 1;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, ' Course Performance Report (' . date('d-M-y g:i A') . ')');
$objPHPExcel->getActiveSheet()->mergeCells('A' . $excell_row . ':' . 'C' . $excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'C' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'C' . $excell_row)->getFont()->setSize(16);
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);

$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'C' . $excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);

$excell_row ++;
$num = 1;

$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row,  'Performance Report of Course "'.$course_title.'"');
$objPHPExcel->getActiveSheet()->mergeCells('A' . $excell_row . ':' . 'C' . $excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'C' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'C' . $excell_row)->getFont()->setSize(12);
$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(28);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row . ':' . 'C' . $excell_row)->applyFromArray($style);

$excell_row++;

$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, 'Institute Name');
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, 'Course Likes');
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, 'Course Dislikes');
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, 'Course Forum Likes');
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, 'Course Forum Dislikes');
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);



$excell_row++;

if(!empty($institutes))
{
    foreach($institutes as $institute)
    {
        $num++;
        // '<pre>';print_r($institutes);die;
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, html_entity_decode($institute['institute_name']));
        $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);
    

        $objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, $institute['cp_course_likes']);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);
    

        $objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, $institute['cp_course_dislikes']);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);
    

        $objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, $institute['cp_forum_likes']);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);
    

        $objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row,  $institute['cp_forum_dislikes']);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);
    
        
        $excell_row++;
    }
}


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Course_Performance' . date('d_M_y-h_i_s') . '.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>