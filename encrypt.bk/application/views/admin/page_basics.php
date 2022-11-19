<?php include_once 'header.php'; ?>
<style>
section.base-cont-top-heading.courses-tab { height: 47px; }
.courses-tab ol.nav li { border-bottom: unset !important; }
.page-content-editor .redactor-in{min-height: 200px}
.base-cont-top-heading.content-wrap{top: 125px;}
.course-container>section.content-wrap {height: unset;}
</style>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
<?php 
$GLOBALS['parent_id'] = $p_parent_id;
$GLOBALS['id']        = $id;
$GLOBALS['selected']  = 'selected="selected"';
$GLOBALS['checked']   = 'checked="checked"';
function page_tree($page_tree, $dash = '')
{
    $html =  '';
    if(!empty($page_tree))
    {
        foreach ($page_tree as $page)
        {
            $html .= ($page['id']!=$GLOBALS['id'])?'<option value="'.$page['id'].'" '.(($GLOBALS['parent_id']==$page['id'])?$GLOBALS['selected']:'').' >'.$dash.$page['p_title'].'</option>':'';
            if(!empty($page['children']))
            {
                $html.= page_tree($page['children'], $dash.'-');
            }
        }
    }
    return $html;
}
?>
<?php 
    //$dynamic_pages = isset($header_pages) ? $header_pages : menu_pages(array('type' => 'header'));
    //$footer_pages_array = menu_pages(array('type' => 'footer')); 
    //echo '<pre>';print_r($footer_pages_array);die;
?>
<section class="courses-tab base-cont-top-heading">
    <ol class="nav nav-tabs offa-tab">
        <li class="active">
            <a href="javascript:void(0)"> <?php echo lang('basic') ?></a>
            <span class="active-arrow"></span>
        </li>
        <li>
            <a href="<?php echo admin_url('page/seo/' . $id) ?>"> <?php echo lang('seo') ?></a>
            <span class="active-arrow"></span>
        </li>
    </ol>
</section>

<section class="content-wrap small-width base-cont-top-heading pad0">

    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
<script>

    var __menuAlreadyComnectedTo = '';

    function formValdate(){

        var error               = false;
        var errorMessage        = '';
        <?php //$footer_pages_array = menu_pages(array('type' => 'footer')); ?>
        if(!$('#p_title').val().trim()){
            error = true;
            errorMessage        += '<p>Page Title is required.</p>';
        }

        if($('#p_goto_external_url').is(':checked')){

            if(!$('#p_external_url').val().trim()){
                errorMessage    += '<p>External URL is required.</p>';
                error = true;
            }else{

                if(!validURL($('#p_external_url').val())){
                    errorMessage += '<p>Invalid External URL.</p>';
                    error = true;
                }
            }
        }else{

            var page_content    = $('#p_content').val().trim();
            console.log(page_content);
            if(!stripHtmlTags(page_content)){
                errorMessage   += '<p>Page Content is required.</p> ';
                error = true;
            }
        }
                           
        //var element         = $('#mm_connected_to').find('option:selected'); 
        //var connected_as    = element.attr("connected_as"); 
        //var connected_slug  = element.attr("connected_slug"); 
        //var connected_name  = element.attr("connected_as_name"); 
        //var page_name       = connected_name;

        if(__menuAlreadyComnectedTo != ''){
            //errorMessage   += ''+__menuAlreadyComnectedTo+'';
            //error = true;

            var messageObject   = {
                        'body': __menuAlreadyComnectedTo,
                        'button_yes': 'CONTINUE',
                        'button_no': 'CANCEL',
                        
                    };
                callback_warning_modal(messageObject, confirmPageAssign);
                return false;
        }

        /*if( connected_as && connected_as != '2' )
        {
            if(error){
                $('.alert').hide();
                $('#js_error_container').show().html(`
                                <a class="close" data-dismiss="alerts" onClick="$('#js_error_container').hide()">×</a>
                                        ${errorMessage}
                                `).addClass('alert-danger');
                $(window).scrollTop(0);
                return false;
            }else{
                var menu_name           = element.html()
                if( connected_as == '0' ){
                    var message         = 'Menu '+menu_name+' is already connected to the page '+page_name+' ';
                }else if( connected_as == '1' ){
                    var message         = 'Menu '+menu_name+' is already connected to the external url '+connected_slug+' ';
                }
                    var messageObject   = {
                        'body': message,
                        'button_yes': 'CONTINUE',
                        'button_no': 'CANCEL',
                        
                    };
                callback_warning_modal(messageObject, confirmPageAssign);
                return false;
            }
            // event.preventDefault();
        }*/
        
            if(error){
                $('.alert').hide();
                $('#js_error_container').show().html(`
                                <a class="close" data-dismiss="alerts" onClick="$('#js_error_container').hide()">×</a>
                                        ${errorMessage}
                                `).addClass('alert-danger');
                $(window).scrollTop(0);
                return false;
            }
        return true;
    }
    function confirmPageAssign()
    {
        __menuAlreadyComnectedTo = '';
        $('#page_basic_form').submit();
    }
</script>
    <div class="left-wrap col-sm-12">
        <div class="container-fluid course-create-wrap">
            <form id="page_basic_form" onSubmit="return formValdate()" class="form-horizontal" action="<?php echo admin_url('page/basics/'.$id) ?>" method="POST">
                <div class="row-fluid course-cont">
                    <div class="form-horizontal" id="page_form">
                        <?php include_once('messages.php');?>
                        <div class="col-sm-4">   
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <?php echo lang('page_title') ?> * : 
                                    <input type="text" class="form-control" maxlength="80" placeholder="eg: Mathematical Calculations" name="p_title" id="p_title" value="<?php echo htmlentities($p_title) ?>" />
                                </div>
                            </div>
                                
                            <div class="form-group">    
                                <div class="col-sm-5">
                                    <label class="control-label">
                                        <input type="checkbox" value="1" name="p_goto_external_url" id="p_goto_external_url" <?php echo ($p_goto_external_url == '1')?$GLOBALS['checked']:'';  ?>>
                                        <?php echo lang('external_url') ?>
                                    </label>
                                </div>
                                <div class="col-sm-7">
                                    <label class="control-label">
                                      <input type="checkbox" value="1" name="p_new_window" id="p_new_window" <?php echo ($p_new_window == '1')?$GLOBALS['checked']:'';  ?>>
                                       <?php echo lang('new_window') ?>
                                    </label>
                                </div>
                            </div>

                            <?php 
                                $to_inner_page     = 'style="display:'.(($p_goto_external_url == '1')?'none':'block').'"';
                                $to_external_page  = 'style="display:'.(($p_goto_external_url != '1')?'none':'block').'"';
                                $external_url      = 'display:'.(($p_goto_external_url != '1')?'none':'block').'';
                                $to_category_page  = 'style="display:'.((isset($p_show_page_in) && $p_show_page_in != '4')?'none':'block').'"';
                                $is_external_url  = 'style="display:'.((isset($p_goto_external_url) && $p_goto_external_url == '1')?'none':'block').'"';
                                
                            ?>
                            <div class="form-group external_url_wrapper" <?php echo $to_external_page ?> >
                                <div class="col-sm-12">
                                    <?php echo lang('external_url') ?> * : 
                                    <input type="text" id="p_external_url" data-validation="url" name="p_external_url" aria-describedby="basic-addon1" placeholder="https://www.google.com/" class="form-control" value="<?php echo $p_external_url;?>">
                                </div>
                            </div>
                            <style>
                                .optionGroup {
                                    font-weight: bold;
                                    font-style: italic;
                                }
                            </style>
                            <div class="form-group" >    
                                <div class="col-sm-6" id=""  >
                                    <?php echo lang('connected_to') ?>:
                                    <select class="form-control" name="mm_connected_to" id="mm_connected_to" >
                                        <option value="" ><?php echo lang('select') ?></option>
                                        <?php if( isset( $menus['header'] ) && !empty( $menus['header'] ) ): ?>
                                            <optgroup label="Headers">
                                                <?php foreach( $menus['header']['parent'] as $header ): ?>
                                                        <?php
                                                            $connected_as       = 2;
                                                            $connected_slug     = "";
                                                            if( $header['mm_connected_as_external'] == 0 &&  $header['mm_connected_as_external'] != null)
                                                            {
                                                                $connected_as   = 0;
                                                                $connected_slug = $header['mm_item_connected_slug'];
                                                            }
                                                            else if( $header['mm_connected_as_external'] == 1 )
                                                            {
                                                                $connected_as   = 1;
                                                                $connected_slug = $header['mm_external_url'];
                                                            }
                                                            if( $header['mm_item_connected_id'] == $id){
                                                                $connected_as   = 2;
                                                            }
                                                        ?>
                                                    <option class="optionGroup" mm_connected_id = <?php echo $header['mm_item_connected_id']; ?>  connected_as="<?php echo $connected_as; ?>" connected_slug="<?php echo $connected_slug; ?>"  value="<?php echo $header['id'] ?>" <?php echo ($p_connected_menu == $header['id'])?$GLOBALS['selected']:'';  ?>><?php echo $header['mm_name']; ?></option>

                                                    
                                                    <?php if(isset($header['child']) && count($header['child']) > 0):?>
                                    
                                                        <?php foreach($header['child'] as $child): ?>
                                                        <?php 
                                                            if( $child['mm_connected_as_external'] == 0 &&  $child['mm_connected_as_external'] != null)
                                                            {
                                                                $connected_as   = 0;
                                                                $connected_slug = $child['mm_item_connected_slug'];
                                                            }
                                                            else if( $child['mm_connected_as_external'] == 1 )
                                                            {
                                                                $connected_as   = 1;
                                                                $connected_slug = $child['mm_external_url'];
                                                            }
                                                            if( $child['mm_item_connected_id'] == $id){
                                                                $connected_as   = 2;
                                                            }
                                                        ?>
                                                    <option mm_connected_id = <?php echo $child['mm_item_connected_id']; ?>  connected_as="<?php echo $connected_as; ?>" connected_slug="<?php echo $connected_slug; ?>"  value="<?php echo $child['id'] ?>" <?php echo ($p_connected_menu == $child['id'])?$GLOBALS['selected']:'';  ?>>&nbsp;&nbsp;&nbsp;<?php echo $child['mm_name']; ?></option>

                                                        <?php endforeach ?>
                                                    <?php endif ?>

                                                <?php endforeach ?>
                                            </optgroup>
                                        <?php endif ?>
                                        <?php if( isset( $menus['footer'] ) && !empty( $menus['footer'] ) ): ?>
                                            <optgroup label="Footers">
                                                <?php foreach( $menus['footer'] as $footer ): ?>
                                                <?php
                                                        $connected_as       = 2;
                                                        $connected_slug     = "";
                                                        if( $footer['mm_connected_as_external'] == 0 )
                                                        {
                                                            $connected_as   = 0;
                                                            $connected_slug = $footer['mm_item_connected_slug'];
                                                        }
                                                        else if( $footer['mm_connected_as_external'] == 0 )
                                                        {
                                                            $connected_as   = 1;
                                                            $connected_slug = $footer['mm_external_url'];
                                                        }
                                                        if( $footer['mm_item_connected_id'] == $id){
                                                            $connected_as   = 2;
                                                        }
                                                    ?>
                                                    <!-- mm_item_connected_id -->
                                                    <option class="optionGroup" mm_connected_id = <?php echo $footer['mm_item_connected_id']; ?> connected_as="<?php echo $connected_as; ?>" connected_slug="<?php echo $connected_slug; ?>"  value="<?php echo $footer['id'] ?>" <?php echo ($p_connected_menu == $footer['id'])?$GLOBALS['selected']:'';  ?>><?php echo $footer['mm_name']; ?></option>
                                                <?php endforeach ?>
                                            </optgroup>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-sm-3 external_category_wrapper" <?php echo $to_category_page ?>>
                                    <?php echo lang('category') ?>:
                                    <select class="form-control" name="p_category" id="p_category"> 
                                        <option value="0">Select Category</option>
                                        <?php foreach ($categories as $category) : 
                                            $selected = ($category['id'] == $p_category)?'selected':'';
                                            ?>
                                        <option value="<?php echo $category['id'] ?>" <?php echo $selected ?>><?php echo $category['ct_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-3" id="page_parent_id" style="display:none;">
                                    <?php echo lang('parent_menu') ?>:
                                    <select class="form-control" id="p_parent_id" name="p_parent_id">
                                        <option value=""><?php echo lang('choose_parent_menu') ?></option>
                                        <?php echo page_tree($page_tree, '');  ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <?php echo lang('status') ?>:
                                    <select class="form-control" name="p_status" id="p_status"> 
                                        <option value="1" <?php echo ($p_status == 1)?$GLOBALS['selected']:'';  ?>><?php echo lang('public') ?></option>
                                        d"                <option value="0" <?php echo ($p_status == 0)?$GLOBALS['selected']:'';  ?>><?php echo lang('private') ?></option>
                                    </select>
                                </div>
                            </div>

                            <!-- <div class="form-group">                          
                                <div class="col-sm-6">
                                    <?php //echo lang('page_position') ?> : 
                                    <input type="number" id="p_position" data-validation-allowing="range[1;1999999999999500]" data-validation-error-msg-number="<?php //echo lang('enter_range_error') ?>" data-validation="number" name="p_position" placeholder="eg: 13234" class="form-control" value="<?php //echo $p_position ?>">
                                </div>
                            </div> -->
                                
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group internal_page_wrapper page-content-editor" <?php echo $to_inner_page ?>>
                                <div class="col-sm-12">
                                    <?php echo lang('page_content') ?> * : 
                                    <textarea class="form-control" rows="10" name="p_content" id="p_content" ><?php echo $p_content ?></textarea>
                                </div>
                            </div>

                            <!-- page preview frame -->
                            <div style="height: calc(100vh - 240px); border: 2px solid #e8e8e8 !important; <?php echo $external_url;?>" class="page-preview-container" <?php echo $to_external_page;?> id="externaliframe" >
                                <iframe id="externaliframelink" frameborder="0" class="" src="<?php echo $p_external_url;?>"></iframe>
                            </div>
                            <!-- page preview frame ends -->

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-sm-12" style="margin-top: 40px;">
                            <input type="submit" id="save_page_details" class="pull-right btn btn-green marg10" value="SAVE">
                        </div>
                   </div>
                
            </form>
        </div>
    </div>
</section>
<!-- JS -->
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js"></script>
<script>
    var __page_id = '<?php echo $id ?>';
</script>
<script src="<?php echo assets_url() ?>js/page_settings.js"></script>
<?php include_once 'footer.php'; ?>
<script>
    $('#p_goto_external_url').on('change',function(){
        if($(this).prop("checked") == true){
            $('#connected_to').css('display','none');
        }
        else{
            $('#connected_to').css('display','block');
        }
    });


    $('#mm_connected_to').on('change',function(){

        __menuAlreadyComnectedTo            = '';

        var connecting_menu_id = $(this).val();

            $.ajax({
                url: admin_url+'page/pageName',
                type: "POST",
                data:{
                    "is_ajax":true, 
                    "connecting_menu_id" : connecting_menu_id, 
                    "connecting_page_id" : __page_id
                    },
                success: function(response) {
                    var data        = $.parseJSON(response);
                    if( data )
                    {
                        __menuAlreadyComnectedTo = 'The selected menu already connected to the page <b>'+data.p_title+'</b></br>';
                    }
                } 
            });
    });
</script>