<?php include_once 'header.php'; ?>

<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
<style>
.inactiveLink {
   pointer-events: none;
   cursor: default;
}
.batch-info-row{
    display:flex;
    padding: 20px 30px 15px 30px;
    border-bottom: 1px solid #ccc;
}
.batch-info-row .batch-img img{height:80px;}
.batch-info-row .batch-info h1{
    vertical-align: middle;
    line-height: 40px;
    padding-left: 15px;
    font-size: 26px;
    text-transform: capitalize;
    font-weight: 600;
}
#bulk_action_wrapper{
    position: fixed;
    right: 15px;
    margin-top: 0px;
    line-height: 0px;
}
.bulk-action-menu{
    position: absolute !important;
    top: 23px !important;
    z-index: -10 !important;
}
.batch-list-fill-width{
    padding-right: 0px !important;
}

.tool-bar-batch-full-width{
    width: calc(100% - 65px) !important;
}
.list-scroll{
    max-height: 65vh;
    overflow: auto;
    height: 64vh !important;
}
.rTable.content-nav-tbl{border-collapse: separate !important;}

#preview_right_section { -ms-overflow-style: none !important;}
.student-name{
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 125px;
    display: inline-block;
    vertical-align: bottom;
}
.inside-box .checkbox-wrap {padding: 5px 0px;}
a.select-all-style {padding-left: 15px;}



::-webkit-scrollbar {width: 10px !important;}
::-webkit-scrollbar-track {background: #f1f1f1 !important;}
::-webkit-scrollbar-thumb {background: #888 !important;}
::-webkit-scrollbar-thumb:hover {background: #555 !important;}
#usersearchclear {
    position: absolute !important;
    z-index: 9;
    right: 65px;
    bottom: 0;
    height: 39px;
    margin: auto;
    font-size: 26px;
    cursor: pointer;
    color: #c0c0c0;
}
#course_group_wrapper li {
    border-bottom: 1px solid #f5f5f5;
    padding: 8px 0px;
}
</style>
<?php 
    $hide = '';
    $group_html  = '';
    if($total_groups < 1) {
        $hide = 'style="display:none;"';
        $group_html = 'No Batches';
    } else {
        $group_html .= sizeof($groups).' / '.$total_groups;
        $group_html .= ($total_groups>1)?' Batches':' Batch';    
    }
    $remaining_group = $total_groups - sizeof($groups);
    $remaining_group = ($remaining_group>0)?'('.$remaining_group.')':'';
?>
<section class="content-wrap create-group-wrap settings-top batch-list-fill-width" id="batch_list_full_width">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 pad0 settings-left-wrap">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid nav-content width-100p nav-js-height tool-bar-batch-full-width" id="tool_bar_batch">
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" placeholder="Search Batch" id="group_keyword" type="text">
                                <span id="searchclear" style="display:none;">×</span> 
                                <a class="input-group-addon" id="search_group" >
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>
                        <?php
                        if(in_array('2', $permissions)):
                            ?>
                        <div class="rTableCell">
                            <!-- lecture-control start -->
                            <a href="javascript:void(0)" onclick="createGroup()" type="button" class="btn btn-green selected" style="margin: 0px 5px;">CREATE BATCH<ripples></ripples></a>
                            <!-- lecture-control end -->
                        </div>
                            <?php
                        endif;
                        ?>
                        
                    </div>
                </div>

            </div>
        </div>
        <!-- Nav section inside this wrap  --> <!-- END -->
        <!-- =========================== -->

        <!-- Group content section  -->
        <!-- ====================== -->

        <div class="col-sm-12 group-content course-cont-wrap group-top"> 
            <div>
                <div class="pull-right">
                    <h4 class="right-top-header group-count">
                        <?php  echo $group_html; ?>
                    </h4>
                </div>
            </div>
            <div class="table course-cont rTable" style="" id="group_wrapper">
                
            </div>
            <div class="rTableCell text-center" >      
                <a id="loadmorebutton" <?php echo ((!$show_load_button)?'style="display:none;"':'') ?>  class="btn btn-green selected " onclick="loadMoreGroups()">Load More <?php echo $remaining_group ?><ripples></ripples></a>               
            </div>
        </div>
        <!-- ====================== -->
        <!-- Group content section  -->
    </div>

    <div class="col-sm-6 pad0 right-content" id="preview_right_section" style="display:none;">


        <!-- Batch name preview -->
        <div class="batch-info-row"> 
           <div class="batch-img" style="border-radius: 50%;overflow:hidden">
                
            </div> 
           <div class="batch-info" id="batch_title">
               
            </div>
        </div>
        <!-- Batch name prev ends -->


        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid nav-content no-nav-style nav-js-height width-100p" <?php echo $hide ?> id="preview_wrapper">
            <div class="col-sm-12">
                        <div class="rTable content-nav-tbl" style="">
                            <div class="rTableRow">
                                <div class="rTableCell txt-left">
                                    <a href="javascript:void(0)" ><label> <input class="user-checkbox-parent" type="checkbox">  Select All</label><span id="selected_users_count"></span></a>
                                </div>
                                <div class="rTableCell" >
                                    <!-- lecture-control start -->
                                    <div class="btn-group lecture-control btn-right-align" id="bulk_action_wrapper" style="display:none;margin-top: 0px;">
                                        <span class="dropdown-tigger" data-toggle="dropdown">
                                            <span class='label-text'>
                                               <b>Bulk Action </b> <?php /* ?><span class="icon icon-down-arrow"></span><?php */ ?>
                                            </span>
                                            <span class="tilder"></span>
                                        </span>
                                        <ul class="bulk-action-menu dropdown-menu pull-right" role="menu">
                                            <li>
                                                <a href="javascript:void(0)" onclick="sendMessageToUser(0)">Send Message</a> 
                                            </li>
                                            <?php
                                            if(in_array('4', $permissions)):
                                                ?>
                                            <li>
                                                <a href="javascript:void(0)" onclick="removeFromGroup(0)">Remove From Batch</a>
                                            </li>
                                                <?php
                                            endif;
                                            ?>
                                            
                                        </ul>
                                    </div>
                                    <!-- lecture-control end -->
                                </div>
                            </div>
                        </div>
            </div>
        </div>
        <!-- Nav section inside this wrap  --> <!-- END -->
        <!-- =========================== -->
        <div class="container-fluid right-box">
            <div class="row">
                <div class="col-sm-12 rel-top50 course-cont-wrap list-scroll"> 
                    <div class="table course-cont rTable" style="" id="group_detail_wrapper">
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>
<!-- <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script> -->
<script>
var __user_selected  = new Array();
var __disabled      = 'disabled="disabled"';
var __checked       = 'checked="checked"';
var __totalGroups   = '<?php echo ($total_groups)?$total_groups:0; ?>';
var __groupObject   = new Array;
    __groupObject   = atob('<?php echo base64_encode(json_encode($groups)) ?>'); 
var __limit         = '<?php echo $limit; ?>';
var __offset        = 2;

var __permissions   = '<?php echo json_encode($permissions); ?>';
    __permissions   = $.parseJSON(__permissions);
    console.log(__permissions);
var __userLimit = '<?php echo $limit_user ?>';

var __instituteAdmin    = '<?php echo $institute_admin ?>';
var __institutes        = '<?php echo json_encode($institutes); ?>';
var __message_flag      = false;
var __groupName         = '';

// var __batchName         = '';
var __groupRequests = new Array();
</script>
<script>
    function renderPageIfNoBatch(hide) {
        if(hide == true) {
            $('#preview_right_section').hide();
            $('#batch_list_full_width').addClass('batch-list-fill-width');
            $('#tool_bar_batch').addClass('tool-bar-batch-full-width');
        } else {
            $('#preview_right_section').show();
            $('#batch_list_full_width').removeClass('batch-list-fill-width ');
            $('#tool_bar_batch').removeClass('tool-bar-batch-full-width');
        }
    }

    var __activeGroup = 0;
    var __groupDetails = false;
    
    $(document).ready(function(){
        __groupObject      = $.parseJSON(__groupObject);
        $('#group_wrapper').html(renderGroupsHtml(__groupObject));
        if(__totalGroups > Object.keys(__groupObject).length)
        {
            $('#loadmorebutton').show();
        }
        if(Object.keys(__groupObject).length == 0)
        {
            renderPageIfNoBatch(true);
            $('#group_wrapper').html(renderPopUpMessage('error', 'No batches found.'));
        }
        if(__activeGroup > 0 ) {
            __groupDetails = true;
            loadGroupDetail(__activeGroup);
        }
        $('#popUpMessage > .close').remove();
    });
    
    function getGroupObjectDetail(group_id)
    {
        var _group = {};
        if(Object.keys(__groupObject).length > 0)
        {
            $.each(__groupObject, function(key, group )
            {
                if(group['id'] == group_id)
                {
                    _group = group;
                    return;
                }
            });    
        }
        return _group;
    }

    function getGroupObjectIndex(group_id)
    {
        var index = null;
        if(Object.keys(__groupObject).length > 0)
        {
            $.each(__groupObject, function(key, group )
            {
                if(group['id'] == group_id)
                {
                    index = key;
                    return;
                }
            });    
        }
        return index;
    }

    // var __gettingGroupsInProgress = false;
   
    function loadGroups()
    {
        // if(__gettingGroupsInProgress == true)
        // {
        //     return false;
        // }
        
        __gettingGroupsInProgress = true;
        $('#loadmorebutton').html('Loading..');
        var keyword  = $('#group_keyword').val().trim();
        if($('.groupRow').length==0){
            __offset = 1;
        }
        abortPreviousAjaxRequest(__groupRequests);
        __groupRequests.push($.ajax({
            url: admin_url+'groups/groups_json',
            type: "POST",
            data:{"is_ajax":true, "keyword":keyword,'limit':__limit,'offset':__offset},
            success: function(response) {
                var data = $.parseJSON(response);
                var remainingGroup = 0;
                $('#loadmorebutton').hide();
                if(Object.keys(data['groups']).length > 0){
                    __groupObject = __groupObject.concat(data['groups']);
                     __offset++;
                    if(__offset == 2)
                    {
                        
                        remainingGroup = (data['total_groups'] - Object.keys(data['groups']).length);
                        // var totalGroupsHtml = Object.keys(data['groups']).length+' / '+data['total_groups']+' '+((data['total_groups'] == 1)?"Batch":"Batches");
                        scrollToTopOfPage();
                        // $('.group-count').html(totalGroupsHtml);
                        
                        $('#group_wrapper').html(renderGroupsHtml(data['groups']));
                        resetCount();
                        renderPageIfNoBatch(false);
                    }
                    else
                    {
                        remainingGroup = (data['total_groups'] - (((__offset-2)*data['limit'])+Object.keys(data['groups']).length));
                        var totalGroupsHtml = (((__offset-2)*data['limit'])+Object.keys(data['groups']).length)+' / '+data['total_groups']+' Batches';
                        $('.group-count').html(totalGroupsHtml);
                        $('#group_wrapper').append(renderGroupsHtml(data['groups']));                     
                    }
                    if(__activeGroup >= 0)
                    {
                                                
                        loadGroupDetail(__activeGroup);
                    }
                }
                else
                {
                    renderPageIfNoBatch(true);
                    $('.group-count').html("No Batches");
                    $('#group_wrapper').html(renderPopUpMessage('error', 'No Batches found.'));
                    $('#group_detail_wrapper').html('');
                    $('#preview_wrapper').hide();
                }
                if(data['show_load_button'] == true)
                {
                    $('#loadmorebutton').show();
                }
                remainingGroup = (remainingGroup>0)?'('+remainingGroup+')':'';
                $('#loadmorebutton').html('Load More '+remainingGroup+'<ripples></ripples>');
                // __gettingGroupsInProgress = false;
            }
        }));
        
    }
    
    function loadGroupUsers()
    {
        $('#loadmoreusersbutton').html('Loading..');
        var groupId     = __activeGroup;
        var index       = getGroupObjectIndex(groupId);
        var offset      = __groupObject[index]['users_offset'];
        var limit       = __userLimit;
        var current_users      = __groupObject[index]['users'];
        $.ajax({
            url: admin_url+'groups/group_users_json',
            type: "POST",
            data:{"is_ajax":true, "group_id":groupId, 'offset':offset+1, 'limit':limit},
            success: function(response) {
                var data = $.parseJSON(response);
                $('#loadmoreusersbutton').hide();
                if(Object.keys(data).length > 0){
                    // $.extend(current_users, data);
                    //__groupObject[index]['users'] = $.extend(__groupObject[index]['users'], data);
                    var oldUsers = current_users;
                    if(data.length > 0 )
                    {
                        for(var user in data)
                        {
                            oldUsers.push(data[user]);
                        }
                    }
                    __groupObject[index]['users'] = oldUsers;
                    __groupObject[index]['users_offset'] = ++__groupObject[index]['users_offset'];
                    loadGroupDetail(__activeGroup);
                }
                // if(__groupObject[__activeGroup])
                // {
                //     $('#loadmoreusersbutton').show();
                // }
                // remainingGroup = (remainingGroup>0)?'('+remainingGroup+')':'';
                $('#loadmoreusersbutton').html('Load More <ripples></ripples>');
                // __gettingGroupsInProgress = false;
            }
        });
    }

    function loadMoreGroups()
    {
        loadGroups();
        
    }

    function loadMoreGroupUsers()
    {
        loadGroupUsers();
    }
    
    function renderGroupsHtml(groups)
    {
        clearCache();
        var groupsHtml  = '';
        if(Object.keys(groups).length > 0 )
        {
            $.each(groups, function(groupKey, group )
            {
                groupsHtml += '<div class="rTableRow groupRow" id="group_'+group['id']+'" data-name="'+group['batch_name']+'">';
                groupsHtml += renderGroupHtml(group);
                groupsHtml += '</div>';
                __activeGroup = (__activeGroup == 0)?group['id']:__activeGroup;
            });
        }
        return groupsHtml;
    }
    
    function renderGroupHtml(group)
    {
        
        __groupName = (__groupName=='')?group['gp_name']:__groupName;
        var groupHtml  = '';
            groupHtml += '    <div class="rTableCell pointer" onclick="loadGroupDetail('+group['id']+')"> ';

            /*groupHtml += '        <span class="icon-wrap-round blue">';
            groupHtml += '            <i class="icon icon-users"></i>';
            groupHtml += '        </span>';*/

           /* groupHtml += '       <span style="display: inline-block; vertical-align: middle;  margin-right: 10px;"><img class="profile-pic media-object pull-left img-circle" data-name="'+group['gp_name']+'"></span>';*/
            
            groupHtml += '        <span style="padding: 9px 0px; display: inline-block;"><svg style="vertical-align: middle; margin: 0px 10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="26px" height="18px" viewBox="0 0 26 18" enable-background="new 0 0 26 18" fill="#64277d" xml:space="preserve"><g> <path d="M15.73,4.37h-0.5c0.005,0.626-0.278,1.617-0.73,2.384c-0.225,0.386-0.489,0.718-0.755,0.936 C13.477,7.911,13.228,8.01,13,8.01c-0.227,0-0.476-0.099-0.744-0.321c-0.4-0.328-0.791-0.917-1.061-1.55 c-0.273-0.63-0.428-1.311-0.425-1.779h-0.5l0.5,0.02c0.048-1.204,1.036-2.142,2.229-2.142L13.09,2.24h0h0 c1.16,0.048,2.093,0.981,2.142,2.142l0.5-0.021h-0.5v0.01H15.73h0.5V4.36V4.349V4.338c-0.072-1.68-1.419-3.027-3.1-3.098h0h0 l-0.131-0.003c-1.727,0-3.159,1.36-3.228,3.102l0,0.01v0.01c0.005,0.884,0.332,1.965,0.867,2.893 c0.27,0.462,0.592,0.883,0.98,1.206C12.002,8.779,12.472,9.009,13,9.01c0.527,0,0.997-0.229,1.382-0.549 c0.581-0.483,1.023-1.184,1.342-1.919c0.315-0.738,0.505-1.506,0.507-2.171H15.73z"></path> <path d="M7.87,16.2c0-1.419,0.573-2.698,1.502-3.628c0.93-0.929,2.209-1.502,3.627-1.502c1.419,0,2.698,0.573,3.628,1.502 c0.929,0.93,1.502,2.209,1.502,3.628h1c0-3.387-2.743-6.13-6.13-6.13c-3.386,0-6.13,2.743-6.13,6.13H7.87z"></path> <path d="M22.55,6.18h-0.5c0.005,0.512-0.231,1.36-0.609,2.012c-0.187,0.328-0.407,0.609-0.622,0.79 C20.6,9.166,20.406,9.241,20.24,9.24c-0.165,0-0.358-0.075-0.577-0.26c-0.326-0.273-0.654-0.777-0.879-1.315 C18.556,7.13,18.428,6.554,18.43,6.18c0.001-1.005,0.805-1.809,1.811-1.81c1.006,0,1.817,0.808,1.819,1.81h0.5v-0.5h-0.01h-0.5v0.5 H22.55v0.5h0.01h0.5v-0.5c0-1.558-1.267-2.807-2.819-2.81c-0.775,0-1.481,0.313-1.989,0.821C17.743,4.698,17.43,5.405,17.43,6.18 c0.006,0.759,0.284,1.689,0.741,2.5c0.23,0.403,0.507,0.774,0.844,1.062c0.335,0.285,0.751,0.497,1.226,0.498 c0.474-0.001,0.889-0.211,1.224-0.495c0.504-0.43,0.881-1.042,1.153-1.682c0.269-0.642,0.431-1.306,0.433-1.883H22.55v0.5V6.18z"></path> <path d="M17.662,12.858c0.771-0.584,1.672-0.863,2.57-0.864c1.294,0.001,2.569,0.583,3.41,1.688l-0.002-0.002l-0.001-0.002 c0.556,0.745,0.86,1.65,0.86,2.581h1c0-1.149-0.375-2.264-1.06-3.179l-0.001-0.002l-0.002-0.002 c-1.036-1.362-2.613-2.083-4.205-2.083c-1.106,0-2.225,0.349-3.174,1.067L17.662,12.858z"></path> <path d="M3.45,6.18h-0.5c0.005,0.768,0.284,1.7,0.741,2.508C3.922,9.09,4.199,9.459,4.536,9.746 c0.335,0.284,0.75,0.494,1.224,0.495c0.475-0.001,0.891-0.213,1.225-0.499c0.504-0.433,0.88-1.047,1.152-1.687 C8.406,7.412,8.567,6.751,8.57,6.18c0-0.775-0.313-1.482-0.821-1.989C7.242,3.684,6.535,3.37,5.76,3.37 C4.207,3.373,2.94,4.622,2.94,6.18v0.5h0.51V6.18h-0.5H3.45v-0.5H3.44v0.5h0.5c0.002-1.002,0.813-1.81,1.82-1.81 c1.006,0.001,1.809,0.804,1.81,1.81C7.575,6.681,7.339,7.531,6.96,8.185C6.773,8.514,6.553,8.798,6.337,8.98 C6.118,9.166,5.925,9.241,5.76,9.24c-0.167,0-0.36-0.074-0.578-0.258C4.856,8.711,4.528,8.211,4.304,7.674 C4.076,7.14,3.948,6.563,3.95,6.18v-0.5h-0.5V6.18z"></path> <path d="M8.942,12.062c-0.949-0.719-2.068-1.067-3.174-1.067c-1.592,0-3.17,0.721-4.206,2.083l-0.001,0.002L1.56,13.081 C0.875,13.996,0.5,15.11,0.5,16.26h1c0-0.931,0.304-1.836,0.86-2.581l-0.001,0.002l-0.001,0.002 c0.841-1.105,2.116-1.688,3.41-1.688c0.898,0.001,1.799,0.28,2.57,0.864L8.942,12.062z"></path></g></svg><a href="javascript:void(0)"  class="normal-base-color grp-click-fn">';
            groupHtml += '                <span class="batch-name-ellipsis">'+group['batch_name']+' </span> ';
            groupHtml += '            </a>';
            groupHtml += '        </span>';
            groupHtml += '     <div class="pull-right group-total-holder"><span class="label-active group-total">'+group['users_count']+'</span> Students</div>';
            groupHtml += '    </div>';
            groupHtml += '    <div class="td-dropdown rTableCell p0">';
            groupHtml += '        <div class="btn-group lecture-control">';
            groupHtml += '            <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">';
            groupHtml += '                 <span class="label-text">';
            groupHtml += '                  <i class="icon icon-down-arrow"></i>';
            groupHtml += '                </span>';
            groupHtml += '                <span class="tilder"></span>';
            groupHtml += '            </span>';
            groupHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
            if(__permissions.indexOf('3') >= 0)
            {
                groupHtml += '                <li>';
                groupHtml += '                      <a href="javascript:void(0)" onclick="editGroup('+group['id']+')">Edit Batch</a>';
                groupHtml += '                </li>';
            }            
            if(group['users_count'] > 0){
                groupHtml += '                <li>';
                groupHtml += '                      <a href="javascript:void(0)" onclick="sendMessageToUserFromGroup('+group['id']+')">Send Message</a>';
                groupHtml += '                </li>';    
            }
            
            if(__permissions.indexOf('2') >= 0)
            {
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0)" onclick="renderAttachUserFormInit('+group['id']+',\''+group['gp_name']+'\');">Attach Student to Batch</a>';
                groupHtml += '                </li>';
            }            
            if( __permissions.indexOf('4') >= 0)
            {
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0)" onclick="removeGroup('+group['id']+',\''+group['batch_name']+'\')">Remove Batch</a>';
                groupHtml += '                </li>';
            } 
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0)" onclick="viewCourses('+group['id']+',\''+group['batch_name']+'\')">View Courses</a>';
                groupHtml += '                </li>';
            

            groupHtml += '            </ul>';
            groupHtml += '        </div>';
            groupHtml += '    </div>';
            groupHtml += '    <div class="rTableCell pos-rel active-institute-custom">';
            groupHtml += '      <span class="active-arrow" style="background: rgb(255, 255, 255);"></span>';
            groupHtml += '    </div>';
            // groupHtml += '    <div class="rTableCell pos-renderGroupHtml(__groupObject[__activeGroup])el active-user-custom">';
            // groupHtml += '        <span class="active-arrrenderGroupHtml(__groupObject[__activeGroup])w group-arrow" style=""></span>';
            // groupHtml += '    </div>';
        return groupHtml;
    }

    function editGroup(groupId)
    {
      
        if( groupId > 0 ) {
            $('#institution_div_wrapper').hide();
        } else {
            $('#institution_div_wrapper').show();
        }
        $('#popUpMessage').hide();
        // var group   = __groupObject[groupId];
        
        var group       = getGroupObjectDetail(groupId);
        $('#group_name').val(group['gp_name']);
        $('#group_year').val(group['gp_year']);

        var institutesHtml = '';
        if(__instituteAdmin == 'false')
        {
            var institutes = $.parseJSON(__institutes);
            institutesHtml += '<select name="institutes" class="form-control" id="institute_select">';
            institutesHtml += '<option value="0">Choose Institute</option>';
            for(var institute in institutes){
                if( group['gp_institute_id'] == institutes[institute]['id'] )
                {
                    institutesHtml += '<option value="'+ institutes[institute]['id'] +'" selected="selected">'+ institutes[institute]['ib_institute_code'] +' - '+institutes[institute]['ib_name'] +'</option>';
                }
                else
                {
                    institutesHtml += '<option value="'+ institutes[institute]['id'] +'">'+ institutes[institute]['ib_institute_code'] +' - '+institutes[institute]['ib_name'] +'</option>';
                }                
            }                        
            institutesHtml += '</select>';
        }
        else
        {
            var institute   = $.parseJSON(__institutes);
            institutesHtml += '<select class="form-control" disabled="disabled" id="institute_select">';
            institutesHtml += '<option value="'+ institute['id'] +'" selected="selected">'+ institute['ib_institute_code']+' - '+ institute['ib_name'] +'</option>';
            // institutesHtml += '<input type="hidden" id="institute_select" value="'+ data['institute']['id'] +'" >';
        }
        $('#institution_div').html(institutesHtml);

        $('#create-btn').attr('onclick','updateGroup('+ groupId +')');
        $('#batch_model').text('EDIT BATCH');
        $('#create-btn').html('UPDATE');
        $('#group-name').modal('show');
    }
    
    function renderAttachUserFormInit(group_id,groupname)
    {
        $('#popUpMessage').remove();
        __groupName         = groupname;
        __activeGroup       = group_id; 
        renderAttachUserForm();
        loadGroupDetail(__activeGroup);
    }
    $(document).on("keypress", "#group_year", function(event){
        var key = window.event ? event.keyCode : event.which;
        if (event.keyCode === 8 || event.keyCode === 46) {
            return true;
        } else if ( key < 48 || key > 57 ) {
            return false;
        } else {
            return true;
        }
    });
    $(document).on("keypress", "#group_keyword", function(e){
        
        if(e.which == 13){
            var user_keyword = $('#group_keyword').val();
            
            if(user_keyword.match(/^ \s+ $/))
            {
                lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
                
                $('#group_keyword').val('');
            } 
            else
            {
                __activeGroup = 0;
                __offset = 1;
                loadGroups();
            }  
            
        }
        
    });
   
    $(document).on('click', '#search_group', function(){
        var user_keyword = $('#group_keyword').val();
            
        if(user_keyword.match(/^ \s+ $/))
        {
            lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
            $('#group_keyword').val('');
        } 
        else
        {
            __activeGroup = 0;
            __offset = 1;
            loadGroups();
        }            
    });
    $(document).on('click', '#searchclear', function(){
            __activeGroup = 0;
            __offset = 1;
            loadGroups();
    });
    
    function filterGroups(groupKeyword)
    {
        clearCache();
        __activeGroup       = 0;
        var groupsHtml      = '';
        var keyword         = groupKeyword.toLowerCase();
        var result_status   = false;
        if(keyword == '')
        {
            groupsHtml = renderGroupsHtml(__groupObject);
            result_status = true;
        }
        else
        {
            if(Object.keys(__groupObject).length > 0 )
            {
                $.each(__groupObject, function(groupKey, group )
                {
                    if(!(group['gp_name'].toLowerCase().indexOf(keyword) == -1) == true  )
                    {
                        groupsHtml += '<div class="rTableRow" id="group_'+group['id']+'">';
                        groupsHtml += renderGroupHtml(group);
                        groupsHtml += '</div>';
                        __activeGroup = (__activeGroup == 0)?group['id']:__activeGroup;
                        result_status = true;               
                    }
                });
            }   
        }
        $('#group_detail_wrapper').html('');
        $('#preview_wrapper').hide();
        if(result_status == true){
            $('#group_wrapper').html(groupsHtml);
        }else{
            $('#group_wrapper').html('<div id="popUpMessage" class="alert alert-danger">    <a data-dismiss="alert" class="close">×</a>    No batches found.</div>');
            $('#group_detail_wrapper').html('');
        }
        if(__activeGroup > 0 )
        {
            loadGroupDetail(__activeGroup);
        }
    }
    function viewCourses(groupId)
    {
        var group       = getGroupObjectDetail(groupId);
        $.ajax({
                    url: admin_url+'groups/group_course_json',
                    type: "POST",
                    data:{"is_ajax":true, "group_id":groupId},
                    success: function(response) {
                        var data        = $.parseJSON(response);
                        $('#course_group_course').html('View courses of batch - '+group.gp_name);
                        $('#users_new_group_wrapper').append('<div class="checkbox-wrap users-to-add-in-new-group"><span class="chk-box"><label class="font14">Loading...</label></span></div>');
                        $('#view-group-course').modal();
                        userHtml         = "";
                        $('#course_group_wrapper').html("");
                        if(data.length > 0 )
                        {
                            console.log(data);
                            $('.select-all-style').show();
                            userHtml     += "<ul>";
                            for (var i=0; i<data.length; i++)
                            {
                                userHtml += '<li ><div class="row">';
                                // userHtml += '    <span class="chk-box">';
                                userHtml += '       <div class="col-md-6"> <label class="font14">';
                                userHtml += '      <span class="course-name" title=" '+data[i]['cb_title']+' ">'+data[i]['cb_title']+'</span></div>';
                                if (data[i]['cb_status'] == '1') {
                                    userHtml += '<div class="col-md-6"><span ><label class="pull-right label label-success" id="action_class_147">Active</label></span>';
                                } else {
                                    userHtml += '<div class="col-md-6"><span class="pad0"><label class="pull-right label label-warning" id="action_class_157">Inactive</label>';
                                }
                                userHtml += '        </label></span></div>';
                                // userHtml += '    </span>';
                                userHtml += '</div></li>';
                            }
                            userHtml     += "</ul>";
                            $('#course_group_wrapper').append(userHtml);
                            checkCount();
                        }
                        else
                        {
                            $('.select-all-style').hide();
                            $('#noCourseFound').remove();
                            $('#course_group_wrapper').append("<span id='noCourseFound' class='no-match-users-group'>No course found</span>");
                        }

                    }
                        
                });
    }
    var __groupDetailRequests = new Array();
    function loadGroupDetail(group_id)
    {
        clearCache();
        renderPageIfNoBatch(false);
        var groupTitle = $('#group_'+group_id).attr('data-name');
        $('#batch_title').html('<h1>'+groupTitle+'</h1>');
        $('#preview_wrapper').hide();
        var index             = getGroupObjectIndex(group_id);
        // //console.log(index, 'index found');
        var group             = (typeof __groupObject[index] != 'undefined')?__groupObject[index]:new Object;
        // //console.log(group);
        var groupDetailHtml   = '';
        var user_img          = '';
   
        $('.active-institute-custom').removeClass('active-table');
        $('#group_'+group_id+' .active-institute-custom').addClass('active-table');
        
        // $('.active-user-custom').removeClass('active-table');
        // $('#group_'+group_id+' .active-user-custom').addClass('active-table');
        
            if(typeof group['users'] != 'undefined' && Object.keys(group['users']).length > 0 )
            {
                // //console.log(group['users']);
                __groupDetails = true;
                $('#group_detail_wrapper').html('<div class="row center-block"><h2>Loading...</h2></div>');
                $.each(group['users'], function(userKey, user ){
                    var user_img     = ((user['us_image'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path')); 
                    groupDetailHtml += '<div class="rTableRow" id="group_user_'+user['id']+'">';
                    groupDetailHtml += '    <div class="rTableCell"> ';
                    groupDetailHtml += '        <input type="checkbox" class="user-checkbox" id="user_checkbox_'+user['id']+'" value="'+user['id']+'" > ';
                    groupDetailHtml += '        <span class="icon-wrap-round img">';
                    groupDetailHtml += '            <img src="'+user_img+''+user['us_image']+'">';
                    groupDetailHtml += '        </span>';
                    groupDetailHtml += '        <span class="wrap-mail"> ';
                    groupDetailHtml += '            <a href="'+webConfigs('admin_url')+'user/profile/'+user['id']+'" target="_blank">'+user['us_name']+'</a> <br>';
                    groupDetailHtml += '            '+user['us_email'];
                    groupDetailHtml += '        </span>';
                    groupDetailHtml += '    </div>';
                    groupDetailHtml += '    <div class="td-dropdown rTableCell">';
                    groupDetailHtml += '        <div class="btn-group lecture-control">';
                    groupDetailHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
                    groupDetailHtml += '                 <span class="label-text">';
                    groupDetailHtml += '                  <i class="icon icon-down-arrow"></i>';
                    groupDetailHtml += '                </span>';
                    groupDetailHtml += '                <span class="tilder"></span>';
                    groupDetailHtml += '            </span>';
                    groupDetailHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
                    groupDetailHtml += '                <li>';
                    groupDetailHtml += '                    <a href="javascript:void(0)" onclick="sendMessageToUser(\''+user['id']+'\')">Send Message</a>';
                    groupDetailHtml += '                </li>';
                    if(__permissions.indexOf('4') >= 0)
                    {
                        groupDetailHtml += '                <li>';
                        groupDetailHtml += '                    <a href="javascript:void(0)" onclick="removeFromGroup(\''+user['id']+'\')">Remove From Batch</a>';
                        groupDetailHtml += '                </li>';
                    }                    
                    groupDetailHtml += '            </ul>';
                    groupDetailHtml += '        </div>';
                    groupDetailHtml += '    </div>';
                    groupDetailHtml += '</div> ';                    
                });
                if(group['users_count'] > Object.keys(group['users']).length){
                    groupDetailHtml += '<div class="rTableCell text-center">';
                    groupDetailHtml += '<a id="loadmoreusersbutton" class="btn btn-green" onclick="loadMoreGroupUsers()">Load More<ripples><ripple style="left: 40px; top: 15px; height: 218px; width: 218px; margin: -109px; transform: scale(1.1);"></ripple></ripples><ripples></ripples></a>';
                    groupDetailHtml += '</div>';
                }                
                $('#preview_wrapper').show();
            }
            else
            {                
                var offset      = __groupObject[index]['users_offset'];
                var limit       = __userLimit;
                var current_users      = __groupObject[index]['users'];
                // //console.log(current_users, 'shooting ajax');
                // //console.log(index, 'Index selected is this');
                // //console.log(__groupObject[index]['users'], 'before ajax');
                abortPreviousAjaxRequest(__groupDetailRequests);
                __groupDetailRequests.push($.ajax({
                    url: admin_url+'groups/group_users_json',
                    type: "POST",
                    data:{"is_ajax":true, "group_id":group_id, 'offset':offset+1, 'limit':limit},
                    success: function(response) {
                        var data        = $.parseJSON(response);
                        if(Object.keys(data).length > 0){
                            var oldUsers = current_users;
                            if(data.length > 0 )
                            {
                                for(var user in data)
                                {
                                    oldUsers.push(data[user]);
                                }
                            }
                            //console.log(oldUsers, 'listing oldUsers');
                            $("#user_keyword").val('');
                            __groupObject[index]['users'] = oldUsers;
                            //console.log(__groupObject[index]['users'], 'after ajax');
                            __groupObject[index]['users_offset'] = ++__groupObject[index]['users_offset'];
                            loadGroupDetail(group_id);
                        } else {
                            // //console.log('response with no data');
                            $('#group_detail_wrapper').html(renderPopUpMessagePage('error', 'No Students.'));
                            $('#popUpMessagePage .close').css('display', 'none');
                            __activeGroup = group['id'];
                        }
                    }
                }));
            }
            if(__groupDetails == true){
                $('#group_detail_wrapper').html(groupDetailHtml);
                __activeGroup = group['id'];
            }
       
    }
    
    $(document).on('change', '.user-checkbox', function(){
        var user_id = $(this).val();
        if($(this).is(':checked'))
        {
            __user_selected.push(user_id);
            if($('.user-checkbox').not(':disabled').length == $('.user-checkbox:checked').not(':disabled').length)
            {
                $('.user-checkbox-parent').prop('checked', true);
            }
        }
        else
        {
            removeArrayIndex(__user_selected, user_id);
            $('.user-checkbox-parent').prop('checked', false);
        }
        
        $("#selected_users_count").html('');
        $('#bulk_action_wrapper').hide();
            //        if(__user_selected.length > 0)
            //        {
            //            $("#selected_users_count").html(' ('+__user_selected.length+')');
            //            $('#bulk_action_wrapper').show();
            //        }
        
        if(__user_selected.length > 0){
            $("#selected_users_count").html(' ('+__user_selected.length+')');
        }else{
            $("#selected_users_count").html(''); 
        }

        if(__user_selected.length > 1){
            $("#bulk_action_wrapper").css('display','block');
        }else{
            $("#bulk_action_wrapper").css('display','none');
        }
    });

    $(document).on('change', '.user-checkbox-parent', function(){
        __user_selected = new Array();
        $("#selected_users_count").html('');
        if($('.user-checkbox-parent').is(':checked'))
        {
            $('.user-checkbox').not(':disabled').each(function( index, value ) {
                __user_selected.push($(this).val());
               
            });
            $('.user-checkbox').not(':disabled').prop('checked', true);
            $("#selected_users_count").html(' ('+__user_selected.length+')');
            $('#bulk_action_wrapper').show();
        }
        else
        {
            $('.user-checkbox').prop('checked', false);
            $('#bulk_action_wrapper').hide();
        }
    });
    
    /*        $('#group_wrapper, #group_detail_wrapper').slimScroll({
                height: '100%',
                wheelStep : 3,
                distance : '10px'
        });
*/  
    
    function createGroup()
    {
        $('#institution_div_wrapper').show();
        $('#group_name').val('');
        $('#group_year').val('');
        $('#popUpMessage').hide();
        $('#create-btn').html('CREATE');
        $('#create-btn').attr('onclick','saveGroup()');
        var institutesHtml = '';
        if(__instituteAdmin == 'false')
        {
            var institutes = $.parseJSON(__institutes);
            institutesHtml += '<select name="institutes" class="form-control" id="institute_select">';
            institutesHtml += '<option value="">Choose Institute</option>';
            for(var institute in institutes)
            {
                institutesHtml += '<option value="'+ institutes[institute]['id'] +'">'+ institutes[institute]['ib_institute_code'] +' - '+institutes[institute]['ib_name'] +'</option>';                
            }                        
            institutesHtml += '</select>';
        }
        else
        {
            $('#institution_div_wrapper').hide();
            var institute   = $.parseJSON(__institutes);
            institutesHtml += '<select class="form-control" disabled="disabled" id="institute_select">';
            institutesHtml += '<option value="'+ institute['id'] +'" selected="selected">'+ institute['ib_institute_code']+' - '+ institute['ib_name'] +'</option>';
            // institutesHtml += '<input type="hidden" id="institute_select" value="'+ data['institute']['id'] +'" >';
        }
        $('#institution_div').html(institutesHtml);
        $('#batch_model').text('CREATE NEW BATCH');
        $('#group-name').modal('show');
    }

    function updateGroup(groupId)
    {
        messageObject ='';
        cleanPopUpMessage();
        var groupName       = $('#group_name').val();
        var groupYear       = $('#group_year').val();
        var courseId        = $('#course_select').val();
        var instituteId     = $('#institute_select').val();

        var errorCount         = 0;
        var errorMessage       = '';

        if(instituteId == '0')
        {
            errorCount++;
            errorMessage += 'Choose Institution<br />';  
        }
        if(groupName == '')
        {
            errorCount++;
            errorMessage += 'Enter batch name<br />'; 
        }
        if(groupYear == '')
        {
            errorCount++;
            errorMessage += 'Enter batch year<br />';  
        }
        else{
            if(isNaN(groupYear))
            {
                errorCount++;
                errorMessage += 'Invalid batch year<br />'; 
            }
        }
        $('#popUpMessage').remove();
        if(errorCount > 0)
        {
            $('#group-name .modal-body').prepend(renderPopUpMessage('error', errorMessage));  
            return false;
        }

        $.ajax({
            url: admin_url+'groups/save',
            type: "POST",
            data:{"is_ajax":true, "id":groupId, "group_name":groupName, "course_id":courseId, "institute_id":instituteId, "group_year":groupYear},
            success: function(response) {
                var data        = $.parseJSON(response);
                if(data['error'] == false)
                {
                    var index               = getGroupObjectIndex(groupId);
                    __groupObject[index]['gp_name']           = data['group']['gp_name'];
                    __groupObject[index]['gp_year']           = data['group']['gp_year'];
                    __groupObject[index]['gp_institute_id']   = data['group']['gp_institute_id'];
                    __groupObject[index]['batch_name']        = data['group']['batch_name'];
                    __groupObject[index]['gp_institute_code'] = data['group']['gp_institute_code'];
                    __groupObject[index]['gp_status']         = data['group']['gp_status'];
                    $('#group-name').modal('hide');
                    // $('#group_wrapper').html(renderGroupsHtml(__groupObject));

                    var messageObject = {
                        'body':'Batch info Updated successfully',
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);
                    __activeGroup = 0;
                    __offset = 1;
                    loadGroups();
                }
                else
                {
                    $('#group-name .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
            }
        });
    }
    
    function saveGroup()
    {
        cleanPopUpMessage();
        messageObject ='';
        var groupName       = $('#group_name').val();
        __groupName         = groupName;
        var groupYear       = $('#group_year').val();
        var instituteId     = $('#institute_select').val();

        var errorCount         = 0;
        var errorMessage       = '';

        if(instituteId == '')
        {
            errorCount++;
            errorMessage += 'Choose Institution<br />';  
        }
        if(groupName == '')
        {
            errorCount++;
            errorMessage += 'Enter batch name<br />'; 
        }
        if(groupYear == '')
        {
            errorCount++;
            errorMessage += 'Enter batch year<br />';  
        }
        else{
            if(isNaN(groupYear) || parseInt(groupYear) < 1800)
            {
                errorCount++;
                errorMessage += 'Invalid batch year<br />'; 
            }
        }
        
        $('#popUpMessage').remove();
        if(errorCount > 0)
        {
            $('#group-name .modal-body').prepend(renderPopUpMessage('error', errorMessage));  
            return false;
        }
        $.ajax({
            url: admin_url+'groups/save',
            type: "POST",
            data:{"is_ajax":true, "group_name":groupName, "institute_id":instituteId, "group_year":groupYear},
            success: function(response) {
                var data        = $.parseJSON(response);
                __message_flag = true;
                if(data['error'] == false)
                {
                    $('#group_wrapper').prepend('<div class="rTableRow groupRow newly-created-group" id="group_'+data['group']['id']+'" data-name="'+data['group']['batch_name']+'">'+renderGroupHtml(data['group'])+'</div>');
                    $('#group-name').modal('hide');
                    __totalGroups = parseInt(__totalGroups)+1;
                    __groupObject.unshift(data['group']);
                    var index = getGroupObjectIndex(data['group']['id']);
                    __groupObject[index]                = data['group'];
                    __activeGroup                       = data['group']['id'];
                    resetCount();
                    renderAttachUserForm();
                    loadGroupDetail(__activeGroup);
                }
                else
                {
                    $('#group-name .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
            }
        });
    }
    
    function renderAttachUserForm()
    {
        
        __user_selected      = new Array();
        var index           = getGroupObjectIndex(__activeGroup);
        $('.users-to-add-in-new-group').remove();
        $('#user_keyword').val('');
        // $('#course_group_create').html('Attach Student to Batch - '+__groupObject[index]['gp_name']);
        $('#course_group_create').html('Attach Student to Batch - '+__groupName);
        $('#attach-group-users').modal();
        $('#reflectCount').html('');
        
        $('#create_group_users').unbind();
        $('#create_group_users').click({}, attachUsers);    
        searchUser($('#user_keyword').val(), __groupObject[index]['gp_institute_id']);
        $('.select-users-new-group-parent').prop('checked', false);
    }
    
    function attachUsers(param)
    { 
        if(__user_selected.length == 0)
        {
            $('#attach-group-users .modal-body').prepend(renderPopUpMessage('error', 'Please select atleast one Student to batch'));
            scrollToTopOfPage();
            return false;
        }
        $('#create_group_users').addClass('inactiveLink');
            $.ajax({
                url: admin_url+'groups/save_group_users',
                type: "POST",
                data:{"is_ajax":true,"group_name":__groupName, "group_id":__activeGroup, "user_ids":JSON.stringify(__user_selected)},
                success: function(response) {
                    var data                                = $.parseJSON(response);    
                    var index                               = getGroupObjectIndex(__activeGroup);            
                    __groupObject[index]['users']           = data['group_users'];
                    __groupObject[index]['users_count']     = __groupObject[index]['users_count'] + __user_selected.length;
                    $('#group_'+__activeGroup).html(renderGroupHtml(__groupObject[index]));
                    loadGroupDetail(__activeGroup);
                    $('#create_group_users').removeClass('inactiveLink'); 
                    $('#attach-group-users').modal('hide');
                    if(__message_flag==true){
                        var messageObject = {
                            'body':'Batch Created successfully and Students added to Batch',
                            'button_yes':'OK', 
                        };
                    }else{
                        var messageObject = {
                            'body':'Students added to Batch',
                            'button_yes':'OK', 
                        };
                    }
                    
                    callback_success_modal(messageObject);
                }
            });
    } 
    
    $(document).on('keyup', '#user_keyword', function(e){

        if(e.which == 13){
            var user_keyword = $('#user_keyword').val();
            
            if(user_keyword.match(/^ \s+ $/))
            {
                lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
                
                $('#user_keyword').val('');
            } 
           
            else
            {
                var index           = getGroupObjectIndex(__activeGroup);
                searchUser($('#user_keyword').val().trim(), __groupObject[index]['gp_institute_id']);
            }  
        
        }
    });
    
    $(document).on('click', '#usersearchclear', function(){

        $('#user_keyword').val(''); 
        var user_keyword = $('#user_keyword').val();   
        if(user_keyword.match(/^ \s+ $/))
        {
            lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
            
            $('#user_keyword').val('');
        } 
        else
        {
            var index           = getGroupObjectIndex(__activeGroup);
            searchUser($('#user_keyword').val().trim(), __groupObject[index]['gp_institute_id']);
        }
    });
    $(document).on('click', '#user_keyword_btn', function(){
        var user_keyword = $('#user_keyword').val();
            
            if(user_keyword.match(/^ \s+ $/))
            {
                lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
                
                $('#user_keyword').val('');
            } 
            else
            {
                var index           = getGroupObjectIndex(__activeGroup);
                searchUser($('#user_keyword').val().trim(), __groupObject[index]['gp_institute_id']);
            }
    });

    function searchUser(userKeyword, instituteId)
    {
        userKeyword = typeof userKeyword != 'undefined' ? userKeyword : '';
        instituteId = typeof instituteId != 'undefined' ? instituteId : '';
        $('.users-to-add-in-new-group').remove();
        $(".no-match-users-group").remove();
        $('#users_new_group_wrapper').append('<div class="checkbox-wrap users-to-add-in-new-group"><span class="chk-box"><label class="font14">Loading...</label></span></div>');
        var userHtml    = '';
        var keyword     = userKeyword.toLowerCase();

        $.ajax({
            url: admin_url+'groups/users',
            type: "POST",
            data:{"is_ajax":true, "keyword":keyword, "exclude_group_id":__activeGroup, 'not_deleted':'1', "institute_id":instituteId},
            success: function(response) {
                var data        = $.parseJSON(response);
                var userHtml    = '';
                $('.users-to-add-in-new-group').remove();
                if(data['users'].length > 0 )
                {
                    $('.select-all-style').show();
                    for (var i=0; i<data['users'].length; i++)
                    {
                        userHtml += '<div class="checkbox-wrap users-to-add-in-new-group" id="user_new_group_'+data['users'][i]['id']+'">';
                        userHtml += '    <span class="chk-box">';
                        userHtml += '        <label class="font14">';
                        userHtml += '            <input type="checkbox" '+((inArray(data['users'][i]['id'], __user_selected) == true)?__checked:'')+' value="'+data['users'][i]['id']+'" class="select-users-new-group">';
                        userHtml += '      <span class="student-name" title=" '+data['users'][i]['us_name']+' ">'+data['users'][i]['us_name']+'</span>';
                        userHtml += '        </label>';
                        userHtml += '    </span>';
                        userHtml += '    <span class="email-label pull-right">';
                        userHtml += '        <span>'+data['users'][i]['us_email']+'</span>';
                        userHtml += '    </span>';
                        userHtml += '</div>';
                    }
                    $('#users_new_group_wrapper').append(userHtml);
                    checkCount();
                }
                else
                {
                    $('.select-all-style').hide();
                    $('#noStudentsToAdd').remove();
                    $('#users_new_group_wrapper').append("<span id='noStudentsToAdd' class='no-match-users-group'>No Students found</span>");
                }
            }
        });
    }
    
    var __user_selected = new Array();
    $(document).on('click', '.select-users-new-group', function(){
        var user_id = $(this).val();
        if ($(this).is(':checked')) {
            __user_selected.push(user_id);
        }else{
            removeArrayIndex(__user_selected, user_id);
            $('.select-users-new-group-parent').prop('checked', false);
        }
        checkCount();
        $('#reflectCount').html(' ('+__user_selected.length+')');
    });
    function checkCount(){

        var visible = $('.users-to-add-in-new-group:visible').length;
        var checked = $('.select-users-new-group:checked').length;
    
        if(checked==visible&&visible!=0){
            $( '.select-users-new-group-parent' ).prop('checked', true);
        }else{
            $( '.select-users-new-group-parent' ).prop('checked', false);
        }
    }
    function onlyUnique(value, index, self) { 
        return self.indexOf(value) === index;
    }


    $(document).on('click', '.select-users-new-group-parent', function(){
        var parent_check_box = this;  
        $( '.select-users-new-group' ).prop('checked', $(parent_check_box).is(':checked'));
        if ($(parent_check_box).is(':checked') == true) {
            $( '.select-users-new-group' ).each(function( index ) {
                var user_id = $( this ).val();
                if(inArray(user_id, __user_selected)==false){
                    __user_selected.push(user_id);
                }
               
            });
            var unique      = __user_selected.filter( onlyUnique );
            __user_selected  = unique;
        }else{
            $( '.select-users-new-group:visible' ).each(function( index ) {
                removeArrayIndex(__user_selected,$( this ).val());
            //    __user_selected.remove($( this ).val());//console.log(visibleList);
            });
        }
        checkCount();
        $('#reflectCount').html(' ('+__user_selected.length+')');
    });
    
    function removeFromGroup(user_id)
    {
        if(user_id > 0 )
        {
            __user_selected = new Array();
            __user_selected.push(user_id);
            $('.user-checkbox, .user-checkbox-parent').prop('checked', false);
            $('#user_checkbox_'+user_id).prop('checked', true);
            $("#selected_users_count").html(' ('+__user_selected.length+')');
        }
        
        if(__user_selected.length == 0 )
        {
            return false;
        }
        var header_text     = 'Do you wish to remove selected student from batch';
        var ok_button_text  = 'REMOVE';
        // $('#confirm_messages_group').modal();
        // $('#confirm_box_title').html(header_text);
        // $('#confirm_box_content, #confirm_box_content_1').html('');
        // $('#confirm_box_ok').unbind().html(ok_button_text+'<ripples></ripples>');
        // $('#confirm_box_ok').click({}, removeFromGroupConfirmed);    
        var messageObject = {
            'body': header_text,
            'button_yes':ok_button_text, 
            'button_no':'CANCEL',
        };
        callback_warning_modal(messageObject, removeFromGroupConfirmed);
    }

    
    function removeFromGroupConfirmed(param)
    {
        $.ajax({
            url: admin_url+'groups/remove_users_from_group',
            type: "POST",
            data:{"is_ajax":true,"group_name":__groupName ,"group_id":__activeGroup, "user_ids":JSON.stringify(__user_selected), 'offset':__groupObject[getGroupObjectIndex(__activeGroup)]['users_offset'], 'limit':__userLimit},
            success: function(response) {
                var data        = $.parseJSON(response);
                var oldUsers    = [];
                var index           = getGroupObjectIndex(__activeGroup);
                    if(Object.keys(__groupObject[index]['users']).length >0)
                    {
                        $.each(__groupObject[index]['users'], function(userKey, user )
                        {
                            if(inArray(user['id'], __user_selected) == false)
                            {
                                oldUsers.push(user);
                            }
                        });
                    }
                    __groupObject[index]['users_count'] = __groupObject[index]['users_count']-__user_selected.length;
                    $('#group_'+__activeGroup).html(renderGroupHtml(__groupObject[index]));

                    if(Object.keys(data['group_users']).length > 0)
                    {
                        for(var user in data['group_users'])
                        {
                            oldUsers.push(data['group_users'][user]);
                        }
                    }
                    var messageObject = {
                        'body':'Students removed from batch successfully',
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);
                    __groupObject[index]['users'] = oldUsers;
                    loadGroupDetail(__activeGroup);
                    // $('#confirm_messages_group').modal('hide');
            }
        });
    }
    
    function removeGroup(group_id,group_name)
    {
        if(group_id == 0 )
        {
            return false;
        }
        // var group           = __groupObject[group_id];

        $.ajax({
            url: admin_url+'test_manager/check_override_batch',
            type: "POST",
            data:{"is_ajax":true, "group_id":group_id},
            success: function(response) {
                var data        = $.parseJSON(response);
                if(data.length>0){
                    var group           = getGroupObjectDetail(group_id);
                    var header_text     = 'This batch is included in override.Are you sure to remove this batch?';
                    var ok_button_text  = 'REMOVE';
                    var messageObject = {
                        'body': header_text,
                        'button_yes':ok_button_text, 
                        'button_no':'CANCEL',
                        'continue_params':{'group_id':group_id,"group_name":group_name},
                    };
                    callback_warning_modal(messageObject, removeGroupConfirmed);  
                } else {
                    var group           = getGroupObjectDetail(group_id);
                    var header_text     = 'Are you sure to remove batch "'+group['gp_name']+'" ?';
                    var ok_button_text  = 'REMOVE';

                    var messageObject = {
                        'body': header_text,
                        'button_yes':ok_button_text, 
                        'button_no':'CANCEL',
                        'continue_params':{'group_id':group_id,"group_name":group['gp_name']},
                    };
                    callback_warning_modal(messageObject, removeGroupConfirmed);  
                }
            }
        });

        
    }

    
    function removeGroupConfirmed(param)
    {
        var group_id    = param.data.group_id;
        var group_name  = param.data.group_name;
        $.ajax({
            url: admin_url+'groups/remove_group',
            type: "POST",
            data:{"is_ajax":true, "group_id":group_id,"group_name":group_name},
            success: function(response) {
                var data  = $.parseJSON(response);    
                if(data['error'] == false)
                {
                    $('#group_'+group_id).remove();
                    __totalGroups = parseInt(__totalGroups)-Number('1');
                    if($('.groupRow').length==0){
                        __activeGroup = 0;
                        __offset = 1;
                        loadGroups();
                    }
                   
                    
                    resetCount();
                    var messageObject = {
                        'body':'Batch removed successfully',
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);
                    renderPageIfNoBatch(true);
                }
                else
                {
                    lauch_common_message('Error Occured', data['message'])
                }
            }
        });
    }
    function sendMessageToUserConfirmed(user_id_temp = ''){
        var user_id                 = (typeof user_id_temp != 'undefined') ? user_id_temp : '';
        
        var send_user_bulk_subject  = $('#invite_send_subject').val();
        var send_user_bulk_message  = btoa($('#redactor_invite').val());
        var errorCount              = 0;
        var errorMessage            = '';
        // var __user_selected                = [];
        if (__user_selected.length > 0) {
            __user_selected                = __user_selected;
        } else {
            if (user_id != '' && user_id > 0) {
                __user_selected.push(user_id);
            } else {
                errorCount++;
                errorMessage += 'Email id cannot be empty<br />';
            }
        }
        // console.log(user_ids);
        if ($.trim(send_user_bulk_subject) == '') {
            errorCount++;
            errorMessage += 'Please enter subject<br />';
        }
        if ($.trim(send_user_bulk_message) == '') {
            errorCount++;
            errorMessage += 'Please enter message<br />';
        }

        if (errorCount > 0) {
            $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', errorMessage));
            scrollToTopOfPage();
            return false;
        }
        $('#message_send_button').text('SENDING..');
        $.ajax({
            url: admin_url + 'user/send_message',
            type: "POST",
            data: {
                "is_ajax": true,
                "send_user_subject": send_user_bulk_subject,
                "send_user_message": send_user_bulk_message,
                "user_ids": JSON.stringify(__user_selected)
            },
            success: function (response) {
                // clearCache();
                var data = $.parseJSON(response);
                if (data.error == false || data.success == true) {
                    // $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('success', data.message));                    
                    $('#invite-user-bulk').modal('hide');
                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                    $('.user-checkbox').prop('checked', false);
                    $('.user-checkbox-parent').prop('checked', false);
                    __user_selected = [];
                } else {
                    $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', data.message));
                }
                $('#message_send_button').text('SEND');

                setTimeout(function () {
                    // $('#faculty_wrapper .lecture-control').css('visibility', 'visible');
                    $('#invite-user-bulk').modal('hide');
                }, 2500);
            }
        });
    }
    function sendMessageToUser(user_id)
    {
        var user_id_temp = (typeof user_id != 'undefined') ? user_id : '';
        $('#invite-user-bulk').modal();
        $('#popUpMessage').hide();
        $('#invite_send_subject').val('');
        // $('#redactor_invite').redactor('set', '');
        $('#redactor_invite').redactor('insertion.set', '');
        // $('#redactor_invite').redactor('core.destroy');
        startTextToolbar();
        $('#message_send_button').attr('onclick', 'sendMessageToUserConfirmed(' + user_id_temp + ')');
    }
    
    function sendMessageToUserFromGroup(group_id)
    {
        $('#redactor_invite').redactor('insertion.set', '');  
        loadGroupDetail(group_id);
        
        $('#invite-user-bulk').modal();
        $('#popUpMessage').hide();
        
        $('#invite_send_subject').val('');
        $('#message_send_button').attr('onclick','sendMessageBulk('+ group_id +')');
    }

    
    var __sendEmailsBulk = new Array();
    function sendMessageBulk(group_id)
    {
        group_id = typeof group_id != 'undefined' ? group_id : '';
        var send_user_bulk_subject = $('#invite_send_subject').val();
        var send_user_bulk_message = btoa($('#redactor_invite').val());
        // var send_user_bulk_emails  = JSON.stringify($('#tokenize_invite').val());
        // __sendEmailsBulk           = $('#tokenize_invite').val();

        var errorCount   = 0;
        var errorMessage = '';
        if ($.trim(send_user_bulk_subject) == '') {
            errorCount++;
            errorMessage += 'Please enter subject<br />';
        }
        if ($.trim(send_user_bulk_message) == '') {
            errorCount++;
            errorMessage += 'Please enter message<br />';
        }
        
        if(errorCount > 0)
        {
            $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', errorMessage));
            scrollToTopOfPage();    
            return false;
        }
        $('#message_send_button').text('SENDING..');
        $.ajax({
            url: admin_url+'groups/send_message_group',
            type: "POST",
            data:{"is_ajax":true, "send_user_subject":send_user_bulk_subject, "send_user_message":send_user_bulk_message, "group_id":group_id},
            success: function(response) {
                var data        = $.parseJSON(response);
                if(data['error'] == false)
                {
                    $('#invite-user-bulk').modal('hide');
                    var messageObject = {
                        'body':data['message'],
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);
                }
                else
                {
                    $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', data['message']));
                }
                $('#message_send_button').text('SEND');
            }
        }); 
    }
    
    function clearCache()
    {
        __user_selected    = new Array();
        $('.user-checkbox-parent').prop('checked', false);
        $('#selected_users_count').html('');
        $('#bulk_action_wrapper').hide();        
    }
    function resetCount(){
        var count = '';
        if(__totalGroups>0||__totalGroups!=''){
            var visibleList = $('.groupRow').length;
            
            var title       = (visibleList == 1)?" Batch":" Batches"; 
            
                count   = visibleList+" / "+__totalGroups+title;
              
        }else{
            count = '0 / 0 Batches';
        }
        $('.group-count').html(count);
    }
        
    $(function()
    {
        startTextToolbar();
    });

    function startTextToolbar()
    {
        $('#redactor_invite').redactor({
            imageUpload: admin_url+'configuration/redactore_image_upload',
            plugins: ['table', 'alignment', 'source'],
            buttons: ['formatting', '|', 'bold', 'italic', '|', 
                    'image','table', 'link', '|',
                    'fontcolor', '|', 'alignment', '|', 'horizontalrule'],
            callbacks: {
                imageUploadError: function(json, xhr)
                {
                     alert('Please select a valid image');
                     return false;
                }
            }   
        });
    }
    function preventSpecialCharector(e) {
        var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57)||k == 45||k == 38);
    }
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script>
    $(document).ready(function(){
        $("#searchclear").hide();
        $(".srch_txt").keyup(function(){
                // $("#searchclear").toggle(Boolean($(this).val()));
                $("#searchclear").show();
        });
        $("#searchclear").toggle(Boolean($(".srch_txt").val()));
        $("#searchclear").click(function(){
        $(".srch_txt").val('').focus();
        
        //$("#user_keyword").val('').focus();
        
        $(this).hide();
        });
        
    });
</script>
<?php include_once 'footer.php'; ?>