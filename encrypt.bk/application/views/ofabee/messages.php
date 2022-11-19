<div class="message_container">
<style> 
.alerts {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}
</style>
    <?php
        //lets have the flashdata overright "$message" if it exists
        if($this->session->flashdata('message'))
        {
            $message    = $this->session->flashdata('message');
        }

        if($this->session->flashdata('error'))
        {
            $error      = $this->session->flashdata('error');
        }

        if(function_exists('validation_errors') && validation_errors() != '')
        {
            $error      = validation_errors();
        }
    ?>
        
    <div id="js_error_container" class="alerts alert-error " style="display:none;"> 
        <p id="js_error"></p>
    </div>
        
    <div id="js_note_container" class="alerts alert-note" style="display:none;">
    </div>
        
    <?php if (!empty($message)): ?>
        <div class="alerts alert-success" onclick="this.remove()">
            <a class="close" data-dismiss="alert">×</a>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alerts alert-error alert-danger" id="alert_danger"  onclick="this.remove()">
            <a class="close" data-dismiss="alert">×</a>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
</div>