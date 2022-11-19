
<!DOCTYPE html>
<html>
    <!-- head start-->

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
        <style type="text/css" media="screen">
            .quiz-list{
                border-bottom: 1px solid #ccc;
                height: 40px;
                padding: 10px 0px;
            }
            .quiz-list-title th{
                font-size: 14px;
                font-weight: 600;
                padding: 20px 25px 5px 25px;
                border-bottom: 1px solid #ccc;
                width:15%;
                
            }
            .quiz-list td{
                font-size: 14px;
                padding: 0 25px;
            }
            .quiz-list-avatar{display: inline-block;vertical-align: inherit;padding: 0 15px;}
            .invisible{visibility: hidden;}
            .bold{font-weight: 600;}
            .course-performance-wrapper{
                top: 10px;
                position: relative;
                padding:0 30px;
            }
            .export-btn{
                padding:15px;
            }
            
        </style>
    </head>
    <body >
        
        <!-- Manin Iner container start -->
        <div class='bulder-content-inner add-question-block'>
            <div class="col-sm-12 bottom-line question-head">
                <h3 class="question-title">Course Performance Report - <?php echo $course_title; ?></h3>
      
                <?php 
                $history_url = admin_url('report/course_performance');
                ?>
                <span class="cb-close-qstn"><i class="icon icon-cancel-1" onclick="location.href='<?php echo $history_url ?>'"></i></span>
            </div>
            <div class="col-sm-12 question-block">
            <div class="rTableCell">
                <?php 
                if($status=='1'){
                ?>
                    <?php
                    if (!empty($this->report_privilege)) {
                        if (in_array($this->privilege['view'], $this->report_privilege) && in_array($this->privilege['edit'], $this->report_privilege)) {
                    ?>

                    <div class="export-btn">
                        <button class="pull-right btn btn-green" onclick="export_excel();">EXPORT</button>
                    </div>
                <?php
                        }
                    }
                }?>
                
                <div class="container-fluid course-performance-wrapper">
                    <?php
                        if($status=='1'){
                    ?>

                        <table style="width: 100%;">
                            <thead class="quiz-list-title">
                                <tr>
                                    <th class="text-left" style="width: 5%;">Sl.no</th>
                                    <th class="text-left">Institute Name</th>
                                    <th class="text-center">Course Likes</th>
                                    <th class="text-center">Course Dislikes</th>
                                    <th class="text-center">Course forum Likes</th>
                                    <th class="text-center">Course forum Dislikes</th>
                                    <!-- <th class="text-center">Expiry Date</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i=1;
                                    foreach($institutes as $institute){
                                ?>
                                
                                    <tr class="quiz-list">
                                        
                                            <td class="text-left text-blue" style="width: 5%;"><?php echo $i++;?></td>
                                            <td class="text-left"><?php echo $institute['institute_name']; ?></td>
                                            <td class="text-green text-center bold"><span class="icon-like"></span><?php echo $institute['cp_course_likes']; ?></td>
                                            <td class="text-red text-center bold"><span class="icon-unlike"></span><?php echo $institute['cp_course_dislikes']; ?></td>
                                            <td class="text-green text-center bold"><span class="icon-like"></span><?php echo $institute['cp_forum_likes']; ?></td>
                                            <td class="text-red text-center bold"><span class="icon-unlike"></span><?php echo $institute['cp_forum_dislikes']; ?></td> 
                                    </tr>
                            
                                <?php
                                    }
                                ?>
                                

                            </tbody>
                        </table>
                        <!-- quiz report ends here -->
                <?php
                }else{
                ?>
                    <p>No Data to Display</p>
                <?php
                }
                ?>
                </div>
            </div>

   
        </div>

    </body>
    <!-- body end-->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>js/app.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>/assets/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>/assets/js/ckeditor/config.js"></script>

<script>

function export_excel(){
    location.href = '<?php echo admin_url('report/course_institute_performance/'.$course_id.'/export') ?>';

}
    </script>
</html>
