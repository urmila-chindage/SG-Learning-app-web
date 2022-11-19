<!-- <link rel="stylesheet" href="<?php //echo assets_url() ?>js/selectize/css/selectize.default.css"> -->
    <!-- Modal pop up contents :: Create new section popup-->
    <style>
        .default {cursor: text;}
    </style>
    <div class="modal fade" id="create_task" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title"><?php echo lang('add_task') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="task_tittle"><?php echo lang('add_task_tittle') ?> <span class="text-danger">*</span>:</label>
                        <input type="text" onkeypress="return preventSpecialCharector(event)" maxlength="100" id="task_tittle" placeholder="<?php echo lang('add_task_tittle') ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="task_description"><?php echo lang('add_task_description') ?> <span class="text-danger">*</span>:</label>
                        <textarea type="text" cols="50" rows="6" id="task_description" placeholder="<?php echo lang('add_task_description') ?>" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="task_due_date">Due date <span class="text-danger">*</span>:</label>
                        <input type="text" id="task_due_date" placeholder="1988-10-10" class="form-control custom-date-picker hasDatepicker" readonly="true">
                    </div>

                    <div class="form-group">
                        <label for="task_prioritys">Priority:</label>
                        <select id="task_priority" class="form-control" onchange="changePriority(this)">
                            <option value="4" class="bg-primary" selected>Low</option>
                            <option value="3" class="bg-success">Normal</option>
                            <option value="2" class="bg-warning">High</option> 
                            <option value="1" class="bg-danger">Urgent</option>
                        </select>
                    </div>
                    
                    <div <?php if (isset($admin) && $admin['us_role_id'] == 8): ?> style="display:none;" <?php endif;?> class="form-group">
                        <label><?php echo lang('task_assignee') ?> <span class="text-danger">*</span>:</label>
                        <input type="text" id="select-to" class=""/>
                    </div>
                    
                    <div class="form-group" id="role_funcationlity_details"></div>
                    <div class="form-group">
                        <div class="checkbox"><label><input type="checkbox" name="send_mail" id="send_mail" value="1"><span class="ap_cont chk-box">Send email to assignee</span></label></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="addTask()" id="create_box_ok"><?php echo lang('create') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->


    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" id="view_task" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="view_task_box_title"><?php echo 'View Task' ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <h4 id="view_task_tittle"></h4>
                    </div>
                    <div class="form-group">
                        <p id="view_task_description"></p>
                    </div>
                    <div class="form-group">
                        <label class="default" for="view_task_due_date">Due date: <span id="view_task_due_date"></span></label>
                        
                    </div>

                    <div class="form-group">
                        <label class="default" for="view_task_priority">Priority: <span class="default" id="view_task_priority"></span></label>
                    </div>
                    
                    <div>
                        <label class="default" ><?php echo lang('task_assignee') ?>:</label>
                        <div class="selectize-control multi default" id="view_task_assigness"> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('close') ?></button>
                    <button type="button" class="btn btn-green" onclick="addComment()" id="create_box_ok"><?php echo 'Add comment';?></button>
                </div>
                
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->


    <?php //print_r($faculties);die;?>
<!-- <script type="text/javascript" src="<?php //echo assets_url() ?>js/selectize/js/standalone/selectize.min.js"></script> -->
    
<script>

var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                  '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';


/*var $select = $('#select-to').selectize({
    persist: false,
    persist: true,
    maxItems: 6,
    valueField: 'id',
    labelField: 'name',
    searchField: ['name'],
    options: JSON.parse(faculties),
    render: {
        item: function(item, escape) {
            return '<div>' +
                (item.us_image ? '<img class="img-circle" width="36" height="36" src="' + __userpath+escape(item.us_image) + ' "/>' : '') +
                (item.name ? '<span class="name"> ' + escape(item.name) + ' </span>' : '') +
                //(item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                (item.id ? '<input type="hidden" class="facultiesSelected" name="faculties" value="' + escape(item.id) + '"/>' : '') +
            '</div>';
        },
        option: function(item, escape) {
            var label = item.name || item.email;
            var caption = item.name ? item.email : null;
            var us_image = item.us_image ? item.us_image : null;
            return '<div>' +
            (us_image ? '<img class="img-circle" width="36" height="36" src="' + userpath+escape(us_image) + ' "/>' : '') +
                '<span class="label"> ' + escape(label) + '</span>' +
                (caption ? '<span class="caption">' + escape(caption) + 'sdfsdfdsfdsfds</span>' : '') +
            '</div>';
        }
    }
});*/

</script>