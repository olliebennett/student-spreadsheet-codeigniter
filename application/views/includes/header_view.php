<!doctype html> 
<html> 
<head> 
	
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<title>Student Spreadsheet</title>
	
	<!-- Stylesheets here, before all scripts. Helps avoid styling issues. -->
	<link href='<?php echo base_url(); ?>assets/theme1/style.css' rel=stylesheet>
	
	<!-- Modernizr -->
	<script src="<?php echo base_url(); ?>assets/js/modernizr.js"></script>
	
	<!-- jQuery -->
	<script src="<?php echo base_url(); ?>assets/js/jquery.js"></script>
	
	<!-- jQuery Plugin: Validate -->
	<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
	
	<script type="text/javascript">
// $.validator.setDefaults({
	// submitHandler: function() { alert("submitted!"); }
// });

$().ready(function() {
	// validate purchases
	$("#purchase_form").validate({
		rules: {
			description: {
				required: true,
				maxlength: 50
			},
			price: {
				// Only required if even split is selected
				required: "#split_even:checked",
				number: true,
			},
			comment: {
				maxlength: 250
			},
		},
		messages: {
			description: {
				required: "Please enter a description",
				maxlength: "Must not exceed 50 characters"
			},
			price: {
				number: "The price must be numeric!",
			},
			comment: {
				maxlength: "Must not exceed 250 characters"
			},
		}
	});
	
	// validate signup form on keyup and submit
	$("#signupForm").validate({
		rules: {
			firstname: "required",
			lastname: "required",
			username: {
				required: true,
				minlength: 2
			},
			password: {
				required: true,
				minlength: 5
			},
			confirm_password: {
				required: true,
				minlength: 5,
				equalTo: "#password"
			},
			email: {
				required: true,
				email: true
			},
			topic: {
				required: "#newsletter:checked",
				minlength: 2
			},
			agree: "required"
		},
		messages: {
			firstname: "Please enter your firstname",
			lastname: "Please enter your lastname",
			username: {
				required: "Please enter a username",
				minlength: "Your username must consist of at least 2 characters"
			},
			password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			},
			confirm_password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long",
				equalTo: "Please enter the same password as above"
			},
			email: "Please enter a valid email address",
			agree: "Please accept our policy"
		}
	});

});
</script>

<style type="text/css">
#signupForm { width: 670px; }
#signupForm label.error {
	margin-left: 10px;
	width: auto;
	display: inline;
}
#purchase_form { width: 670px; }
#purchase_form label.error {
	margin-left: 10px;
	width: auto;
	display: inline;
}
#newsletter_topics label.error {
	display: none;
	margin-left: 103px;
}
</style>
	
	<!-- Cufon -->
	<script src="<?php echo base_url(); ?>assets/js/cufon.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/js/dock11.font.js" type="text/javascript"></script>
	<script type="text/javascript">
		Cufon.replace('h1,h2');
	</script>

</head>

<body>
		
	<div id=container>
		<header>
			<div id="topbar">
				<h1>The Student Spreadsheet</h1>
				<div id="user_info">
<?php if (isset($user['fb_id'])) : ?>
					<span id="user_name"><?php echo $user['firstname']; ?><br /><?php echo $user['lastname']; ?></span>
					<img id="user_pic" src="https://graph.facebook.com/<?php echo $user['fb_id']; ?>/picture" />
<?php else : ?>
					<span id="user_name">Login<br />First</span>
					<img id="user_pic" height="50" width="50" src="<?php echo base_url(); ?>assets/img/member_photo_placeholder.png" />
<?php endif; ?>
				</div><!-- #user_info -->
				
				<div class="clearfix"></div>
			</div><!-- #topbar -->
	
			<nav id="main">
				<span class="left"><a href="<?php echo site_url(); ?>" class="nav<?php echo (($slug == 'home') ? ' nav_current' : '') ?> nav_home">Home</a></span>
				<span class="left"><a href="<?php echo site_url('purchases'); ?>" class="nav<?php echo (($slug == 'purchases') ? ' nav_current' : '') ?> nav_pos_left nav_purchases">Purchases</a><a href="<?php echo site_url('purchases/add'); ?>" class="nav<?php echo (($slug == 'add_purchase') ? ' nav_current' : '') ?> nav_pos_right nav_add">&nbsp;</a></span>
				<span class="left"><a href="<?php echo site_url('items'); ?>" class="nav<?php echo (($slug == 'items') ? ' nav_current' : '') ?> nav_pos_left nav_items">Items</a><a href="<?php echo site_url('items/add'); ?>" class="nav<?php echo (($slug == 'add_item') ? ' nav_current' : '') ?> nav_pos_right nav_add">&nbsp;</a></span>
				<span class="left"><a href="<?php echo site_url('settings'); ?>" class="nav<?php echo (($slug == 'settings') ? ' nav_current' : '') ?> nav_settings">Settings</a></span>
<?php if(isset($user['fb_id'])) : ?>
				<span class="right"><a href="<?php echo site_url('stspauth/logout'); ?>" class="nav nav_logout">Logout</a></span>
<?php else : ?>
				<span class="right"><a href="<?php echo site_url('stspauth/login'); ?>" class="nav nav_fblogin">Login</a></span>
<?php endif; ?>
				<span class="right"><a href="<?php echo site_url('help'); ?>" class="nav<?php echo (($slug == 'help') ? ' nav_current' : '') ?> nav_help">Help</a></span>
<?php if(isset($user['is_admin']) && ($user['is_admin'] === '1')) : ?>
				<span class="right"><a href="<?php echo site_url('admin'); ?>" class="nav<?php echo (($slug == 'admin') ? ' nav_current' : '') ?> nav_admin">Admin</a></span>
<?php endif; ?>
				<div class="clearfix"></div>
			</nav>
	
			<div class="clearfix"></div>
			
		</header>
		
		<div id="content">
