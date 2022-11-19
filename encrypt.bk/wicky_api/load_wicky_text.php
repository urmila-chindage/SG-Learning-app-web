<?php 
if(isset($_REQUEST['uri']))
{
	$url=$_REQUEST['uri']; 
}
else
{
	$url="http://en.wikipedia.org/wiki/Yahoo!";
	
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<!--<form action="" method="post" name="mform"><input name="uri" type="text" value="<?php echo $url; ?>" /><input name="submit" type="submit" /></form>-->
<?php
include_once("simple_html_dom.php");

/*$url = "http://en.wikipedia.org/wiki/Facebook";
$ch = curl_init();
$timeout = 5;
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$html = curl_exec($ch);
curl_close($ch);*/

//$html=file_get_contents("http://en.wikipedia.org/wiki/Facebook");
$doc = new simple_html_dom();


$doc->load_file($url);
//$content=$doc->getElementById('content');
//echo $doc->find('div[id=content]');

//$time_elapsed_secs = microtime(true) - $start;
//echo "This process used " . $time_elapsed_secs;

foreach($doc->find('div[id=mw-content-text]')  as $link) {
        # Show the <a href>
		$flag=0;
		$arraychecker=array('Journals [ edit]','See also [ edit]','References [ edit]','Biography [ edit]','External links [ edit]','Journals','References','See also','Biography','External links');
		foreach($link->childNodes() as $elm)
		{
	//		echo trim($elm->plaintext).'<br>';
			if (in_array(trim($elm->plaintext),$arraychecker) || $flag==1)
			{
				
				 $elm->outertext = '';
				$flag=1;
			}
			
		}
		
}
$doc->save();

foreach($doc->find('.infobox,div[id=jump-to-nav],div[id=catlinks],div[id=toc],.mw-editsection,.reference,.ambox,.printfooter,.noprint,.mw-indicators') as $aa){
		 $aa->outertext = '';
		}
$doc->save();


foreach($doc->find('a') as $aa){
		 $aa->outertext = '<span>'.$aa->innertext.'</span>';
		}
$doc->save();





foreach($doc->find('div[id=content]')  as $link) {
        # Show the <a href>
		
        echo $link;
        echo "<br />";
}

?>

<?php 

//$time_elapsed_secs = microtime(true) - $start;
//echo "This process used " . $time_elapsed_secs;
	?>
</body>
</html>

