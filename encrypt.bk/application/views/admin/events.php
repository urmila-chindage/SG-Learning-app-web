<?php include_once "header.php"; ?> 
<!-- <link rel="stylesheet" href="<?php echo assets_url('css').'datepicker.min.css'; ?>"> -->
<link rel="stylesheet" href="<?php echo assets_url('css').'jquery-ui.css'; ?>">
<link rel="stylesheet" href="<?php echo assets_url('css').'timepicker.css'; ?>">

<style>
.searchclear {
    position: absolute !important;
    z-index: 9;
    right: 65px;
    bottom: 0;
    height: 39px;
    margin: auto;
    font-size: 26px;
    cursor: pointer;
    color: #c0c0c0;
    display:none;
}
.event-fullwidth{
    overflow: hidden;
    padding-right: 0px;
    height: 100% !important;
}
.btn-group.lecture-control{margin: 0px 8px 0px 0px;}
section.base-cont-top.courses-tab {height: 51px !important;}

.only-course .rTableRow .rTableCell:first-child {
    min-width: 350px;
}
.pb-30{padding-bottom:30px;}
.pr-0{padding-right:0px;}
.pr-25{padding-right: 25px;}
.pt-15{padding-top:15px !important;}

section.base-cont-top.courses-tab {height: 47px;}
.courses-tab ol.nav li {border-bottom: unset;}

</style>
<?php $section_attributes = 'class="content-wrap base-cont-top event-fullwidth"';?>
<?php if(in_array($this->privilege['add'], $this->event_privilege)): ?>
<div class="right-wrap base-cont-top container-fluid pull-right">
    <a href="javascript:void(0)" class="btn btn-green btn-big full-width-btn" onclick="addEventInit()">CREATE EVENT</a>
</div>
<?php $section_attributes = 'class="content-wrap base-cont-top"';?>
<?php endif; ?>
<section <?php echo $section_attributes ?>>
    <div class="container-fluid nav-content nav-course-content">
        <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow">
                    <div class="rTableCell dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text">All Events<span class="caret"></span></a>
                        <ul class="dropdown-menu white">
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_all" onclick="filter_events_by('all')">All</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_active" onclick="filter_events_by('active')">Active Events</a></li>
                            <li><a href="javascript:void(0)" id="filer_dropdown_list_inactive" onclick="filter_events_by('inactive')">Inactive Events</a></li>
                        </ul>
                    </div>
                    <div class="rTableCell">
                        <div class="input-group">
                            <input type="text" class="form-control srch_txt" id="event_keyword" placeholder="Event Name" />
                            <span id="searchclear" style="">×</span>
                            <a class="input-group-addon" id="basic-addon2">
                                <i class="icon icon-search"> </i>
                            </a>
                        </div> 
                    </div>
                    <div class="rTableCell" >
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="left-wrap col-sm-12 pad0">
        <div class="container-fluid">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap" id="show_message_div"> 
                    <div class="pull-right">
                        <?php 
                        /*    $event_html  = '';
                            if($total_events < 1) 
                            {
                                $event_html = 'No Events';
                            } else 
                            {
                                $event_html .= sizeof($events).' / '.$total_events;
                                $event_html .= ($total_events>1)?' Events':' Event';    
                            }*/
                        ?>
                       <h4 class="right-top-header course-count"><span id="visible-events"></span> <?php //echo $event_html; ?> <span id="total-events"></span></h4>
                    </div>
                    <div class="table course-cont only-course rTable" style="" id="event_row_wrapper"> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div id="pagination_wrapper">
        </div>
    </div>

</section>
<?php //print_r($institutes); die;?>
<?php include_once 'footer.php'; ?>
<script type="text/javascript" src="<?php echo assets_url('js').'datepicker.js'; ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('js').'datepicker.en.js'; ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('js').'jquery.timepicker.min.js'; ?>"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<!-- <script src="< ?php echo assets_url() ?>js/jquery-ui.min.js"></script> -->
<script type="text/javascript">
    const __jqueryUi__      = '<?php echo assets_url() ?>js/jquery-ui.min.js';
    var __events            = atob('<?php echo base64_encode(json_encode($events)) ?>');
    var __admin_url         = '<?php echo admin_url(); ?>';
    var __limit             = '<?php echo $limit ?>';
    var __totalEvents       = '<?php echo $total_events; ?>';
    var __courses           = atob('<?php echo base64_encode(json_encode($courses)) ?>');
    var __institutes        = atob('<?php echo base64_encode(json_encode($institutes)) ?>');
    var __batches           = atob('<?php echo base64_encode(json_encode($batches)) ?>');
    const __previlages__    = atob('<?php echo base64_encode(json_encode($this->event_privilege)) ?>');
    var __visible_events    = Number(0);
    //console.log(__institutes);
    // //console.log(__previlage.hasAdd());
    // //console.log(__previlage.hasEdit());
    // //console.log(__previlage.hasDelete());
</script>
<script src="<?php echo assets_url() ?>js/event.js"></script>
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="new_event" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">SCHEDULE NEW EVENT</h4>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Event Name* :</label>
                        <input id="event_name" onkeypress="return preventSpecialCharector(event)" type="text" class="form-control" placeholder="eg: Inauguration">
                    </div>
                    <div class="form-group">
                        <label>Description* :</label>
                        <textarea onkeyup="validateMaxLength(this.id)" onkeypress="return preventSpecialCharector(event)" maxlength="300" class="form-control" id="short_description" placeholder="eg : This is a short description for the event."></textarea>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Event Date* :</label>
                                <input id="event_date" type="text" class="form-control" readonly="" style="background: #fff;" />
                            </div>
                            <div class="col-sm-6">
                                <label>Event Time* :</label>
                                <span id="time-wrpper">
                                    <input id="event_time" class="form-control ui-timepicker-input" type="text" class="form-control" onkeypress="return false;">
                                </span>
                                
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6 no-padding">
                            <label>Event Type * : </label>
                            <div>
                                <label class="pad-top10">
                                    <input type="radio" name="event_type" value="1">
                                    Live Event
                                </label> &nbsp;
                                <label>
                                    <input type="radio" name="event_type" value="0" checked>
                                    Offline Event
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 pb-30 pr-0" id="studio-wrapper" style="visibility:hidden">
                            <label> Studio *:</label>
                            <?php if(!empty($studios)) {
                                ?>
                                <select class="form-control" id="studio_list">
                                    <option value="">SELECT</option>
                                    <?php
                                    foreach($studios as $studio) {
                                        
                                        ?>
                                        <option value="<?php echo $studio['id'] ?>" > <?php echo $studio['st_dial_in_number']. ' - ' .$studio['st_name'] ?> </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pr-25">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <a href="javascript:void(0)" class="btn btn-green" id="create-btn" onclick="saveEvent()" >CREATE</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="invite_participant" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="icon icon-cancel-1"></span>
                </button>
                <h4 class="modal-title" id="process_title"></h4>
            </div>
            <div class="modal-body">
                <div class="ann_add_step2">
                    <div class="form-group">
                        <label for="">Send Invitation To: * </label>
                        <div class="radio" style="display:inline;">
                            <label><input type="radio" class="invitation-type" name="invitation_type" checked="checked" value="course">Courses</label>
                        </div>
                        <div class="radio" style="display:inline;">
                            <label><input type="radio" class="invitation-type" name="invitation_type" value="institute">Institution</label>
                        </div>
                        <div class="radio" style="display:inline;">
                            <label><input type="radio" class="invitation-type" name="invitation_type" value="batch">Batch<span id="total_batch_selected"></span></label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="institution-select">
                        <div class="inside-box pos-rel pad-top50" style="overflow-x: hidden;">
                            <div id="invitation_type_wrapper" class="container-fluid nav-content pos-abslt width-100p nav-js-height">
                                
                                <div id="type_course" class="row invitation-type-wrapper">
                                    <div class="rTable content-nav-tbl normal-tbl" style="background:#fff;">
                                        <div class="rTableRow">
                                            <div class="rTableCell">
                                                <a href="javascript:void(0)" id="selected_course_bar" class="select-all-style"></a>
                                            </div>
                                            <div class="rTableCell" style="width: 250px !important;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control srch_txt" id="course_keyword" placeholder="Search by name">
                                                    <span class="searchclear">×</span>
                                                    <a class="input-group-addon" id="basic-addon2"><i class="icon icon-search"> </i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="add-selectn alignment-order">
                                        <div class="inside-box-padding invitation-content-wrapper">
                                        </div>
                                    </div>
                                </div>
                            
                                <div id="type_institute" class="row invitation-type-wrapper">
                                    <div class="rTable content-nav-tbl normal-tbl" style="background:#fff;">
                                        <div class="rTableRow">
                                            <div class="rTableCell">
                                                <a href="javascript:void(0)" id="selected_institute_bar" class="select-all-style"></a>
                                            </div>
                                            <div class="rTableCell" style="width: 250px !important;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control srch_txt" id="institute_keyword" placeholder="Search by name">
                                                    <span class="searchclear">×</span>
                                                    <a class="input-group-addon" id="basic-addon2"><i class="icon icon-search"> </i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="add-selectn alignment-order">
                                        <div class="inside-box-padding invitation-content-wrapper">
                                            
                                        </div>
                                    </div>
                                </div>

                                <div id="type_batch" class="row invitation-type-wrapper">
                                    <div class="rTable content-nav-tbl normal-tbl" style="">
                                        <div class="rTableRow">
                                            <div class="rTableCell">
                                                <a href="javascript:void(0)" style="padding:0px 10px !important;" id="selected_batch_bar" class="select-all-style"></a>
                                            </div>
                                            <div class="rTableCell dropdown" style="width:70%;">
                                                <select id="event_institute_batches" style="border:none;padding: 11px 10px;width:100%;" data-toggle="" data-original-title="" data-placement="bottom" >
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="add-selectn alignment-order">
                                        <div class="inside-box-padding invitation-content-wrapper">  
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="invite_participant_confimed" class="btn btn-green pull-right add-continue">CONTINUE</button>
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
            </div>
        </div>
    </div>
</div>
<style>
.invitation-type-wrapper{ display:none;}
.errortext {display:none;font-size: 18px;color: #929292;text-align: center;padding: 30px;}
.disabled-batch label{ color:#ccc !important;}
.disabled-batch input{ opacity:.3 !important;}
#total_batch_selected {color: #53b665;font-weight: 600;padding-left: 3px;}
</style>
<script>    
 $('#event_time').bind("cut copy paste",function(e) {
     e.preventDefault();
 });
// function removeFileAddded(filename) {
//     var targetelement   = "script";
//     var targetattr      = "src";
//     var allsuspects     = document.getElementsByTagName(targetelement)
//     for (var i=allsuspects.length; i>=0; i--){ //search backwards within nodelist for matching elements to remove
//     if (allsuspects[i] && allsuspects[i].getAttribute(targetattr)!=null && allsuspects[i].getAttribute(targetattr).indexOf(filename)!=-1)
//         allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
//     }
// }

var datePickerLoaded = false;
function loadDatePickerFiles() {
    if(datePickerLoaded == true ) {
        return false;
    }
    datePickerLoaded = true;
    var fileref = document.createElement('script')
        fileref.setAttribute("type","text/javascript")
        fileref.setAttribute("src", __jqueryUi__)
    if( typeof fileref != "undefined" ) {
        document.getElementsByTagName("head")[0].appendChild(fileref);
        fileref.addEventListener('load', function() {
            var today = new Date();
            $('#event_time').timepicker({ timeFormat: 'h:i A' });
            $("#event_date").datepicker({
                language: 'en',
                minDate: today,
                format: 'dd-mm-yyyy',
                autoClose: true,
                onSelect: function(dateText, inst) {
                    
                    var sel_date            = new Date(dateText);
                    var today_date          = new Date();
                    var today_date_second   = new Date(today_date.getFullYear(),today_date.getMonth(),today_date.getDate(),0,0,0,0);

                    if(sel_date.getDate() == today_date_second.getDate()){
                        var current_time = today_date.getHours();
                        $('#event_time').remove();
                        $('#time-wrpper').prepend('<input id="event_time" class="form-control ui-timepicker-input" type="text" class="form-control" onkeypress="return false;">');
                        $('#event_time').timepicker({
                            timeFormat: 'h:i A',
                            interval: 60,
                            minTime: (current_time+1).toString(),
                            maxTime: '11:30pm',
                            dynamic: false,
                            dropdown: true,
                            
                        });
                    }else{

                        $('#event_time').remove();
                        $('#time-wrpper').prepend('<input id="event_time" class="form-control ui-timepicker-input" type="text" class="form-control" onkeypress="return false;">');
                        $('#event_time').timepicker({
                            timeFormat: 'h:i A',
                            interval: 60,
                            maxTime: '11:30pm',
                            dynamic: false,
                            dropdown: true,
                        });
                    }
                }
                    
            });
        });
    }
}

</script>