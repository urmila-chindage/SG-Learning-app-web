<?php //echo '<pre>';print_r($previews);die; ?>
<?php 
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

function userDateformate ($date) 
    { 
        if($date == null)
        {
            return 'Nill';
        }
        $sec = strtotime($date); 
        $date = date("d/m/Y", $sec); 
        return $date; 
    } 
    function minutes( $seconds )
{
	return sprintf( "%02.2d:%02.2d", floor( $seconds / 60 ), $seconds % 60 );
}

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('test');
//$objPHPExcel->getActiveSheet()->setTitle($this->config->item('site_name'));

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
//processing excell rows
$excell_row     = 1;
					
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$this->config->item('site_name').' Free Preview Details ('.date('d-M-y g:i A').')');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.'E'.$excell_row);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setSize(16);
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

$objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->applyFromArray($style);
$objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(34);

$excell_row ++;
$num = 0;
foreach ($previews as $key => $preview){

    $num++;
    $text = count($preview['users'])>1?' Free preview)':' Free previews)';
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$preview['cb_title'].' ||  Previewed Users - '.count($preview['users']).' ||  Preview Time - '.gmdate("i:s",$preview['cb_preview_time']  ).' Minutes');
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$excell_row.':'.'E'.$excell_row);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->getFont()->setSize(12);
    $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(28);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row.':'.'E'.$excell_row)->applyFromArray($style);

    $excell_row++;

    $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,'Name');
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row,'E-mail');
    $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row,'Phone');
    $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row,'Last View');
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->applyFromArray($style);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row,'viewed Time');
    $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->applyFromArray($style);

    
    $excell_row++;

    foreach($preview['users'] as $user){
        //echo '<pre>';print_r($user);die;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$user['us_name']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->getFont()->setSize(10);
        $style = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle('A'.$excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('B'.$excell_row,$user['us_email']);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->getFont()->setSize(10);
        $style = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle('B'.$excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('C'.$excell_row,$user['us_phone']);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->getFont()->setSize(10);
        $style = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle('C'.$excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row,userDateformate($user['preview_date']));
        $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setSize(10);
        $style = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->applyFromArray($style);
        
        
        
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row,(($user['preview_time'] > $preview['cb_preview_time'] ) ? gmdate("i:s",$preview['cb_preview_time']  ) : gmdate("i:s",$user['preview_time'])));
        $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->getFont()->setSize(10);
        $style = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->applyFromArray($style);

        
        $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(14);
        $excell_row++;
    }
    $objPHPExcel->getActiveSheet()->getRowDimension($excell_row)->setRowHeight(16);
    $excell_row ++;
}


header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Free_preview_'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>