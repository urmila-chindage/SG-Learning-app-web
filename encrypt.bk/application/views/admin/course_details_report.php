<?php
    include 'PHPExcel.php';
    include 'PHPExcel/IOFactory.php';
    
    $updated_date       = date('Y-m-d-H-i-s');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0);
    
    $objPHPExcel->getActiveSheet()->setTitle('Course details');
    
    // Loop through the result set 
    $rowNumber = 1;
    $objPHPExcel->getActiveSheet()
                                    ->setCellValue('A' . $rowNumber, 'Course Title')
                                    ->setCellValue('B' . $rowNumber, 'Number of Subscribed Students')
                                    ->setCellValue('C' . $rowNumber, 'Number of Wishlist')
                                    ->setCellValue('D' . $rowNumber, 'Number of Topics')
                                    ->setCellValue('E' . $rowNumber, 'Number of Lectures')
                                    ->setCellValue('F' . $rowNumber, 'Number of Assessment');

   

    $rowNumber = 2;
    foreach($courses_list as $cell) 
    {
        foreach($courses as $key=>$cell_all){
            if($cell['id'] == $key){
                $objPHPExcel->getActiveSheet()
                                            ->setCellValue('A' . $rowNumber, $cell["cb_title"])
                                            ->setCellValue('B' . $rowNumber, $cell_all["enrolled_users"])
                                            ->setCellValue('C' . $rowNumber, $cell_all["wishlist_users"])
                                            ->setCellValue('D' . $rowNumber, $cell_all["sections"])
                                            ->setCellValue('E' . $rowNumber, $cell_all["lectures"])
                                            ->setCellValue('F' . $rowNumber, $cell_all["assesments"]);
            }

        
        }
        $rowNumber++;
    }
    
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:A200')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
    
    $i=1;
    foreach($courses_list as $cell) 
    {
        // Create a new worksheet, after the default sheet
        $objPHPExcel->createSheet();
    
        // Add some data to the second sheet, resembling some different data types
        $objPHPExcel->setActiveSheetIndex($i);

        // Rename 2nd sheet
        $sheet_title = ((strlen($cell["cb_title"])>31)?(substr($cell["cb_title"], 0, 28).'...'):$cell["cb_title"]);
        $objPHPExcel->getActiveSheet()->setTitle($sheet_title);
    
        // Loop through the result set 
        $rowNumber = 1;
        $objPHPExcel->getActiveSheet()
                                    ->setCellValue('A' . $rowNumber, 'Course Title')
                                    ->setCellValue('B' . $rowNumber, 'Total number of Students')
                                    ->setCellValue('C' . $rowNumber, 'Total number of Wishlist')
                                    ->setCellValue('D' . $rowNumber, 'Number of Topics')
                                    ->setCellValue('E' . $rowNumber, 'Number of Lectures')
                                    ->setCellValue('F' . $rowNumber, 'Number of Assessment');

   

        $rowNumber = 2;
        foreach($courses as $key=>$cell_all){
            if($cell['id'] == $key){
                $objPHPExcel->getActiveSheet()
                                            ->setCellValue('A' . $rowNumber, $cell["cb_title"])
                                            ->setCellValue('B' . $rowNumber, $cell_all["enrolled_users"])
                                            ->setCellValue('C' . $rowNumber, $cell_all["wishlist_users"])
                                            ->setCellValue('D' . $rowNumber, $cell_all["sections"])
                                            ->setCellValue('E' . $rowNumber, $cell_all["lectures"])
                                            ->setCellValue('F' . $rowNumber, $cell_all["assesments"]);
            }
        
        }
        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 8, "Student Report");
        $objPHPExcel->getActiveSheet()->mergeCells('A8:F8');
        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A8:F8")->applyFromArray($style);
        
       
            foreach($courses as $key=>$cell_all){
                if($cell['id'] == $key){
                    $rowNumber = 9;
                     if(empty($cell_all['enrolled_details'])){
                        $objPHPExcel->getActiveSheet()->setCellValue('A9', 'No student reports available');
                    }else{
                        foreach($cell_all['enrolled_details'] as $enrolled_users){
                            $objPHPExcel->getActiveSheet()
                                                        ->setCellValue('A' . $rowNumber, 'Student Name')
                                                        ->setCellValue('B' . $rowNumber, '% completion');
                        }
                    }
                }

            }


            foreach($courses as $key=>$cell_all){
                if($cell['id'] == $key){
                    $rowNumber = 10;
                    foreach($cell_all['enrolled_details'] as $enrolled_users){
                        $objPHPExcel->getActiveSheet()
                                                    ->setCellValue('A' . $rowNumber, $enrolled_users["us_name"])
                                                    ->setCellValue('B' . $rowNumber, $enrolled_users["percentage"]);
                        $colNumber = 2;
                        foreach($enrolled_users['user_attempted_assessment'] as $assessment_report){
                            $GLOBALS['user_id'] = $assessment_report['aa_user_id'];
                            $GLOBALS['assessment_id'] = $assessment_report['aa_assessment_id'];
                            
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($colNumber, $rowNumber, $assessment_report['cl_lecture_name']);
                            $colNumber++;
                            $total_marks = $assessment_report['obtained_mark'].' / '.$assessment_report['actual_mark'];
                            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($colNumber, $rowNumber, $total_marks);
                            $colNumber++;
                        }

                        $rowNumber++;
                    }
                }

            }
        
        
    
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A8:F8')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A9:F9')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1:A200')->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        
        $i++;
    }

    // Redirect output to a client�s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Course_details-'.$updated_date.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
?>