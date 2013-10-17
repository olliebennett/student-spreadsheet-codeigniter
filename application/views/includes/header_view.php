<!doctype html>
<html lang="en">
<head>
<?php $this->load->view('includes/header_meta'); ?>

  <title><?php echo isset($title) ? $title : ucfirst($this->uri->segment(1, 'home')); ?> : Student Spreadsheet</title>

  <!-- Stylesheets here, before all scripts. Helps avoid styling issues. -->
  <link href='<?php echo base_url(); ?>assets/lib/bootstrap/css/bootstrap.css' rel="stylesheet">
  <link href="<?php echo base_url(); ?>assets/lib/font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <!--<link href='<?php echo base_url(); ?>assets/css/hauth.css' rel=stylesheet>-->
  <link href='<?php echo base_url(); ?>assets/theme1/style.css' rel="stylesheet">

  <!-- Modernizr -->
  <script src="<?php echo base_url(); ?>assets/lib/modernizr/modernizr-latest.js"></script>

  <!-- jQuery -->
  <script src="<?php echo base_url ();?>assets/lib/jquery/jquery.min.js"></script>

  <!-- jQuery Form + Validation -->
  <script src="<?php echo base_url ();?>assets/lib/jquery-form/jquery.form.js"></script>
  <script src="<?php echo base_url ();?>assets/lib/jquery-validation/jquery.validate.js"></script>

  <!-- jQuery TimeAgo -->
  <script src="<?php echo base_url ();?>assets/lib/jquery-timeago/jquery.timeago.js"></script>
  <script>jQuery.timeago.settings.allowFuture = true;</script>

<?php if ($this->uri->segment(1) == 'purchases') : ?>
  <!-- jQuery PickADate -->
  <link href='<?php echo base_url ();?>assets/lib/jquery-pickadate/themes/pickadate.01.default.css' rel="stylesheet">
  <script src="<?php echo base_url ();?>assets/lib/jquery-pickadate/source/pickadate.min.js"></script>
<?php endif; ?>
<?php if ($this->uri->segment(1) == 'register') : ?>
  <!-- jQuery Select2 -->
  <link href='<?php echo base_url ();?>assets/lib/jquery-select2/select2.css' rel="stylesheet">
  <script src="<?php echo base_url ();?>assets/lib/jquery-select2/select2.js"></script>
<?php endif; ?>

  <!-- Bootstrap -->
  <script src="<?php echo base_url(); ?>assets/lib/bootstrap/js/bootstrap.js"></script>

  <!-- Cufon -->
  <script src="<?php echo base_url(); ?>assets/js/cufon.js" type="text/javascript"></script>
  <script src="<?php echo base_url(); ?>assets/js/dock11.font.js" type="text/javascript"></script>
  <script type="text/javascript">
    Cufon.replace('h1,h2');
  </script>

</head>

<body>

  <div id="container">
    <header>
      <div id="topbar">
        <div id="stsp_logo">
          <img src="<?php echo base_url(); ?>assets/img/stsp_64.png" alt="#" />
        </div>
        <h1>
          Student Spreadsheet
<?php if (ENVIRONMENT == 'demo') : ?>
          <span class="text-warning">DEMO</span>
<?php elseif (ENVIRONMENT == 'beta') : ?>
          <span class="text-warning">BETA</span>
<?php elseif (ENVIRONMENT == 'development') : ?>
          <span class="text-warning">DEV</span>
<?php endif; ?>
        </h1>
        <div id="user_info">
<?php if ($this->session->userdata('social_identifier_facebook')) : ?>
          <div id="user_name">
          <?php echo isset($user['user_name_first']) ? $user['user_name_first'] : ($this->session->userdata('social_firstName')) ? $this->session->userdata('social_firstName') : 'Log In'; ?>
          <br />
          <?php echo isset($user['user_name_last']) ? $user['user_name_last'] : ($this->session->userdata('social_lastName')) ? $this->session->userdata('social_lastName') : ''; ?>
          </div>
          <div class="user_pic_container">
            <img class="user_pic" src="https://graph.facebook.com/<?php echo $this->session->userdata('social_identifier_facebook'); ?>/picture" height="50" width="50" alt="Profile Pic" />
            <span class="user_pic_social_overlay"><img src="<?php echo base_url(); ?>assets/img/fb_16x16.png" width="16" height="16" /></span> 
          </div>

<?php else : ?>
          <div id="user_name"><a href="<?php echo site_url('auth'); ?>">Log In</a></div>
          <img class="user_pic" height="50" width="50" src="<?php echo base_url(); ?>assets/img/member_photo_placeholder.png"  alt="Profile Pic" />
<?php endif; ?>
        </div><!-- #user_info -->

        <div class="clearfix"></div>
      </div><!-- #topbar -->

      <nav id="main">
        <span class="pull-left"><a href="<?php echo site_url(); ?>" class="nav<?php echo (($this->uri->segment(1) == '') ? ' nav_current' : '') ?> nav_home">Home</a></span>
        <span class="pull-left"><a href="<?php echo site_url('purchases'); ?>" class="nav<?php echo $this->uri->uri_string() == 'purchases' ? ' nav_current' : ''; ?> nav_pos_left nav_purchases">Purchases</a><a href="<?php echo site_url('purchases/add'); ?>" class="nav<?php echo (($this->uri->uri_string() == 'purchases/add') ? ' nav_current' : '') ?> nav_pos_right nav_add">&nbsp;</a></span>
        <span class="pull-left"><a href="<?php echo site_url('settings'); ?>" class="nav<?php echo (($this->uri->segment(1) == 'settings') ? ' nav_current' : '') ?> nav_settings">Settings</a></span>
<?php if ($this->session->userdata('social_identifier_facebook')) : ?>
        <span class="pull-right"><a href="<?php echo site_url('auth/logout'); ?>" class="nav nav_logout">Logout</a></span>
<?php else : ?>
        <span class="pull-right"><a href="<?php echo site_url('auth'); ?>" class="nav<?php echo (($this->uri->segment(1) == 'auth') ? ' nav_current' : '') ?> nav_login">Login</a></span>
<?php endif; ?>
<?php if (isset($user) && !is_null($user) && $user !== FALSE && !isset($user['user_id'])) : ?>
        <span class="pull-right"><a href="<?php echo site_url('register'); ?>" class="nav<?php echo (($this->uri->segment(1) == 'register') ? ' nav_current' : '') ?> nav_register">Register</a></span>
<?php endif; ?>
        <span class="pull-right"><a href="<?php echo site_url('help/about'); ?>" class="nav<?php echo (($this->uri->segment(1) == 'help') ? ' nav_current' : '') ?> nav_help">Help</a></span>
<?php if ((isset($user['user_id']) && in_array($user['user_id'], $this->config->item('stsp_admins')))) : ?>
        <span class="pull-right"><a href="<?php echo site_url('admin'); ?>" class="nav<?php echo (($this->uri->segment(1) == 'admin') ? ' nav_current' : '') ?> nav_admin">Admin</a></span>
<?php endif; ?>
        <div class="clearfix"></div>
      </nav>

    </header>

    <div id="content">
