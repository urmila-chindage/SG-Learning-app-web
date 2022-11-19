<?php  include_once "header.php"; ?>

<section>
    <div class="my-profile-blocks">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="row">
                    <div class="col-md-12"><?php include_once('messages.php'); ?></div>
                    <div class="col-md-8 col-sm-12 col-xs-12 col-md-offset-2" id="mandatory_blocks" style="margin-top: 50px;">
                        <div class="myprofile-cards-wraper">
                            <?php if (!empty($mandatory_profile_fields)): ?>
                                <?php $block_html = '<div class="myprofile-card-body">' ?>
                                <?php $field_ids = array(); ?>
                                <?php foreach ($mandatory_profile_fields as $field): ?>
                                    <?php if(!in_array($field['id'], $excluded_user_profile_fields)): ?>
                                        <?php
                                        $field_ids[] = array('id' => $field['id'], 'field_mandatory' => $field['pf_mandatory'], 'field_name' => $field['pf_name']);
                                        $field_required = (($field['pf_mandatory']) ? ' <span class="field-required field-required">*</span> ' : '');
                                        $field_value = isset($user_profile_fields[$field['id']]) ? $user_profile_fields[$field['id']] : '';
                                        ?>
                                        <?php
                                        $block_html .= '<span class="table-wrap profile-fields">';
                                        $block_html .= '    <span class="table-cell-mail">' . $field['pf_label'] . $field_required . '</span>';
                                        $block_html .= '    <span class="table-cell-e-address">';
                                        $block_html .= '        <span class="change-date field_label_display" id="field_value_' . $field['id'] . '">' . $field_value . '</span>';
                                        //$block_html .= '        <input type="text" class="replace-text field_label_form keyword_for_auto_value prevent-blur" data-pf-id="' . $field['id'] . '" onKeyup="getAutoFieldsValue(event)" id="' . $field['pf_name'] . '" auto-suggestion-status="' . $field['pf_auto_suggestion'] . '" name="' . $field['pf_name'] . '" value="' . $field_value . '" placeholder="' . $field['pf_placeholder'] . '" >';
                                        if($field['pf_field_input_type'] == '2')
                                        {
                                            $block_dropdown = '';
                                            $block_options = explode(',', $field['pf_default_value']);
                                            if(!empty($block_options))
                                            {
                                                $block_dropdown .= '<select class="replace-text field_label_form keyword_for_auto_value prevent-blur"  data-pf-id="'.$field['id'].'" name="'.$field['pf_name'].'" id="'.$field['pf_name'].'">';
                                                $block_dropdown .= '<option value=""> Choose '.$field['pf_label'].'</option>';
                                                foreach($block_options as $b_option)
                                                {
                                                    $block_dropdown .= '<option '.(($b_option==$field_value)?'selected="selected"':'').' value="'.$b_option.'">'.$b_option.'</option>';
                                                }
                                                $block_dropdown .= '</select>';
                                            }
                                            $block_html .= $block_dropdown;
                                        }
                                        else
                                        {
                                            $block_html .= '        <input type="text" class="replace-text field_label_form keyword_for_auto_value prevent-blur" data-pf-id="'.$field['id'].'" onKeyup="getAutoFieldsValue(event)" id="' . $field['pf_name'] . '" auto-suggestion-status="' . $field['pf_auto_suggestion'] . '" name="' . $field['pf_name'] . '" value="' . $field_value . '" placeholder="' . $field['pf_placeholder'] . '" >';
                                        }
                                        $block_html .= '        <ul id ="fieldListId-' . $field['pf_name'] . '" class="prevent-blur field_values_list" style="display:none;list-style-type: none;">';
                                        $block_html .= '        </ul>';
                                        $block_html .= '    </span>';
                                        $block_html .= '</span>';
                                        ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php 
                            $block_html .= '<div class="text-center" style="margin-top: 25px;">';
                            $block_html .= '    <button onclick="saveBlockStep2(\''.base64_encode(json_encode($field_ids)).'\')" class="btn  btn-orange2" id="save_block" style="padding:15px;">Save and Continue</button>';
                            $block_html .= '</div>';
                            ?>
                                
                                <?php $block_html .= '</div>' ?>
                                <div class="myprofile-card-head">
                                    <span class="my-profile-about">Build Your Profile</span>
                                    <?php /* ?><span class="pensil-wrap" onclick="editBlockStep2()" id="edit_block">
                                        <img class="edit-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-pencil.svg">
                                    </span><?php */ ?>
                                    <span class="save-close-wrap" id="block_action">
                                        <?php /* ?><img class="edit-close" id="cancel_block" onclick="cancelEdit()" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-close.svg"><?php */ ?>
                                    </span>
                                </div><!--myprofile-card-head-->    
                                <?php echo $block_html; ?>
                            <?php endif; ?>
                        </div><!--myprofile-cards-wraper-->
                    </div><!--columns-->
                </div><!--row-->                
            </div><!--container-reduce-width-->
        </div><!--container container-altr-->  
    </div><!--my-profile-blocks-->	
</section>


<script  src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.matchHeight.js"></script>
<script>
    $(document).ready(function(){
        editBlockStep2();
    });
    /*for profile*/
    function editBlockStep2()
    {
        $('.field_label_display').hide();
        $('.field_label_form').show();
        $('#edit_block').hide();
        $('#block_action').show();
        $('.field-required').show();
    }


    function saveBlockStep2(field_ids)
    {
        if (field_ids != '')
        {
            var fields = $.parseJSON(atob(field_ids));
            var fieldValues = new Object;
            var errorMessage = '';
            var errorCount = 0;

            if (fields.length > 0)
            {
                for (var i = 0; i < fields.length; i++)
                {
                    fieldValues[fields[i]['field_name']] = $('#' + fields[i]['field_name']).val();
                    $('#' + fields[i]['field_name']).removeClass('error-fields');
                    if (fields[i]['field_mandatory'] == 1 && fieldValues[fields[i]['field_name']] == '')
                    {
                        $('#' + fields[i]['field_name']).addClass('error-fields');
                        errorCount++;
                    }
                }

                if (errorCount > 0)
                {
                    return false;
                }

                $('#save_block').text('Saving..');
                $.ajax({
                    url: site_url + '/dashboard/save_profile_values_step2',
                    type: "POST",
                    data: {"is_ajax": true, 'profile_values': JSON.stringify(fieldValues)},
                    success: function (response) {
                        $('#save_block').hide();
                        var redirect_url = site_url+"course/listing";
                        setTimeout(function() { window.location.href = redirect_url }, 500);
                    }
                });

            }
        }
    }
    /*End*/

    var site_url = '<?php echo site_url() ?>';
    var __timeOut = '';
    /*
     * To get the autofill values for the profile block dynamic fields
     * Created by : Neethu KP
     * Created at : 06/01/2017
     */
    function getAutoFieldsValue(e) {
        clearTimeout(__timeOut);
        __timeOut = setTimeout(function () {
            var AutosuggestionStatus = $(e.target).attr('auto-suggestion-status');
            $('.field_values_list').hide();
            if (AutosuggestionStatus == 1) {
                var userKeyword = $(e.target).val();
                var fieldValueId = $(e.target).attr('id');
                var field_id = $(e.target).attr('data-pf-id');
                var fieldListId = 'fieldListId-' + fieldValueId;
                var fieldHtml = '<li>Loading...</li>';
                $('#' + fieldListId).html(fieldHtml).show();
                var keyword = userKeyword.toLowerCase();
                $.ajax({
                    url: site_url + 'dashboard/get_fileds_value',
                    type: "POST",
                    data: {"is_ajax": true, "keyword": keyword, "field_id": field_id},
                    success: function (response) {
                        if (response) {
                            var data = $.parseJSON(response);
                            var fieldHtml = '';
                            $('#' + fieldListId).html(fieldHtml);
                            if (data['field_values'].length > 0)
                            {
                                for (var i = 0; i < data['field_values'].length; i++)
                                {
                                    //fieldHtml += '<li id="' + data['field_values'][i]['upf_field_id'] + '">' + data['field_values'][i]['upf_field_value'] + '</li>';
                                    fieldHtml += '<li id="' + data['field_values'][i] + '">' + data['field_values'][i] + '</li>';
                                }
                                $('#' + fieldListId).append(fieldHtml).show();

                            }
                        }
                    }
                });
            }
        }, 600);
    }

    /*
     * To place the selected value from the auto suggestion list
     * Created by : Neethu KP
     * Created at : 06/01/2017
     */
    $(document).on('click', '.field_values_list li', function () {

        var fieldText = $(this).text();
        var fieldListId = $(this).parent().attr('id');
        var fieldValueId = fieldListId.split('-');
        $('#' + fieldValueId[1]).val(fieldText);
        $('.field_values_list').hide();
    });

    $(document).on('click', 'body', function (e) {
        if ($(e.target).hasClass('prevent-blur')==false) {
            $('.field_values_list').html('');
        }
    });

</script>
<script type="text/javascript">
    $(".block-load-in").click(function (event) {
        event.preventDefault();
    });
</script>

<style>
    .error-fields{ border: 1px solid #a41f1f;}
    .field-required{ display: none;}
</style>

<?php include_once "footer.php"; ?>
