<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <meta name="description" content="Free Web tutorials">
        <meta name="keywords" content="HTML,CSS,XML,JavaScript">
        <meta name="author" content="John Doe">
        <link rel="icon" href="<?php echo base_url('favicon.png'); ?>" type="image/x-icon"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <?php /* ?><link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.mobile-1.5.0-alpha.1.min.css"><?php */ ?>
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/css/custom.css?v=27092019" >
        <style>
            .ui-body-a{ display: none;}
            .alert-modal-new .modal-header{
                border-bottom: 0;
                float: right;
                width: 40px;
                height: 40px;
                background: none;
            }
            .alert-modal-new .modal-header .close {
                font-size: 24px;
                color: #838383;
                right: 13px;
                top: 9px;
                z-index: 9;
                position: relative;
            }
            .alert-modal-new .modal-footer{
                border-top:0px;
            }
            .alert-text {
                padding: 25px;
                font-size: 16px;
                font-weight: 400;
                color: #8f8f8f;
            }
            .dash_line{
                border-bottom: 1px solid black;
                padding: 0em 2em;
                margin: 0em 1em;
                word-break: break-word;
                display: inline-block;
                
            }
            .assesment-grey{
                background:#f0f0f0!important;
            }
            #test_name{
            }
            #course-tile{
                margin-right:5px;
                display:inline-block;
                color: #969696;
                font-weight: 500;
            }
            .table-row-radio label{
                cursor: pointer;
            }
            .table-radio .table-row-radio label {
                background: transparent !important;
                margin-bottom: 5px;
            }
            .table-radio .table-row-radio label:hover {background: #f1f1f1 !important;}
            input[type=checkbox], input[type=radio] {display: none;}
            .radio-success{background: #2492000f !important;}
            .break-cursor{pointer-events: none;}
            .checkbox-success{background: #2492000f !important;}
            
        </style>
    </head>
<body>
<?php 
$assesment_instruction          = $assesment['a_instructions'];
if($assesment_instruction)
{
    $assesment_instruction    = json_decode($assesment_instruction, true);        
    if(json_last_error() == JSON_ERROR_NONE)
    {
        if(!empty($assesment_instruction))
        {
            $assesment['a_instructions'] = array();
            foreach ($assesment_instruction as $language_id => $instruction)
            {
                $assesment['a_instructions'][$language_id] = ($instruction);
            }
        }
    }
}
//$assesment_instruction          = $assesment['a_instructions'][1];
?>
    <style>
        .test-main-wrap{ visibility: hidden;}
    </style>
    <div class="test-main-wrap onlinetest-window" id="test_wrapper">
        <div class="question-wrap-only">
            <div class="question-wrap-head">
                <h4 class="question-wrap-title" >
                    <span id="course-tile">fdcgdfg</span>:
                    <span id="test_name"></span>
                </h4>
                <span class="min-left-mob">
                    <span class="time-high" id="min_left"></span> min left
                </span>
                <div class="progressbar-wrap">
                    <div class="question-remaining-wrap">
                        <span id="current_question"></span>
                        <span class="time-bar">/</span>
                        <span class="time-remain" id="total_question">76</span>
                    </div>
                    <div class="progressbar-question-wrap">
                        <span class="pogress-ans" style="width: 0%"></span>
                    </div>
                </div>
                <!-- progressbar-wrap -->

            </div>

            <div class="timer-xs-visible">
                <span class="min-left-mob">
                    <span class="time-high" id="min_left"></span> min left
                </span>
            </div>
            <!-- question-wrap-head -->
            <div class="question-wrap-full">
                <div class="question-type-wrap">
                    <span><span class="q1mob" id="question_number_small"></span><span class="q-type-hide">Question Type : </span><span class="semibold" id="question_type_label"></span></span>
                    <span class="quest-no-btn" id="question_number_big"></span>
                    <div class="question-drops-wrap" id="test_language">
                    </div>
                </div>
                <!-- question-type-wrap -->
                <div class="negative-mark-wrap" style="display: none;">This question carries <span class="mark-highlight" id="positive_mark"></span> mark(s)</div>
                
                <div class="question-slider-wrap" id="question_scroll-trigger">
                    <!-- <div class="arrows arrow-left-dot" id="prev-question" onclick="previousQuestion()">
                        <svg fill="#ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/>
                            <path d="M0-.5h24v24H0z" fill="none"/>
                        </svg>
                    </div> -->
                    <div class="questionAndanswer-warp" id="question_answer_wrapper">
                    </div>
                    <!-- questionAndanswer-warp -->
                    <!-- <div class="arrows arrow-right-dot" id="next-question" onclick="nextQuestion()">
                        <svg fill="#ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/>
                            <path d="M0-.25h24v24H0z" fill="none"/>
                        </svg>
                    </div> -->
                </div>
                <!-- question-slider-wrap -->
                <div class="questionAndanswer-warp-footer">
                    <div id="mark_review_html">
                    </div>
                    <div class="clearSave-wrap">
                        <span class="cleardropsbtn" onclick="ClearResponse()">
                                    <span>Clear</span>
                        <svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                                        </svg>
                        </span>
                        <a id="save_and_next" onclick="scrolltop()" class="btn btn-blue btn-text-align" href="javascript:void(0)"></a>
                    </div>
                    <!-- clearSave-wrap -->
                </div>
                <!-- questionAndanswer-warp-footer -->
            </div>


        </div>
        <!-- question-wrap-only -->

        <div class="question-pallet-wrap">
            <div class="question-pallet-head">
                <span class="time_left">TIME LEFT</span>
                <div class="timmer-wrap-count">
                    <svg fill="#82919b" height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 0h24v24H0z" fill="none"/>
                            <path d="M22 5.72l-4.6-3.86-1.29 1.53 4.6 3.86L22 5.72zM7.88 3.39L6.6 1.86 2 5.71l1.29 1.53 4.59-3.85zM12.5 8H11v6l4.75 2.85.75-1.23-4-2.37V8zM12 4c-4.97 0-9 4.03-9 9s4.02 9 9 9c4.97 0 9-4.03 9-9s-4.03-9-9-9zm0 16c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                        </svg>
                    <div class="time-main-wrap">
                        <div id="time_remaining">
                            <?php /* ?><span>01</span>:
                            <span>23</span>:
                            <span>41</span><?php */ ?>
                        </div>
                        <div class="time-sep">/</div>
                        <div class="remain-time-wrap" id="total_time">
                            <?php /* ?><span>01</span>:
                            <span>23</span>:
                            <span>41</span><?php */ ?>
                        </div>
                    </div>
                    <!-- time-main-wrap -->
                </div>
                <!-- timmer-wrap-count -->
            </div>

            <!-- question pallete container starts -->
            <div class="question-pallete-container">
                <!-- question-pallet-head -->
                <div class="gen-view-question" id="test_map" style="display: none;">
                </div>
                <!-- gen-view-question -->
                <div class="quest-pallet-title">
                    Question Palette
                </div>
                <div class="question-pallets-pal">
                    <ul id="question_pallette">
                        <?php /* ?><li class="not-answerd-pallet">1</li>
                        <li class="answerd-pallet">2</li>
                        <li>3</li>
                        <li class="marked-pallet">4</li>
                        <li class="not-answerd-pallet">5</li>
                        <li>6</li>
                        <li>7</li>
                        <li class="answerd-pallet">8</li>
                        <li>9</li>
                        <li>10</li>
                        <li class="not-answerd-pallet">11</li>
                        <li>12</li>
                        <li>13</li>
                        <li class="answerd-pallet">14</li>
                        <li>15</li>
                        <li>16</li>
                        <li class="not-answerd-pallet">17</li>
                        <li>18</li>
                        <li class="marked-pallet">19</li>
                        <li>20</li>
                        <li>21</li>
                        <li class="marked-pallet">22</li>
                        <li class="answerd-pallet">23</li>
                        <li>24</li>
                        <li>25</li>
                        <li class="not-answerd-pallet">26</li>
                        <li>27</li>
                        <li class="answerd-pallet">28</li>
                        <li class="answerd-pallet">29</li>
                        <li>30</li>
                        <li>31</li>
                        <li>32</li>
                        <li>33</li>
                        <li>34</li>
                        <li>35</li>
                        <li>36</li>
                        <li>37</li>
                        <li>38</li>
                        <li>39</li>
                        <li>40</li>
                        <ul>
                            <li class="marked-pallet">31</li>
                            <li>32</li>
                            <li class="marked-pallet">33</li>
                        </ul><?php */ ?>
                    </ul>

                </div>
                <!-- question-pallets-pal -->
                <ul class="blocks-colors-wrap">
                    <li>
                        <span class="answerd-pallet"></span>
                        <label>Answered</label>
                    </li>
                    <li>
                        <span class="not-answerd-pallet"></span>
                        <label>Not Answered</label>
                    </li>
                    <li>
                        <span class="marked-pallet"></span>
                        <label>Marked</label>
                    </li>
                    <li>
                        <span></span>
                        <label>Not Visited</label>
                    </li>
                </ul>
                <div class="submit-finish-wrap">
                    <input class="btn btn-submit" type="button" onclick="renderInstructionPopUp()" value="Instructions">
                    <input class="btn btn-submit-finsih" onclick="renderOverviewPopUp()" type="button" value="Submit & Finish">
                </div>
            </div>
            <!-- question pallete container ends -->

            <!-- submit-finish-wrap -->
            <div class="slide-puller-wrap">
                <svg id="pallete-opnr" fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/>
                    <path d="M0-.5h24v24H0z" fill="none"/>
                </svg>
                <svg id="pallete-clsr" style="display:none" fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    <path d="M0 0h24v24H0z" fill="none"/>
                </svg>
            </div>
            <!-- slide-puller-wrap -->

        </div>
        <!-- question-pallet-wrap -->
    </div>
    <!-- test-main-wrap -->
    
    <div id="portfolio-popup">
        <div class="portfolio-popup-area">
            <div class="portfolio-popup-inner"><a id="close_button" style="display:none;" href="javascript:void(0)" onclick="closeOverview()" class="modal-close-btn"><span class="popclose">
                    <svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        <path d="M0 0h24v24H0z" fill="none"/>
                    </svg>
            </span></a></div>
            <!--  CONTENT GOES FROM HERE -->
            <div class="congtrz-wrap congtrz-wrap-alter" id="overview_popup" style="display:none;">
                <h4 class="asseseSum">Quiz Summary</h4>
                <div class="assesTimeWrap">
                    <span class="time">Time&nbsp;-</span>
                    <span class="time-counter" id="timer_display"></span>
                </div>
                <!-- assesTimeWrap -->
                <div class="assesment-anser-wrap">
                    <div class="answer-row">
                        <div class="left-side-answer">
                            <div class="assesblock-wrap">
                                <span class="assesment-block answerd-pallet"></span>
                                <span  id="total_answered"></span>
                            </div>
                            <div class="assesblock-wrap">
                                <span class="assesment-block assesment-grey"></span>
                                <span id="total_not_visited"></span>
                            </div>
                        </div>
                        <!-- left-side-answer -->
                        <div class="right-side-answer">
                            <div class="assesblock-wrap">
                                <span class="assesment-block not-answerd-pallet"></span>
                                <span id="total_not_answered"></span>
                            </div>
                            <div class="assesblock-wrap">
                                <span class="assesment-block marked-pallet"></span>
                                <span id="total_marked_review"></span>
                            </div>
                        </div>

                    </div>
                    <!-- answer-row -->
                </div>
                <!-- assesment-anser-wrap -->
                <div class="assesment-btn-wrap">
                    <input class="btn btn-grey btn-grey-right-margin" onclick="closeOverview()" type="button" value="Review">
                    <input class="btn btn-blue btn-blue-large" id="submit_exam_btn" type="button" onclick="submitExamInit()" value="Submit">
                </div>
            </div>
            <!-- CONTENT ENDS HERE -->
            
            <!-- Instruction here -->
            <div class="congtrz-wrap congtrz-wrap-alter instruction-topped" id="instruction_popup" style="display:none;">
                <img class="congratz-logo" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/instruction.png" alt="Congratz_logo">
                <h2 class="congratz-title instruction-text">Here are some important instructions to be followed</h2>
                
                <div id="instruction_content_wrapper" class="text-left">
                    <?php echo $assesment_instruction; ?>
                </div>
                <!-- congratz-score-wrap -->
                <div class="instructionBtn-wrap">
                    <a class="btn btn-blue btn-condition" href="javascript:void(0)" id="start_test">
                        <span>START TEST</span>
                        <img class="arrow-btn" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/btn-arrow.png" alt="Instruction icon tick">
                    </a>
                </div>
                <!-- instructionBtn-wrap -->
            </div>
            <!-- Instruction set ends here -->
            
            <!--  CONTENT GOES FROM HERE -->
            <div class="question-wrap-full" id="report_wrapper" style="display: none;">
                <div class="question-type-wrap"> 
                    <span><span class="q1mob" id="question_number_small_report"></span><span class="q-type-hide">Question Type : </span><span class="semibold" id="question_type_reort_label"></span></span>
                    <span class="quest-no-btn" id="question_number_big_report"></span>
                    <div class="question-drops-wrap">
                        <div class="instructionBtn-wrap"><a class="btn btn-blue btn-condition" href="javascript:void(0)" onclick="closeReport()"><span>RESUME TEST</span></a></div>
                        <?php /* ?><div class="dropdown">
                            <button class="btn btn-default btn-need-trans dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">English
                                <svg fill="#999999" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"></path>
                                    <path d="M0-.75h24v24H0z" fill="none"></path>
                                </svg>
                            </button>
                            <ul class="dropdown-menu englsih-drop-left" role="menu" aria-labelledby="menu1">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">HTML</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">CSS</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">JavaScript</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">About Us</a></li>
                            </ul>
                        </div><?php */ ?>
                    </div>
                    <!-- question-drops-wrap -->
                </div>
                <!-- question-type-wrap -->
                <div class="negative-mark-wrap">This question carries <span class="mark-highlight" id="positive_mark_report"></span> mark(s)</div>
                <!-- negative-mark-wrap -->
                <div class="question-slider-wrap question-slider-no-scroll">
                    <div class="questionAndanswer-warp" id="question_answer_report_wrapper">
                    </div>
                    <!-- questionAndanswer-warp -->
                </div>
                
                <!-- question-slider-wrap -->
            </div>


        </div>
    </div>

    <?php 
    $web_languages   = array();
    include_once "modals.php"; ?>
    

<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.mobile-1.5.0-alpha.1.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/js/bootstrap.min.js"></script>
<script>
var Configs = function()
{
    this._assesmentSettings = $.parseJSON(atob('<?php echo base64_encode(json_encode($assesment)) ?>')); 
    this._assesmentId = '<?php echo $assesment_id ?>';
    this._attemptId = '<?php echo $attempt_id ?>';
    this._activeLanguage = '1';
    this._siteUrl = '<?php echo site_url(); ?>';
};
</script>
<script>
    const __url_token   = '<?php echo isset($user_token)?$user_token:"" ?>';
</script>

<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/assesment/js/online_exam_beta.js"></script>
<script>
    var __swap_count   = 0;
    var __swap_total   = 5;
    var __swap_balance = 0;
    document.addEventListener("visibilitychange", function() {
        if (document.hidden){
            __swap_count = __swap_count+1; 
            __swap_balance = __swap_total-__swap_count;
            if(__swap_balance <= 0){
                submitExamInit();
            } else {
                showCommonModal('Heading', 'You have minimized the window for '+__swap_count+' times. if you attempt '+__swap_balance+' more times,the quiz will be auto submit.', 3);
            }
        }
    });
    $(window).blur(function(e) {
            __swap_count = __swap_count+1; 
            __swap_balance = __swap_total-__swap_count;
            if(__swap_balance <= 0){
                submitExamInit();
            } else {
                showCommonModal('Heading', 'You have minimized the window for '+__swap_count+' times. if you attempt '+__swap_balance+' more times,the quiz will be auto submit.', 3);
            }
        // 
    });
    $(document).focus(function(e) {
        if (document.hidden){
            __swap_count = __swap_count+1; 
            __swap_balance = __swap_total-__swap_count;
            if(__swap_balance <= 0){
                submitExamInit();
            } else {
                showCommonModal('Heading', 'You have minimized the window for '+__swap_count+' times. if you attempt '+__swap_balance+' more times,the quiz will be auto submit.', 3);
            }
        }
    });
   
   
    $(document).ready(function() {
        renderTestHtml(__settings.testObject());
        $('#test_wrapper').css('visibility', 'visible');
        closeOverview();
        $('#close_button').removeAttr('style');
        $('#start_test span').html('RESUME TEST');
        $('#start_test').unbind('click');
        $('#start_test').on("click", function(){
            closeOverview();
        });
        $(".slide-puller-wrap").on("click", function() {
            $(".slide-puller-wrap svg").each(function() {
                if ($(".slide-puller-wrap svg").css('display') == 'none') {
                    $(this).css("display", "block");
                } else {
                    $(this).css("display", "none");
                }
            });
            $(".question-pallet-wrap").toggleClass("question-pallet-wrap-puller");
        });
    });
</script>

<script type="text/javascript">
    let closeButton = false;
    try { 
        AndroidScriptInterface.progressDismiss();
        
    }
    catch(err) {
       console.log(err);
    }
    // $(document).ready(function () {
    //     //Disable cut copy paste
    //     $('body').bind('cut copy paste', function (e) {
    //         e.preventDefault();
    //     });
    //     //Disable mouse right click
    //     $("body").on("contextmenu",function(e){
    //         return false;
    //     });
    // });

    // When the user clicks on the Save & Next, scroll to the top of the Question
    function scrolltop() {
        var elmnt = document.getElementById("question_scroll-trigger");
        elmnt.scrollTop = 0;
    }
    $("#pallete-opnr").click(function(){
        window.location.hash = '#pallete';
    });
    $("#pallete-clsr").click(function(){
        closeButton = true;
        parent.location.hash = '';
    });
    $(window).bind("hashchange", function(event){
        console.log('hashchange');
    });
    $(window).on("navigate",function(event, data) {
        palleteHandler();
    });    
    document.addEventListener('backbutton', function(){
        console.log('backbutton pressed');
        palleteHandler();
    });
    function palleteHandler(){
        var hash = window.location.hash;
        console.log(hash);
        if(hash!='#pallete')
        {
            if(closeButton !== true){
                $('#pallete-clsr').trigger('click');
            }
            closeButton = false;
        }
        else if(hash == '#pallete')
        {
            let status = $('#pallete-clsr').css('display');
            if(status != 'block'){
                $('#pallete-opnr').trigger('click');
            }
        }
    }
    function palletStatus(){
        let status = $('#pallete-clsr').css('display');
            if(status == 'block'){
                $('#pallete-clsr').trigger('click');
                WebAppInterface.getSideBarStatus('0');
            }else{
                WebAppInterface.getSideBarStatus('1');
            }
    }
</script>
</body>
</html>
