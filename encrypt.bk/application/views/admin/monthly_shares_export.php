<?php 
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Shares Report for '.$period);

//processing excell rows
$excell_row     = 1;
$excell_column  = 'A';
					
$objPHPExcel->getActiveSheet()
                            ->setCellValue('A'.$excell_row, 'Teacher Name')
                            ->setCellValue('B'.$excell_row, 'Invoice Number')
                            ->setCellValue('C'.$excell_row, 'Student Name')
                            ->setCellValue('D'.$excell_row, 'Date of Purchase')
                            ->setCellValue('E'.$excell_row, 'Amount Paid')
                            ->setCellValue('F'.$excell_row, 'Course Name')
                            ->setCellValue('G'.$excell_row, 'Course Price')
                            ->setCellValue('H'.$excell_row, 'Course Discount')
                            ->setCellValue('I'.$excell_row, 'Teacher Share');
                            //->setCellValue('J'.$excell_row, $this->config->item('site_name').' Share');
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'I'.$excell_row)->getFont()->setBold(true);
//echo '<pre>'; print_r($monthly_shares);die;
if(!empty($monthly_shares))
{
    foreach($monthly_shares as $monthly_share)
    {
        $excell_row++;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $monthly_share['us_name']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'C'.$excell_row)->getFont()->setBold(true);
        if(!empty($monthly_share['details']))
        {
            $excell_row++;
            foreach($monthly_share['details'] as $details)
            {
                $objPHPExcel->getActiveSheet()
                                            ->setCellValue('B'.$excell_row, $details['ph_order_id'])
                                            ->setCellValue('C'.$excell_row, $details['student_name'])
                                            ->setCellValue('D'.$excell_row, date("F j, Y, g:i a", strtotime($details['ph_payment_date_cp'])))
                                            ->setCellValue('E'.$excell_row, $details['payed_amount'])
                                            ->setCellValue('F'.$excell_row, $details['cb_title'])
                                            ->setCellValue('G'.$excell_row, $details['ph_course_price'])
                                            ->setCellValue('H'.$excell_row, (($details['ph_course_discount'])?$details['ph_course_discount']:''))
                                            ->setCellValue('I'.$excell_row, round($details['teacher_share'],2));
                                            //->setCellValue('J'.$excell_row, round(($details['payed_amount']-$details['ph_teacher_share']), 2));
                $excell_row++;
            }
        }
    }
}
//End

//Writing content to excell file
$generated_date = 'Monthly_Sales_Report_for_'.$period;
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Advanced-report-'.$generated_date.'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

?>