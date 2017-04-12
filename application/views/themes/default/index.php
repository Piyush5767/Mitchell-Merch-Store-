<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title; ?></title>		
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=0.5, maximum-scale=1.0"/>
	<meta content="<?php echo $meta_description; ?>" name="description" />
	<meta content="<?php echo $meta_keywords; ?>" name="keywords" />
	<meta content="<?php echo $meta_robots; ?>" name="robots" />	
	<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css" rel="stylesheet" media="all" />
	<?php echo $meta; ?>
	<?php echo $this->output->assets(); ?>
	<link rel="shortcut icon" href="<?php echo base_url('media/assets/icon.png'); ?>" />
	<link href="http://fonts.googleapis.com/css?family=Oswald%7CPT+Sans%7COpen+Sans" rel="stylesheet" type="text/css"/>
	<link type="text/css" href="<?php echo base_url('application/views/themes/default/css/template.css'); ?>" rel="stylesheet" media="all" />
</head>
 <body>	
	<div class="progressloader"></div>
	<?php echo $subview;?>
 </body>
</html>