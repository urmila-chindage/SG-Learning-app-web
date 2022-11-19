<?php include 'PHPExcel.php';
        include 'PHPExcel/IOFactory.php';

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Create a first sheet, representing sales data
        //$objPHPExcel->setActiveSheetIndex(0);
        //$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Something');
        
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Course Users');
        
        // Loop through the result set 
        $rowNumber = 1;
        $objPHPExcel->getActiveSheet()
                                        ->setCellValue('A' . $rowNumber, 'Username')
                                        ->setCellValue('B' . $rowNumber, 'User Email');
        
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);
        
        $rowNumber = 2;
        foreach($users_detail as $cell) 
        { 
            $objPHPExcel->getActiveSheet()
                                        ->setCellValue('A' . $rowNumber, $cell["us_name"])
                                        ->setCellValue('B' . $rowNumber, $cell["us_email"]);
 
            $rowNumber++;
        } 
        
       

        

        // Create a new worksheet, after the default sheet
        $objPHPExcel->createSheet();

        // Add some data to the second sheet, resembling some different data types
        $objPHPExcel->setActiveSheetIndex(1);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'More data');

        // Rename 2nd sheet
        $objPHPExcel->getActiveSheet()->setTitle('Second sheet');

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="user.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        ?>