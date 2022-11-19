<?php
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet 1
$objPHPExcel    = new PHPExcel();

$style_center   = array(
                        'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        )
                    );
$style_left     = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    )
                );

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Students');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

//processing excell first rows
$excell_row     = 1;			
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row, $this->config->item('site_name').' - STUDENTS ('.date('d-M-y g:i A').')');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.'E'.$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->applyFromArray($style_left);

$objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row.':'.'E'.$excell_row)->applyFromArray($style_center);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);
$excell_row ++;
//End of processing excell first rows

$cell    = 'A';
/*
$headers = array(
                    'us_name' => 'Name', 
                    'us_email' => 'Email', 
                    'us_phone' => 'Phone Number',
                    'us_institute_id' => 'Institute', 
                    'us_branch' => 'Branch'
                );
*/
$headers = array(
    'us_name' => 'Name', 
    'us_email' => 'Email', 
    'us_phone' => 'Phone Number',
    'us_institute_id' => 'Institute'
);
foreach($headers as $field_key => $header)
{
    $objPHPExcel->getActiveSheet()->getColumnDimension($cell)->setWidth(30);
    $objPHPExcel->getActiveSheet()->setCellValue($cell.$excell_row, $header);
    $cell++;
}
if(!empty($profiles))
{
    foreach($profiles as $profile_key => $profile_label)
    {
        $objPHPExcel->getActiveSheet()->getColumnDimension($cell)->setWidth(30);
        $objPHPExcel->getActiveSheet()->setCellValue($cell.$excell_row, $profile_label);
        $cell++;
    }    
}
$cell--;
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$cell.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$cell.$excell_row)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(30);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.$cell.$excell_row)->applyFromArray($style_center);
$excell_row++;

$objPHPExcel->getActiveSheet()->getStyle($excell_row)->getAlignment()->setWrapText(true);

if(!empty($students))
{
    foreach($students as $student)
    {
        $cell    = 'A';
        foreach($headers as $field_key => $header)
        {
            $cell_value = $student[$field_key];
            if($field_key == 'us_institute_id')
            {
                $institute  = isset($institutes[$student[$field_key]])?$institutes[$student[$field_key]]:'';
                $cell_value = isset($institute['ib_institute_code'])?$institute['ib_institute_code'].' - '.$institute['ib_name']:'-';
            }
            else
            {
                // if($field_key == 'us_branch')
                // {
                //     $branch     = $branches[$student[$field_key]];
                //     $cell_value = $branch['branch_code'].' - '.$branch['branch_name'];
                // }                    
            }
            $objPHPExcel->getActiveSheet()->setCellValue($cell.$excell_row, $cell_value);
            $cell++;
        }
        if(!empty($profiles))
        {
            $user_profile   = explode('{#}', $student['us_profile_fields']);
            $profile_values = array();
            if (!empty($user_profile)) 
            {
                foreach ($user_profile as $profile_field) 
                {
                    $profile_field          = substr($profile_field, 2);
                    $profile_field          = substr($profile_field, 0, -2);
                    $temp_field             = explode('{=>}', $profile_field);
                    $key                    = isset($temp_field[0]) ? $temp_field[0] : 0;
                    $value                  = isset($temp_field[1]) ? $temp_field[1] : '';
                    $profile_values[$key]   = $value;
                }
            }
            foreach($profiles as $profile_key => $profile_label)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($cell.$excell_row, (isset($profile_values[$profile_key])?$profile_values[$profile_key]:''));
                $cell++;
            }    
        }       
        $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(30); 
        $excell_row++;
    }
}

// echo '<pre>'; print_r($institutes);die;
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Students_'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

?>