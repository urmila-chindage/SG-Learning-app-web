<?php include_once 'header.php';?>

         <style>
            .width-auto {width: auto !important;}
            .dropdown-menu.white {height: 209px;overflow: auto;}
            .dropdown-menu.white.multi-drop{padding-left:15px;}
            .rTable.content-nav-tbl .rTableRow > .rTableCell label{ padding: 0 0 13px;}
            .load-reports{width: 100px;display: inline-block;left: 27px;position: absolute;top: 60px;}

               /* Menu Manager */
               .menu-manager{
                  position: relative;
                  top: 80px;
                  padding: 25px;
               }
               .menu-manager .section-title-holder{
                  background: #fff;
                  padding: 5px 25px;
                  line-height: 30px;
                  font-size: 15px;
                  font-weight: 600;
                  border: 0px;
                  box-shadow: 0px 0px 20px #ebebeb;
               }
               .menu-container{
                  border-radius: 4px;
                  overflow: hidden;
               }
               .menu-manager ul.menu-wrapper{
                  padding: 10px 25px !important;
                  background: #fff;
                  min-height: calc(100vh - 165px);
                  max-height: calc(100vh - 165px);
                  overflow-y: auto;
                  border-radius: 4px;
               }
               .menu-manager .left-menu-container{
                  padding-left: 0px;
                  padding-right: 12px;
               }
               .menu-manager .right-menu-container{
                  padding-left: 12px;
                  padding-right: 0px;
               }
               .menu-manager .menu-ul>li{border: 0px;box-shadow: none;}
               /*.menu-manager .menu-wrapper{padding: 10px 25px;}*/
               .menu-manager .menu-list{
                  /*background: #fff !important;
                  margin: 7px 0px; */
                  margin: 0px;
                  position: relative;
               }
               .menu-manager .menu-item{
                  border: 1px solid #efefef;
                  border-radius: 3px;
                  padding: 8px 0px;
                  display: flex;
                  background: #fff;
               }
               .dragger-icon-holder{
                  width: 38px;
                  cursor: move;
                  position: relative;
               }
               .menu-manager .dragger-icon{
                  display: inline-block;
                  width: 20px;
                  height: 25px;
                  word-break: break-word;
                  line-height: 7px;
                  color: #d4d4d4;
                  font-size: 25px;
                  font-weight: 700;
                  position: absolute;
                  left: 10px;
                  top: -7px;
                  opacity: 1;
                  cursor: move;
               }
               .menu-title{
                  padding-left: 35px !important;
                  font-size: 14px;
                  font-weight: 500;
               }
               .menu-list-child{padding-left: 35px;/*margin-bottom: 15px;*/margin-bottom: 0;}
               .border-blue{border-left: 4px solid #01acf1 !important;}
               .border-orange{border-left: 4px solid #f36621 !important;}
               .border-red{border-left: 4px solid #f11b27 !important;}
               .border-green{border-left: 4px solid #8ec63f !important;}
               .border-pink{border-left: 4px solid #f06eac !important;}
               .border-darkblue{border-left: 4px solid #448cc7 !important;}
               .menu-list-child .menu-list{ margin:7px 0px !important;}
               .section-highlight {height: 41px !important;margin-bottom: 7px !important;}
               .menu-list-child .menu-list:last-child{ margin-bottom: 15px !important;}
               .menu-list-child{ min-height: 6px !important;}
               /* Menu Manager */
         </style>
         
         <?php
            $border_colors = array(
                                  0 => 'border-blue',
                                  1 => 'border-orange',
                                  2 => 'border-red',
                                  3 => 'border-green',
                                  4 => 'border-pink',
                                  5 => 'border-darkblue',
                                  6 => 'border-pink',
                                 );
         ?>

         <!-- Menu Manager -->
         <div class="menu-manager">
            <div><a style="margin-bottom : 1% !important" class="btn btn-success pull-right create_menu">CREATE NEW MENU</a></div>
            <div class="col-md-12 pad0">
               <div class="col-md-6 left-menu-container">
                  <div class="menu-container">
                        <ul class="menu-ul">
                           <li class="section">
                              <div class="section-title-holder menu-header">
                                 <span class="section-name"> Header </span>
                              </div>
                              <ul class="menu-wrapper header-sortables" >
                                 <?php if ( !empty($headers) ): ?>
                                    <?php foreach ( $headers as $parents ): ?>
                                       <li class="menu-list section" id="section_wrapper_<?php echo $parents['id'] ?>" >
                                          <div class="menu-item justify-between <?php echo $border_colors[$parents['id'] % 6] ?>">
                                             <div>
                                                <div class="dragger-icon-holder drager">
                                                   <span class="dragger-icon">......</span>
                                                </div>
                                                <div class="menu-title">
                                                   <a href="javascript:void(0)" section_menu_name = <?php echo $parents['mm_name'];  ?>  id="section_name_<?php echo $parents['id'] ?>"> <?php echo $parents['mm_name']  ?></a>
                                                </div>
                                             </div>
                                             <div>
                                                <div class="btn-group lecture-control" style="margin:0px;">
                                                   <span class="dropdown-tigger" data-toggle="dropdown">
                                                            <span class='label-text'>
                                                            <i class="icon icon-down-arrow"></i>
                                                      </span>
                                                   </span>
                                                   <ul class="dropdown-menu pull-right" role="menu" id="<?php echo $parents['id'] ?>">
                                                      <li class = "add_child">
                                                         <a href="javascript:void(0)"><?php echo 'Add child' ?></a>
                                                      </li>
                                                      <li onclick="parent_edit('header', <?php echo $parents['id'] ?> )" >
                                                         <a href="javascript:void(0)"><?php echo 'Edit' ?></a>
                                                      </li>
                                                      <li onclick="menuDelete(<?php echo $parents['id'] ?>, 'parent')">
                                                         <a href="javascript:void(0)"><?php echo 'Delete' ?></a>
                                                      </li>
                                                            
                                                   </ul>
                                                </div>
                                             </div>
                                          </div>

                                          <ul class="menu-list-child  lecture-wrapper" parent_id=<?php  echo $parents['id'] ?> id="section_lecture_<?php  echo $parents['id'] ?>">
                                             <?php if ( isset($childs[$parents['id']]) && !empty($childs[$parents['id']])): ?>
                                                <?php foreach ($childs[$parents['id']] as $key => $child): ?>
                                                
                                                   <li class="menu-list child_<?php echo $parents['id'];?>" id="lecture_id_<?php echo $child['id']; ?>">
                                                   
                                                      <div class="menu-item justify-between <?php echo $border_colors[$child['id'] % 6] ?>">
                                                         <div>
                                                            <div class="dragger-icon-holder drager">
                                                               <span class="dragger-icon">......</span>
                                                            </div>
                                                            <div class="menu-title">
                                                               <a href="javascript:void(0)" child_menu_name=<?php echo $child['mm_name']; ?> id="childname_<?php echo $child['id'];  ?>"  ><?php echo $child['mm_name']; ?></a>
                                                            </div>
                                                         </div>

                                                         <div>
                                                            <div class="btn-group lecture-control" style="margin:0px;">
                                                               <span class="dropdown-tigger" data-toggle="dropdown">
                                                                        <span class='label-text'>
                                                                        <i class="icon icon-down-arrow"></i>
                                                                  </span>
                                                               </span>
                                                               <ul class="dropdown-menu pull-right" role="menu" id="course_action_<?php echo "F" ?>">
                                                               
                                                                  <li onclick = "child_edit(<?php echo $child['id'] ?>,<?php echo $parents['id'] ?>)">
                                                                     <a href="javascript:void(0)"><?php echo 'Edit' ?></a>
                                                                  </li>
                                                                  <li  onclick="menuDelete(<?php echo $child['id'] ?>, 'child' )">
                                                                     <a href="javascript:void(0)"><?php echo 'Delete' ?></a>
                                                                  </li>
                                                                        
                                                               </ul>
                                                            </div>
                                                         </div>
                                                         
                                                      </div>
                                                   </li>
                                                <?php endforeach;?>
                                                
                                                
                                             <?php endif;?>
                                          </ul>
                                       </li>
                                    <?php endforeach;?>
                                    
                                 <?php endif;?>
                                 
                              </ul>
                              <!-- lecture-wrapper end -->
                           </li>
                           <!-- Section end -->
                        </ul>
                        <!-- sortable curriculum end -->
                     </div>
                  </div>
               <div class="col-md-6 right-menu-container">
                  <div class="menu-container">
                     <ul class="menu-ul">
                        <li class="section">
                           <div class="section-title-holder menu-header">
                              <span class="section-name"> Footer </span>
                           </div>
                           <ul class="menu-wrapper footer-sortable">

                              <?php if ( !empty($footers) ): ?>
                                 <?php foreach ( $footers as $parents ): ?>
                                    <li class="menu-list section" id="section_wrapper_<?php echo $parents['id'] ?>" >
                                       <div class="menu-item justify-between <?php echo $border_colors[$parents['id'] % 6] ?>">
                                          <div>
                                             <div class="dragger-icon-holder drager">
                                                <span class="dragger-icon">......</span>
                                             </div>
                                             <div class="menu-title">
                                                <a href="javascript:void(0)" section_menu_name=<?php echo $parents['mm_name']  ?> id="section_name_<?php echo $parents['id'] ?>"> <?php echo $parents['mm_name']  ?></a>
                                             </div>
                                          </div>
                                          <div>
                                             <div class="btn-group lecture-control" style="margin:0px;">
                                                <span class="dropdown-tigger" data-toggle="dropdown">
                                                         <span class='label-text'>
                                                         <i class="icon icon-down-arrow"></i>
                                                   </span>
                                                </span>
                                                <ul class="dropdown-menu pull-right" role="menu" id="course_action_<?php echo "F" ?>">
                                                   <li onclick="parent_edit('footer', <?php echo $parents['id'] ?>)">
                                                      <a href="javascript:void(0)"><?php echo 'Edit' ?></a>
                                                   </li>
                                                   <li onclick="menuDelete(<?php echo $parents['id'] ?>, 'parent' )">
                                                      <a href="javascript:void(0)"><?php echo 'Delete' ?></a>
                                                   </li>
                                                            
                                                </ul>
                                             </div>
                                          </div>
                                       </div>
                                       <ul class="menu-list-child">
                                       </ul>
                                    </li>
                                 <?php endforeach;?>
                              <?php endif;?>
                             
                           </ul>
                           <!-- lecture-wrapper end -->
                        </li>
                        <!-- Section end -->
                     </ul>
                     <!-- sortable curriculum end -->
                  </div>
               </div>
            </div>
         </div>
      
      <script>



function updatePageSectionPositon(position, selector, serielezing_class){
    var current_position = parseInt(position+1);
    var section_id       = selector.split('_');
        section_id       = section_id[2];
    $.ajax({
        url: admin_url+'Menu_sorted/update_page_section_position',
        type: "POST",
        data:{ "is_ajax":true, 'section_id':section_id, 'position':position, 'structure':$('.'+serielezing_class).sortable('serialize')},
        success: function(response) {
        }
    });    
}

function updatePageLecturePosition(section_id, items){
    $.ajax({
        url: admin_url+'Menu_sorted/update_page_lecture_position',
        type: "POST",
        data:{ "is_ajax":true, 'section_id':section_id, 'structure':items},
        success: function() {
            
        }
    });  
}

      </script>
      <script type="text/javascript" src="<?php echo assets_url() ?>js/jquery-ui.min.js"></script>
      <script type="text/javascript" src="<?php echo assets_url() ?>js/pages_sort.js"></script>

      <!-- menu modal -->
      
      
      <script>
         $('.create_menu').click(function(){
            $('#menu_form').html("");
            $('#add_menu_btn').val("CREATE");
            $('#menu_manager_modal .modal-title').html("ADD NEW MENU");
            $('#add_menu_form')[0].reset();
            $('#parent_menus_dropdown option:selected').removeAttr('selected');
            $('#menu_show_in option:selected').removeAttr('selected');
            $('.menu_parent_div').css('display','block');
            $('#menu_manager_modal').modal('show');
         });
      </script>
      <?php include_once 'footer.php'; ?>
      
      <div class="modal fade" data-backdrop="static" data-keyboard="false" id="menu_manager_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <form class="" id="add_menu_form" name="Form" onsubmit="return validateForm();"   method="post" action="<?php echo admin_url('Menu_sorted/saveMenu/'); ?>" >
               <div class="modal-content" id="">
                  <div class="modal-header">
                     <button  onClick="showParentHidden($('#parentId').val())" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                     <h4 class="modal-title" id="">CREATE NEW MENU</h4>
                  </div>
                  <div class="modal-body ">
                  <span id="menu_form"></span>
                     <div class="form-group">
                        <label for="usr">Name *:</label>
                        <input type="text" class="form-control" id="menu_name" name="menu_name" placeholder = "eg : About Us">
                     </div>
                     <div class="form-group menu_show_in_div">
                        <label for="pwd">Show In *:</label>
                        <select class="form-control" id="menu_show_in" onchange="menuShowIn()" name="menu_show_in">
                           <option value="">Choose position</option>
                           <option value="1">Header</option>
                           <option value="2">Footer</option>
                        </select>
                     </div>
                     <div class="form-group menu_parent_div">
                        <label for="pwd">Parent:</label>
                        <select class="form-control" id="parent_menus_dropdown" name="menu_parent">
                        <option value="0">Choose parent menu</option>
                        <?php if ( !empty($headers) ): ?>
                           <?php foreach ( $headers as $parents ): ?>
                              <option value="<?php echo $parents['id'] ?>"><?php echo $parents['mm_name'] ?></option>
                           <?php endforeach;?>
                         <?php endif;?>
                        </select>
                     </div>
                     <input type="hidden" value="page" name="menu_item_type">
                     <input type="hidden" value="page" id="parentId">
                     <input type="hidden" value="page" id="menuType">
                     
                  </div>
                  <div class="modal-footer">
                     <a type="button" class="btn btn-red " data-dismiss="modal" onClick="showParentHidden($('#parentId').val())">CANCEL</a>
                     <input class="btn btn-green" id="add_menu_btn" type="submit" value="CREATE">
                  </div>
               </div>
            </form>
        </div>
    </div>


    <script>
    var form_url              = "<?php echo admin_url('Menu_sorted/saveMenu/'); ?>";
     function validateForm()
    {
        $('#add_menu_btn').attr('disabled','true');
        var menu_name         = $('#menu_name').val();
        var menu_show_in      = $('#menu_show_in').val();
        var errorCount        = 0;
        var errorMessage      = '';
        
        if( menu_name == '' || menu_name == undefined ){
            errorMessage      += 'Please enter menu name<br />';
            errorCount++;
        }
        
        var parentId          =  $('#parentId').val();
        var selectedParent    =  $('#parent_menus_dropdown').val();
        var menuType          =  $('#menuType').val();

        if(menuType == 'parent' && selectedParent != '0'){
            if($('.child_'+parentId).length > 0){
               errorMessage      += 'There are submenus behind this parent menu, please unassign them and try again!<br />';
               errorCount++;
            }
        }
        //console.log($('.child_'+parentId).length);return false;

        if(menu_show_in == '' || menu_show_in == undefined ){
            errorMessage      += 'Please select a menu position<br />';
            errorCount++;
        }
       if(errorCount > 0){
         $('#add_menu_btn').attr('disabled', false);
            $('#menu_form').html(renderPopUpMessage('error', errorMessage));
            event.preventDefault();
       }else{

           return true;
       }
    }
   function menuShowIn(){
      var show_in = $('#menu_show_in').val();
      if( show_in == 2 ){
          $('.menu_parent_div').css('display','none');
      }else{
         $('.menu_parent_div').css('display','block');

      }
   }
   $('.add_child').click(function(){
      $('#add_menu_btn').val("CREATE");
      $('#menu_manager_modal .modal-title').html("ADD NEW MENU");
      $('#parent_menus_dropdown option:selected').removeAttr('selected');
      $('#menu_show_in option:selected').removeAttr('selected');
      $('#menu_name').val("");
      var parent_id    = $(this).parent('ul').attr('id');
      var show_in_id   = "1";
      $('.menu_parent_div').css('display','block');
      $("#menu_show_in option[value='"+show_in_id+"']").attr('selected','true');
      $("#parent_menus_dropdown option[value='"+parent_id+"']").attr('selected','true');
      $('#menu_manager_modal').modal('show');
   });

   function parent_edit(show_in, id){
      //console.log($('.child_'+id).length);
      $('#menu_form').html("");
      form_url               = "<?php echo admin_url(); ?>Menu_sorted/saveMenu/"+id;
      var name               = $('#section_name_'+id).html();
      $('#menu_manager_modal .modal-title').html("UPDATE MENU");
      $('#menu_show_in option:selected').removeAttr('selected');
      $('#add_menu_btn').val("UPDATE");
      $('#add_menu_form').attr('action',form_url);
      
      if(show_in == 'header'){
         $('.menu_parent_div').css('display','block');
          var show_in_id     = 1;
      }else{
         $('.menu_parent_div').css('display','none');
         var show_in_id      = 2;
      }

      $('#parentId').val(id);
      $('#menuType').val('parent');
      $("#parent_menus_dropdown option[value='" + id + "']").hide();
      $('#menu_name').val(name);
      $("#menu_show_in option[value='"+show_in_id+"']").attr('selected','true');
      $('#menu_manager_modal').modal('show');
   }

   function showParentHidden(opId){
      $("#parent_menus_dropdown option[value='" + opId + "']").show();
      $('#parent_menus_dropdown').prop('selectedIndex',0);
   }

   function child_edit( id, parent_id){
      $('#menu_form').html("");
      form_url          = "<?php echo admin_url(); ?>Menu_sorted/saveMenu/"+id;
      var name          = $('#childname_'+id).html();
      var show_in_id    = 1;
      parent_id         = getChildParent( id );
      $('#menuType').val('child');
      $('.menu_parent_div').css('display','block');
      $('#menu_manager_modal .modal-title').html("UPDATE MENU");
      $('#add_menu_btn').val("UPDATE");
      $('#add_menu_form').attr('action',form_url);
      $('#parent_menus_dropdown option:selected').removeAttr('selected');
      $('#menu_show_in option:selected').removeAttr('selected');
      $('#menu_name').val(name);
      $("#parent_menus_dropdown option[value='"+parent_id+"']").attr('selected','true');
      $("#menu_show_in option[value='"+show_in_id+"']").attr('selected','true');
      $('#menu_manager_modal').modal('show');
   }

   function getChildParent( id ){
      return $('#childname_'+id).parents('.menu-list-child').attr('parent_id');
   }

   function menuDelete(id, menu_type){
      $.ajax({
         url: admin_url+'Menu_sorted/checkChildExist/'+id,
         type: "POST",
            success: function( data ) {

               var resultData = JSON.parse( data );
               var data       = resultData['child'];
               var linkedPage = resultData['linked_page'];

               if( data > 0){
                  deleteMenuModal(id,'warning',menu_type, data)
               }else{
                  deleteMenuModal(id,'delete', menu_type, child_no = "" , linkedPage)
               }
         }
      });  
   }


   function deleteMenuModal(id, type, menu_type, child_no = "" , linkedPage = "0"){
     
      if(menu_type == 'parent'){
         var name            = $('#section_name_'+id).html();
      }else{
         var name            = $('#childname_'+id).html();
      }
      

      var _post_fix          = "";
      if( type == 'warning' ){
         if( child_no > 1 ){
            _post_fix         = "'s";
         }
         ok_button_text = "ok";
         var header_text   = 'Unable to delete the menu '+name+' please delete or reassign the submenu'+_post_fix;
         var messageObject = {
            'body': header_text,
            'button_yes':ok_button_text, 
            'button_no': 'false',
            'prevent_button_no' : true
        };
        callback_danger_modal(messageObject);
      }else{
         var linkedPageName = '';
         
         if (linkedPage['page_title'] != "0") {
            linkedPageName = 'The menu linked with page named "' + linkedPage['page_title'] + '", '
         }
         var actionMessage   = linkedPageName + 'Are you sure to delete the menu named "'+name+'" ?';
         var messageObject    = {
                                 'body': actionMessage ,
                                 'button_yes': 'DELETE',
                                 'button_no': 'CANCEL',
                                 'continue_params': {
                                       "menu_id": id,
                                       "menu_type": menu_type,
                                        },
                                 };
         $('#add_menu_btn').val("CREATE");
         callback_warning_modal(messageObject, deleteMenuConfirmed);
      }
         
   }

      function deleteMenuConfirmed(params =''){
         var menu_id       = params.data.menu_id;
         var menu_type       = params.data.menu_type;
         $.ajax({
            url: admin_url + 'Menu_sorted/delete',
            type: "POST",
            data: {
                  "menu_id": menu_id,
            },
            success: function (response) {
         
               var data = $.parseJSON(response);

               var messageObject = {
                  'body': data.message,
                  'button_yes': 'OK',
               };
      
               if (data.error == false) {
                  
                  callback_success_modal(messageObject);
                  if(menu_type == 'parent'){
                     $("#parent_menus_dropdown option[value='"+menu_id+"']").remove();
                     $('#section_wrapper_'+menu_id).remove();
                  }else{
                     $('#lecture_id_'+menu_id).remove();
                  }
                  
               } else {
                  
                  callback_danger_modal(messageObject);
               }
            }
         });

      }
    </script>

