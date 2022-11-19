<?php include('header.php'); ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/styles.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/ekko-lightbox.min.css" rel="stylesheet">

<section>
    <div class="blue-strip">
        <div class="container">
            <div class="container-reduce-width">
                <h2 class="funda-head expl-course"><?php echo lang('expert_lectures') ?></h2> 
            </div><!--container-reduce-width-->
        </div><!--container-->
   </div><!--blue-strip-->  
</section>

<div class="video-library-area">
    <div class="container">
        <div class="container-reduce-width" id="expert_lecture_wrapper">
            
                
        </div><!--container-reduce-width-->  
    </div><!--container-->
</div><!--video-library-area-->

<div class="container">
    <div class="container-reduce-width">
        <div class="row">
            <div class="col-md-12">
                <div  class="btn-center-div btn-center-alter view-more-size" id="view_more_btn">
                      <a onclick="loadMoreExpertLecture()" class="btn orange-flat-btn orange-flat-btn-alter orange-course-btn more-library-btn-padding inline-blk">View more</a>
                 </div>
            </div><!--columns-->
        </div><!--row-->
    </div>
</div>

<!--Video popup start from here-->
<div class="modal fade" id="library_video" role="dialog">
    <div class="modal-dialog modal-video-responsive"> 
      <!-- Modal content-->
        <div class="modal-content video-content">
            <button type="button" id="expert_lecture_close" class="close close-btn-outside" data-dismiss="modal">&times;</button>
            <div class="embed-responsive embed-responsive-16by9">
                <iframe id="youtube_url_expert" src="" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
  <!--modal-dialog--> 
</div>
<!--modal--> 

<?php 
 function generate_youtube_url($url=false)
{
    $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if ($result) {
        //return $matches[1];
        return 'https://www.youtube.com/embed/'.$matches[1];
    }
    return false;
}
 ?> 

<script type="text/javascript">
    var __iframe_url = '';
    
    function video_url(url){
        
        var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        var match = url.match(regExp);

        if (match && match[2].length == 11) {
            __iframe_url = 'https://www.youtube.com/embed/'+match[2];
        } else {
            return false;
        }
    }
    $(document).on("click","#video_click_link",function(){
        $("#youtube_url_expert").unbind();
        $("#youtube_url_expert").attr('src', __iframe_url);
        $("#library_video").modal('show');
    });
    
    $(document).on("click","#expert_lecture_close",function(){
        $("#youtube_url_expert").unbind();
        $("#youtube_url_expert").attr('src', '');
    });
    
    var __expertlectureObjects = atob('<?php echo base64_encode(json_encode($expert_lectures)) ?>');
    
    var __site_url     = '<?php echo site_url(); ?>';
    var __perPage      = '<?php echo $per_page ?>';
    var __offset       = 2;
    var __start        = false;
    var __asset_url    = '<?php echo assets_url() ?>';
    var __theme_url    = '<?php echo $this->config->item('theme')?>';
    
    $(document).ready(function () {
        __expertlectureObjects      = $.parseJSON(__expertlectureObjects);
        
        $('#expert_lecture_wrapper').html(renderExpertLecturesHtml(__expertlectureObjects));
    });
    
    
    
    function loadMoreExpertLecture()
    {
        $('#view_more_btn a').html('Loading...');
        getExpertLectures();
    }
    
    function getExpertLectures()
    {
        AbortPreviousAjaxRequest();
        __requests.push($.ajax({
            url: __site_url+'expert_lectures/expert_lectures_json',
            type: "POST",
            data:{"is_ajax":true, 'offset':__offset},
            success: function(response) {
                var data = $.parseJSON(response);
                
                if(data['error']==false)
                {
                    if(__start == true)
                    {
                       
                        $('#expert_lecture_wrapper').html('');
                        $('#expert_lecture_wrapper').html(renderExpertLecturesHtml(data['expert_lectures']));
                    }
                    else
                    {
                        $('#expert_lecture_wrapper').append(renderExpertLecturesHtml(data['expert_lectures']));
                    }
                    __start = false;
                    __offset++;
                }
            }
        }));
    }
    
    function renderExpertLecturesHtml(expert_lectures)
    {
        var expertlecturesHtml = '';
        $('#view_more_btn a').html('View More').hide();
        if(Object.keys(expert_lectures).length > 0 )
        {
            var count_video = 1;
            
            $.each(expert_lectures, function(expertlectureKey, expert_lecture )
            {
                    
                    if(count_video%4 == 1){
                        expertlecturesHtml += '<div class="row">';
                    }
                    expertlecturesHtml += '  <div class="col-md-3 col-sm-3 xs-replacer">';
                    expertlecturesHtml += '      <div class="video-card-library-wrap">';
                    expertlecturesHtml += '          <div class="video-card-wrap">';
                    expertlecturesHtml += '              <span class="video-click-img video-click-library">';
                    expertlecturesHtml += '                  <a href="javascript:void(0);" id="video_click_link" class="video-click-link" onclick="video_url(\''+expert_lecture['el_url']+'\')">';
                    expertlecturesHtml += '                      <img src="'+expert_lecture['el_image']+'" class="video-thumb video-thumb-library">';
                    expertlecturesHtml += '                      <span class="hover-video"><img src="'+__asset_url+'themes/'+__theme_url+'/img/video-hover.svg"></span>';
                    expertlecturesHtml += '                  </a>';
                    expertlecturesHtml += '              </span>';
                    expertlecturesHtml += '              <span class="videocard-description">';
                    expertlecturesHtml += '                  <label>'+expert_lecture['el_title']+'</label>';
                    expertlecturesHtml += '              </span>';
                    expertlecturesHtml += '          </div>';
                    expertlecturesHtml += '      </div>';
                    expertlecturesHtml += '  </div>';
                    if(count_video%4 == 0){
                        expertlecturesHtml += '</div>';
                    }
            
                count_video++;
                
            });
            
            if (count_video%4 != 1){expertlecturesHtml += '</div>';} 
            if( Object.keys(expert_lectures).length == __perPage)
            {
                $('#view_more_btn a').css('display', 'inline-block');                
            }
        }
        return expertlecturesHtml;
    }
    
    
    var __requests = new Array();
    function AbortPreviousAjaxRequest()
    {
        for(var i = 0; i < __requests.length; i++)
        {
            __requests[i].abort();
        }
    }
</script>
<style>
#view_more_btn a{ display: none;}
</style>

<?php include('footer.php'); ?>

