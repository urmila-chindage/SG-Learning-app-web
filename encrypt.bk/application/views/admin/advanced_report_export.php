<?php 
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';
//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($this->config->item('site_name').' - Advanced Report');
//current row 
$excell_row     = 1;
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$this->config->item('site_name').' - Advanced Report ('.date('d-M-y g:i A').')');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.num2alpha(count($header_labels)+2).$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($header_labels)+2).$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($header_labels)+2).$excell_row)->getFont()->setSize(16);
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($header_labels)+2).$excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);

$excell_row++;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.num2alpha(count($header_labels)+2).$excell_row);
$excell_row++;

$excell_column  = 'D';
//configuring location
$locations = array();
if(isset($cities) && !empty($cities))
{
    foreach ($cities as $city)
    {
        $locations[$city['id']] = $city['city_name'];
    }
}
//End
//configuring courses
$courses = array();
if(isset($subscribed_courses) && !empty($subscribed_courses))
{
    foreach ($subscribed_courses as $course)
    {
        $courses[$course['id']] = $course['cb_title'];
    }
}
//End
//developing header

$objPHPExcel->getActiveSheet()
                                ->setCellValue('A'.$excell_row, 'Student Name')
                                ->setCellValue('B'.$excell_row, 'Region')
                                ->setCellValue('C'.$excell_row, 'Course');
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.num2alpha(count($header_labels)+2).$excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')
        ->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')
        ->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')
        ->setAutoSize(true);
if(isset($header_labels) && !empty($header_labels))
{
    foreach ($header_labels as $label)
    {
        $objPHPExcel->getActiveSheet()->setCellValue(($excell_column++).$excell_row, $label);
    }
}
$objPHPExcel->getActiveSheet()->getStyle('A1:'.($excell_column).$excell_row)->getFont()->setBold(true);
//end
//starting excell body
if(isset($users) && !empty($users))
{
    foreach ($users as $user)
    {
        $excell_row++;
        $excell_column  = 'D';
        $objPHPExcel->getActiveSheet()
                                ->setCellValue('A'.$excell_row, $user['us_name'])
                                ->setCellValue('B'.$excell_row, (isset($locations[$user['us_native']])?$locations[$user['us_native']]:''));
        $style = array(
                'alignment' => array(
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);

        //processing course column
        $course_html    = '';
        $course_ids     = isset($user['course_ids'])?$user['course_ids']:'';
        $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(16);
        if($course_ids)
        {
            $course_ids     = explode(',', $course_ids);
            $course_count   = count($course_ids);
            $course_count   = ($course_count==1)?16:$course_count*15;
            $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight($course_count);
            if(!empty($course_ids))
            {
                $course_names = array();
                foreach($course_ids as $course_id)
                {
                    $course_names[] = $courses[$course_id];
                }
                $course_html = implode(', '.PHP_EOL, $course_names);
            }
        }
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row, $course_html);
        //End
        if(isset($header_labels) && !empty($header_labels))
        {
            foreach ($header_labels as $label_id => $label)
            {
                $cell_value = isset($user['fields'][$label_id])?$user['fields'][$label_id]:'';
                $objPHPExcel->getActiveSheet()->getColumnDimension($excell_column)->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->setCellValue(($excell_column++).$excell_row, $cell_value);
            }
        }
        $excell_column++;
    }
}
//End

//Writing content to excell file
$generated_date = date('Y-m-d-h-i-s');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Advanced-report-'.$generated_date.'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

function num2alpha($n)
{
    for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r;
    return $r;
}
?>