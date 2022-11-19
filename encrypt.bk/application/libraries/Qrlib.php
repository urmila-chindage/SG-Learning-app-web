<?php
/*
 * PHP QR Code encoder
 *
 * Root library file, prepares environment and includes dependencies
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
	
    $QR_BASEDIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'QRCode'.DIRECTORY_SEPARATOR;
	// Required libs
	
	include $QR_BASEDIR."qrconst.php";
	include $QR_BASEDIR."qrconfig.php";
	include $QR_BASEDIR."qrtools.php";
	include $QR_BASEDIR."qrspec.php";
	include $QR_BASEDIR."qrimage.php";
	include $QR_BASEDIR."qrinput.php";
	include $QR_BASEDIR."qrbitstream.php";
	include $QR_BASEDIR."qrsplit.php";
	include $QR_BASEDIR."qrrscode.php";
	include $QR_BASEDIR."qrmask.php";
	include $QR_BASEDIR."qrencode.php";

class Qrlib 
{
    function __construct()
    {
        $this->qrcode_upload_path = $this->qrcode_upload_path();
        $this->level = array('L','M','Q','H');
        $this->qr_image = new QRcode();
    }

    function qrcode( $params = array() )
    {
        $this->response = array();
        $this->response['error']    = false;
        $this->response['data']     = array();
        $this->response['message']  = 'Input converted QR code';

        $input = isset($params['input'])?$params['input']:'';
        if($input == '')
        {
            $this->response['error'] = true;
            $this->response['message'] = 'Input cannot be empty';
            return $this->response;exit;     
        }
        $level      = (isset($params['level']) && in_array($params['level'], array('L','M','Q','H')))?$params['level']:'L';
        $size       = (isset($params['size']))?min(max((int)$params['size'], 1), 10):4;
        $file_name  = 'file'.md5($input.'|'.$level.'|'.$size).'.jpg';
        $file_path  = $this->qrcode_upload_path.$file_name;
        $this->qr_image->png($input, $file_path, $level, $size, 2);    
        $this->response['data'] = array(
            'file_path' => $file_name,
            'file_name' => $file_name
        );
        return $this->response;
    }

    function qrcode_upload_path()
    {
        if (!file_exists(qrcode_upload_path()))
        {
            mkdir(qrcode_upload_path(), 0777, true);
        }
        return qrcode_upload_path();
    }
}