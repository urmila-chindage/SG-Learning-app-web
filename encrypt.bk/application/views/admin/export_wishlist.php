<?php //echo '<pre>';print_r($wishlist);die; ?>
<?php 
include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';

//create an excell sheet
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle($this->config->item('site_name'));

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
//processing excell rows
$excell_row     = 1;
					
$objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$this->config->item('site_name').' Wishlist Details ('.date('d-M-y g:i A').')');
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
foreach ($wishlist as $key => $wish){

    $num++;
    $text = count($wish['users'])>1?' Wishes)':' Wish)';
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$excell_row,$num.') '.$wish['cb_title'].' ('.count($wish['users']).$text);
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

    $objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row,'Wished Date');
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->applyFromArray($style);

    $objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row,'Joined On');
    $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->getFont()->setSize(10);
    $style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
    $objPHPExcel->getActiveSheet()->getStyle('E'.$excell_row)->applyFromArray($style);
    $excell_row++;

    foreach($wish['users'] as $user){
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

        $objPHPExcel->getActiveSheet()->setCellValue('D'.$excell_row,date('d M Y',strtotime($user['wished_date'])));
        $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->getFont()->setSize(10);
        $style = array(
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
        $objPHPExcel->getActiveSheet()->getStyle('D'.$excell_row)->applyFromArray($style);

        $objPHPExcel->getActiveSheet()->setCellValue('E'.$excell_row,date('d M Y',strtotime($user['created_date'])));
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
header('Content-Disposition: attachment;filename="Wishlist_'.date('d_M_y-h_i_s').'.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>