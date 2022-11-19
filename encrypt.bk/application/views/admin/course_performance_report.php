<?php include_once 'header.php';?>
<?php include_once ('report_tab.php');?>
<!-- <div class='dashbrd-container pos-top50'>
    <div class="col-sm-12 nav-content course-report-nav">
        <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow">

                    <div class="rTableCell">
                    <div class="input-group">
                        <input class="form-control srch_txt" id="student_keyword" placeholder="Search by name" type="text">
                        <a class="input-group-addon" id="student_search">
                            <i class="icon icon-search"> </i>
                        </a>
                    </div>
                    </div>
                </div>
                <div class="rTableCell"></div>
            </div>
        </div>
    </div>
</div> -->
<div class="container-fluid align-top145 course-perform-wrapper course-wrapper-align">
		<!-- quiz report starts here -->
<?php
    if(!empty($courses)){
?>
    <?php
        if (in_array($this->privilege['view'], $this->report_privilege) && in_array($this->privilege['edit'], $this->report_privilege)) {
    ?>
        <div class="row export-btn-holder">
            <div class="save-btn"><button class="pull-right btn btn-green" onclick="exportPerformanceReport();">EXPORT</button></div>
        </div>
    <?php
        }
    ?>
        <table>
            <thead class="quiz-list-title">
                <tr>
                    <th class="text-left" style="width: 5%;">Sl.no</th>
                    <th class="text-left">Course Name</th>
                    <th>Course Likes</th>
                    <th class="text-center">Course Dislikes</th>
                    <th class="text-center">Course forum Likes</th>
                    <th class="text-center">Course forum Dislikes</th>
                    <th class="text-center">Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i=1;
                    foreach($courses as $course){
                ?>
                
                    <tr class="quiz-list clickable-row" data-href='<?php echo admin_url('report/course_institute_performance/'.$course['id']) ?>'>
                        
                        <td class="text-blue" style="width: 5%;"><?php echo $i++;?></td>
                        <td><?php /*<a href="<?php echo admin_url('report/course_institute_performance/'.$course['id']) ?>">*/?><?php echo $course['cb_title']; ?></a></td>
                        <td class="text-green text-center bold"><span class="icon-like"></span><?php echo $course['cb_course_likes']; ?></td>
                        <td class="text-red text-center bold"><span class="icon-unlike"></span><?php echo $course['cb_course_dislikes']; ?></td>
                        <td class="text-green text-center bold"><span class="icon-like"></span><?php echo $course['cb_course_forum_likes']; ?></td>
                        <td class="text-red text-center bold"><span class="icon-unlike"></span><?php echo $course['cb_course_forum_dislikes']; ?></td>
                        <td class="text-red text-center bold">
                            <?php
                                if($course['cb_access_validity'] == 1 ){

                                    echo $course['cb_validity'],($course['cb_validity']==1)?' day':' days';
                                }
                                else if( $course['cb_access_validity'] == 2){
                    
                                    echo date('d-m-Y',strtotime($course['cb_validity_date']));
                        
                                }else{
                                    echo "unlimited";
                                }
                            ?>
                        </td>               
                        
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
    <p>No Course to Display</p>
<?php
}
?>
</div>
<?php include_once 'footer.php'; ?>
<script>

function exportPerformanceReport(){
    
    location.href = '<?php echo admin_url('report/export_course_performance') ?>';
}

jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
    
});
</script>
