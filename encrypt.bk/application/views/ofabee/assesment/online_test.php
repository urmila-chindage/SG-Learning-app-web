<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="images/favicon.ico">
        <title><?php echo isset($title) ? $title : config_item('acct_name') ?> </title>
        <!-- Bootstrap core CSS -->
        <link href="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>css/bootstrap-select.css" rel="stylesheet">
        <link href="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>css/owl.carousel.css" rel="stylesheet">
        <link href="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>css/starability-all.min.css" rel="stylesheet">
        <link href="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>css/jquery.mCustomScrollbar.css" rel="stylesheet">
        <link href="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>css/styles.css" rel="stylesheet">
        <link href="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>css/submit-test.css" rel="stylesheet">
        
        <link rel="icon" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/favicon.ico">
        <!--[if lt IE 9]>
        <script src="js/ie8-responsive-file-warning.js"></script>
        <![endif]-->
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/ie-emulation-modes-warning.js"></script>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">

            #instruction_tab {
                background: #fcfcfc none repeat scroll 0 0;
                margin: 0 auto;
                padding: 8px;
                width: 80%;
            }
            .instruction_button > a {
                background: #8b00b3 none repeat scroll 0 0;
                border-radius: 3px;
                color: #fff;
                padding: 8px 15px;
                text-align: center;
                width: auto;
                text-decoration: none;
				text-align:center;
					    position: absolute;
    margin-left: 46%;
            }
        </style> 
    </head>

    <body>
        <!---- online text ----->
        <div class="online_text_main" id="exam_asset_tab" style="display: none;">
            <div class="online_text">
                <div class="row">
                    <div class="qus_main">
                        <div class="col-sm-9 wd">
                            <h2 class="sbi" ><?php echo isset($title) ? $title :''; ?></h2>
                            <div class="mobile_button"><a id="open">View Question Palette</a> <a id="close">Close Question Palette</a></div>
                            <div class="sction_div">
                                <ul class="nav nav-tabs" id="categories">
                                </ul>
                                <div class="tab-content" id="question_tabs">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 wd2 mobile_qus">
                            <div class="time_main">
                                <div class="time">
                                    <div class="time_left">Time Left:</div>
                                    <div class="time_right" id="time_out_label"></div>
                                </div>
                                <div class="qus_heading" id="current_category_title"></div>
                                <div class="content">
                                    <h3>Question Palette:</h3>
                                    <div class="qus_section" id="question_pallette">
                                        <?php /* ?><a class="qus_number red_bg" data-id="1">01</a>
                                        <a class="qus_number green_bg" data-id="13">13</a>
                                        <a class="qus_number purpal_bg" data-id="21">21</a><?php */ ?> 
                                    </div>
                                </div>
                                <div class="legend">
                                    <h4>Legend:</h4>
                                    <div class="allowd">
                                        <div class="allowd_box"><span class="green"></span> Answered</div>
                                        <div class="allowd_box"><span class="red"></span> Not answered</div>
                                        <div class="allowd_box"><span class="purpal"></span> Marked</div>
                                        <div class="allowd_box"><span class="gray"></span> Not visited</div>
                                    </div>
                                    <div class="all_qus_main">
                                        <a href="javascript:void(0)" onclick="renderQuestionPaper()">All Questions</a>
                                        <a href="javascript:void(0)" onclick="renderInstruction()">Instructions</a>
                                    </div>
                                    <div class="submit_form"><a href="javascript:void(0)" onclick="reviewAndSubmitExam()">Review and Submit</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div  class="" id="instruction_tab" style="display:none;">
            <div class="instruction_content">
                <?php echo (trim($assesment['a_instructions']) && strip_tags(trim($assesment['a_instructions'])) != '')?$assesment['a_instructions']:get_instruction(); ?>
            </div>
            <div class="instruction_button">
                <a href="javascript:void(0)" id="load_exam_button">Start Test</a>
            </div>
        </div>
        <div class="" id="quesion_loading_tab" style="display: none;">
            <div class="online_text">
            <div class="container container-margin-bottom">
                <div class="row ">
                <div class="col-lg-12 question-content"><div class=" text-center"><h3>Loading exam assets..</h3></div></div>
                   </div><!--container-->
                </div>
            </div>
        </div>
        <div  class="" id="quesion_paper_tab" style="display: none;">
            <div class="online_text">
            <div class="container container-margin-bottom">
                <div class="row ">
                <div class="col-lg-12 question-content"></div>
                   </div><!--container-->
                </div>
            </div>
        </div>
        
        <!-- Modal pop up contents:: Edit Profile Popup-->
        <div class="modal fade" id="assesment-summary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header modal-header-alter">
                        <!--<button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>-->
                        <h3 class="modal-title modal-title-center" id="myModalLabel">ASSESSMENT SUMMARY</h3>
                    </div>
                    <div class="modal-body modal-body-bottom">
                    </div>
                </div>
            </div>
        </div>
        
        <!--------->

        <!-- Bootstrap core JavaScript -->
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/jquery.min.js"></script>
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/responsive-tabs.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/ie10-viewport-bug-workaround.js"></script>
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/bootstrap-select.js"></script>
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/owl.carousel.js"></script>
        <?php /* ?><script src="<?php echo assets_url(config_item('theme') . '/assesment') ?>js/bootstrap-hover-dropdown.js"></script<?php */ ?>
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/jquery.mCustomScrollbar.concat.min.js"></script> 
        <script>
            var __site_url      = '<?php echo site_url() ?>';
            var __course_id     = '<?php echo $course_id ?>';
            var __lecture_id    = '<?php echo $lecture_id ?>';
        </script>
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/online_exam.js"></script> 
        
    </body>
</html>

<?php 
function get_instruction()
{
    return '<div id="dvInstruction">
            <p class="headings-altr"><strong>General Instructions:</strong></p>
            <ol class="header-child-alt">
            <li>The clock has been set at the server and the countdown timer at the top right corner of your screen will display the time remaining for you to complete the exam. When the clock runs out the exam ends by default - you are not required to end or submit your exam.</li>
            <li>The question palette at the right of screen shows one of the following statuses of each of the questions numbered:
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not visited the question yet. ( In White Color )</td>
            <td style="padding-left: 7px;"><div class="gray" style="width: 20px;height: 20px;border-radius: 4px;"></div></td></tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have not answered the question. ( In Red Color )</td>
            <td style="padding-left: 7px;"><div class="red" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have answered the question. ( In Green Color )</td><td style="padding-left: 7px;"><div class="green" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            <table style="margin-bottom: 3px;"><tbody>
            <tr><td>You have marked the for review.( In Pink Color ) </td><td style="padding-left: 7px;"><div class="purpal" style="width: 20px;height: 20px;border-radius: 4px;"></div></td>
            </tr>
            </tbody>
            </table>
            </li>
            <li>Click on the question number on the question palette at the right of your screen to go to that numbered question directly.</li>
            <li>The Marked for Review status simply acts as a reminder that you have set to look at the question again. <em>If an answer is selected for a question that is Marked for Review, the answer will be considered in the final evaluation.</em></li>
            </ol>
            <p class="headings-altr"><strong>Navigating to a question :</strong></p>
            <ol start="5" class="header-child-alt">
            <li>To select a question to answer, you can do one of the following:
            <ol type="a">
            <li>Click on the question number on the question palette at the right of your screen to go to that numbered question directly. Note that using this option does NOT save your answer to the current question.</li>
            <li>Click on Save and Next to save answer to current question and to go to the next question in sequence.</li>
            <li>Click on Mark for Review and Next to save answer to current question, mark it for review, and to go to the next question in sequence.</li>
            </ol>
            </li>
            <li>You can view the entire paper by clicking on the <strong>Question Paper</strong> button.</li>
            </ol>
            <p class="headings-altr"><strong>Answering questions :</strong></p>
            <ol start="7"  class="header-child-alt">
            <li>For multiple choice type question :
            <ol type="a">
            <li>To select your answer, click on one of the option buttons</li>
            <li>To change your answer, click the another desired option button</li>
            <li>To save your answer, you MUST click on <strong>Save & Next</strong></li>
            <li>To deselect a chosen answer, click on the chosen option again or click on the <strong>Clear Response</strong> button.</li>
            <li>To mark a question for review click on <strong>Mark for Review & Next</strong>.&nbsp;</li>
            </ol>
            </li>
            <li>For a numerical answer type question
            <ol type="a">
            <li>To enter a number as your answer, use the virtual numerical keypad</li>
            <li>A fraction (eg. 0.4 or -0.4) can be entered as an answer ONLY with \'0\' before the decimal point</li>
            <li>To save your answer, you MUST click on <strong>Save & Next</strong></li>
            <li>To clear your answer, click on the<strong> Clear Response </strong>button</li>
            </ol>
            </li>
            <li>To change an answer to a question, first select the question and then click on the new answer option followed by a click on the <strong>Save & Next</strong> button.</li>
            <li>Questions that are saved or marked for review after answering will ONLY be considered for evaluation.</li>
            </ol>
            <p class="headings-altr"><strong>Navigating through sections :</strong></p>
            <ol start="11" class="header-child-alt">
            <li>Sections in this question paper are displayed on the top bar of the screen. Questions in a section can be viewed by clicking on the section name. The section you are currently viewing is highlighted.</li>
            <li>After clicking the <strong>Save & Next</strong> button on the last question for a section, you will automatically be taken to the first question of the next section.</li>
            <li>You can move the mouse cursor over the section names to view the status of the questions for that section.</li>
            <li>You can shuffle between sections and questions anytime during the examination as per your convenience.</li>
            </ol></div>';
}
?>