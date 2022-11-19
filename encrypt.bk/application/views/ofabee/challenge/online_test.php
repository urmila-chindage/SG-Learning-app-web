<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
        <!-- online text -->
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
        <div  class="" id="instruction_tab">
            <div class="instruction_content">
                <?php echo $challenge['cz_instructions'] ?>
            </div>
            <div class="instruction_button">
                <a href="javascript:void(0)" id="load_exam_button">Start Test</a>
            </div>
        </div>
        <div  class="" id="quesion_paper_tab" style="position: absolute; display: none;">
            <div class="online_text">
                <div class="row question-content">
                   
                </div>
            </div>
        </div>
        <!--------->

        <!-- Modal pop up contents:: Edit Profile Popup-->
        <div class="modal fade" id="assesment-summary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header modal-header-alter">
                        <!--<button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>-->
                        <h3 class="modal-title modal-title-center" id="myModalLabel">ASSESMENT SUMMARY</h3>
                    </div>
                    <div class="modal-body modal-body-bottom">
                    </div>
                </div>
            </div>
        </div>
        
        
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
            var __challenge_id  = '<?php echo $challenge_id ?>';
        </script>
        <script src="<?php echo assets_url('themes/' . config_item('theme') . '/assesment') ?>js/online_challenge.js"></script> 
        
    </body>
</html>