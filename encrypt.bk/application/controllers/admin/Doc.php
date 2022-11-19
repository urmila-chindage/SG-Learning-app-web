<?php 

class Docsdfgsdfgsdfg extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->__question_types   = array('single' => '1', 'multiple' => '2', 'subjective' => '3');
        $this->__difficulty       = array('easy' => '1', 'medium' => '2', 'hard' => '3');
        $this->__single_type      = '1';
        $this->__multi_type       = '2';
        $this->__subjective_type  = '3';
    }
    
    function index()
    {
        
        $full_path    	= '/var/www/olp_phase2/uploads/test.onlineprofesor.com/question/49300fd4b2590f20a4423e19cd5efa3c.docx';
        $extract_path   = '/var/www/olp_phase2/uploads/test.onlineprofesor.com/question/49300fd4b2590f20a4423e19cd5efa3c';
        $upload_data['raw_name'] = '49300fd4b2590f20a4423e19cd5efa3c';
        $command = 'libreoffice --headless --convert-to html '.$full_path.' --outdir '.$extract_path.'';
        //shell_exec($command);
        //die('------------');

        
        $html_file = '/var/www/olp_phase2/uploads/test.onlineprofesor.com/question/49300fd4b2590f20a4423e19cd5efa3c/49300fd4b2590f20a4423e19cd5efa3c.html';
        libxml_use_internal_errors(true);
        $html   = ($html_file);
        $doc    = new DOMDocument;
        $doc->loadHTMLFile($html);
        $columns   		= $doc->getElementsByTagName('td');
        $this->doc_objects      = array();
	$column_count 		= 1;//this will be incremented in each loop. this is used to identify the loop is odd loop OR even loop
        $this->question_number  = 0;//for $this->doc_objects index
        $this->mechanism 	= 1;//ths is pointed to $method array. this will be increment in each loop(even loop only)
        $methods   		= array(
                                            1 => 'sl_no', 2 => 'set_question_type', 3 => 'set_difficulty',
                                            4 => 'set_direction', 5 => 'set_question', 6 => 'set_options',
                                            7 => 'set_answer', 8 => 'set_positive_mark', 9 => 'set_negative_mark',
                                            10 => 'set_catagory', 11 => 'set_explanation'
                        		);
        $end_of_option          = false;
        $subjective 		= false;
        $this->q_categories     = array();
        $image_count            = 1;
        foreach ($columns as $column)
        {
            $even_column = 	(($column_count%2) == 0)?true:false;
            // setting image path 
            $find_image =  $column->getElementsByTagName('img'); 
            
            foreach($find_image as $image)
            {
                $imageName = question_upload_path().$upload_data['raw_name'].'/image_'.($image_count++).'.jpg';
                $imageData = $image->getAttribute('src'); 
                $imageData = explode('base64,', $imageData);
                $imageData = isset($imageData[1])?$imageData[1]:'';
                $imageData = base64_decode($imageData); 

                $myfile = fopen($imageName, "w");
                $txt = $imageData;
                fwrite($myfile, $txt);
                fclose($myfile);
                

                //file_put_contents($imageName, $imageData);
                //$source = imagecreatefromstring($imageData);
                //$imageSave = imagejpeg($source,$imageName);
                //imagedestroy($source);
                
                $image->setAttribute('src', $imageName);
            } 
			
            //removing style and class      
            $find_p =  $column->getElementsByTagName('p'); 
            foreach($find_p as $p)
            {
                $p->removeAttribute('class');
                $p->removeAttribute('style');
            } 
			//save html      
            $column_html = trim($doc->saveXML($column));   
			
            //check the end of the question piece
            if( strtolower($this->trim_doc_objects($column_html)) == 'sl_no')
            {
                $this->sl_no();//reset the variables
            }
			//confirms the option is over
            if( strtolower($this->trim_doc_objects($column_html)) == 'answer')
            {
                /*
                 * switch to method set_answer. this is because the variable $this->mechanism is 
                 * reseted to 5, when its value is 6. this is to save all the option in option array.
                 * once all the option issaved then we swict to answer
                 */
                $this->mechanism = 7;
            }
			
            //checking whether the question isd subjecvtive
            if( strtolower($this->trim_doc_objects($column_html)) == 'subjective' )
            {
                    $subjective = true;
            }
			
            if(isset($methods[$this->mechanism]) && $this->mechanism > 0 && $even_column == true)
            {
		//call coresponding method to set the values in array $this->doc_objects
                $current_method = $methods[$this->mechanism];
                $this->$current_method($column_html);
				
                /*
                 * Basically subjective question dont have option. So in this case, when we reach 5(set_question)
                 * we skip method set_option and set_answer
                 */
                if($subjective==true && $this->mechanism==5)
                {
                        $this->mechanism = 7;
                        $subjective = false;
                }
				
                //recursing methos to set the option
                if($this->mechanism==6)
                {
                        $this->mechanism = 5;
                }
				
	        $this->mechanism++;
            }
			
            $column_count++;
        }
        echo '<pre>'; print_r($this->doc_objects);die('===');
    }
    
    private function parse_answer_key($key='A')
    {
            $parser = array('A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26);
            return isset($parser[$key])?$parser[$key]:false;
    }
	
    private function getTextBetweenTags($html)
    {
        $position1      = strpos($html, '>')+1;
        $position2      = strrpos($html, "</td>");
        $html_temp      = substr($html, $position1, ($position2-$position1));
        return $html_temp;
    }
    
    private function sl_no()
    {
        $this->mechanism = 1;
        $this->question_number++;
    }
	
    private function set_question_type($row_html)
    {
        $temp_html  	= $this->getTextBetweenTags($row_html);
        $line       	= $this->trim_doc_objects($temp_html);
	$question_types = array( 'single_choice' =>  'single', 'multiple_choice' =>  'multiple', 'subjective' =>  'subjective', );
        $this->doc_objects[$this->question_number]['q_type'] = $this->__question_types[$question_types[$line]];
    }

    private function set_difficulty($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_difficulty'] = $this->__difficulty[$line];
    }
    
    private function set_positive_mark($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_positive_mark'] = $line;
    }
    
    private function set_negative_mark($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html);
        $this->doc_objects[$this->question_number]['q_negative_mark'] = $line;
    }
    
    private function set_direction($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_directions'] = $temp_html;
    }
    
    private function set_question($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_question'] = $temp_html;        
    }

    private function set_explanation($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_explanation'] = $temp_html;                
    }
    
    private function set_answer($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = strtoupper($this->trim_doc_objects($temp_html));
        $this->doc_objects[$this->question_number]['q_answer'] = $line;   
    }
    
    private function set_options($row_html)
    {
        $temp_html = $this->getTextBetweenTags($row_html);
        $this->doc_objects[$this->question_number]['q_option'][] = $temp_html;                
    }
	
    private function set_catagory($row_html)
    {
        $temp_html  = $this->getTextBetweenTags($row_html);
        $line       = $this->trim_doc_objects($temp_html, false);
        $this->doc_objects[$this->question_number]['q_category'] = $line;
    }
    
    private function trim_doc_objects($string, $string_to_lower=true)
    {
        $string_temp = $string;
        $string_temp = trim($string_temp);
        $string_temp = strip_tags($string_temp);
        $string_temp = str_replace(' ', '', $string_temp);
        $string_temp = str_replace('&#13;', '', $string_temp);
        $string_temp = trim($string_temp);        
        if($string_to_lower)
        {
            $string_temp = strtolower($string_temp);        
        }
        return $string_temp;
    }
}
?>