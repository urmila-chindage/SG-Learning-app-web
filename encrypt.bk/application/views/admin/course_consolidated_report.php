<?php
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet 1
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Course wise Report');

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);

//processing excell header rows
$excell_row     = 1;			
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $this->config->item('site_name').' - Course Consolidated Report ('.date('d-M-y g:i A').')');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.'I'.$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'I'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'I'.$excell_row)->getFont()->setSize(16);

$style_center = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);
$style_left = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
);


$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'I'.$excell_row)->applyFromArray($style_center);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);
$excell_row ++;
//End of processing excell header rows



$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, 'Course Name');
$objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row, 'Total Enrolled');
$objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row, 'Total Completed');
$objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row, 'Expiry');
$objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row, 'Status');
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setSize(12);


$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(28);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->applyFromArray($style_center);
$excell_row++;

if(!empty($course_wise_report))
{
    foreach($course_wise_report as $course_id => $report)
    {
        if(isset($courses[$course_id]))
        {
            $course = $courses[$course_id]; 
            $expiry = 'unlimited';

            $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, html_entity_decode($course['cb_title']));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row, $report['enrolled']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row, $report['completed']);
            if($course['cb_access_validity'] == 1 )
            {
                $expiry = $course['cb_validity'].($course['cb_validity'] == 1 ? ' day' : ' days');
            }
            else
            {
                if( $course['cb_access_validity'] == 2)
                {
                    $expiry = date('d-m-Y', strtotime($course['cb_validity_date']));
                }    
            }
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row, $expiry);  
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row, ($course['cb_status']=='1')?'Active':'Inactive');
            $backgroundColorParams = array();
            if($course['cb_status'] == '1')
            {
                $backgroundColorParams = array('fill' =>
                    array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '09bf63')
                        )
                    );
                
            }
            else
            {
                $backgroundColorParams = array('fill' =>
                    array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'ec971f')
                        )
                    );
                
            }
            $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->applyFromArray($backgroundColorParams);

            $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row.':'.'E'.$excell_row)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setSize(10);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'A'.$excell_row)->applyFromArray($style_left);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row.':'.'E'.$excell_row)->applyFromArray($style_center);
            $excell_row++;
        }
    }
}


if(!empty($institute_wise_report))
{
    //create an excell sheet 2
    $objPHPExcel->createSheet(1);
    $objPHPExcel->setActiveSheetIndex(1);
    $objPHPExcel->getActiveSheet()->setTitle('Institute wise Report');
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);

    //processing excell header rows
    $excell_row     = 1;			
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $this->config->item('site_name').' - Course Consolidated Report ('.date('d-M-y g:i A').')');
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.'I'.$excell_row);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'I'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'I'.$excell_row)->getFont()->setSize(16);


    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'I'.$excell_row)->applyFromArray($style_center);
    $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);
    $excell_row ++;
    //End of processing excell header rows


    $header_column = 'A';
    $objPHPExcel->getActiveSheet()->setCellValue($header_column.$excell_row, 'INSTITUTION NAME \ COURSE NAME');
    $objPHPExcel->getActiveSheet()->getStyle($header_column.$excell_row.':'.$header_column.$excell_row)->getFont()->setSize(11);
    foreach($courses as $course)
    {
        $header_column++;
        $objPHPExcel->getActiveSheet()->setCellValue($header_column.$excell_row,  htmlspecialchars_decode($course['cb_title'], ENT_QUOTES));
    }
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$header_column.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$header_column.$excell_row)->getFont()->setSize(10);

    $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(28);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$header_column.$excell_row)->applyFromArray($style_center);
    $excell_row++;

    foreach($institute_wise_report as $institute_id => $report)
    {
        if(isset($institutes[$institute_id]))
        {
            $institute      = $institutes[$institute_id];
            $header_column  = 'A';
            $objPHPExcel->getActiveSheet()->setCellValue($header_column.$excell_row, $institute['ib_institute_code'].'-'.$institute['ib_name']);
            foreach($courses as $course_id => $course)
            {
                $header_column++;
                $objPHPExcel->getActiveSheet()->setCellValue($header_column.$excell_row, (isset($report[$course_id])?$report[$course_id]['enrolled']:0));
            }
            $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row.':'.$header_column.$excell_row)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$header_column.$excell_row)->getFont()->setSize(10);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'A'.$excell_row)->applyFromArray($style_left);
            $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row.':'.$header_column.$excell_row)->applyFromArray($style_center);
            $excell_row++;    
        }
    }
    
    $objPHPExcel->setActiveSheetIndex(0);
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Course-Consolidated-Report_'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

?>