<?php
function get_video_content($data)
{
    $content = '';
    $content = str_replace("<","&lt;",$data);
    $content = str_replace(">","&gt;",$data);
    if (strpos($content, '[youtube]') !== false)
    {
        $url         = get_string_between_tags($content,'[youtube]','[/youtube]');
        $video_frame = replace_youtube_tag($content,$url);
        $video_string= '[youtube]'.$url.'[/youtube]';
        $content     = str_replace($video_string,$video_frame,$data);
    }
    return $content;
}

function get_string_between_tags($string, $start, $end)
{
    $string     = ' ' . $string;
    $ini        = strpos($string, $start);
    if ($ini == 0) return '';
    $ini       += strlen($start);
    $len        = strpos($string, $end, $ini) - $ini;
    $url        = substr($string, $ini, $len);
    return $url;
}

function replace_youtube_tag($content,$url)
{
    $video_id     = explode("?v=", $url); 
    if (empty($video_id[1]))
        $video_id = explode("/v/", $url);
    $video_id     = explode("&", $video_id[1]); 
    $video_id     = $video_id[0];
    $frame        = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    return $frame; 
}
?>
