<html>
<!-- head start-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
    <link rel="icon" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/favicon.ico">
    <!-- Customized bootstrap css library -->
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/bootstrap-multiselect.css" type="text/css"/>
    <style>
        .filter-table td{ padding: 10px 5px;
    color: #2e3e4e;
    font-size: 13px;
    text-align: left;
    vertical-align: top;}
		ul.multiselect-container li a:hover{background-color:#ccc !important}
    </style>
</head>
<!-- head end-->

<!-- body start-->
<body>
    <!-- Top head start-->
        <?php include_once 'head.php'; ?>
    <!-- Top head end-->

    <!-- Side Menu start-->
        <?php include_once "sidebar.php"; ?>
    <!-- Side Menu end-->


    <!-- Manin Iner container start -->
    <div class='dashbrd-container pos-top50 main-content'>
    <ol class="breadcrumb">
<li class="">
    <a><i class="fa fa-dashboard"></i> Home</a>
</li>
<li class="active">
    Advanced Reports                                        
</li>
</ol>
        <div class="dash-progrs-wrap prt20">
            <form method="POST" action="<?php echo admin_url('advanced_report/export') ?>" id="filter_form">
            <table class="filter-table report-filter" border="1">
                <thead>
                    <tr>
                        <td><input class="form-control" style="width:150px;" id="keyword" name="keyword" placeholder="Enter Keyword" type="text"></td>
                        <td>
                            <?php $javascript_location = array(); ?>
                            <select id="user_region" multiple="multiple" class="multiple-select" name="user_region[]">
                                <option disabled="disabled" value="">All Region</option>
                                <?php if(!empty($cities)): ?>
                                    <?php foreach ($cities as $city): ?>
                                        <?php $javascript_location[$city['id']] = $city['city_name']; ?>
                                    <option value="<?php echo $city['id'] ?>"><?php echo $city['city_name'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                        <td>
                            <?php $javascript_courses = array(); ?>
                            <select id="user_courses" multiple="multiple" class="multiple-select" name="user_courses[]">
                                <option disabled="disabled" value="">All Courses</option>
                                <?php if(!empty($subscribed_courses)): ?>
                                    <?php foreach ($subscribed_courses as $course): ?>
                                        <?php if($course['cb_title']): ?>
                                            <?php $javascript_courses[$course['id']] = $course['cb_title']; ?>
                                            <option value="<?php echo $course['id'] ?>"><?php echo $course['cb_title'] ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                        <?php $column_count = 3; ?>
                        <?php //echo '<pre>'; print_r($header_labels); print_r($report_headers);die; ?>
                        <?php if(!empty($header_labels)): ?>
                            <?php foreach ($header_labels as $field_id => $label): ?>
                                <td>
                                    <select class="dynamic_headers multiple-select" multiple="multiple" id="dynamic_header_<?php echo $field_id ?>" name="header_filters[<?php echo $field_id ?>][]" data-field-id="<?php echo $field_id ?>">
                                        <option disabled="disabled" value="">All <?php echo (isset($header_labels[$field_id])?$header_labels[$field_id]:'') ?></option>
                                        <?php if( isset($report_headers[$field_id])  && !empty($report_headers[$field_id])): ?>
                                            <?php foreach ($report_headers[$field_id] as $header): ?>
                                                <option value="<?php echo $header ?>"><?php echo $header ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </td>
                            <?php $column_count++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td colspan="<?php echo $column_count ?>">
                            <input type="button" class="btn btn-green" value="Filter" id="filter_button" onClick="initFilterUsers()">
                            <input type="button" class="btn btn-red" value="Clear" onClick="clearFilter()">
                            <input type="submit" class="btn btn-pink" value="Export" >
                        </td>
                    </tr>
                </thead>
                <tbody id="report_item_body"></tbody>
            </table>
            </form>
        </div>
        <div class="row">
            <div class="col-sm-12 text-center">
                <a id="loadmorebutton" style="display:none;" class="btn btn-green selected " onclick="loadMoreReports()">Load More <ripples></ripples></a>               
            </div>
        </div>
    </div>
    <!-- Manin Iner container end -->
</body>
<!-- body end-->

</html>
<!-- Jquery library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>

<!-- bootstrap library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<!-- custom layput js handling tooltip and hide show switch -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-multiselect.js"></script>
<!-- Initialize the multiselect plugin: -->
<script type="text/javascript">
    $(document).ready(function() {
        $('.multiple-select').multiselect();
    });
</script>

<script>
var __isFilterInProgress = false;
var __adminUrl           = '<?php echo admin_url() ?>';
var __headerLabels       = new Object;
<?php if(isset($header_labels) && !empty($header_labels)): ?>
    __headerLabels       = $.parseJSON(atob('<?php echo base64_encode(json_encode($header_labels)) ?>'));
<?php endif; ?>
var __userCourses        = new Object;
<?php if(isset($javascript_courses) && !empty($javascript_courses)): ?>
    __userCourses        = $.parseJSON(atob('<?php echo base64_encode(json_encode($javascript_courses)) ?>'));
<?php endif; ?>
var __userCity        = new Object;
<?php if(isset($javascript_location) && !empty($javascript_location)): ?>
    __userCity        = $.parseJSON(atob('<?php echo base64_encode(json_encode($javascript_location)) ?>'));
<?php endif; ?>
 
var __limit         = '<?php echo $limit; ?>';
var __offset        = 1;
/*function exportReport()
{
    $('#filter_form').submit({      selectedText: '# selected'});
}*/

function loadMoreReports()
{
    filterUsers();    
}

function initFilterUsers()
{
    __offset = 1;
    filterUsers();
}
function filterUsers()
{
    var keyword     = $('#keyword').val();
    var userRegion = $('#user_region').val();
    var userCourses = $('#user_courses').val();
    
    var headerFilters = new Object;
    $(".dynamic_headers").each(function(){
        var headerId     = $(this).attr('id')
        var headerIndex  = $(this).attr('data-field-id');
            var filterValues = $('#'+headerId).val();
            if( filterValues != null )
            {
                headerFilters[headerIndex] = filterValues;        
            }
    });
    //console.log(headerFilters);

    if(__isFilterInProgress==true)
    {
        return false;
    }
    __isFilterInProgress = true;
    $('#filter_button').val('Filtering..');
    $.ajax({
        url: __adminUrl+'advanced_report/filter_users',
        type: "POST",
        data:{"is_ajax":true, "keyword":keyword, 'locations':JSON.stringify(userRegion), 'course_ids':JSON.stringify(userCourses), 'header_filters':JSON.stringify(headerFilters), 'limit':__limit, 'offset':__offset},
        success: function(response) {
            var data = $.parseJSON(response);
            var remainingReport = 0;
                $('#loadmorebutton').hide();
                if(Object.keys(data['users']).length > 0){
                     __offset++;
                    if(__offset == 2)
                    {
                        remainingReport = (data['total_reports'] - Object.keys(data['users']).length);
                        var totalReportsHtml = Object.keys(data['users']).length+' / '+data['total_reports']+' '+((data['total_reports'] == 1)?"item":"items");
                        $('#filter_button').val('Filter ('+totalReportsHtml+')');
                        $('#report_item_body').html(renderReportHtml(data['users']));
                        scrollToTopOfPage();
                    }
                    else
                    {
                        remainingReport = (data['total_reports'] - (((__offset-2)*data['limit'])+Object.keys(data['users']).length));
                        var totalReportsHtml = (((__offset-2)*data['limit'])+Object.keys(data['users']).length)+' / '+data['total_reports']+' items';
                        $('#filter_button').val('Filter ('+totalReportsHtml+')');
                        $('#report_item_body').append(renderReportHtml(data['users']));
                    }
                }
                else
                {
                    $('#filter_button').val('Filter');
                    $('#report_item_body').html(renderPopUpMessage('error', 'No Reports found.'));
                }
                if(data['show_load_button'] == true)
                {
                    $('#loadmorebutton').removeAttr('style');
                }
                remainingReport = (remainingReport>0)?'('+remainingReport+')':'';
                $('#loadmorebutton').html('Load More '+remainingReport+'<ripples></ripples>');
                __isFilterInProgress = false;
        }
    });
}

function renderReportHtml(users)
{
    var reportHtml = '';
    if(Object.keys(users).length > 0 )
    {
        $.each(users, function(userKey, user ){
            reportHtml += '<tr>';
            reportHtml += '<td>'+user['us_name']+'</td>';
            
            //processing course name starts here
            var userLocations = new Array;
            var userLocation  = '';
            if(typeof user['us_native'] == 'string')
            {
                userLocation = __userCity[user['us_native']];
            }

            reportHtml += '<td>'+userLocation+'</td>';
            //End
            
            //processing course name starts here
                var courseIds = new Array;
                if(typeof user['course_ids'] != 'object')
                {
                    courseIds = user['course_ids'];
                }
                
                var courseHtml          = '';
                if( courseIds.length > 0  )
                {
                    var userCoursesString   = courseIds.split(","); 
                    var userCourses         = new Array;
                    if(userCoursesString.length > 0 )
                    {
                        for(var j=0; j<userCoursesString.length;j++)
                        {
                            userCourses.push(__userCourses[userCoursesString[j]]);
                        }
                        courseHtml = userCourses.join(', ');
                    }
                }
                reportHtml += '<td>'+courseHtml+'</td>';
            //end
            
            if(Object.keys(__headerLabels).length > 0 ) 
            {
                $.each(__headerLabels, function(headerId, headerName ){
                    reportHtml += '<td>'+((typeof user['fields'][headerId] != 'undefined' )?user['fields'][headerId]:'')+'</td>';            
                });
            }
            
            reportHtml += '</tr>';
        });
    }
    return reportHtml;
}

function clearFilter()
{
    __offset = 1;
    $("#keyword").val("");
    $("#user_region option:selected").removeAttr("selected");
    $("#user_courses option:selected").removeAttr("selected");
    $("#user_region, #user_courses").multiselect('refresh');
    $(".dynamic_headers").each(function(){
        var headerId    = $(this).attr('id')
        $('#'+headerId+" option:selected").removeAttr("selected");
        $("#"+headerId).multiselect('refresh');
    });
    $('#filter_button').trigger('click');
}

</script>