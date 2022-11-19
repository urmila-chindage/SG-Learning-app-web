<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        setTimeout(function(){
            location.href = '<?php echo $redirect_url; ?>';
        }, 2000);
    </script>
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
</head>
<body>
    <h2><?php echo $title ?></h2>
    <p>Page will be redirecting in 2 seconds...</p>
</body>
</html>