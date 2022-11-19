<?php 
class Ofabeeconverter
{    
    private $CI;
    private $__input;
    private $__output;
    private $__s3_upload;
    private $__from_cisco;
    private $__extension;
    private $__filename;
    private $__conversion_routes;
    private $__video_conversion_engine;
    private $__document_conversion_engine;
    private $__scorm_conversion_engine;
    private $__engine;    
    private $__video_lecture_type    = 1;
    private $__document_lecture_type = 2;
    private $__scorm_lecture_type    = 10;
    private $__cisco_lecture_type    = 11;
    private $__response = array('success' => true, 'message' => '');
    private $__update_db;
    private $__lecture_id;
    private $__queue_id;
    private $__lecture_table    = 'course_lectures';
    private $__queue_table      = 'conversion_queue';
        
    private $__lecture_type;

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    
    public function initialize($config = array())
    {
	$curlHandle = curl_init($config['target_url']);
	$defaultOptions = array (
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $config,
		CURLOPT_RETURNTRANSFER => false ,
		CURLOPT_TIMEOUT_MS => 1000,

	);

	curl_setopt_array($curlHandle , $defaultOptions);
	curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);     
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2); 
	curl_exec($curlHandle);
	curl_close($curlHandle);
	return true;
    }
    
    /*
     * initializing the input parameters before starting conversion.
     * Parameters decide source file and destination folders and conversion mechanism
     * params details for send_mail
     *  input           ===> input will be an source file path along with name
     *  s3_upload       ===> indicates whether we have to upload it to s3. s3 if true and local server if false
     *  engine          ===> indicates the conversion mechanism. Video or Document conversion.
     */
    public function initialize_config($config = array())
    {
        $this->__input           = isset($config['input'])?$config['input']:'';
        $this->__output          = isset($config['output'])?$config['output']:'';
        $this->__s3_upload       = isset($config['s3_upload'])?$config['s3_upload']:false;
        $this->__from_cisco      = isset($config['from_cisco'])?$config['from_cisco']:false;
        $this->__engine          = isset($config['engine'])?$config['engine']:'';
        $this->__lecture_id      = isset($config['lecture_id'])?$config['lecture_id']:0;
        $this->__filename        = isset($config['file_name'])?$config['file_name']:false;
        $this->__lecture_type    = isset($config['lecture_type'])?$config['lecture_type']:false;
        $this->__update_db       = isset($config['update_db'])?$config['update_db']:true;
        $this->__queue_id        = isset($config['queue_id'])?$config['queue_id']:0;
        
    }
    
    /*
     * Method to do the conversion. This method will set the input need for conversion
     * and route the conversion engine according to the file type.
     */
    public function convert()
    {
        $this->__org_input  = $this->__input;
        $this->__input      = ($this->__s3_upload||$this->__from_cisco)?$this->__input:$this->absolute_path().$this->__input;
        $this->__filename   = ($this->__filename)?$this->__filename:$this->file_name($this->__input);
        $this->__extension  = $this->extension($this->__filename);
        $conversion_engine  = $this->get_conversion_engine();
        $this->$conversion_engine();
        return $this->__response;
    }
    
    /*
     * Method for video conversion
     */
    private function video_converter()
    {
        //setting the output path after conversion

        $this->__output               = ($this->__output)?$this->__output:$this->absolute_path().video_upload_path();
        //saving lecture details before conversion
        $save                         = array();
        $save['id']                   = $this->__lecture_id;
        $save['cl_lecture_type']      = ($this->__from_cisco)?$this->__cisco_lecture_type:$this->__video_lecture_type; //video lecture

        if(!file_exists($this->__output))
        {
            mkdir($this->__output,0777);
        }
        if($this->__s3_upload || $this->__from_cisco)
        {
            //cant wait untill copy.. timecinsuming so. just update
            if($this->__queue_id && $this->__from_cisco)
            {
                $save_queue                      = array();
                $save_queue['conversion_status'] = 2; //conversion started
                $this->update_queue_table($save_queue);
            }
            if(copy($this->__input, $this->__output.$this->__filename))
            {
                if($this->__update_db)
                {
                    $save['cl_conversion_status'] = 2; //conversion started
                    //$this->CI->db->where('id', $this->__lecture_id);            
                    //$this->CI->db->update($this->__lecture_table, $save);
                    $this->update_table($save);
                    
                    if($this->__queue_id)
                    {
                        $save_queue                      = array();
                        $save_queue['conversion_status'] = 2; //conversion started
                        $this->update_queue_table($save_queue);
                    }
                }
            }
            else
            {
                $this->__response['success']  = false;
                $this->__response['message'] .= 'Error in copying file from s3 amazon<br />';
                if($this->__update_db)
                {
                    $error                         = array();
                    $error['cl_conversion_status'] = 5; //conversion started
                    $this->update_table($error);
                    if($this->__queue_id)
                    {
                        $error_queue                      = array();
                        $error_queue['conversion_status'] = 5; //conversion started
                        $this->update_queue_table($error_queue);
                    }
                }           
            }
        }
        else
        {
            if($this->__update_db)
            {
                $save['cl_conversion_status'] = 2; //conversion started
                //$this->CI->db->where('id', $this->__lecture_id);            
                //$this->CI->db->update($this->__lecture_table, $save);     
                $this->update_table($save);
                if($this->__queue_id)
                {
                    $save_queue                      = array();
                    $save_queue['conversion_status'] = 2; //conversion started
                    $this->update_queue_table($save_queue);
                }
            }       
        }

        /*
         * getting video details
         */
        $ffmpeg_path         = config_item('ffmpeg');  
        $vid                 = realpath($this->__output.$this->__filename);
        ob_start();
        $ffmpeg_check_cmd    = $ffmpeg_path." -i \"{$vid}\" 2>&1";
        passthru($ffmpeg_check_cmd);
        $durationOut         = ob_get_contents();
        ob_end_clean();
        $search              ='/Duration: (.*?),/';
        $duration            = preg_match($search, $durationOut, $matches, PREG_OFFSET_CAPTURE, 3);
        $duration            = $matches[1][0];
        $timearray           = explode(":", $duration);
        $hr                  = 3600*$timearray[0];
        $min                 = 60*$timearray[1];
        $sec                 = $timearray[2];
        $ttime               = $hr+$min+$sec; 
        $durationArray       = explode(".",$ttime);
        $duration            = $durationArray[0];
        
        //$resultVideoRes      = preg_match ( '/[0-9]\?[0-9][0-9][0-9]x[0-9][0-9][0-9][0-9]\?/', $durationOut, $regs );  
        $regex_sizes = "/Video: ([^,]*), ([^,]*), ([0-9]{1,4})x([0-9]{1,4})/";
        if (preg_match($regex_sizes, $durationOut, $regs)) {
            $codec = $regs [1] ? $regs [1] : null;
            $width = $regs [3] ? $regs [3] : null;
            $height = $regs [4] ? $regs [4] : null;
        } 
        if (!isset ( $height ))
        {  
             $width            = 640;
             $height           = 360;
        }
        
        //$this->writeLog($width.'x'.$height);
        //$this->writeLog($durationOut);
        /*
        * mp4 conversion start
        */
        $aspect               = "";
        $aspectRatio          = (float)($width/$height);
        if(round($aspectRatio,2)==1.78)
        {
            $aspect             = "-aspect 16:9"; 
        }
        $filenameOnly           = $this->remove_extension($this->__filename);
        $outName                = $filenameOnly."_con.mp4";
        $vid2                   = $this->__output.$outName;
        $ffcmd                  = $ffmpeg_path.' -i "'.$vid.'" -vcodec libx264 -pix_fmt yuv420p '.$aspect.' -qscale 1 "'.$vid2.'"';
        shell_exec($ffcmd);

        /*
        * create thumb start
        */
        $thumbdir    = $this->__output.'thumbs/';
        $webthumbdir = $this->__output.'web/';
        if(!file_exists($thumbdir))
        {
            mkdir($thumbdir,0777);
        }
        if(!file_exists($webthumbdir))
        {
            mkdir($webthumbdir,0777);
        }
        if(($height/($width/233))>131)
        {
            $thumbSmallHeight = 131;
            $thumbSmallwidth = intval($width/($height/131));
        }
        else
        {
            $thumbSmallwidth = 233;
            $thumbSmallHeight = intval($height/($width/233));
        }
        $thumbLargewidth = intval($width/($height/360));
        $imagePath = $filenameOnly.'.jpg';
        if($duration<15)
        {
        $thumb_cmd = $ffmpeg_path." -i \"{$vid2}\" -ss 00:00:00.0 -f image2 -s \"{$thumbLargewidth}X360\" -vframes 1 ".$thumbdir.$filenameOnly.".jpg";
        shell_exec($thumb_cmd);
        $thumb_cmd_1 = $ffmpeg_path." -i \"{$vid2}\" -ss 00:00:00.0 -f image2 -s \"{$thumbSmallwidth}X{$thumbSmallHeight}\" -vframes 1 ".$webthumbdir.$filenameOnly.".jpg";
        shell_exec($thumb_cmd_1);
        }
        else
        {
        $thumb_cmd = $ffmpeg_path." -i \"{$vid2}\" -ss 00:00:15.0 -f image2 -s \"{$thumbLargewidth}X360\" -vframes 1 ".$thumbdir.$filenameOnly.".jpg";
        shell_exec($thumb_cmd);
        $thumb_cmd_1 = $ffmpeg_path." -i \"{$vid2}\" -ss 00:00:15.0 -f image2 -s \"{$thumbSmallwidth}X{$thumbSmallHeight}\" -vframes 1 ".$webthumbdir.$filenameOnly.".jpg";
        shell_exec($thumb_cmd_1);
        }
        
        /*
         * uploading thumb to s3
         */
        if($this->__s3_upload)
        {
            uploadToS3($thumbdir.$filenameOnly.".jpg",  video_upload_path()."thumbs/".$filenameOnly.".jpg");
            uploadToS3($webthumbdir.$filenameOnly.".jpg",  video_upload_path()."web/".$filenameOnly.".jpg");
        }

        /*
         * m3u8 conversion start
         */
        $convertedDir = $this->__output.$filenameOnly.'/';
        if(!file_exists($convertedDir))
        {
            mkdir($convertedDir,0777);
        }
        $videoResolutionArray = array();
        if($height>=1080)
        {
            array_push($videoResolutionArray,0);
            array_push($videoResolutionArray,1);
            array_push($videoResolutionArray,2);
            array_push($videoResolutionArray,3);
            array_push($videoResolutionArray,4);
        }
        else if($height>=720)
        {
            array_push($videoResolutionArray,0);
            array_push($videoResolutionArray,1);
            array_push($videoResolutionArray,2);
            array_push($videoResolutionArray,3);
        }
        else if($height>=480)
        {
            array_push($videoResolutionArray,0);
            array_push($videoResolutionArray,1);
            array_push($videoResolutionArray,2);
        }
        else if($height>=360)
        {
            array_push($videoResolutionArray,0);
            array_push($videoResolutionArray,1);
        }
        else 
        {
            array_push($videoResolutionArray,0);
        }
        $result = "#EXTM3U\r\n";
        for($i=0;$i<count($videoResolutionArray);$i++)  
        {
            switch($videoResolutionArray[$i])
            {
                case 0:
                {
                    $newwidth = $width/($height/240);
                    $newwidth = (int)$newwidth;
                    if($newwidth%2 != 0)
                    {
                        $newwidth++;
                    }
                    
                    shell_exec($ffmpeg_path." -i \"".$vid2."\" -vcodec libx264 -acodec copy -b:v 128k -s ".$newwidth."x240 -flags -global_header -map 0 -f segment -segment_list \"".$convertedDir."240p.m3u8\" -segment_format mpegts \"".$convertedDir."str240%0005d.ts\"");
                    $result .= "#EXT-X-STREAM-INF:BANDWIDTH=481677,RESOLUTION=".$newwidth."x240\r\n240p.m3u8\r\n";
                    break;
                }
                case 1:
                {
                    
                    $newwidth = $width/($height/360);
                    $newwidth = (int)$newwidth;
                    if($newwidth%2 != 0)
                    {
                        $newwidth++;
                    }
                    
                    shell_exec($ffmpeg_path." -i \"".$vid2."\" -vcodec libx264 -acodec copy -b:v 300k -s ".$newwidth."x360 -flags -global_header -map 0 -f segment -segment_list \"".$convertedDir."360p.m3u8\" -segment_format mpegts \"".$convertedDir."str360%0005d.ts\"");  
                    $result .= "#EXT-X-STREAM-INF:BANDWIDTH=1308077,RESOLUTION=".$newwidth."x360\r\n360p.m3u8\r\n"; 
                    break;
                }
                case 2:
                {
                    
                    $newwidth = $width/($height/480);
                    $newwidth = (int)$newwidth;
                    if($newwidth%2 != 0)
                    {
                        $newwidth++;
                    }
                    shell_exec($ffmpeg_path." -i \"".$vid2."\" -vcodec libx264 -acodec copy -b:v 450k -s ".$newwidth."x480 -flags -global_header -map 0 -f segment -segment_list \"".$convertedDir."480p.m3u8\" -segment_format mpegts \"".$convertedDir."str480%0005d.ts\"");  
                    $result .= "#EXT-X-STREAM-INF:BANDWIDTH=1808077,RESOLUTION=".$newwidth."x480\r\n480p.m3u8\r\n";         
                    break;
                }
                case 3:
                {
                    
                    $newwidth = $width/($height/720);
                    $newwidth = (int)$newwidth;
                    if($newwidth%2 != 0)
                    {
                        $newwidth++;
                    }
                    shell_exec($ffmpeg_path." -i \"".$vid2."\" -vcodec libx264 -acodec copy -b:v 600k -s ".$newwidth."x720 -flags -global_header -map 0 -f segment -segment_list \"".$convertedDir."720p.m3u8\" -segment_format mpegts \"".$convertedDir."str720%0005d.ts\"");
                    $result .= "#EXT-X-STREAM-INF:BANDWIDTH=2208077,RESOLUTION=".$newwidth."x720\r\n720p.m3u8\r\n";                 
                    break;
                }
                case 4:  
                {
                    
                    $newwidth = $width/($height/1080);  
                    $newwidth = (int)$newwidth;
                    if($newwidth%2 != 0)
                    {
                        $newwidth++;
                    }
                    shell_exec($ffmpeg_path." -i \"".$vid2."\" -vcodec libx264 -acodec copy -b:v 800k -s ".$newwidth."x1080 -flags -global_header -map 0 -f segment -segment_list \"".$convertedDir."1080p.m3u8\" -segment_format mpegts \"".$convertedDir."str1080%0005d.ts\"");
                    $result .= "#EXT-X-STREAM-INF:BANDWIDTH=2650941,RESOLUTION=".$newwidth."x1080\r\n1080p.m3u8\r\n";       
                    break;
                }
            }
            
        }
        $myfile                 = fopen($convertedDir.$filenameOnly.".m3u8", "w");
        fwrite($myfile, $result);
        fclose($myfile);
        $resultdata             = array();
        $resultdata['width']    = $width;
        $resultdata['height']   = $height;
        $resultdata['duration'] = $duration;
        if($this->__update_db)
        {
            $save['cl_conversion_status']       = 3; //conversion completed
            //$this->CI->db->where('id', $this->__lecture_id);            
            //$this->CI->db->update($this->__lecture_table, $save);
            $this->update_table($save);
            if($this->__queue_id)
            {
                $save_queue                      = array();
                $save_queue['conversion_status'] = 3; //conversion completed
                $this->update_queue_table($save_queue);
                
            }
        }
        if($this->__from_cisco)
        {
            $queued_lecture = array();
            $queued_lecture['id'] = $this->__lecture_id;
            $queued_lecture['cl_filename'] = $this->remove_extension($this->__filename);
            $queued_lecture['cl_org_file_name'] = $this->remove_extension($this->__filename).'.mp4';
            $this->CI->db->where('id', $this->__lecture_id);            
            $this->CI->db->update('course_lectures', $queued_lecture);
        }
        /*
         * uploading all m3u8 and mp4 files to s3
         */
        if($this->__s3_upload)
        {
            foreach(glob($convertedDir.'*.*') as $file) 
            {
                $fileListsArray = explode('/',$file);
                uploadToS3($file,  video_upload_path().$filenameOnly."/".$fileListsArray[count($fileListsArray)-1]);
            }
            uploadToS3($vid2,  video_upload_path().$filenameOnly.".mp4");
        }
        if($this->__s3_upload)
        {
            //$this->deleteDir($convertedDir);  
            //unlink($vid2);
            //unlink($thumbdir.$filenameOnly.".jpg");
            //unlink($webthumbdir.$filenameOnly.".jpg");
        } 
        //saving lecture details after conversion
        if($this->__update_db)
        {
            $save                               = array();
            $save['id']                         = $this->__lecture_id;
            $save['cl_width']                   = $width;
            $save['cl_height']                  = $height;
            $save['cl_duration']                = $duration;
            $save['cl_lecture_type']            = $this->__video_lecture_type; //document lecture
            $save['cl_conversion_status']       = 3; //conversion completed
            //$this->CI->db->where('id', $this->__lecture_id);            
            //$this->CI->db->update($this->__lecture_table, $save);
            $this->update_table($save);
            if($this->__queue_id)
            {
                $save_queue                      = array();
                $save_queue['conversion_status'] = 3; //conversion completed
                $this->update_queue_table($save_queue);
            }
        }
        $this->__response['success']  = true;
        $this->__response['message']  = 'Convertion completed successfully<br />';
    }
    // private function deleteDir($dirPath) {
    //     if (! is_dir($dirPath)) {
    //         throw new InvalidArgumentException("$dirPath must be a directory");
    //     }
    //     if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
    //         $dirPath .= '/';
    //     }
    //     $files = glob($dirPath . '*', GLOB_MARK);
    //     foreach ($files as $file) {
    //         if (is_dir($file)) {
    //             self::deleteDir($file);
    //         } else {
    //             unlink($file);
    //         }
    //     }
    //     rmdir($dirPath);
    // }
    /*
     * Method for document conversion
     */
    private function document_converter()
    {
        $this->__output               = ($this->__output)?$this->__output:$this->absolute_path().document_upload_path();
        $docroot                      = $this->__output;
        //saving lecture details before conversion
        if($this->__update_db)
        {
            $save                         = array();
            $save['id']                   = $this->__lecture_id;
            $save['cl_lecture_type']      = ($this->__lecture_type)?$this->__lecture_type:$this->__document_lecture_type; //document lecture
            $save['cl_conversion_status'] = 2; //conversion started
            //$this->CI->db->where('id', $this->__lecture_id);            
            //$this->CI->db->update($this->__lecture_table, $save);
            $this->update_table($save);
            if($this->__queue_id)
            {
                $save_queue                      = array();
                $save_queue['conversion_status'] = 2; //conversion started
                $this->update_queue_table($save_queue);
            }
        }
        
        // $LIBREOFFICE_PATH = 'libreoffice';  //"/usr/bin/libreoffice";
        $LIBREOFFICE_PATH = config_item('libre_office');  //"/usr/bin/libreoffice";
        //$webroot          = "http://ec2-54-67-3-97.us-west-1.compute.amazonaws.com/".config_item('upload_folder')."/documents/";
        $webroot          = $docroot;
        $apiurl           = "http://pdfconverter.ofabee.com/";
        $docroot          =  $this->__output;
        $density          =  300;
        // $density          =  config_item('density');  //300;
        $quality          =  90;
        // $quality          =  config_item('quality');  //90;
        if(!file_exists($docroot))
        {
            mkdir($docroot,0777);
        }
        
        $file_name        = $this->__filename;
        $filenameonly     = $this->remove_extension($file_name);
        $extension        = $this->get_extension($file_name);
        /*
        * Downloading document from s3
        */

        if($this->__s3_upload)
        {
            if(copy($this->__input, $docroot.$file_name))
            {
              
                if($this->__update_db)
                {
                    $save['cl_conversion_status'] = 2; //conversion started
                    //$this->CI->db->where('id', $this->__lecture_id);            
                    //$this->CI->db->update($this->__lecture_table, $save);
                    $this->update_table($save);
                    if($this->__queue_id)
                    {
                        $save_queue                      = array();
                        $save_queue['conversion_status'] = 2; //conversion started
                        $this->update_queue_table($save_queue);
                    }
                }
            }
            else
            {
                $this->__response['success']  = false;
                $this->__response['message'] .= 'Error in copying file from s3 amazon<br />';
                if($this->__update_db)
                {
                    $error                         = array();
                    $error['cl_conversion_status'] = 5; //conversion started
                    $this->update_table($error);
                    if($this->__queue_id)
                    {
                        $error_queue                      = array();
                        $error_queue['conversion_status'] = 5; //conversion started
                        $this->update_queue_table($error_queue);
                    }
                }           
            }
        }
        else
        {
            if($this->__update_db)
            {
                $save['cl_conversion_status'] = 2; //conversion started
                //$this->CI->db->where('id', $this->__lecture_id);            
                //$this->CI->db->update($this->__lecture_table, $save);   
                $this->update_table($save);
                if($this->__queue_id)
                {
                    $save_queue                      = array();
                    $save_queue['conversion_status'] = 2; //conversion started
                    $this->update_queue_table($save_queue);
                }
            }         
        }
        if($extension!='pdf')
        {
            $convert_script = 'export HOME=/tmp && '.$LIBREOFFICE_PATH.' --headless --convert-to pdf --outdir '.$docroot.' '.$docroot.$file_name;
            $output         = shell_exec($convert_script);
            // echo $output;die;

            if(!file_exists($docroot.$filenameonly.'.pdf'))
            {
                $this->__response['success']  = false;
                $this->__response['message'] .= 'Failed to convert other formats to PDF<br />';
                if($this->__update_db)
                {
                    $error                         = array();
                    $error['cl_conversion_status'] = 5; //conversion started
                    $this->update_table($error);
                    if($this->__queue_id)
                    {
                        $error_queue                      = array();
                        $error_queue['conversion_status'] = 5; //conversion started
                        $this->update_queue_table($error_queue);
                    }
                }           
                //echo $convert_script;die;
           }
        }
        $pdf_to_image_cmd = 'identify -verbose  -format "%Wx%H" '.$docroot.$filenameonly.'.pdf'.'[0] 2>&1';
        $result = shell_exec($pdf_to_image_cmd);
        // echo $pdf_to_image_cmd;die;
        // echo $docroot.$filenameonly;die;
        $dimensionArr = explode('x',$result);
        $wid = $dimensionArr[0];
        $hei = $dimensionArr[1];
        $hei = (float) $wid/1.39;
        $outputfolder = $filenameonly;
        if(!file_exists($docroot.$outputfolder))
        {
            mkdir($docroot.$outputfolder,0777); 
        }
        $convertCmd     ='convert -density '.$density.' -quality '.$quality.' -background white -alpha remove '.$docroot.$filenameonly.'.pdf'.' '.$docroot.$outputfolder.'/page.jpg';
        $this->__response['convertion_objects'] = array(
                                                        'output' => $docroot.$outputfolder.'/page.jpg'
                                                    );
                                                    //$convertCmd     ='convert -colorspace RGB -density 400 -quality 100% '.$docroot.$filenameonly.'.pdf'.' '.$docroot.$outputfolder.'/page.jpg';
                                                    
                                                    /*$imagick = new Imagick(); 
        $imagick->readImage($docroot.$filenameonly.'.pdf'); 
        $imagick->writeImages($docroot.$outputfolder.'/page.jpg', false); */
        
        //$convertCmd     ='convert -density '.$density.' -quality '.$quality.' '.$docroot.$filenameonly.'.pdf'.' '.$docroot.$outputfolder.'/page.jpg';   
        //echo $convertCmd;die;
        shell_exec($convertCmd);

        
        
        //shell_exec($convertCmd);

        $totalpages     = $this->getNumPagesPdf($docroot.$outputfolder);

        $this->__response['convertion_objects'] = array(
            'output' => $docroot.$outputfolder.'/page.jpg'
        );

        /*
        * uploading all docuemnt images to s3
        */
        if($this->__s3_upload)
        {
                foreach(glob($docroot.$outputfolder.'/*.*') as $file) 
                {
                    $fileListsArray = explode('/',$file);
                    uploadToS3($file,  document_upload_path().$filenameonly."/".$fileListsArray[count($fileListsArray)-1]);
                }
            
               uploadToS3($docroot.$filenameonly.'.pdf',  document_upload_path().$filenameonly.".pdf");
        }
        
        if($this->__s3_upload)
        {
            //$this->deleteDir($docroot.$outputfolder);  
            //unlink($docroot.$filenameonly.'.pdf');
            //unlink($docroot.$filename);
        } 
        //echo 'document conversion mechanism initiated..';exit;
        
        //saving lecture details after conversion
        if($this->__update_db)
        {
            $save                         = array();
            $save['id']                   = $this->__lecture_id;
            $save['cl_lecture_type']      = ($this->__lecture_type)?$this->__lecture_type:$this->__document_lecture_type;
            $save['cl_total_page']        =  $totalpages;
            $save['cl_conversion_status'] = 3; //conversion completed
            //$this->CI->db->where('id', $this->__lecture_id);            
            //$this->CI->db->update($this->__lecture_table, $save);
            $this->update_table($save);
            if($this->__queue_id)
            {
                $save_queue                      = array();
                $save_queue['conversion_status'] = 3; //conversion completed
                $this->update_queue_table($save_queue);
            }
        }
        $this->__response['success']  = true;
        $this->__response['message']  = 'Convertion completed successfully<br />';
    }
    
    /*
     * Method for scorm conversion
     */
    private function scorm_converter()
    {
        $this->__response['success'] = true;
        $this->__output = ($this->__output)?$this->__output:$this->absolute_path().scorm_upload_path();
        //saving lecture details before conversion
        if($this->__update_db)
        {
            $save                         = array();
            $save['id']                   = $this->__lecture_id;
            $save['cl_lecture_type']      = ($this->__lecture_type)?$this->__lecture_type:$this->__scorm_lecture_type; //scorm lecture
            $save['cl_conversion_status'] = 2; //conversion started
            $this->update_table($save);
            if($this->__queue_id)
            {
                $save_queue                      = array();
                $save_queue['conversion_status'] = 2; //conversion started
                $this->update_queue_table($save_queue);
            }
        }
                
        if(!is_dir($this->__output))
        {
            mkdir($this->__output, 0777, true);
        }
        
        $file_name        = $this->__filename;
        $filenameonly     = $this->remove_extension($file_name);
        $extension        = $this->get_extension($file_name);
        
        //echo $this->__input;die; 
        /*
        * Downloading document from s3
        */
        
        if($this->__s3_upload)
        {
            if(copy($this->__input, $this->__output.$file_name))
            {
              
                if($this->__update_db)
                {
                    $save['cl_conversion_status'] = 2; //conversion started
                    //$this->CI->db->where('id', $this->__lecture_id);            
                    //$this->CI->db->update($this->__lecture_table, $save);
                    $this->update_table($save);
                    if($this->__queue_id)
                    {
                        $save_queue                      = array();
                        $save_queue['conversion_status'] = 2; //conversion started
                        $this->update_queue_table($save_queue);
                    }
                }
            }
            else
            {
                $this->__response['success']  = false;
                $this->__response['message'] .= 'Error in copying file from s3 amazon<br />';
                if($this->__update_db)
                {
                    $error                         = array();
                    $error['cl_conversion_status'] = 5; //conversion started
                    $this->update_table($error);
                    if($this->__queue_id)
                    {
                        $error_queue                      = array();
                        $error_queue['conversion_status'] = 5; //conversion started
                        $this->update_queue_table($error_queue);
                    }
                }      
            }
        }
        else
        {
            if($this->__update_db)
            {
                $save['cl_conversion_status'] = 2; //conversion started
                //$this->CI->db->where('id', $this->__lecture_id);            
                //$this->CI->db->update($this->__lecture_table, $save);   
                $this->update_table($save);
                if($this->__queue_id)
                {
                    $save_queue                      = array();
                    $save_queue['conversion_status'] = 2; //conversion started
                    $this->update_queue_table($save_queue);
                }
            }         
        }
        
        if($this->__response['success'])
        {
            $outputfolder = $filenameonly;
            if(!file_exists($this->__output.$outputfolder))
            {
                mkdir($this->__output.$outputfolder,0777); 
            }

            //unzip the package
            $zip  = new ZipArchive;
            if($this->__s3_upload)
            {
                $this->__org_input = $this->__output.$this->__filename;
            }
            if ($zip->open($this->__org_input) === TRUE)
            {
                $zip->extractTo($this->__output.$outputfolder);
                $zip->close();

                //set the output folder
                $output_folder =  str_replace( $_SERVER['DOCUMENT_ROOT'],"", $this->__output.$outputfolder);

                $root_folder = scandir($this->__output.$outputfolder, 1);
                // if(isset($root_folder[0]))
                // {
                //     $output_folder = $output_folder.'/'.$root_folder[0];
                // }
                // echo "<pre>"; print_r($root_folder); echo $output_folder; die();
                //End of setting output folder
                //check for manifest file
                $this->__manifest_file = 'imsmanifest.xml';
                $manifest_found = false;
                $root_folder_files = $root_folder;//scandir($_SERVER['DOCUMENT_ROOT'].$output_folder, 1);
                if(!empty($root_folder_files))
                {
                    foreach($root_folder_files as $root_folder_file)
                    {
                        if($root_folder_file==$this->__manifest_file)
                        {
                            $xml    = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].'/'.$output_folder.'/'.$this->__manifest_file);
                            $resources = (array)$xml->resources->resource;
                            $initiating_file = '';
                            if(isset($resources))
                            {
                                foreach ($resources as $key => $value)
                                {
                                    //echo '<pre>' ; print_r($value);die;
                                    $initiating_file = $value['href'];
                                    break;
                                }
                            }
                            $output_folder = $output_folder.'/'.$initiating_file;
                            $manifest_found = true;
                            break;
                        }
                    }
                }
                if(!$manifest_found)
                {
                    $this->__response['success']  = false;
                    $this->__response['message'] .= 'Invalid package.<br />';
                    if($this->__update_db)
                    {
                        $error                         = array();
                        $error['cl_conversion_status'] = 5; //conversion started
                        $this->update_table($error);
                        if($this->__queue_id)
                        {
                            $error_queue                      = array();
                            $error_queue['conversion_status'] = 5; //conversion started
                            $this->update_queue_table($error_queue);
                        }
                    }           
                }
                else
                {
                    /*
                    * uploading all docuemnt images to s3
                    */
                    if($this->__s3_upload)
                    {
                        $this->__scorm_file = array();
                        $this->__scan_dir($this->__output.$outputfolder.'/');
                        // echo '<pre>'; print_r($this->__scorm_file);die;
                        uploadToS3Bulk($this->__scorm_file);
                    }
                    if($this->__s3_upload)
                    {
                        //$this->deleteDir($this->__output.$outputfolder);  
                        //unlink($this->__output.$filenameonly.'.pdf');
                        //unlink($this->__output.$filename);
                    } 
                    //echo 'document conversion mechanism initiated..';exit;
                    //saving lecture details after conversion
                    if($this->__update_db)
                    {
                        $save                         = array();
                        $save['id']                   = $this->__lecture_id;
                        $save['cl_lecture_type']      = ($this->__lecture_type)?$this->__lecture_type:$this->__scorm_lecture_type;
                        $save['cl_conversion_status'] = 3; //conversion completed
                        $save['cl_filename']          = $output_folder;
                        $save['cl_org_file_name']     = $output_folder;
                        $this->update_table($save);
                        if($this->__queue_id)
                        {
                            $save_queue                      = array();
                            $save_queue['conversion_status'] = 3; //conversion completed
                            $this->update_queue_table($save_queue);
                        }
                    }
                    $this->__response['cl_filename']    = $output_folder;
                    $this->__response['success']  = true;
                    $this->__response['message']  = 'Convertion completed successfully<br />';  
                }
                //end of checking manifest file
            }
            else
            {
                $this->__response['success']  = false;
                $this->__response['message'] .= 'Error in extracting package<br />';
                if($this->__update_db)
                {
                    $error                         = array();
                    $error['cl_conversion_status'] = 5; //conversion started
                    $this->update_table($error);
                    if($this->__queue_id)
                    {
                        $error_queue                      = array();
                        $error_queue['conversion_status'] = 5; //conversion started
                        $this->update_queue_table($error_queue);
                    }
                }           

            }
            //end of unziping package
        }
    }
    
    private function __scan_dir($location)
    {
        $files  = scandir($location);
        $files  = array_diff($files, array('.', '..'));
        if(!empty($files))
        {
            foreach($files as $file)
            {
                if(is_dir($location.$file))
                {
                    $this->__scan_dir($location.$file.'/');
                }
                else
                {
                    $this->__scorm_file[$location.$file] = $location.$file;
                }
            }
        }
    }
    

    private function getNumPagesPdf($filepath) 
    {
        $filearray = scandir($filepath);
        $totalPage = 0;
        foreach ($filearray as $key => $value) 
        {
            if (strpos($value,'page') !== false) 
            {
                $totalPage=$totalPage+1;
            }
        }
        return $totalPage;
    }   
    private function custom_url_encode($url)
    {
        return preg_replace( "#^[^:/.]*[:/]+#i", "", preg_replace( "{/$}", "", urldecode( $url ) ) );
    }
    private function inavlid_conversion()
    {
        $this->__response['success']  = false;
        $this->__response['message'] .= 'Invalid conversion mechanism invoked<br />';
        if($this->__update_db)
        {
            $error                         = array();
            $error['cl_conversion_status'] = 5; //conversion started
            $this->update_table($error);
            if($this->__queue_id)
            {
                $error_queue                      = array();
                $error_queue['conversion_status'] = 5; //conversion started
                $this->update_queue_table($error_queue);
            }
        }           
    }
    
    public function absolute_path()
    {
        return isset($_SERVER['DOCUMENT_ROOT'])?$_SERVER['DOCUMENT_ROOT'].'/':'';
    }  
    private function get_extension($name)
    {
        $pieces = explode('.', $name);
        return strtolower($pieces[sizeof($pieces)-1]);
    }
    public function remove_extension($file_name)
    {
    return substr($file_name, 0 , (strrpos($file_name, ".")));
    }
    public function file_name($file)
    {
        $pieces = explode('/', $file);
        return $pieces[sizeof($pieces)-1];
    }
    
    public function extension($filename)
    {
        $pieces = explode('.', $filename);
        return strtolower($pieces[sizeof($pieces)-1]);
    }
    
    /*
     * If user did not set the engine(document or video), this method will 
     * return the conversion engine according to the file type.
     */
    private function get_conversion_engine()
    {
        if( $this->__engine )
        {
            return $this->__engine;
        }
        $this->__conversion_routes          = array();
        $this->__video_conversion_engine    = 'video_converter';
        $this->__document_conversion_engine = 'document_converter';
        $this->__scorm_conversion_engine    = 'scorm_converter';
        
        //SETTING VIDEO FILE TYPES
        $this->__conversion_routes['mp4'] = $this->__video_conversion_engine;
        $this->__conversion_routes['flv'] = $this->__video_conversion_engine;
        $this->__conversion_routes['avi'] = $this->__video_conversion_engine;
        $this->__conversion_routes['f4v'] = $this->__video_conversion_engine;

        //SETTING DOCUMENT FILE TYPES
        $this->__conversion_routes['doc']   = $this->__document_conversion_engine;
        $this->__conversion_routes['docx']  = $this->__document_conversion_engine;
        $this->__conversion_routes['odt']   = $this->__document_conversion_engine;
        $this->__conversion_routes['xls']   = $this->__document_conversion_engine;
        $this->__conversion_routes['pdf']   = $this->__document_conversion_engine;
        $this->__conversion_routes['ppt']   = $this->__document_conversion_engine;
        $this->__conversion_routes['pptx']  = $this->__document_conversion_engine;

        //SETTING FOR CONVERTING SCORM
        $this->__conversion_routes['zip']   = $this->__scorm_conversion_engine;
        return isset($this->__conversion_routes[$this->__extension])?$this->__conversion_routes[$this->__extension]:'inavlid_conversion';
    }
    
    private function update_table($save)
    {
        $this->CI->db->where('id', $this->__lecture_id);            
        $this->CI->db->update($this->__lecture_table, $save);
    }
    
    private function update_queue_table($save)
    {
        $this->CI->db->where('id', $this->__queue_id);            
        $this->CI->db->update($this->__queue_table, $save);
    }
    public function writeLog($txt){
        $myfile = fopen("/var/www/olp_phase2/uploads/ace.ofabee.com/videos/log.txt", "w") or die("Unable to open file!");
        fwrite($myfile, $txt);
        fclose($myfile);
    }
}

