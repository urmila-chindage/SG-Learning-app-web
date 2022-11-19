<!DOCTYPE html>
<html lang="en">
<head>
	<title>Assessment Report</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="<?php echo base_url('favicon.png') ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
</head>
<body>
<div class="container">
	<nav class="navbar navbar-default">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="#"> </a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul class="nav navbar-nav">
	        
	      </ul>
	      
	      <form class="navbar-form navbar-left">
	      	<div class="form-group">
		        <select id="course_select" class="form-control">
				  <option value="">Select Course</option>
			<?php if(isset($courses)){ 
				foreach ($courses as $key => $value) {
					echo '<option value="'.$value['id'].'">'.$value['cb_title'].'</option>';
				}
			} ?>
				</select>
		    </div>
		    <div class="form-group">
		      	<select id="sort_students" class="form-control">
			  		<option value="">Sort By</option>
			  		<option value="1">Name A - Z</option>
			  		<option value="2">Mark High to Low</option>
			  		<option value="3">Mark Low to High</option>
			  		<option value="4">Passed First</option>
			  		<option value="5">Failed First</option>
				</select>
		    </div>
	        <div class="form-group">
	          <input type="text" id="search_name" class="form-control" placeholder="Search by name">
	        </div>
	        <button type="button" class="btn btn-default">Submit</button>
	      </form>
	      
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>
	<div class="row">
		<div class="col-xs-3" id="lecture_list">
			<ul></ul>
		</div>
		<div class="col-xs-9" id="assessment_list">
			<table class="table"></table>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
<script type="text/javascript">
	var admin_url   = '<?php echo admin_url() ?>';
	var currentLectureId = "";
	function webConfigs(key)
	{
	    return localStorage.getItem(key);
	}
	function get_lectures(course_id){
		$.ajax({
			url: admin_url+'assessment_report/get_lectures',
			dataType: 'JSON',
			data: { course_id: course_id },
			type: 'POST',
			success: function(data){
				if(data.lectures){
					var lectures = data.lectures;
					var htm = '';
					for(var x in lectures){
						htm += '<li data-id="'+lectures[x].id+'"><a>'+lectures[x].cl_lecture_name+'</a></li>';
					}
					$("#lecture_list ul").html(htm);
				}
			}
		})
	}
	function get_assessments(obj){
		obj.sort = $("#sort_students").val();
		obj.search = $("#search_name").val();
		$.ajax({
			url: admin_url+'assessment_report/get_assessments',
			dataType: 'JSON',
			data: obj,
			type: 'POST',
			success: function(data){
				if(data.assessments){
					var assessments = data.assessments;
					var htm = '';
					for(var x in assessments){
						var markHtm = '<a>Evaluate</a>';
						var pass = '';
						if(assessments[x].aa_valuated == "1"){
						 	markHtm = assessments[x].ll_marks+'%';
						}
						if(assessments[x].pass >= 0){
							pass = "Passed";
						} else {
							pass = "Failed";
						}
						user_img = ((assessments[x].us_image == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path'));
						htm += '<tr>';
						htm += '<td><img src="'+user_img+assessments[x].us_image+'" alt="Profile picture" style="border-radius:50%;width:40px"></td>';
						htm += '<td>'+assessments[x].us_name+'</td>';
						htm += '<td>'+assessments[x].aa_attempted_date+'</td>';
						htm += '<td>'+assessments[x].aa_duration+'</td>';
						htm += '<td>'+markHtm+'</td>';
						htm += '<td>'+pass+'</td>';
						htm += '</tr>';
						
					}
					$("#assessment_list table").html(htm);
				}
			}
		})
	}
	$(function(){

		$("#course_select").on("change", function(e){
			$("#lecture_list ul, #assessment_list table").html("");
			get_lectures($(this).val());
		});

		$("#lecture_list").on("click", "li", function(e){
			$("#assessment_list table").html("");
			currentLectureId = $(this).data("id");
			get_assessments({ lecture_id: currentLectureId });
		});

		$("#sort_students").on("change", function(){
			get_assessments({ lecture_id: currentLectureId });
		});

		$(function(){
	        $('.right-box').slimScroll({
	            height: '100%',
	            width: '100%',
	            wheelStep : 3,
	            distance : '10px'
	        });
	    });
	    
	})
</script>
</body>
</html>