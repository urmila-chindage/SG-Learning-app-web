<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo 'Welcome to '.(($title)?$title:'Ofabee') ?></title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
                background-attachment: fixed;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}
        
	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
        
        
        .background-image {
            left: auto;
            opacity: 0.1;
            position: fixed;
            right: auto;
            width: 90%;
            z-index: -1;
        }
        
        .background-image img{
            width: 100%;
        }
	</style>
        
</head>
<body>
    <div class="background-image">
        <img src="<?php echo base_url('assets/images/ofabee.png') ?>">
    </div>
<div id="container">
	<h1>Welcome to Ofabee!</h1>

	<div id="body">
		<p>Create Your Own Cloud Based Online Training Platform in Minutes .</p>

		<p>Built with simplicity of use in mind, learning more hassle free.
Ofabee helps you to create & deliver courses within an hour.
Easily Integrate various type of contents such as videos, presentations, PDFs etc. to make learning more interesting.</p>

	</div>

</div>

</body>
</html>