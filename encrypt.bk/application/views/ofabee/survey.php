<?php include 'header.php'; ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
<style>
body{
	font-family: 'open Sans', sans-serif !important;
}
.required{
	color: #c21a1a;
}
.rating-level{
    position: relative;
    bottom: 16px;
    left: 18px;
    font-weight: 400;
    color: #959595;
}
.survey-holder .radio-inline{
	padding: 0 25px;
}
.survey-holder{
	padding: 30px 0px;
}
.rating-info-text{
	font-size: 16px;
    font-weight: 600;
    padding: 0 10px;
}
</style>


<div class="wrapper">
	<div class="sction_1">
    	<div class="row">
        	<div class="col-sm-12 survey-iframe" id="survey_iframe">
            	<h3>SURVEY FORM</h3>
            	 
        	</div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <form method="POST" action="<?php echo site_url() ?>/material/survey_save">
    <input type="hidden" name="survey_id" value="<?php echo $survey_details['survey_id'] ?>">
    <input type="hidden" name="course_id" value="<?php echo $survey_details['s_course_id'] ?>">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-12">
            <?php
            if(!empty($survey_details)):
                ?>
                <h2><?php echo $survey_details['s_name'] ?></h2>
                <?php
            endif;            
            ?>
                <?php
                if(!empty($survey_questions)){
                    $required_span   = '<span class="required">*</span>';
                    $required        = 'required="required"';
                    foreach($survey_questions as $question)
                    {
                        switch($question['sq_type']){
                            case '1':
                                ?>
                                    <div class="form-group">
                                        <label for="usr"><?php echo $question['sq_question'] ?> <?php echo ($question['sq_required'] == '1')?$required_span:'' ?></label>
                                        <input type="hidden" name="question[<?php echo $question['id']?>]" value="<?php echo $question['sq_question']?>">
                                            <?php
                                            $options    = json_decode($question['sq_options'], true);
                                            foreach($options as $option){
                                                ?>
                                                <div class="radio">
                                                <label><input type="radio" name="answer[<?php echo $question['id'] ?>]" value="<?php echo $option ?>" <?php echo ($question['sq_required'] == '1')?$required:'' ?>><?php echo $option ?></label>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                    </div>
                                <?php
                            break;
                            case '2':
                                ?>
                                    <div class="form-group">
                                        <label for="usr"><?php echo $question['sq_question'] ?> <?php echo ($question['sq_required'] == '1')?$required_span:'' ?></label>                                            
                                        <input type="hidden" name="question[<?php echo $question['id']?>]" value="<?php echo $question['sq_question']?>">
                                        <?php
                                            $options    = json_decode($question['sq_options'], true);
                                            foreach($options as $key=>$option){
                                                ?>
                                                <div class="checkbox">
                                                <label>
                                                <input type="checkbox" 
                                                        name="answer[<?php echo $question['id'] ?>][]" 
                                                        value="<?php echo $option ?>"><?php echo $option ?>
                                                </label>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                    </div>
                                <?php
                            break;
                            case '3':
                                ?>
                                    <div class="form-group">
                                        <label for="comment"> <?php echo $question['sq_question'] ?> <?php echo ($question['sq_required'] == '1')?$required_span:'' ?></label>
                                        <input type="hidden" name="question[<?php echo $question['id']?>]" value="<?php echo $question['sq_question']?>">
                                        <textarea class="form-control" rows="2" name="answer[<?php echo $question['id'] ?>]" <?php echo ($question['sq_required'] == '1')?$required:'' ?>></textarea>
                                    </div>
                                <?php
                            break;
                            case '4':
                                ?>
                                    <div class="form-group">
                                        <label for="usr"> <?php echo $question['sq_question'] ?> <?php echo ($question['sq_required'] == '1')?$required_span:'' ?></label>
                                        <input type="hidden" name="question[<?php echo $question['id']?>]" value="<?php echo $question['sq_question']?>">
                                        <div class="col-md-12">
                                            <div class="survey-holder">
                                            <?php
                                            $low_limit      = $question['sq_low_limit'];
                                            $high_limit     = $question['sq_high_limit'];
                                            ?>
                                                <span class="rating-info-text"><?php echo $question['sq_low_limit_label']; ?></span>
                                                <?php
                                                for($i=$low_limit; $i<=$high_limit; $i++):
                                                    ?>
                                                    <span class="rating-level"><?php echo $i; ?></span>
                                                    <label class="radio-inline">
                                                    <input type="radio" name="answer[<?php echo $question['id'] ?>]" value="<?php echo $i ?>" <?php echo ($question['sq_required'] == '1')?$required:'' ?>>
                                                    </label>
                                                    <?php
                                                endfor;
                                                ?>                                                
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            break;
                            case '5':
                                ?>
                                    <div class="form-group">
                                        <label for="sel1"><?php echo $question['sq_question'] ?> <?php echo ($question['sq_required'] == '1')?$required_span:'' ?></label>
                                        <input type="hidden" name="question[<?php echo $question['id']?>]" value="<?php echo $question['sq_question']?>">
                                        <select class="form-control" name="answer[<?php echo $question['id'] ?>]" <?php echo ($question['sq_required'] == '1')?$required:'' ?>>
                                        <option value="">SELECT</option>
                                        <?php
                                            $options    = json_decode($question['sq_options'], true);
                                            foreach($options as $option){
                                                ?>
                                                <option value="<?php echo $option ?>"><?php echo $option ?></option>
                                                <?php
                                            }
                                        ?>
                                        </select>
                                    </div>
                                <?php
                            break;
                        }
                    }
                }
                ?>				
                
			</div>
		</div>
        <div class="row">
            <button type="submit" name="survey_form" class="btn btn-blue" >Submit</button>
            <button type="button" name="survey_form" class="btn" >Cancel</button>
        </div>
    </form>
</div>
<?php include 'footer.php'; ?>
