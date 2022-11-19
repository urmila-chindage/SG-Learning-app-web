<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>IMPORT ERROR PREVIEW</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/error_preview_style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
</head>

<body style="margin:0;">
    <form action="<?php echo $action ?>" id="preview_form" method="POST">
    <input type="hidden" name="institute_id" value="<?php echo $excell['institute_id'] ?>">
    <textarea style="display:none;" name="preview_data" id="preview_data"></textarea>
        <div class="table-title">
            <span>Error Entry</span>
            <div class="import-option-btn">
                <input type="button" class="back-btn" name="back" onclick="location.href='<?php echo admin_url('user') ?>'" value="BACK">
                <input type="button" class="import-btn" onclick="exportUsers()" value="EXPORT">
                <input type="button" class="import-btn" name="import" onclick="importUsers()" value="SAVE">
            </div>
        </div>
        <table class="inst-preview-table" cellpadding="0" cellspacing="0">
            <thead>
                <?php if(sizeof($instructions)): ?>
                    <tr>
                        <th colspan="<?php echo (count($excell['headers'])+1) ?>">
                            <div class="import-instruction">
                                <ul>
                                    <?php foreach($instructions as $instruction): ?>
                                        <li><?php echo $instruction ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </th>
                    </tr>
                <?php endif; ?>
                <tr class="inst-upload-preview">
                <th>#</th>
                    <?php foreach($excell['headers'] as $headers): ?>
                    <th><?php echo $headers ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody id="import_form_field">
            <?php /* ?><?php foreach($excell['content'] as $key => $content): ?>
                    <tr class="<?php echo $content['type'] ?>">
                        <td><?php echo $content['row_number']+1 ?></td>
                        <?php $column = 0; ?>
                        <?php foreach($content['row'] as $row_key => $value): ?>
                            <?php 
                            $class_type = '';
                            $tool_tip = '';
                            if(in_array($column, $content['defect_columns']))
                            {
                                $class_type = 'class="invalid_data"';
                                switch($content['type'])
                                {
                                    case "duplicate_data_row":
                                        $tool_tip  = 'data-toggle="tooltip" data-original-title="Duplicate Entry !"';
                                    break;
                                    case "invalid_data_row":
                                        $tool_tip  = 'data-toggle="tooltip" data-original-title="Invalid Entry !"';    
                                    break;
                                }
                            }
                            ?>
                            <td <?php echo $class_type ?>>
                            <?php if(isset($excell['column_dropdown'][($key).'_'.$column])): ?>
                                <?php echo $excell['column_dropdown'][($key).'_'.$column] ?>
                            <?php else: ?>
                                <input <?php echo $tool_tip ?> type="text" name="content[<?php echo $key ?>][row][<?php echo $row_key ?>]" value="<?php echo $value ?>">
                            <?php endif; ?>
                            </td>
                            <?php $column++ ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?><?php */ ?>
            </tbody>
        </table>
    </form>
<?php 
$objects        = array();
$objects['key'] = 'insbtch'.$this->__loggedInUser['id'];
$callback       = 'institute_batches';
$batches        = $this->memcache->get($objects, $callback, array('institute_id' => $excell['institute_id'], 'select' => 'id, gp_name')); 

$objects              = array();
$objects['key']       = 'branches';
$callback             = 'branches';
$branches             = $this->memcache->get($objects, $callback); 


$batch_html           = '';
$branch_html          = '';
if(!empty($batches))
{
    foreach($batches as $b_obj)
    {
        $batch_html     .= '<option value ="'.$b_obj['gp_name'].'">'.$b_obj['gp_name'].'</option>';
    }
}

if(!empty($branches))
{
    foreach($branches as $br_obj)
    {
        $branch_html     .= '<option value ="'.$br_obj['branch_code'].'">'.$br_obj['branch_code'].'-'.$br_obj['branch_name'].'</option>';
    }
}

?>
        <!-- scripts here -->
        <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
        <script>
            var __import_key            = '<?php echo $key; ?>';
            var __previewObjects = $.parseJSON(atob('<?php echo base64_encode(json_encode($excell['content'])) ?>'));
            var __columnDropdown = $.parseJSON(atob('<?php echo base64_encode(json_encode($excell['column_dropdown'])) ?>'));
            $(document).ready(function(){
                // console.log(renderPreview());
                $('#import_form_field').html(renderPreview());
                $('[data-toggle="tooltip"]').tooltip();   
                <?php 
                if($excell['failed'] > 0)
                {
                    $message   = 'Successfully processed <b>'.($excell['inserted']+$excell['failed']).'</b> rows from uploaded file!<br />';
                    if($excell['inserted'] > 0)
                    {
                        $message  .= '<b>'.$excell['inserted'].'</b> rows loaded successfully<br />';
                    }
                    $message  .= 'Found invalid/duplicate entries in <b>'.$excell['failed'].'</b> rows. Please correct and save.';
                ?>
                    var messageObject = {
                        'body':'<?php echo $message ?>',
                        'button_yes':'OK', 
                        'prevent_button_no':true, 
                    };
                    callback_danger_modal(messageObject);
                <?php
                }
                ?>
                // var batchHTML = '<?php //echo $batch_html ?>';
                // $('.all_batches_selector').append(batchHTML);
                // $('.all_batches_selector').attr('data-toggle', 'tooltip');
                // $('.all_batches_selector').attr('data-original-title', 'Invalid Entry !');
                var branchHTML = '<?php echo $branch_html ?>';
                $('.all_branches_selector').append(branchHTML);
                $('.all_branches_selector').attr('data-toggle', 'tooltip');
                $('.all_branches_selector').attr('data-original-title', 'Invalid Entry !');
            });
            function inArray(needle, haystack) {
                var length = haystack.length;
                for (var i = 0; i < length; i++) {
                    if (haystack[i] == needle) return true;
                }
                return false;
            }

            function renderPreview() {
                var previewHtml = '';
                if( Object.keys(__previewObjects).length > 0 ) {
                    $.each(__previewObjects, function(key, content){
                        previewHtml += '<tr class="'+content['type']+'">';
                        previewHtml += '    <td>'+(content['row_number']+1)+'</td>';
                        var column = 0;
                        $.each(content['row'], function(row_key, value){
                            var class_type = '';
                            var tool_tip = '';
                            if(inArray(column, content['defect_columns']) == true) {
                                var class_type = 'class="invalid_data"';
                                switch(content['type'])
                                {
                                    case "duplicate_data_row":
                                    tool_tip  = 'data-toggle="tooltip" data-original-title="Duplicate Entry !"';
                                    break;
                                    case "invalid_data_row":
                                    tool_tip  = 'data-toggle="tooltip" data-original-title="Invalid Entry !"';    
                                    break;
                                }
                            }
                            previewHtml += '    <td '+class_type+'>';
                            if(typeof __columnDropdown[key+'_'+column] != 'undefined') {
                                previewHtml +=  __columnDropdown[key+'_'+column];
                            } else {
                                previewHtml += '        <input '+tool_tip+' type="text" name="content['+key+'][row]['+row_key+']" value="'+value+'">';
                            }
                            previewHtml += '    </td>';
                            column++;
                        });
                        previewHtml += '</tr>';
                    });
                }
                        // console.log(previewHtml);
                return previewHtml;
            }

            $(document).on('change', '.all_batches_selector', function(){
                var elementClass = $(this).attr('class').split(' ')[1];
                if($('.'+elementClass).length > 1) {
                    var messageObject = {
                    'body':'Identified <b>'+($('.'+elementClass).length-1)+'</b> more field with same value. Change that field with selected value?',
                    'button_yes':'YES', 
                    'button_no':'NO',
                    'continue_params':{'element_class':elementClass, 'element_value':$(this).val()},
                    };
                    callback_warning_modal(messageObject, changeOtherObject);
                }
            });
            function changeOtherObject(param) {
                $('.'+param.data.element_class).val(param.data.element_value);
                $('#common_message_advanced').modal('hide');
            }

            function importUsers() {
                $('body').hide();
                var importData = new Object();
                var fieldName = '';
                $( "input[name*='content'], select[name*='content']" ).each(function(){
                    fieldName = $(this).attr('name');
                    fieldName = fieldName.replace("content[", "");
                    fieldName = fieldName.replace("][row][", "_");
                    fieldName = fieldName.replace("]", "");
                    fieldName = fieldName.split("_");
                    if(typeof importData[fieldName[0]] == 'undefined') {
                        importData[fieldName[0]] = new Object();
                    }
                    importData[fieldName[0]][fieldName[1]] = $(this).val();
                });
                $('#preview_data').val(JSON.stringify(importData));
                $('#import_form_field').html('');
                $('#preview_form').submit();
            }

            function webConfigs(key)
            {
                return localStorage.getItem(key);
            }

            function exportUsers()
            {
                if(__import_key!='')
                {
                    location.href = webConfigs('admin_url')+'user/export_preview/'+btoa(__import_key);
                }
            }
        </script>
        <?php include_once "common_modals.php" ?>
</body>

</html>