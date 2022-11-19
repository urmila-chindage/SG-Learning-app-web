<style type="text/css">
    body {
     counter-reset:counter;
    }
    .nouseclass:before {
      display: table-cell;
      counter-increment: counter;
      content: counter(counter) '.';
    }
</style>
<div class="all-challenges">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="tab-content">
                    <div id="sdpkDashboard" class="tab-pane active">
                        <div class="row dash-row">    
                            <div class="col-sm-12">
                                <?php if(isset($review_questions['wrong_answer']) && !empty($review_questions['wrong_answer'])): ?>
                                <div class="row">
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <h4 class="sd-course-title">Wrong / Untouched Questions</h4>
                                    </div>                        
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                    <div class="btn-group cat-menu dashboard-cat-menu error-menu">
                                        <button type="button" class="form-control btn dropdown-toggle big-input course-drop" data-toggle="dropdown">
                                            <h3 class="menu-h3 dash-h3 error-h3" id="qcat_selected">Choose <strong>Topic</strong></h3> 
                                            <span class="category-caret dash-caret error-caret">
                                             <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                                <g>
                                                    <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                                    <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                                </g>
                                            </svg>                                   
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu generate-dropdown dashboard-drop">
                                            <?php foreach($question_categories as $key => $qcat): ?>
                                                <li><a href="javascript:void(0)" onclick="change_topic('<?php echo base64_encode($qcat['id']); ?>',<?php echo $key; ?>)"><?php echo $qcat['qc_category_name'] ?></a></li>    
                                            <?php endforeach; ?>
                                        </ul>
                                    </div> 
                                    </div>                        
                                </div>
                                <div class="my-notify">
                                   <ul class="list-group notification revision-questions">
                                        <?php echo render_questions($review_questions['wrong_answer']); ?>
                                    </ul> 
                                </div> 
                                <?php else: ?>
                                    <div class="no-course-container">
                                        <img class="no-questions-svg" src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/no-results.svg">
                                        <span class="no-discussion no-content-text"><span>Oops! </span>No assessments attended yet.</span>
                                        <div class="text-center">
                                            <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Attend Now</a></span>
                                        </div><!--text-center-->
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>   
                    </div>


                    <div id="sdpkReportcard" class="tab-pane fade">

                        <div class="row">
                            <div class="col-xs-6 col-sm-4 col-md-4">
                                <h3 class="dashboard-mycourse-h3">Wishlisted Courses</h3>
                            </div>
                            <div class="col-xs-6 col-sm-8 col-md-8">
                                <div class="btn-group cat-menu dashboard-cat-menu">
                                    <button type="button" class="form-control btn dropdown-toggle big-input course-drop" data-toggle="dropdown">
                                        <h3 class="menu-h3 dash-h3">View by <strong>Category</strong></h3> 
                                        <span class="category-caret dash-caret">
                                            <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                            <g>
                                            <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"/>
                                            <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"/>
                                            </g>
                                            </svg>                                   
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu generate-dropdown dashboard-drop">
                                        <li><a href="#">Category Name</a></li>
                                        <li><a href="#">Category Name</a></li>
                                        <li><a href="#">Category Name</a></li>
                                    </ul>
                                </div> 
                            </div>
                        </div>
                        <div class="row course-cards-row">
                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart tool-tip" data-toggle="tooltip" data-placement="left" title="Remove Wishlist"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit sidebar-card"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                        <!--courser-bottom-half--> 
                                    </div>
                                    <!--course-block-1--> 
                                </a></div>

                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit sidebar-card"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                    <!--course-block-1--> 
                                </a>
                            </div>
                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit sidebar-card"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                        <!--courser-bottom-half--> 
                                    </div>
                                    <!--course-block-1--> 
                                </a>
                            </div>


                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit sidebar-card"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>

                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>                       

                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>


                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div> 


                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>
                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>
                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div> 
                        </div>
                        <div class="row">    
                            <div class="col-sm-12 dashboard-no-course">

                                <div class="no-course-container">
                                    <img class="no-questions-svg" src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/no-wishlist.svg">
                                    <span class="no-discussion no-content-text"><span>Use wishlist </span>to keep track of courses you want to purchase</span>
                                    <div class="text-center">
                                        <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Browse Courses</a></span>
                                    </div><!--text-center-->
                                </div> 				

                            </div>              
                        </div>   
                    </div>
                </div>            
            </div>	<!--container-reduce-width-->
        </div><!--container altr-->       
    </div>

<script type="text/javascript">
    function view_answer(question_id){
        question_id     = atob(question_id);
        var option_html = '';
        var ques_id     = '';
        $.ajax({
            url: __site_url+'material/get_answers',
            type: "POST",
            data:{"is_ajax":true,'question_id':question_id},
            success: function(response) {
                var data = $.parseJSON(response);
                if(data['success'] == false){
                    toastr["error"](data["message"]);
                }else{
                    //console.log(data);
                    ques_id     = '';
                    if (data['question'].id.toString().length == 1) {
                        ques_id = "0" + data['question'].id;
                    }else{
                        ques_id = data['question'].id
                    }
                    $('#modalQuestion').html('<span class="badge lefty round-label main-label qstno"><span class="big-modal-date">'+ques_id+'</span></span>'+data['question'].q_question);
                    $.each(data['options'], function(op_index,option) {
                      option_html += '<li class="list-group-item single-event modal-event">';
                      if(option['stat'] == true){
                        option_html += '<a href="javascript:void(0)"><span class="badge lefty round-label modal-badge answer-round"><img src="'+__assets_url+'themes/ofabee/img/checked.svg" width="24"></span>';
                      }else{
                        option_html += '<a href="javascript:void(0)"><span class="badge lefty round-label modal-badge answer-round"><img src="'+__assets_url+'themes/ofabee/img/unchecked.svg" width="24"></span>';
                      }
                      option_html += '<span class="event-title qstn-answer">'+option['qo_options']+'</span></a>';
                      option_html += '</li>';
                    });
                    $('#questionOptions').html(option_html);
                    $('#answerModal').modal('show');
                }
            }
        });
    }
</script>


<?php 
function render_questions($questions)
{ 
    $question_html = '';
    foreach($questions as $question)
    {
        $question_html .= '<li class="list-group-item qcat-hide qcat'.$question['q_category'].'">';
        $question_html .= ' <span class="sl-nbr"><span class="nouseclass"></span></span>';
        $question_html .= '  <span class="rev-nbr">#'.$question['id'].'</span>';
        $question_html .= '  <span class="rev-content">';
        $question_html .= '      <p class="rev-text">'.$question['q_question'].'</p></span>';
        $question_html .= '      <span class="rev-link">';
        if($question['q_type']!=3)
        {
            $question_html .= '     <a href="javascript:void(0)" onclick="view_answer(\''.base64_encode($question['id']).'\')">View Answer</a>';
        }
        $question_html .= '      </span>';
        $question_html .= '</li>';
    } 
    return $question_html;
 }                     
