<?php include_once 'header.php'; ?>
<?php include_once('report_tab.php') ?>
<section class="content-wrap create-group-wrap settings-top reports-left">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 pad0 settings-left-wrap assessment-left">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        
        
   <div class="col-sm-12 nav-content faculty-nav-content assessments-content">
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">

                        <div class="rTableCell dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="course_name">Select Course<span class="caret"></span></a>
                            <ul class="dropdown-menu white" id="course_select">
                            <?php if(isset($courses)): ?>
                                <li data-course_id="0"><a href="javascript:void(0)">All</a></li>
                                <?php foreach ($courses as $key => $value): ?>
					<li data-course_id="<?php echo $value['id']; ?>"><a href="javascript:void(0)"><?php echo $value['cb_title']; ?></a></li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </ul>
                        </div>
                        <div class="rTableCell dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="sort_by">Sort By<span class="caret"></span></a>
                            <ul class="dropdown-menu white" id="sort_students">
								<li data-sort_value="1"><a href="javascript:void(0)">Name A - Z</a></li>
                                <li data-sort_value="2"><a href="javascript:void(0)">Mark High to Low</a></li>
                                <li data-sort_value="3"><a href="javascript:void(0)">Mark Low to High</a></li>
                                <li data-sort_value="4"><a href="javascript:void(0)">Passed First</a></li>
                                <li data-sort_value="5"><a href="javascript:void(0)">Failed First</a></li>
                            </ul>
                        </div>
                        <!-- <div class="rTableCell dropdown" style="min-width: 220px">
	                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"  data-role-id="0" id="selected_institute_label"><span id="institute_label">Choose Institute</span><span class="caret"></span></a>
	                        <ul class="dropdown-menu white">
	                        <li><a onclick="loadInstituteReport('<?php echo base64_encode("0"); ?>','<?php echo base64_encode("All Institutes"); ?>')">All Institute</a></li>
	                        <?php if (isset($institutes) && !empty($institutes)):
	                                foreach($institutes as $institute): ?>
	                                        <li><a onclick="loadInstituteReport('<?php echo base64_encode($institute['id']); ?>','<?php $ins_name = (strlen($institute['us_name'])>38)?substr($institute['us_name'],0,38):$institute['us_name']; echo base64_encode($ins_name); ?>')" href="javascript:void(0)"><?php echo (strlen($institute['us_name'])>38)?substr($institute['us_name'],0,36).'...':$institute['us_name'] ?></a></li>
	                               <?php endforeach;
	                            endif; ?>
	                        </ul>
	                    </div> -->
                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" id="search_name" placeholder="Search by name" type="text">
                                <a class="input-group-addon" id="student_search">
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>
                        <div class="rTableCell">
                        </div>
                        <div class="rTableCell">
                            <div class="save-btn"><button class="pull-right btn btn-green" onclick="export_excel()">EXPORT</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>     

        <!-- Nav section inside this wrap  --> <!-- END -->
        <!-- =========================== -->

        <!-- Group content section  -->
        <!-- ====================== -->

        <div class="col-sm-12 group-content course-cont-wrap group-top list-tp"> 
            <div class="table course-cont rTable list-cont" style="" id="lecture_list">
            </div>
            
        </div>
        <!-- ====================== -->
        <!-- Group content section  -->
    </div>     

    <div class="col-sm-6 pad0 right-content list-right assessment-right">
        <div class="container-fluid right-box list-bx">
            <div class="row">
                <div class="col-sm-12 rel-top50 course-cont-wrap"> 
                    <div class="table course-cont rTable right-table" style="" id="assessment_list">
                   	</div>
                </div>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>

<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script type="text/javascript">

	var admin_url   		= '<?php echo admin_url() ?>';
	var currentLectureId 	= "";
	var sort_order 			= "";
	var currentCourseId 	= "";
	var __admin_url         = '<?php echo base64_encode(admin_url()); ?>';
    __admin_url             = atob(__admin_url);
    var __offset        = 2;
	var __getLectures   = false;
	var __ins_selected  = 0;

	function webConfigs(key)
	{
	    return localStorage.getItem(key);
	}

	function get_lectures(course_id){
		if(course_id){
			currentCourseId 	= course_id;
		}
		$.ajax({
			url: admin_url+'assessments/get_lectures',
			dataType: 'JSON',
			data: { course_id: course_id },
			type: 'POST',
			success: function(data){
				var htm 			= '';
				var active_lecture 	= '';
				if(data.lectures){
					var lectures 	= data.lectures;
					active_lecture 	= lectures[0]['id']; 
					for(var x in lectures){
						htm 	+= '<div class="list-row">';
						htm 	+= '<div class="list-col">';
						htm 	+= '<span class="wrap-mail ellipsis-hidden"> ';
						htm 	+= '<div class="ellipsis-style">';
						htm 	+= '<i class="icon icon-clipboard"></i>           ';
						htm 	+= '<a href="javascript:void(0)" class="lecture_name" data-lecture_id="'+lectures[x].id+'">'+lectures[x].cl_lecture_name+'</a>';
						htm 	+= '</div>';
						htm 	+= '</span>';
						htm 	+= '</div>';
						htm 	+= '</div>';
					}
				}else{
					htm 	+= '<div id="popUpMessage" class="alert alert-danger" style="text-align:center;">No assessments found for this course<br></div>';
				}
				$("#lecture_list").html(htm);
				if(active_lecture!=''){
					currentLectureId 	= active_lecture;
					get_assessments({ lecture_id: currentLectureId, sort: sort_order });
				}else{
					currentLectureId 	= '';
				}
			}
		})
	}

	function loadInstituteReport(instituteId, instituteName)
	{
	    $('#selected_institute_label').html('<span id="institute_label">'+atob(instituteName)+'</span><span class="caret"></span>');
	    __ins_selected  = atob(instituteId);
	    __offset    = 1;
	    __getLectures   = true;
	    getCourseReport(true);     
	}

	function get_assessments(obj){
		obj.search = $("#search_name").val();
		$("div.list-row.active").removeClass('active');
		$('a[data-lecture_id="'+obj.lecture_id+'"]').closest('div.list-row').addClass('active');
		$("#assessment_list").html('<div class="rTableRow"><div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)">Loading...</a></div></span></div></div>');
		$.ajax({
			url: admin_url+'assessments/get_assessments',
			dataType: 'JSON',
			data: obj,
			type: 'POST',
			success: function(data){
				var htm = '';
				if(data.assessments){
					var assessments = data.assessments;
					for(var x in assessments){
                                            //console.log(assessments[x]);
                                            //assessments[x]['attempt_id'] = 1;
						var markHtm = '<button onclick="location.href=\''+webConfigs('admin_url')+'coursebuilder/evaluate_assessment/'+assessments[x]['a_lecture_id']+'/'+assessments[x]['aa_user_id']+'/'+assessments[x]['attempt_id']+'\';" class="btn btn-green">EVALUATE</button>';
						var pass = '-';
						var class_name 	= '';
						if(assessments[x].aa_valuated == "1"){
						 	markHtm = assessments[x].ll_marks+'%';
						 	if(assessments[x].pass >= 0){
								pass = "Passed";
							} else {
								class_name 	= 'font-red';
								pass 		= "Failed";
							}
						}else{
						}
						
						
						user_img = ((assessments[x].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
						htm 	+= '<div class="rTableRow">';
						htm 	+= '<div class="rTableCell"> ';
						htm 	+= '<span class="icon-wrap-round img">';
						htm 	+= '<img src="'+user_img+assessments[x].us_image+'">';
						htm 	+= '</span>';
						htm 	+= '<span class="wrap-mail ellipsis-hidden"> ';
						htm 	+= '<div class="ellipsis-style">';
						htm 	+= '<a target="_blank" href="'+webConfigs('admin_url')+'coursebuilder/evaluate_assessment/'+assessments[x]['a_lecture_id']+'/'+assessments[x]['aa_user_id']+'/'+assessments[x]['attempt_id']+'">'+assessments[x].us_name+'</a>';
						htm 	+= '</div>';
						htm 	+= '</span>';
						htm 	+= '</div>';
						htm 	+= '<div class="rTableCell"> ';
						htm 	+= '<span class="wrap-mail ellipsis-hidden"> ';
						htm 	+= '<div class="ellipsis-style">'+format_date(assessments[x].aa_attempted_date)+'</div>';
						htm 	+= '</span>';
						htm 	+= '</div> ';
						htm 	+= '<div class="rTableCell"> ';
						htm 	+= '<span class="wrap-mail ellipsis-hidden"> ';
						htm 	+= '<div class="ellipsis-style">'+assessments[x].aa_duration+'</div>';
						htm 	+= '</span>';
						htm 	+= '</div>';
						htm 	+= '<div class="rTableCell pad0" style="min-width:100px;">';
						htm 	+= '<span class="green-text">'+markHtm+'</span>';
						htm 	+= '</div>';
						htm 	+= '<div class="rTableCell text-center"> ';
						htm 	+= '<span class="wrap-mail ellipsis-hidden"> ';
						htm 	+= '<div class="ellipsis-style '+class_name+'">'+pass+'</div>';
						htm 	+= '</span>';
						htm 	+= '</div>';
						htm 	+= '</div> ';
					}
					htm = reportHeaderHtml()+htm;
				}else{
					if(obj.search!=''){
						htm 	+= '<div id="popUpMessage" class="alert alert-danger">    <a data-dismiss="alert" class="close">×</a>    Sorry no result found for search.</div>';
					}else{
						htm 	+= '<div id="popUpMessage" class="alert alert-danger" style="text-align:center;">No user results found for this assesment<br></div>';
					}
				}
				$("#assessment_list").html(htm);
			}
		})
	}

	/*function format_date_arun(dateStr)
	{
		dateEdit 	= dateStr.replace(/-/g, "/");
		date 		= new Date(dateEdit);
		mlist 		= [ "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];
  		return mlist[date.getMonth()] +' '+ date.getDay();
	}*/

	function format_date(date_str){
		//console.log(date_str);
		var d = new Date(Date.parse(date_str.replace(/-/g, "/")));
		var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

		var date = d.getDate() + " " + month[d.getMonth()];
		var time = d.toLocaleTimeString().toLowerCase();

		return date;
	}

	$(function(){
		get_lectures();
		$("#course_select li").on("click", function(e){
			$("#lecture_list, #assessment_list").html("");
			$("#course_name").html($(this).text()+' '+'<span class="caret"></span>');
			get_lectures($(this).data("course_id"));
		});
		$("#lecture_list").on("click", "a.lecture_name", function(e){
			$("#assessment_list").html("");
			currentLectureId = $(this).data("lecture_id");
			get_assessments({ lecture_id: currentLectureId, sort: sort_order });
		});
		$("#sort_students li").on("click", function(){
			$("#sort_by").html($(this).text()+' '+'<span class="caret"></span>');
			sort_order 	= $(this).data('sort_value');
			get_assessments({ lecture_id: currentLectureId, sort: sort_order });
		});
		$("#search_name").on("keyup", function(){
			get_assessments({ lecture_id: currentLectureId });
		});
	});

	function export_excel()
	{
		var keyword 			= $("#search_name").val();
		var params 				= {};
		params.keyword 			= keyword;
		params.lecture_id 		= currentLectureId;
		params.filter 			= sort_order;
		var export_assessment 	= btoa(JSON.stringify(params));
		window.open(__admin_url+'assessments/export/'+export_assessment, '_blank');
	}
        
        function reportHeaderHtml()
        {
            return '<div class="rTableRow"><div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)"><strong>Student Name</strong></a></div></span></div><div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)"><strong>Date Attended</strong></a></div></span></div> <div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)"><strong>Duration</strong></a></div></span></div><div class="rTableCell pad0" style="min-width:100px;"><span><strong>Marks Percentage</strong></span></div><div class="rTableCell"><span class="wrap-mail ellipsis-hidden"><div class="ellipsis-style"><a href="javascript:void(0)"><strong>Status</strong></a></div></span></div></div>';
        }
</script>
<?php include_once 'footer.php'; ?>
