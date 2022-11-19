<?php include_once 'header.php';?>
<!-- Jquery ui library -->
<script type="text/javascript" src="<?php echo assets_url()?>js/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url()?>css/toastr.min.css">
<script type="text/javascript" src="<?php echo assets_url()?>js/toastr.min.js"></script>
<script>

toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-bottom-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
                } 

    var __admin_url           = '<?php echo admin_url() ?>';
    $(document).ready(function() {
        $("#category_manage_wrapper").sortable({
            connectWith: ["#category_manage_wrapper"],
            update: function() {
                var categories = []
                    $('.dragging').each(function(){
                        categories.push($(this).attr('id').replace(/category_/g, ''))
                    });
                $.ajax({
                    url: __admin_url+'question_manager/update_category_position',
                    type: "POST",
                    async:true,
                    data:{ "is_ajax":true, "category_id":categories},
                    success: function(response) {
                        //console.log(response);
                        if(!response.error){
                            toastr["success"]('',"Categories successfully reordered");
                        }else{
                            toastr["warning"]('',"Failed to reorder the category");
                        }
                    }
                });
            }
        });
    });
</script>

<style type="text/css">
   .catagory-wrap{
   height: 100%;
   border-right: 1px solid #ccc;
   overflow-y:auto;
   }
.question-category-lecturename {
    width: calc(100% - 39px);
    display: inline-block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding-left: 15px;
    line-height: 40px;
}
.lecture-control {
    margin: 9px 8px 0px 0px;
}
.select{
    background: #64277d;
}
.select .dropdown-tigger{
    color: #fff;
}
.select .question-category-lecturehold
{
    color: #fff;
}
.label-tag{
  font-size:12px;
}
.data-available{
    margin-top:10em;text-align:center;
}

</style>
<?php //print_r($category_manager);exit;?>
<section class="content-wrap create-group-wrap settings-top" style="padding:0px" >
   <div class="col-sm-4 course-cont-wrap contact-settings innercontent catagory-wrap" style="display: block;">
      <h3 class="pull-left">Course Category</h3>
      <div class="buldr-header clearfix">
         <div class="pull-right rite-side" style="margin-top: 16px;">
            <div class="btn-group">
                <?php if(in_array('2', $category_manager)): ?>
               <button type="button" class="btn btn-success"  data-toggle="modal" data-target="#bulk_category_manage"  onclick="bulkCategory()" style="
                  margin: unset;
                  ">Add Category</button>
                <?php endif; ?>
            </div>
         </div>
      </div>
      <div>
      <ul class="lecture-wrapper ui-sortable category-list" id="category_manage_wrapper">
         <?php
         if(in_array('1', $category_manager)){
         $total_course_categories = count($course_categories);
         if(!empty($course_categories)){ 
         foreach($course_categories as $course_category): 
          ?>
          <li class="dragging <?php if($course_categories[0]['id']==$course_category['id']){ ?>select <?php } ?>" id="category_<?php echo $course_category['id'] ?>" onclick="populateSubjects('<?php echo $course_category['id'] ?>')">
            
            <div class="drager ui-sortable-handle">
                <div class="drager-icon">............</div>
            </div>
             
             <div class="lecture-hold question-category-lecturehold"  >
              <span id="cat_<?php echo $course_category['id']; ?>"  class="lecture-name question-category-lecturename catagory" data-toggle="tooltip" data-placement="top" data-original-title="<?php if(strlen($course_category['ct_name'])>22){ echo $course_category['ct_name']; } ?>">
                <?php echo ((strlen($course_category['ct_name'])>25)?(strip_tags(substr($course_category['ct_name'], 0, 25)).'...'):strip_tags($course_category['ct_name']))?>
                <?php $ct_status_label = ($course_category['ct_status']) ? 'public':'private'; ?>
                <?php $ct_status_class = ($course_category['ct_status']) ? 'success':'warning'; ?>
                    <label class="pull-right label label-<?php echo $ct_status_class;?>" style="margin-top: 10px;" id="action_class_<?php echo $course_category['id'];?>"><?php echo lang($ct_status_label);?></label>
             </span>
              <?php if((in_array('3', $category_manager)) ||(in_array('4', $category_manager))){ ?>
              <div class="btn-group lecture-control"  >
                   <span class="dropdown-tigger"   data-toggle="dropdown">
                        <span class="label-text">
                            <i class="icon icon-down-arrow"></i>
                        </span>
                        <span class="tilder"></span>
                   </span>
                   <ul class="dropdown-menu pull-right" role="menu">
                   <?php if(in_array('3', $category_manager)): ?>
                        <li id="status_btn_<?php echo $course_category['id'] ?>">
                            <?php $ct_status        = ($course_category['ct_status'])?'make_private':'make_public'; ?>
                            <?php $ct_status_label  = ($course_category['ct_status'])?'public':'private'; ?>
                            <a href="javascript:void(0);" onclick="changeCategoryStatus('<?php echo $course_category['id'] ?>', '<?php echo $ct_status ?>','<?php echo addslashes($course_category['ct_name'])?>' )" ><?php echo lang($ct_status); ?></a>
                        </li>
                      <li>
                        <a href="javascript:void(0)" onclick="editCategory('<?php echo $course_category['id'] ?>', '<?php echo $ct_status_label; ?>')">Edit</a>
                
                      </li>
                      <li>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory('<?php echo base64_encode($course_category['ct_name']) ?>', '<?php echo $course_category['id'] ?>')">Migrate</a>
                      </li>
                    <?php endif; ?>
                    <?php if(in_array('4', $category_manager)): ?>
                      <li>
                        <a href="javascript:void(0)" onclick="deleteCategory('<?php echo base64_encode($course_category['ct_name']) ?>', '<?php echo $course_category['id'] ?>')">Delete</a>
                      </li>
                    <?php endif; ?>
                   </ul>
                </div>
               
              <?php } ?>
             </div>
          </li>
          <?php endforeach; 
           } 
           else
           {
           ?>
           <p class="subject-data data-available">No categories available.</p>
           <?php
           } 
        }else {
            ?>
            <p class="subject-data data-available">Permission denied.</p>
            <?php
        }
        ?>
            </ul>
      </div>
   </div>
   <div class="col-sm-4 course-cont-wrap contact-settings innercontent catagory-wrap" style="display: block;">
      <h3 class="pull-left">Question Subjects</h3>
      <div class="buldr-header clearfix">
         <div class="pull-right rite-side" style="margin-top: 16px;">
         <?php if(in_array('2', $question_manager)): ?>
            <div class="btn-group">
               <button type="button" class="btn btn-success" data-toggle="modal" data-target="#bulk_subject_manage"  onclick="bulkSubjects()"  style="
                  margin: unset;
                  ">Add Subject</button>
               <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="
                  min-width: auto;
                  ">
               <span class="caret"></span>
               <span class="sr-only">Toggle Dropdown</span>
               </button>
               <ul class="dropdown-menu dropdown-menu-right">
                  <li><a href="#" data-toggle="modal" onclick="generateSubjectList();" data-target="#bulk_subject_merge">Merge</a></li>
               </ul>
            </div>
        <?php endif; ?>
         </div>
      </div>
      <div>
       <ul class="lecture-wrapper ui-sortable" id="question_subject_manage_wrapper">
    <?php if(in_array('1', $question_manager)){
        if(!empty($question_subjects)){ ?>
         <?php foreach($question_subjects as $question_subject): ?>
          <li id="subject_<?php echo $question_subject['id'] ?>" onclick="populateTopics('<?php echo $question_subject['qs_category_id'] ?>','<?php echo $question_subject['id'] ?>')"  <?php if($selected_subject==$question_subject['id']){ ?>class="select" <?php } ?>>
             <div class="lecture-hold question-category-lecturehold">
              <span class="lecture-name question-category-lecturename catagory"  data-toggle="tooltip" data-placement="top" data-original-title="<?php if((strlen($question_subject['qs_subject_name'])>22)){ echo $question_subject['qs_subject_name']; }?>"><?php
                echo ((strlen($question_subject['qs_subject_name'])>25)?(strip_tags(substr($question_subject['qs_subject_name'], 0, 22)).'...'):strip_tags($question_subject['qs_subject_name'])) ?></span>
                <?php if((in_array('3', $question_manager)) ||(in_array('4', $question_manager))){ ?>
                <div class="btn-group lecture-control">
                   <span class="dropdown-tigger" data-toggle="dropdown">
                   <span class="label-text">
                   <i class="icon icon-down-arrow"></i>
                   </span>
                   <span class="tilder"></span>
                   </span>
                   <ul class="dropdown-menu pull-right" role="menu">
                    <?php if(in_array('3', $question_manager)): ?>
                      <li>
                        <a href="javascript:void(0)" onclick="editQuestionSubject('<?php echo $question_subject['id'] ?>')">Edit</a>
                      </li>
                    <?php endif; 
                    if(in_array('4', $question_manager)):
                    ?>  
                      <li>
                        <a href="javascript:void(0)" onclick="deleteSubject('<?php echo base64_encode($question_subject['qs_subject_name']) ?>', '<?php echo $question_subject['id'] ?>')">Delete</a>
                      </li>
                      <?php endif; ?>
                   </ul>
                </div>
                <?php } ?>
             </div>
          </li>
        <?php endforeach; } else
           {
           ?>
           <p class="subject-data  data-available">No subjects available.</p>
           <?php
           } 
        } else {
            ?>
            <p class="subject-data data-available">Permission denied.</p>
            <?php
        } ?>
       </ul>
       
      </div>
   </div>
   <div class="col-sm-4 course-cont-wrap contact-settings innercontent" style="display: block; overflow-y:auto; height: 100%">
      <h3 class="pull-left">Question Topics</h3>
      <div class="buldr-header clearfix">
         <div class="pull-right rite-side" style="margin-top: 16px;">
         <?php if(in_array('2', $question_manager)): ?>
            <div class="btn-group">
               <button type="button" class="btn btn-success" onclick="bulkTopics();" data-toggle="modal"   data-target="#bulk_topic_manage" style="
                  margin: unset;
                  ">Add Topic</button>
               <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="
                  min-width: auto;
                  ">
               <span class="caret"></span>
               <span class="sr-only">Toggle Dropdown</span>
               </button>
               <ul class="dropdown-menu dropdown-menu-right">
                  <li><a href="#" data-toggle="modal" data-target="#bulk_topic_merge" onclick="generateTopicList();">Merge</a></li>
               </ul>
            </div>
         <?php endif; ?>
         </div>
      </div>
      <div>
       <?php 
      //if(!empty($question_topics)): ?>
       <ul class="lecture-wrapper ui-sortable" id="question_topic_manage_wrapper">
        <?php 
        if(in_array('1', $question_manager)){
            if(!empty($question_topics)){ 
            foreach($question_topics as $question_topic): ?>
          <li id="topic_<?php echo $question_topic['id'] ?>">
             <div class="lecture-hold question-category-lecturehold">
              <span class="lecture-name question-category-lecturename catagory" data-toggle="tooltip" data-placement="top" data-original-title="<?php if((strlen($question_topic['qt_topic_name'])>22)){ echo $question_topic['qt_topic_name']; } ?>"><?php
                echo ((strlen($question_topic['qt_topic_name'])>25)?(strip_tags(substr($question_topic['qt_topic_name'], 0, 22)).'...'):strip_tags($question_topic['qt_topic_name'])) ?></span>
                <?php if((in_array('3', $question_manager)) || (in_array('4', $question_manager))): ?>
                <div class="btn-group lecture-control">
                   <span class="dropdown-tigger" data-toggle="dropdown">
                   <span class="label-text">
                   <i class="icon icon-down-arrow"></i>
                   </span>
                   <span class="tilder"></span>
                   </span>
                   <ul class="dropdown-menu pull-right" role="menu">
                   <?php if(in_array('3', $question_manager)): ?>
                      <li>
                        <a href="javascript:void(0)" onclick="editQuestionTopic('<?php echo $question_topic['id'] ?>')">Edit</a>
                
                      </li>
                      <?php endif; 
                        if(in_array('4', $question_manager)):
                        ?>  
                      <li>
                        <a href="javascript:void(0)" onclick="deleteTopic('<?php echo base64_encode($question_topic['qt_topic_name']) ?>', '<?php echo $question_topic['id'] ?>')">Delete</a>
                      </li>
                      <?php endif; ?>
                   </ul>
                </div>
                <?php endif; ?>
             </div>
          </li>
          <?php endforeach; 
          } else { ?>
            <p id="topic-data" class="topic-data data-available" >No topics available.</p>  
          <?php 
          }
        } else { ?>
            <p id="topic-data" class="topic-data data-available" >Permission denied.</p> 
        <?php } ?>
         
       </ul>
      </div>
   </div>
</section>


<!-- Modal pop up contents:: Delete Section popup-->
<div class="modal fade alert-modal-new" id="deleteCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
            <div class="modal-body">
                <div class="form-group">
                    <b id="category_delete_header_text"></b>
                    <p class="m0" id="category_delete_message"></p>
                    <p>This action cannot be undone</p>
                </div>
            </div>
            <div class="modal-footer" id="category_delete_footer">
                <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-red" id="delete_category_ok">YES, DELETE !</button>
            </div>
        </div>
    </div>
</div>
<!-- !.Modal pop up contents :: Delete Section popup-->



<script>
        
        $(document).ready(function() {

            $("#category_manage_wrapper").on("click", ".catagory,.lecture-control", function(e){
            e.preventDefault();
            var $this = $(this).parent().parent();
            $this.addClass("select").siblings().removeClass("select");
            })

            $("#question_subject_manage_wrapper").on("click", ".catagory,.lecture-control", function(e){
            e.preventDefault();
            var $this = $(this).parent().parent();
            $this.addClass("select").siblings().removeClass("select");
            })

            $("#question_topic_manage_wrapper").on("click", ".catagory,.lecture-control", function(e){
            e.preventDefault();
            var $this = $(this).parent().parent();
            $this.addClass("select").siblings().removeClass("select");
            })
        });

    </script>
    <script>
    var __cat_permission = <?php echo json_encode($category_manager); ?>;
    var __qus_permission = <?php echo json_encode($question_manager); ?>;
    var assets_url       = '<?php echo assets_url() ?>';
    var __sel_category   = '<?php echo $selected_category; ?>';
    var __sel_subject    = '<?php echo $selected_subject; ?>';
    //var __sel_topic     = 0;
</script>


<script src="<?php echo assets_url() ?>js/question_manager.js"></script>
<?php include_once 'footer.php';?>

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="bulk_category_manage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">ADD CATEGORIES</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Category Names <span class="label-tag">(Comma seperated or in new line) </span> *:</label>
                    <textarea id="bulk_categories" name="bulk_categories" class="form-control" placeholder="eg: Electronics and communication,Computer science.. " rows="5" cols="200"></textarea>
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_category_btn" onclick="saveBulkCategory()" >SAVE</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" id="bulk_subject_merge" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">MERGE SUBJECTS</h4>
            </div>

               <div class="modal-body">
                 <div class="form-group">
                    <label>Choose Question Subjects *:</label>
           <div class="ann_add_step1" style="display: block;">
                        <div class="form-group">
                            <div class="institution-select" style="display: block;">
                                <div class="inside-box pos-rel pad-top50" id="users_new_group_wrapper">
                                    <div id="render_data" class="container-fluid nav-content pos-abslt width-100p nav-js-height">
                                        <div class="row">
                           
                                            <div class="add-selectn announce-list-height alignment-order">
                                                <div class="inside-box-padding" id="merge_subjects">
                                                   
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- <div class="form-group">
                    <label>Course Subject *:</label>
                    <select class="form-control" id="merge_subjects" multiple="multiple">
                        <option value="">Choose Course Subject</option>
                        <?php /*foreach($question_subjects as $question_subject): ?>
                            <option value="<?php echo $question_subject['id'] ?>"><?php echo $question_subject['qs_subject_name'] ?></option>
                        <?php endforeach; */ ?>
                    </select>
                </div> -->
                <div class="form-group" id="subjectMergeError">
                    
                </div>
                <div class="form-group">
                    <label>Subject Name *:</label>
                    <input type="text" maxlength="80" placeholder="eg: Aptitude" id="merge_subject_name" name="merge_subject_name" class="form-control">
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_merge_subject_btn" onclick="saveMergeSubject()" >SAVE</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" id="bulk_topic_manage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">ADD TOPICS</h4>
            </div>

               <div class="modal-body">
               <!--div class="form-group">
                    <label>Course Category *:</label>
                    <select class="form-control" id="topic_bulk_category" onclick="generateSubjectList()">
                        <option value="">Choose course category</option>
                        <?php 
                        /*
                        foreach($course_categories as $course_category): ?>
                            <option value="<?php echo $course_category['id'] ?>"><?php echo $course_category['ct_name'] ?></option>
                        <?php endforeach;
                        */
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Course Subject *:</label>
                    <select class="form-control" id="topic_bulk_subject">
                        <option value="">Choose Course Subject</option>
                        <?php
                        /*
                        foreach($question_subjects as $question_subject): ?>
                            <option value="<?php echo $question_subject['id'] ?>"><?php echo $question_subject['qs_subject_name'] ?></option>
                        <?php endforeach;
                        */
                        ?>
                    </select>
                </div-->
                <div class="form-group">
                    <label>Topic Names <span class="label-tag">(Comma seperated or in new line) </span> *:</label>
                    <textarea id="bulk_topics" name="bulk_topics" placeholder="eg: Diodes,Communication system.. " class="form-control" rows="5" cols="200" ></textarea>
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_bulk_topic_btn" onclick="saveBulkTopic()" >SAVE</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bulk_topic_merge" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">MERGE TOPICS</h4>
            </div>

               <div class="modal-body">
           <div class="form-group">
                    <label>Choose Question Topics *:</label>
           <div class="ann_add_step1" style="display: block;">
                        <div class="form-group">
                            <div class="institution-select" style="display: block;">
                                <div class="inside-box pos-rel pad-top50" id="users_new_group_wrapper">
                                    <div id="render_data" class="container-fluid nav-content pos-abslt width-100p nav-js-height">
                                        <div class="row">
                                           <!--  <div class="rTable content-nav-tbl normal-tbl" style="background:#fff;">
                                                <div class="rTableRow">
                                                    <div class="rTableCell">
                                                        <a href="javascript:void(0)" class="select-all-style">
                                                            <label>
                                                                <input onclick="selectAll('instAll', 'inst-course')" value="1" class="select-users-new-group-parent" id="instAll" type="checkbox"> Select All</label>
                                                        </a>
                                                    </div>
                                                
                                                </div>
                                            </div> -->
                                            <div class="add-selectn announce-list-height alignment-order">
                                                <div class="inside-box-padding" id="merge_topics">
                                                   
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="form-group">
                    <label>Topic Name *:</label>
                    <input type="text" maxlength="80" placeholder="eg: Logic Gates" id="merge_topic_name" name="merge_topic_name" class="form-control">
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_merge_topic_btn" onclick="saveMergeTopic()" >SAVE</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" id="bulk_subject_manage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">ADD SUBJECTS</h4>
            </div>

            <div class="modal-body">
               <!--div class="form-group">
                    <label>Course Category *:</label>
                    <select class="form-control" id="question_course_category">
                        <option value="">Choose course category</option>
                        <?php 
                        /*
                        foreach($course_categories as $course_category): ?>
                            <option value="<?php echo $course_category['id'] ?>"><?php echo $course_category['ct_name'] ?></option>
                        <?php endforeach; 
                        */
                        ?>
                    </select>
                </div-->
                <div class="form-group">
                    <label>Subject Names <span class="label-tag">(Comma seperated or in new line) </span> *:</label>
                    <textarea id="bulk_subject" name="bulk_subjects" placeholder="eg: Signals and systems,Digital electronics.. " class="form-control" rows="5" cols="200" ></textarea>
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_subject_btn" onclick="saveBulkSubject()" >SAVE</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="category_migrate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">MIGRATE CATEGORY</h4>
            </div>
            <div class="modal-body">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>From *:</label>
                        <select class="form-control" id="category_selected_migrate">
                            
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>To *:</label>
                        <select class="form-control" id="category_select_migrate">
                            
                        </select>
                    </div>
                </div>       
            </div>
            <div class="modal-footer">
                 <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                 <button type="button" class="btn btn-green" id="save_migrate_category_btn" >MIGRATE</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="category_manage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">EDIT CATEGORY</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Category Name *:</label>
                    <input type="text" maxlength="80" placeholder="eg: Electronics and Communication" id="category_name" class="form-control">
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_category_btn" onclick="saveCategory()" >SAVE</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" id="subject_manage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">EDIT SUBJECT</h4>
            </div>
            <div class="modal-body">
               
                <div class="form-group">
                    <label>Subject Name *:</label>
                    <input type="text" maxlength="80" placeholder="eg: Digital Electronics" id="qusetion_subject" name="qusetion_subject" class="form-control">
                    <input type="hidden" id="editQuestionSubjectSubId" name="editQuestionSubjectSubId"/>
                    <input type="hidden"  id="subject_action" name="subject_action" class="form-control" value="0">
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_que_subject_btn" onclick="saveQusetionSubject()" >SAVE</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents :: Create html -->

<!-- Modal pop up contents :: Create html -->
<div class="modal fade" id="topic_manage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                <h4 class="modal-title" id="myModalLabel">EDIT TOPIC</h4>
            </div>
            <div class="modal-body">
               
                <div class="form-group">
                    <label>Topic Name *:</label>
                    <input type="text" maxlength="80" placeholder="eg: Diodes" id="question_topic" name="question_topic" class="form-control">
                    <input type="hidden"  id="topic_action" name="topic_action" class="form-control" value="0">
                </div>
        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                <button type="button" class="btn btn-green" id="save_que_subject_btn" onclick="saveQusetionTopic()" >SAVE</button>
            </div>
        </div>
    </div>
</div>
<script>
var __asstes_url = '<?php echo assets_url()?>';
</script>
<!-- Modal pop up contents :: Create html -->



