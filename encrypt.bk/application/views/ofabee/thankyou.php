<style type="text/css">
body{
	margin:0;
	border:0;
	padding:0;
	background-color:#d5d5d5;
	width:100%;
	height:100%;
}
.form-main-altr{
	background-color:white;
	width:70%;
position: absolute;
	top:0;
	bottom: 0;
	left: 0;
	right: 0;
	margin: auto;
	padding-top:10%;
}
.thank{
	text-align:center;
	font-family:Arial, Helvetica, sans-serif;
}
.thank-color{
	color:#666;
}
.img-container{
	width:241px;
	height:inherit;
	position:relative;
	margin-left:auto;
	margin-right:auto;
	border:1px solid  #1CB463;
	border-radius:100%;
	padding:49px 12px;
}
.sucess-tick{
	vertical-align:middle;
	display:block;
	margin-left:auto;
	margin-right:auto;
	height:148px;
	width:auto;
}
</style>
<html>
    <title>Thank You
    </title>
    <body>
        <div class="register_main register_main-alt">
    <div class="container">
        <div class="register">
            
            <div class="form_main form-main-altr">
                <div class="row">
                    
                    <div class="col-md-12 brd text-center">
                    <div class="img-container">
                      <img class="sucess-tick" src="<?php echo assets_url('themes/'.config_item('theme').'/images') ?>tick.png"/>
                    </div><!--img-container-->
                   
                        <h1 class="thank">Thank you</h1>
                       
                    </div>
                    <div class="col-xs-12 col-sm-12 brd text-center custom-text">
                       <p class="thank thank-color">You have completed the exam..</p> 
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">//setTimeout("window.close();", 3000);</script>
    </body>
</html>

