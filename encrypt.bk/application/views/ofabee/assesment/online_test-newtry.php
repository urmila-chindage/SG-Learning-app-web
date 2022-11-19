<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <meta name="description" content="Free Web tutorials">
        <meta name="keywords" content="HTML,CSS,XML,JavaScript">
        <meta name="author" content="John Doe">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <?php /* ?><link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.mobile-1.5.0-alpha.1.min.css"><?php */ ?>
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/css/custom.css">
        <style>
            .ui-body-a{ display: none;}
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
            $assesment['a_instructions'][1]=($instruction);   
        }
    }
}

?>
    <style>
        .test-main-wrap{ visibility: hidden;}
    </style>
    <div class="test-main-wrap" id="test_wrapper">
        <div class="question-wrap-only">
            <div class="question-wrap-head">
                <h4 class="question-wrap-title" id="test_name"></h4>
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
                
                <div class="question-slider-wrap" >
                    <div class="arrows arrow-left-dot" onclick="previousQuestion()">
                        <svg fill="#ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/>
                            <path d="M0-.5h24v24H0z" fill="none"/>
                        </svg>
                    </div>
                    <div class="questionAndanswer-warp" id="question_answer_wrapper">
                    </div>
                    <!-- questionAndanswer-warp -->
                    <div class="arrows arrow-right-dot" onclick="nextQuestion()">
                        <svg fill="#ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/>
                            <path d="M0-.25h24v24H0z" fill="none"/>
                        </svg>
                    </div>
                </div>
                <!-- question-slider-wrap -->
                <div class="questionAndanswer-warp-footer">
                    <input style="visibility:hidden;" type="button" id="mark_for_review" class="btn btn-footer" value="Mark for review & Next">
                    <div class="clearSave-wrap">
                        <span class="cleardropsbtn" onclick="ClearResponse()">
                                    <span>Clear</span>
                        <svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                                        </svg>
                        </span>
                        <a id="save_and_next" class="btn btn-blue btn-text-align" href="javascript:void(0)"></a>
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
                <input class="btn btn-submit-finsih" onclick="renderOverviewPopUp()" type="button" value="Submit and Finish">
            </div>
            <!-- submit-finish-wrap -->
            <div class="slide-puller-wrap">
                <svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/>
                        <path d="M0-.5h24v24H0z" fill="none"/>
                    </svg>

                <svg style="display:none" fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
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
                <h4 class="asseseSum">Assessment Summary</h4>
                <div class="assesTimeWrap">
                    <span class="time">Time&nbsp;-</span>
                    <span class="time-counter" id="timer_display"></span>
                </div>
                <!-- assesTimeWrap -->
                <div class="assesment-anser-wrap">
                    <div class="answer-row">
                        <div class="left-side-answer">
                            <div class="assesblock-wrap">
                                <span class="assesment-block assesment-magent"></span>
                                <span id="total_answered"></span>
                            </div>
                            <div class="assesblock-wrap">
                                <span class="assesment-block assesment-green"></span>
                                <span id="total_not_visited"></span>
                            </div>
                        </div>
                        <!-- left-side-answer -->
                        <div class="right-side-answer">
                            <div class="assesblock-wrap">
                                <span class="assesment-block assesment-pink"></span>
                                <span id="total_not_answered"></span>
                            </div>
                            <div class="assesblock-wrap">
                                <span class="assesment-block assesment-grey"></span>
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
            <!-- CONTENT ENDS HERE -->

        </div>
    </div>
    
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.mobile-1.5.0-alpha.1.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/js/bootstrap.min.js"></script>
<script>
var Configs = function()
{
    this._assesmentSettings = $.parseJSON(atob('<?php echo base64_encode(json_encode($assesment)) ?>'));
    
    this._assesmentId = '<?php echo $assesment_id ?>';
    this._attemptId = '<?php echo 1 //$attempt_id ?>';
    this._activeLanguage = '1';
    this._siteUrl = '<?php echo site_url(); ?>';
};
//console.log(data =new Configs());
</script>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/assesment/js/online_exam_beta.js"></script>
<script>
    $(document).ready(function() {
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
</body>
</html>
