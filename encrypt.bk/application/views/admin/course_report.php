<?php
    include 'PHPExcel.php';
    include 'PHPExcel/IOFactory.php';
    
    $updated_date       = date('Y-m-d-H-i-s');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0);
    
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Overall Statistics');

    $objPHPExcel->getActiveSheet()
                                    ->setCellValue('A1', 'Sitename')
                                    ->setCellValue('B1', $site_name);
    
    $objPHPExcel->getActiveSheet()
                                    ->setCellValue('A3', 'Total number of courses')
                                    ->setCellValue('B3', $live_courses);
    
    $objPHPExcel->getActiveSheet()
                                    ->setCellValue('A5', 'Total number of Students')
                                    ->setCellValue('B5', $live_users);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A10')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);

    // Create a new worksheet, after the default sheet
    $objPHPExcel->createSheet();
    
    // Add some data to the second sheet, resembling some different data types
    $objPHPExcel->setActiveSheetIndex(1);

    // Rename 2nd sheet
    $objPHPExcel->getActiveSheet()->setTitle('Course details');
    
    // Loop through the result set 
    $rowNumber = 1;
    $objPHPExcel->getActiveSheet()
                                    ->setCellValue('A' . $rowNumber, 'Course Title')
                                    ->setCellValue('B' . $rowNumber, 'Total number of Students')
                                    ->setCellValue('C' . $rowNumber, 'Total number of Wishlist');

   

    $rowNumber = 2;
    foreach($courses as $cell) 
    { 
        $objPHPExcel->getActiveSheet()
                                    ->setCellValue('A' . $rowNumber, $cell["cb_title"])
                                    ->setCellValue('B' . $rowNumber, $cell["enrolled_users"])
                                    ->setCellValue('C' . $rowNumber, $cell["wishlist_users"]);

        $rowNumber++;
    }
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A200')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Overall_details-'.$updated_date.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
?>