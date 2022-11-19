<div class="col-sm-12 course-cont-wrap contact-settings innercontent" id="course_category">
<h3 class="text-center social-heading">Course category Management</h3>
    <div class="buldr-header clearfix">
        <div class="pull-right rite-side">
            <!-- Header right side items with buttons -->
            <ul class="top-rite-materals">
                <li><a class="btn btn-green" onclick="editCategory(0)">ADD CATEGORY</a></li>
            </ul>
        </div>
    </div>
    <div <?php /* ?>class="box"<?php */ ?>>
        <!-- sortable curriculum start -->
        <ul class="curriculum">
            <?php if(!empty($course_categories)): ?>
                <li class="section">
                    <ul class="lecture-wrapper" id="category_manage_wrapper">
                    <?php foreach($course_categories as $course_category): ?>
                        <li id="category_<?php echo $course_category['id'] ?>">
                            <div class="lecture-hold question-category-lecturehold">
                                <div class="lecture-counter"></div>
                                <?php /* ?><div class="drager">
                                    <img src="<?php echo assets_url() ?>images/drager.png">
                                </div><?php */ ?>
                                <a href="javascript:void(0)" class="lecture-innerclick category-innerclick">
                                    <span class="lecture-name question-category-lecturename"><?php echo ((strlen($course_category['ct_name'])>53)?(strip_tags(substr($course_category['ct_name'], 0, 50)).'...'):strip_tags($course_category['ct_name'])) ?></span>
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
                                            <a href="javascript:void(0)" onclick="editCategory('<?php echo $course_category['id'] ?>')">Edit</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory('<?php echo base64_encode($course_category['ct_name']) ?>', '<?php echo $course_category['id'] ?>')">Migrate</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteCategory('<?php echo base64_encode($course_category['ct_name']) ?>', '<?php echo $course_category['id'] ?>')">Delete</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
        <!-- sortable curriculum end -->
    </div>
</div>    

<style>
    .category-innerclick{ padding: 0 0 0 1px  !important;}
    .field_edit_icon{ cursor: pointer;}
    .form-control.error-field {
        border: 1px solid #a40000;
    }
 #category_manage_wrapper li .lecture-counter::before {
    content: counter(subsection, decimal) " ";
    counter-increment: subsection;
}

 #category_manage_wrapper li .lecture-counter {
    float: left;
    padding: 0 0px 0 27px;
    margin-right: 14px;
    border-right: 1px solid rgba(167,170,174,.65);
    background: rgba(232,232,232,.28);
    max-width: 69px;
    width: 100%;
}
</style>
<script>
    var assets_url      = '<?php echo assets_url() ?>';
</script>


<script src="<?php echo assets_url() ?>js/category_listing.js"></script>
