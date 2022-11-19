<!-- Modal pop up contents :: Add users to course -->
 <div class="modal fade" id="add-catalog" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
     <div class="modal-dialog modal-small" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                 <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_course_catalog') ?></h4>
             </div>
             <div class="modal-body">
                 <div class="form-group">
                     <label><?php echo lang('select_courses') ?>* :</label>
                 </div>
                 <div class="inside-box" id="get_courses_list">
                 </div>

             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-green" onclick="importCoursesToCatalog()"><?php echo lang('import') ?></button>
                 <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
             </div>
         </div>
     </div>
 </div>
 <!-- Modal pop up contents :: Add users to course -->
 
 <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="publish-course" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="confirm_box_title"></b>
                            <p class="m0" id="confirm_box_content"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                        <button type="button" class="btn btn-green" id="confirm_box_ok" ><?php echo lang('continue') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->
 