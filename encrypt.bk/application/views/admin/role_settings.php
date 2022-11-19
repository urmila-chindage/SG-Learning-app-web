<?php include_once 'header.php';?>
<?php $actions = config_item('actions'); ?>
<?php
// $admins = array();
// $sub_admins = $this->config->item('sub_admins');
// foreach ($sub_admins as $key => $value) {
//     $admins[$key] = $value;
// }
// $admins[count($admins)] = $this->config->item('super_admin');
//echo '<pre>';print_r($admins);die;
?>

<link rel="stylesheet" href="<?php echo assets_url() ?>css/bootstrap-multiselect.css">

    <section class="base-cont-top course-content-wrap">     
    <form action='<?php echo admin_url().'role_settings/save' ?>' method='POST'>   
        <div class="container-fluid nav-content nav-course-content">
            <div class="role-edit-btn">
                <button type="button" class="btn btn-danger" onclick="location.href='<?php echo admin_url('role') ?>'">Back</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
            <div class="row">
                <p class="role-title"><?php echo $role['rl_name'] ?></p>
            </div>
        </div>

        <div class="left-wrap col-sm-12">            
            <div class="container role-table-align">          
            <table class="table table-striped role-table">
            <thead>
                <tr class="t-header text-center">
                <th class="role-head"></th>
                <th class="align-left" style="width: 200px;">
                    <label><input type="checkbox" id="view" value="">
                    <span>View</span></label>
                </th>
                <th class="align-left">
                    <label><input type="checkbox" id="add" value="">
                    <span>Add</span></label>
                </th>
                <th class="align-left">
                    <label><input type="checkbox" id="edit" value="">
                    <span>Edit</span></label>
                </th>
                <th class="align-left">
                    <label><input type="checkbox" id="delete" value="">
                    <span>Delete</span></label>
                </th>
                </tr>
            </thead>
            <tbody>
                        
            <input type="hidden" name="role_id" value="<?php echo $role['id'] ?>">
            <?php
            
            $checked = 'checked="checked"';
            foreach($modules as $module):
                $module['module_permissions'] = explode(',', $module['module_permissions']);
                ?>
                <tr class="parent-module">
                    <td class="role-name">                                              
                        <?php echo $module['module_name']; ?>
                        <?php
                            $name = 'module'.'_'.$module['id'];
                            if( !empty($$name) )
                            {
                                echo '<span class="toggle-arrow"><i id="module_toggle" class="module'.$module['id'].' icon icon-up-open"></i></span>';
                            }
                        ?>  
                    </td>
                    <td class="align-check">
                        <div class="checkbox">
                            <label><input type="checkbox" class="access1 module<?php echo $module['id'] ?> all-access-view" name="access[<?php echo $module['id'] ?>][1]" <?php echo (isset($permissions[$module['id']]) && in_array(1,$permissions[$module['id']]))?$checked:'' ?> value="1" style="<?php echo (!in_array('1',$module['module_permissions']))?'display: none;':'' ?>" onchange="checkAndActivateChildAndParent(this, '<?php echo $module['id'] ?>', '', '')"></label>
                        </div>
                    </td>
                    <td class="align-check">
                        <div class="checkbox">
                            <label><input type="checkbox" class="access2 module<?php echo $module['id'] ?> all-access-add" name="access[<?php echo $module['id'] ?>][2]" <?php echo (isset($permissions[$module['id']]) && in_array(2,$permissions[$module['id']]))?$checked:'' ?> value="2" style="<?php echo (!in_array('2',$module['module_permissions']))?'display: none;':'' ?>"></label>
                        </div>
                    </td>
                    <td class="align-check">
                        <div class="checkbox">
                            <label><input type="checkbox" class="access3 module<?php echo $module['id'] ?> all-access-edit" name="access[<?php echo $module['id'] ?>][3]" <?php echo (isset($permissions[$module['id']]) && in_array(3,$permissions[$module['id']]))?$checked:'' ?> value="3" style="<?php echo (!in_array('3',$module['module_permissions']))?'display: none;':'' ?>"></label>
                        </div>
                    </td>
                    <td class="align-check">
                        <div class="checkbox">
                            <label><input type="checkbox" class="access4 module<?php echo $module['id'] ?> all-access-delete" name="access[<?php echo $module['id'] ?>][4]" <?php echo (isset($permissions[$module['id']]) && in_array(4,$permissions[$module['id']]))?$checked:'' ?> value="4" style="<?php echo (!in_array('4',$module['module_permissions']))?'display: none;':'' ?>"></label>
                        </div>
                    </td>
                </tr>
                <?php                    
                    if( !empty($$name) )
                    {
                        if($module['id'] == '3'){
                            ?>
                            <tr style="<?php //echo (!in_array(1,$permissions[$module['id']]))?'display:none;':'' ?>" class="module<?php echo $module['id']; ?>">
                                <td></td>
                                <td style="font-weight: 600;vertical-align: sub;"><label><input type="radio" name="full_course" value="1" <?php echo ($role['rl_full_course'] == '1')? 'checked="checked"':'' ?>> All course access</label> </td>
                                <td style="font-weight: 600;vertical-align: sub;width: 205px;" class="text-left"><label><input type="radio" name="full_course" value="0" <?php echo ($role['rl_full_course'] == '0')? 'checked="checked"':'' ?>> Restricted course access </label></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                        }                        
                        foreach($$name as $sub_module):
                            
                            $sub_module['module_permissions'] = explode(',', $sub_module['module_permissions']);
                        ?>
                        <tr style="<?php //echo (!in_array(1,$permissions[$module['id']]))?'display:none;':'' ?>" class="module<?php echo $module['id']; ?> sub-module-row">
                            <td class="role-name">
                                <?php echo $sub_module['module_name']; ?>
                            </td>
                            <td class="align-check">
                                <div class="checkbox">
                                    <label><input type="checkbox" class="access1 module<?php echo $sub_module['id'] ?> module<?php echo $module['id'] ?>_sub" name="access[<?php echo $sub_module['id'] ?>][1]" <?php echo (isset($permissions[$sub_module['id']]) && in_array(1,$permissions[$sub_module['id']]))?$checked:'' ?> value="1" <?php echo (!in_array('1',$sub_module['module_permissions']))?'disabled="disabled"':'' ?> onchange="checkAndActivateParent(this, '<?php echo $module['id'] ?>','<?php echo $sub_module['id'] ?>')"></label>
                                </div>
                            </td>
                            <td class="align-check">
                                <div class="checkbox">
                                    <label><input type="checkbox" class="access2 module<?php echo $sub_module['id'] ?> module<?php echo $module['id'] ?>_sub" name="access[<?php echo $sub_module['id'] ?>][2]" <?php echo (isset($permissions[$sub_module['id']]) && in_array(2,$permissions[$sub_module['id']]))?$checked:'' ?> value="2" <?php echo (!in_array('2',$sub_module['module_permissions']))?'disabled="disabled"':'' ?> onchange="checkAndActivateChildAndParent(this, '<?php echo $sub_module['id'] ?>', '<?php echo $module['id'] ?>', '<?php echo 'content_add' ?>')" ></label>
                                </div>
                            </td>
                            <td class="align-check">
                                <div class="checkbox">
                                    <label><input type="checkbox" class="access3 module<?php echo $sub_module['id'] ?> module<?php echo $module['id'] ?>_sub" name="access[<?php echo $sub_module['id'] ?>][3]" <?php echo (isset($permissions[$sub_module['id']]) && in_array(3,$permissions[$sub_module['id']]))?$checked:'' ?> value="3" <?php echo (!in_array('3',$sub_module['module_permissions']))?'disabled="disabled"':'' ?> onchange="checkAndActivateChildAndParent(this, '<?php echo $sub_module['id'] ?>', '<?php echo $module['id'] ?>')" ></label>
                                </div>
                            </td>
                            <td class="align-check">
                                <div class="checkbox">
                                    <label><input type="checkbox" class="access4 module<?php echo $sub_module['id'] ?> module<?php echo $module['id'] ?>_sub" name="access[<?php echo $sub_module['id'] ?>][4]" <?php echo (isset($permissions[$sub_module['id']]) && in_array(4,$permissions[$sub_module['id']]))?$checked:'' ?> value="4" <?php echo (!in_array('4',$sub_module['module_permissions']))?'disabled="disabled"':'' ?> onchange="checkAndActivateChildAndParent(this, '<?php echo $sub_module['id'] ?>', '<?php echo $module['id'] ?>')" ></label>
                                </div>
                            </td>
                        </tr>
                        <?php
                        if($sub_module['id'] == '5')        // Course content module
                        {
                            ?>
                            <tr style="<?php echo (!in_array(2,$permissions[$sub_module['id']]))?'display:none;':'' ?>" class="module<?php echo $sub_module['id']; ?>" id="content_types_div">
                                <td></td>
                                <td style="font-weight: 600;vertical-align: sub;"><label> Content Types Allowed: </label> </td>
                                <td style="font-weight: 600;vertical-align: sub;width: 205px;" class="text-left">
                                    <?php 
                                        $lecture_types = array( 'video'=>'video',
                                                                'document'=>'document',
                                                                'quiz'=>'quiz',
                                                                'youtube'=>'youtube',
                                                                'text'=>'html',
                                                                // 'wikipedia'=>,
                                                                'live'=>'live lecture',
                                                                'descriptive_test'=>'assignment',
                                                                // 'recorded_videos'=>'recorded_videos',
                                                                'scorm'=>'scorm',
                                                                //'cisco_recorded_videos'=>'recorded videos',
                                                                'audio'=>'audio',
                                                                'survey'=>'survey',
                                                                'certificate'=>'certificate'
                                                            );

                                    ?>
                                    <select id="listing_content" name="content_types[]" class="multiselect" multiple="multiple">
                                        <?php foreach($lecture_types as $l_type => $type): ?>
                                            <option value="<?php echo $l_type ?>"  <?php echo (!empty($role_content_types) && in_array($l_type,$role_content_types))? 'selected':'' ?> ><?php echo strtoupper($type) ?> </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php
                        endforeach;                        
                    }                
            endforeach;
            ?>
            
            </form>
            </tbody>
            </table>
        </div>
        </div>
    </section>

<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap-multiselect.js"></script>

    <!-- <script type="text/javascript" src="<?php echo assets_url() ?>js/role.js"></script> -->
<script>
$(document).ready(function(){
    $('#add').prop('checked', true);
    $('#view').prop('checked', true);
    $('#edit').prop('checked', true);
    $('#delete').prop('checked', true);
    $('.access2').each(function(){
        if( $(this).prop('checked') == false){
            $('#add').prop('checked', false);
        }
    });
    $('.access1').each(function(){
        if( $(this).prop('checked') == false){
            $('#view').prop('checked', false);
        }
    });
    $('.access3').each(function(){
        if( $(this).prop('checked') == false){
            $('#edit').prop('checked', false);
        }
    });
    $('.access4').each(function(){
        if( $(this).prop('checked') == false){
            $('#delete').prop('checked', false);
        }
    });

    $('.access2').change(function(){
        $('#add').prop('checked', true);
        $('.access2').not("[disabled]").each(function(){
            if( $(this).prop('checked') == false){
                $('#add').prop('checked', false);
            }
        });
    });
    $('.access1').change(function(){
        $('#view').prop('checked', true);
        $('.access1').not("[disabled]").each(function(){
            if( $(this).prop('checked') == false){
                $('#view').prop('checked', false);
            }
        });
             
    });
    $('.access3').change(function(){
        $('#edit').prop('checked', true);
        $('.access3').not("[disabled]").each(function(){
            if( $(this).prop('checked') == false){
                $('#edit').prop('checked', false);
            }
        });
    });
    $('.access4').change(function(){
        $('#delete').prop('checked', true);
        $('.access4').not("[disabled]").each(function(){
            if( $(this).prop('checked') == false){
                $('#delete').prop('checked', false);
            }
        });
    });

    //bulk action trigger
    $('#view').change(function(){
        if( $(this).prop('checked') == true) {
            $('.access1').not("[disabled]").prop('checked', true);
            // $('tr[class^=module]').show();
        } else {
            $('.access1').not("[disabled]").prop('checked', false);
            $('.access2').not("[disabled]").prop('checked', false);
            $('.access3').not("[disabled]").prop('checked', false);
            $('.access4').not("[disabled]").prop('checked', false);
            // $('tr[class^=module]').hide();
            $('#view, #add, #edit, #delete').prop('checked', false);
            $('#content_types_div').css('display', 'none');
            $('.all-access-add, .all-access-edit, .all-access-delete').prop('checked', false);
        }
    });
    $('#add').change(function(){        
        if($(this).prop('checked') == true) {
            $('.access2').not("[disabled]").prop('checked', true);
            $('#view').prop('checked', false);
            $('#view').trigger('click');
            $('#content_types_div').css('display', 'table-row');
        } else {
            $('.access2').not("[disabled]").prop('checked', false);
            $('#content_types_div').css('display', 'none');
        }
    });
    $('#edit').change(function(){
        if($(this).prop('checked') == true) {
            $('.access3').not("[disabled]").prop('checked', true);
            $('#view').prop('checked', false);
            $('#view').trigger('click');
        } else {
            $('.access3').not("[disabled]").prop('checked', false);
        }
    });
    $('#delete').change(function(){
        if($(this).prop('checked') == true) {
            $('.access4').not("[disabled]").prop('checked', true);
            $('#view').prop('checked', false);
            $('#view').trigger('click');
        } else {
            $('.access4').not("[disabled]").prop('checked', false);
        }
    });
    $('#module_toggle').on('click', function(){
        var classes     = $(this).attr("class");
        var classArray  = classes.split(" ");
        if(typeof classArray[0] !== 'undefined')
        {
            $('tr.'+classArray[0]).toggle();          
        } 
    });
     //accordion icon toggles       
    // $('.toggle-arrow').click(function() {
    //     $(".icon", this).toggleClass("icon-down-open icon-up-open");
    // });
   //toggle ends  
});
$(document).on('change', '.access1.module3', function(){
    if($(this).prop('checked') == false) {
        $('.access1.module3_sub').prop('checked', false);
        $('.access2.module3_sub').prop('checked', false);
        $('.access3.module3_sub').prop('checked', false);
        $('.access4.module3_sub').prop('checked', false);
        $('#content_types_div').css('display', 'none');
    }
});
$(document).on('change', '.access1', function(){
    if($(this).prop('checked') == false) {
        $(this).parents('.parent-module').find('input[type=checkbox]:checked').removeAttr('checked');
        $(this).parents('.sub-module-row').find('input[type=checkbox]:checked').removeAttr('checked');
    }
});

$(document).on('change', '.access2, .access3, .access4', function(){
    if($(this).prop('checked') == true) {
        $(this).parents('.parent-module').find('input[type=checkbox].access1').prop('checked', true);
        var classes = $(this).attr('class');
        var classArray  = classes.split(" ");
        if(typeof classArray[1] !== 'undefined')
        {
            $(this).parents('.sub-module-row').find('input[type=checkbox].access1.'+classArray[1]).prop('checked', true);
        }        
        // $(this).parents('.sub-module-row').find('input[type=checkbox].access1').prop('checked', true);
    }
});
function checkAndActivateParent(event, parent_id,module_id='')
{
    if(event.checked == true)
    {
        $('.access1.module'+parent_id).prop('checked', true);
    }else{
        $('.access2.module'+module_id).prop('checked', false);
        $('.access3.module'+module_id).prop('checked', false);
        $('.access4.module'+module_id).prop('checked', false);
        if(module_id == '5') {
            $('#content_types_div').css('display', 'none');
        }
    }
    
}
function checkAndActivateChildAndParent(event, module_id, parent_id, access='')
{
    //console.log(event.checked);
    if(event.checked == true)
    {
        $('.access1.module'+module_id+'_sub').prop('checked', true);
        // $('.access1.module'+module_id).prop('checked', true);
        $('.access1.module'+parent_id).prop('checked', true);

        if(module_id == '5' && access == 'content_add') {
            $('#content_types_div').css('display', 'table-row');
        }
    } else {
        if(module_id == '5' && access == 'content_add') {
            $('#content_types_div').css('display', 'none');
        }
        $('.access1.module'+module_id+'_sub').prop('checked', false);
        $('.access2.module'+module_id).prop('checked', false);
        $('.access3.module'+module_id).prop('checked', false);
        $('.access4.module'+module_id).prop('checked', false);
        
    }
}
<?php
    $popup              = $this->session->flashdata('popup'); 
?>
$(document).ready(function(){
    var popup_message       = atob('<?php echo base64_encode(json_encode($popup)) ?>');
    popup_message   = $.parseJSON(popup_message);
    if(popup_message != null){
        var messageObject = {
            'body':popup_message.message,
            'button_yes':'OK',
            'prevent_button_no': true
        };
        if(popup_message.success == true){
            callback_success_modal(messageObject);
        }else{
            callback_danger_modal(messageObject);
        }
    }
});
$(document).ready(function() {
        $('#listing_content').multiselect({
            includeSelectAllOption: ($('#listing_content option').length>1),
            buttonWidth:'100%',
        });
    });

</script>
<?php include_once 'footer.php';?>
