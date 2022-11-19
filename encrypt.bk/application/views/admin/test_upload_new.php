<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://ofabeetutor.s3.amazonaws.com/source/ajaxuploadprogress/jquery.ui.widget.js"></script> 
<script src="https://ofabeetutor.s3.amazonaws.com/source/ajaxuploadprogress/jquery.iframe-transport.js"></script> 
<script src="https://ofabeetutor.s3.amazonaws.com/source/ajaxuploadprogress/jquery.fileupload.js"></script> 
<script>
            var admin_url = 'http://localhost/ofabeeversion3/admin/';
    $(document).ready(function(){
      $('#lession_file').fileupload({
        url: admin_url+'coursebuilder/upload',
		type: 'POST',
		datatype: 'xml',
		add: function (event, data) {
                   data.submit();
		},
		progress: function(e, data){
			
            var progress = parseInt(data.loaded / data.total * 100, 10);
			$('.bar').css( 'background-color','#5eb95e');
			$('.bar').html( progress+'%');
            $('.bar').css( {'width': progress+'%'} );
			$('.bar_complete').html( progress+'% Complete');
			
        },
		fail: function(e, data) {
			$('.bar').css('width', '100%').addClass('red');
		},
		done: function (event, data) {
                    alert('done');
                },
		});  
    });
    
</script>


<div class="progress progress-success prog_bar">
<div class="bar" style="background-color: rgb(94, 185, 94); width: 42%;"></div>
</div>
<p class="bar_complete"></p>
<input type="file" id="lession_file" name="file">