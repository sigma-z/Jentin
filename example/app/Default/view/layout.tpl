<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $this->framework ?></title>
    <link href="css/screen.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body>

<h1><?php echo $this->framework; ?></h1>

<?php echo $this->raw('content'); ?>

<div class="copy">
    &copy; 2012 - <?php echo date('Y'); ?>&nbsp;sigma-scripts.de
</div>

</body>
</html>
