<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>IMPORT ERROR PREVIEW</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/error_preview_style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
</head>

<body style="margin:0;">
    <form action="<?php echo $action ?>" method="POST" id="preview_form">
        <textarea style="display:none;" name="preview_data" id="preview_data"></textarea>
        <div class="table-title">
            <span>Error Entry</span>
            <div class="import-option-btn">
                <input type="button" class="back-btn" name="back" onclick="location.href='<?php echo $excell['back_to_home']; ?>'" value="BACK">
                <input type="button" class="import-btn" name="import" onclick="exportInstitutes()" value="EXPORT">
                <input type="button" class="import-btn" name="import" onclick="importUsers()" value="SAVE">
            </div>
        </div>
        <div class="scroll-preview-table">
            <table class="inst-preview-table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr class="inst-upload-preview">
                        <th>#</th>
                        <?php foreach($excell['headers'] as $headers): ?>
                        <th><?php echo $headers ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody id="import_form_field">
                    <?php foreach($excell['content'] as $key => $content): ?>
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
                                    if(isset($content['defect_reason']) && array_key_exists($column, $content['defect_reason']))
                                    {
                                        $tool_tip_message = $content['defect_reason'][$column];

                                    }
                                    else
                                    {
                                        $tool_tip_message = '';
                                        switch($content['type'])
                                        {
                                            case "duplicate_data_row":
                                                $tool_tip_message  = 'Duplicate Entry !';
                                            break;
                                            case "invalid_data_row":
                                                $tool_tip_message  = 'Invalid Entry !';    
                                            break;
                                        }    
                                    }
                                    $tool_tip  = 'data-toggle="tooltip" data-original-title="'.$tool_tip_message.'"';    
                                }
                                ?>
                                <td <?php echo $class_type ?>>
                                <?php if(isset($excell['column_dropdown'][($key).'_'.$column])): ?>
                                    <?php echo $excell['column_dropdown'][($key).'_'.$column] ?>
                                <?php else: ?>
                                    <input <?php echo $tool_tip ?> autocomplete="off" type="text" name="content[<?php echo $key ?>][row][<?php echo $row_key ?>]" value="<?php echo $value ?>">
                                <?php endif; ?>
                                </td>
                                <?php $column++ ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
<?php 
// echo '<pre>'; print_r($excell['column_dropdown']);die;
$ins_objects        = array();
$ins_objects['key'] = 'institutes';
$callback           = 'institutes';
$institutes         = $this->memcache->get($ins_objects, $callback, array()); 
$institute_html     = '';
$branch_html        = '';
if(!empty($institutes))
{
    foreach($institutes as $institute)
    {
        $institute_html     .= '<option value ="'.$institute['ib_institute_code'].'">'.$institute['ib_name'].'</option>';
    }
}
$import_key = (isset($import_key))?$import_key:'';
?>
        <!-- scripts here -->
        <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
        <script>
            var __import_key = '<?php echo $import_key; ?>';
            $(document).ready(function(){
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
                var instituteHTML = '<?php echo $institute_html ?>';
                $('.all_institutes_selector').append(instituteHTML);
            });
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

            function exportInstitutes()
            {
                if(__import_key!='')
                {
                    location.href = webConfigs('admin_url')+'institutes/export_preview/'+btoa(__import_key);
                }
            }

        </script>
        <?php include_once "common_modals.php" ?>

</body>

</html>