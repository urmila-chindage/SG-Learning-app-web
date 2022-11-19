<div class="col-sm-12 course-cont-wrap contact-settings innercontent" id="profileFieldsSettings"> 
    <div class="buldr-header clearfix">
        <div class="pull-right rite-side">
            <!-- Header right side items with buttons -->
            <ul class="top-rite-materals">
                <li class="btn-add-section ui-dragable-btn"><a class="btn btn-blue" onclick="addBlock()" data-toggle="modal" data-target="#addblock">ADD BLOCK</a></li>
                <li><a class="btn btn-green" onclick="editField(0)">ADD FIELD</a></li>
            </ul>
        </div>
    </div>
    <div <?php /* ?>class="box"< ?php */ ?>>
    <?php //echo '<pre>'; print_r($profile_blocks); die; ?>
        <!-- sortable curriculum start -->
        <?php if(empty($profile_blocks)): ?>
        <p id="no_field_button" style="text-align:center;">Click the <b>"ADD FIELD"</b> button to create new field.</p>
        <?php endif; ?>
        <ul id="sortable" class="curriculum listing_profile_fields_li">
            <?php if(!empty($profile_blocks)): ?>
            <?php foreach($profile_blocks as $block): ?>
                <li class="section block_listing" id="block_<?php echo $block['id'] ?>" data-block-name="<?php echo $block['pb_name'] ?>">
                    <div class="section-title-holder">
                        <div class="drager">
                            <img src="<?php echo assets_url() ?>images/drager.png">
                        </div>
                        <span class="section-name" id="block_name_<?php echo $block['id'] ?>"> <?php echo ((strlen($block['pb_name'])>43)?(substr($block['pb_name'], 0, 40).'...'):$block['pb_name']) ?> </span>
                        <div class="btn-group section-control">
                            <span class="dropdown-tigger" data-toggle="dropdown">
                                <span class='label-text'>
                                    <i class="icon icon-down-arrow"></i>
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="javascript:void(0)" data-toggle="modal" onclick="renameBlock('<?php echo $block['id'] ?>')" data-target="#rename_block">Rename</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="deleteBlock('<?php echo base64_encode($block['pb_name']) ?>', '<?php echo $block['id'] ?>')">Delete</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <ul class="lecture-wrapper" id="block_field_<?php echo $block['id'] ?>">
                    <?php if(!empty($block['profile_fields'])): ?>
                    <?php foreach($block['profile_fields'] as $profile_field): ?>
                        <li id="field_<?php echo $profile_field['id'] ?>">
                            <div class="lecture-hold">
                                <div class="drager">
                                    <img src="<?php echo assets_url() ?>images/drager.png">
                                </div>
                                <a href="javascript:void(0)" class="lecture-innerclick">
                                    <span class="lecture-name"><?php echo ((strlen($profile_field['pf_label'])>53)?(substr($profile_field['pf_label'], 0, 50).'...'):$profile_field['pf_label']) ?></span>
                                </a>
                                <div class="btn-group lecture-control">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                        <span class='label-text'>
                                            <i class="icon icon-down-arrow"></i>
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a href="javascript:void(0)" onclick="editField('<?php echo $profile_field['id'] ?>')">Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteField('<?php echo base64_encode($profile_field['pf_label']) ?>', '<?php echo $profile_field['id'] ?>')">Delete</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <!-- sortable curriculum end -->
    </div>
</div>    

<style>
    .lecture-innerclick, .section-name { padding: 0 0 0 40px;}
    .field_edit_icon{ cursor: pointer;}
    .form-control.error-field {
        border: 1px solid #a40000;
    }
</style>
<script>
    var assets_url      = '<?php echo assets_url() ?>';
</script>
<script src="<?php echo assets_url() ?>js/dynamic_field.js"></script>
