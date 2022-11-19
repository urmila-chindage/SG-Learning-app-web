<?php
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);

//processing excell rows
$excell_row = 1;
$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, 'student_name');
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, 'student_email');
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, 'student_password');
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, 'student_mobile');
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, 'student_batch_name');
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);
$excell_row++;


foreach ($users as $user) {
    $student           = (!empty($user['row']))?$user['row']:array();
    if(!empty($student))
    {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, $student['0']);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, $student['1']);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, $student['2']);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, $student['3']);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, $student['4']);
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
header('Content-Disposition: attachment;filename="usertemplate_' . date('d_M_y-h_i_s') . '.csv"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
$objWriter->save('php://output');
?>