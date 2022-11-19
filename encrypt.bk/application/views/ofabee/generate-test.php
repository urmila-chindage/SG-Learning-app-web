<?php include_once "header.php" ?>
<style>
    .multiselect-search:hover, .multiselect-search:focus{border:solid 1px #ccc !important;}
</style>
<section>
    <div class="category-terms pb100">
        <div class="container container-altr">
            <div class="container-reduce-width">

                <div class="col-sm-9 col-md-9 col-lg-9 category-right col-lg-push-3 col-md-push-3 col-sm-push-3 generate-right">
                    <h3>Generate Test</h3>


                    <div class="generate-content" id="form_wrapper_generate_test">
                            <div class="form-group row">
                                <label for="difficulty" class="col-sm-4 col-form-label generate-label">Choose <strong>Difficulty</strong></label>
                                <div class="col-sm-8">
                                    <div class="btn-group select-time">
                                        <button id="assessment_mode" type="button" class="form-control btn dropdown-toggle big-input" data-toggle="dropdown">
                                            Levels <span class="caret drop-caret"></span>
                                        </button>
                                        <ul class="dropdown-menu generate-dropdown dropdown-assessment-mode">
                                            <?php if(isset($assessment_medium) && !empty($assessment_medium)): ?>
                                            <?php foreach($assessment_medium as $key => $medium): ?>
                                                <li><a href="javascript:void(0)" data-mode-id="<?php echo $key ?>"><?php echo $medium ?></a></li>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>                            
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="difficulty" class="col-sm-4 col-form-label generate-label">Choose <strong>Topics</strong></label>
                                <div class="col-sm-8">
                                    <div class="btn-group select-time">
                                        <select id="topic-select" multiple="multiple" class="form-control category-sel">
                                            <?php if(isset($categories) && !empty($categories)): ?>
                                            <?php foreach($categories as $category): ?>
                                                <option value="<?php echo $category['id'] ?>"><?php echo $category['qc_category_name'] ?></option>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>                           
                                    </div>                      

                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="difficulty" class="col-sm-4 col-form-label generate-label">Choose <strong>Duration</strong></label>

                                <div class="col-sm-8">
                                    <div class="btn-group select-time">
                                        <button id="assessment_duration" type="button" class="form-control btn dropdown-toggle big-input" data-toggle="dropdown">
                                            Duration <span class="caret drop-caret"></span>
                                        </button>
                                        <ul class="dropdown-menu generate-dropdown dropdown-assessment-duration">
                                            <?php if(isset($assessment_duration) && !empty($assessment_duration)): ?>
                                            <?php foreach($assessment_duration as $key => $duration): ?>
                                                <li><a href="javascript:void(0)" data-duration-id="<?php echo intval($key+1) ?>"><?php echo $duration ?></a></li>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <span class="pull-right full-box">
                                        <button class="btn btn-black" onclick="cancelAssessment()" id="cancel_selected_value_generate_test">Cancel</button>
                                        <button class="btn btn-orange" id="generate_assessment" onclick="generateAssessment();">Generate</button>
                                    </span>                        
                                </div>
                            </div>                                       
                    </div>
                </div>            


        <?php include('sidebar_beta.php'); ?>

            </div>

        </div>	<!--container-reduce-width-->
    </div><!--container-->       
</div><!--category-terms-->
</section>

<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/bootstrap-multiselect.js"></script>
<script>
    var __assessment_mode     = null;
    var __assessment_duration = null;
    var __site_url            = '<?php echo site_url() ?>';
    var __category_id         = '<?php echo $category_id ?>';
    $(document).on('click', '.dropdown-assessment-mode li a', function(){
        __assessment_mode = $(this).attr('data-mode-id');
        $('#assessment_mode').html($(this).text()+' <span class="caret drop-caret"></span>');
    });
    $(document).on('click', '.dropdown-assessment-duration li a', function(){
        __assessment_duration = $(this).attr('data-duration-id');
        $('#assessment_duration').html($(this).text()+' <span class="caret drop-caret"></span>');
    });
    
    var __generatingInProgress = false;
    function generateAssessment()
    {
        if(__generatingInProgress==true)
        {
            return false;
        }
        var asssessment_category = $('#topic-select').val();
        var errorMessage = '';
        var errorCount   = 0;
        if(__assessment_mode == null)
        {
            errorCount++;
            errorMessage += 'Choose assessment mode<br />';
        }
        if(asssessment_category == null || asssessment_category.length == 0)
        {
            errorCount++;
            errorMessage += 'Choose assessment Topics<br />';           
        }
        if(__assessment_duration == null)
        {
            errorCount++;
            errorMessage += 'Choose assessment duration<br />';
        }
        
        if(errorCount > 0 )
        {
            $('#form_wrapper_generate_test').prepend(renderPopUpMessage('error', errorMessage));
            return false;
        }
        __generatingInProgress = true;
        $('#generate_assessment').html('Generating...');
        $.ajax({
           url: __site_url+'course/generate_assessment_proceed',
           type: "POST",
           data:{ "is_ajax":true, 'mode' : __assessment_mode, 'duration':__assessment_duration, 'assessment_category':JSON.stringify(asssessment_category), 'category_id':__category_id },
           success: function(response) {
               $('#generate_assessment').html('Generate');
               var data = $.parseJSON(response);
               if(data['error']==false)
               {
                   location.href = data['link'];
               }
               else
               {
                    $('#form_wrapper_generate_test').prepend(renderPopUpMessage('error', data['message']));
               }
               __generatingInProgress = false;
           }
       });   
    }
    
    function renderPopUpMessage(template, message){
        $('#popUpMessage').remove();
        var errorClass   = (template=='error')?'danger':'success';
        var messageHtml  = '';
            messageHtml += '<div id="popUpMessage" class="alert alert-'+errorClass+'">';
            messageHtml += '    <a data-dismiss="alert" class="close">&cross;</a>';
            messageHtml += '    '+message;
            messageHtml += '</div>';
        return messageHtml;
    }
    
    
    function cancelAssessment()
    {
        __assessment_mode = null;
        __assessment_duration = null;
        $('#topic-select option:selected').each(function() {
            $(this).prop('selected', false);
        })
        $('#topic-select').multiselect('refresh');
        $('#assessment_mode').html('Levels <span class="caret drop-caret"></span>');
        $('#assessment_duration').html('Duration <span class="caret drop-caret"></span>');
    }
    
    $(document).on("#cancel_selected_value_generate_test","click", function(){
        $('#topic-select').multiselect('deselectAll', false);
        $('#topic-select').multiselect('updateButtonText');
    });
    $(document).ready(function () {
        $('#topic-select').multiselect({
            includeSelectAllOption: false,
            maxHeight: 200,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonContainer: '<div class="btn-group topic-group" />',
            buttonClass: 'btn btn-default btn-topic'
        });
    });
</script>
<?php include_once "footer.php"; ?>