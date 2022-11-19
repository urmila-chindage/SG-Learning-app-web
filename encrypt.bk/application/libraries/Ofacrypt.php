<?php 
class Ofacrypt
{
    function __construct()
    {
    }
        
    public function encrypt($string,$key)
    {	
	$returnString   = "";
	$charsArray     = str_split("e7NjchmKXbVPiAqn8DLzWoMCEGgTpsx3_6.tvwJQ-R0OUrSaFYyuHk954fd2~1lIBZ");
	$charsLength    = count($charsArray);
	$stringArray    = str_split($string);
	$keyArray       = str_split($this->strToHex(hash('md5',$key.'954fd2FYyuH')));
	
    $randomKeyArray = array();
    
	while(count($randomKeyArray) < $charsLength)
	{
            $randomKeyArray[] = $charsArray[rand(0,$charsLength-1)];
    }
    
	$numeric        = 0;;
	$numericString  = "";
        for ($a = 0; $a < count($stringArray); $a++)
        {
            $charValue      = $this->utf8Ord($stringArray[$a]);
            $numericString .= $charValue;
            $numeric        = $charValue + $this->utf8Ord($randomKeyArray[$a%$charsLength]);
            $returnString  .= $charsArray[floor($numeric/$charsLength)];
            $returnString  .= $charsArray[$numeric%$charsLength];
        }
	$randomKeyEnc   = "";
	for ($a = 0; $a < $charsLength; $a++)
        {
            $numeric        = $this->utf8Ord($randomKeyArray[$a])+  $this->utf8Ord($keyArray[$a%count($keyArray)]);
            $randomKeyEnc  .= $charsArray[floor($numeric/$charsLength)];
            $randomKeyEnc  .= $charsArray[$numeric%$charsLength];  
        }
	return $randomKeyEnc.$this->strToHex(hash('md5',$numericString.'954fd2FYyuH')).$returnString;
    }

    public function decrypt($string,$key){
        $returnString       = "";
        $charsArray         = str_split("e7NjchmKXbVPiAqn8DLzWoMCEGgTpsx3_6.tvwJQ-R0OUrSaFYyuHk954fd2~1lIBZ");
        $charsLength        = count($charsArray);
        $keyArray           = str_split($this->strToHexdec(hash('md5',$key.'954fd2FYyuH')));
        $stringArray        = str_split(substr($string,($charsLength*2)+64));
        $md5                = substr($string,($charsLength*2),64);
        $randomKeyArray     = str_split(substr($string,0,$charsLength*2));
        $randomKeyDec       = array();
        $stringAsNumeric    = '';
        
        if((count($randomKeyArray) < $charsLength*2))
        {
            return false;
        }
        
        for ($a = 0; $a < $charsLength*2; $a+=2)
        {
            $numeric         = array_search($randomKeyArray[$a],$charsArray) * $charsLength;
            $numeric        += array_search($randomKeyArray[$a+1],$charsArray);
            $numeric        -= $this->utf8Orddec($keyArray[floor($a/2)%count($keyArray)]);
            $randomKeyDec[]  = chr($numeric);
        }

        for ($a = 0; $a < count($stringArray); $a+=2)
        {
            $numeric         = array_search($stringArray[$a],$charsArray) * $charsLength;
            $numeric        += array_search($stringArray[$a+1],$charsArray);
            $numeric        -= $this->utf8Orddec($randomKeyDec[floor($a/2)%$charsLength]);
            $stringAsNumeric.= $numeric;
            $returnString   .= chr($numeric);
        }

        if($this->strToHexdec(hash('md5',$stringAsNumeric.'954fd2FYyuH')) != $md5)
        {
            return "false";
        }
        else
        {
            return $returnString;
        }
    }

    private function utf8Orddec($c)
    {
	list(, $ord) = unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
	return $ord;
    }
    
    private function utf8Ord($c)
    {
	list(, $ord) = unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
	return $ord;
    }   

    private function strToHexdec($string)
    {
        $hex            = '';
        for ($i=0; $i<strlen($string); $i++)
        {
            $ord        = ord($string[$i]);
            $hexCode    = dechex($ord);
            $hex       .= substr('0'.$hexCode, -2);
        }
        return strToUpper($hex);
    }
    
    private function strToHex($string)
    {
        $hex            = '';
        for ($i=0; $i<strlen($string); $i++){
            $ord        = ord($string[$i]);
            $hexCode    = dechex($ord);
            $hex       .= substr('0'.$hexCode, -2);
        }
        return strToUpper($hex);
    }
}
?>