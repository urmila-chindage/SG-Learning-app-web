<?php
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);

//processing excell rows
$excell_row = 1;
$objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, 'institute_name');
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, 'institute_code');
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, 'admin_email');
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, 'admin_password');
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);

$objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, 'institute_address');
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('F' . $excell_row, 'institute_phone');
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('G' . $excell_row, 'institute_head_name');
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('H' . $excell_row, 'institute_head_email');
$objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('I' . $excell_row, 'institute_head_phone');
$objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('J' . $excell_row, 'institute_officer_name');
$objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('K' . $excell_row, 'institute_officer_email');
$objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('L' . $excell_row, 'institute_officer_phone');
$objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('M' . $excell_row, 'institute_about');
$objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->applyFromArray($style);


$objPHPExcel->getActiveSheet()->setCellValue('N' . $excell_row, 'institute_class_code');
$objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->getFont()->setSize(10);
$style = array(
    'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->applyFromArray($style);
$excell_row++;


foreach ($institutes as $institute) {
    $institute_content           = (!empty($institute['row']))?$institute['row']:array();
    if(!empty($institute_content))
    {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $excell_row, $institute_content['0']);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('A' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('B' . $excell_row, $institute_content['1']);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('B' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('C' . $excell_row, $institute_content['2']);
        $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('C' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('D' . $excell_row, $institute_content['3']);
        $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('D' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('E' . $excell_row, $institute_content['4']);
        $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('E' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('F' . $excell_row, $institute_content['5']);
        $objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('F' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('G' . $excell_row, $institute_content['6']);
        $objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('G' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('H' . $excell_row, $institute_content['7']);
        $objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('H' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('I' . $excell_row, $institute_content['8']);
        $objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('I' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('J' . $excell_row, $institute_content['9']);
        $objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('J' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('K' . $excell_row, $institute_content['10']);
        $objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('K' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('L' . $excell_row, $institute_content['11']);
        $objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('L' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('M' . $excell_row, $institute_content['12']);
        $objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('M' . $excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('N' . $excell_row, $institute_content['13']);
        $objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->getFont()->setSize(10);
        $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle('N' . $excell_row)->applyFromArray($style);
        $excell_row++;

    }
    
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="template_' . date('d_M_y-h_i_s') . '.csv"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
$objWriter->save('php://output');
?>