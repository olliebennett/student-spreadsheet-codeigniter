<?php $this->load->view('includes/header_view'); ?>

<?php if ($this->config->item('stsp_demo') || $this->config->item('stsp_beta')) : // DEMO or BETA ?>

        <div class="alert alert-warning">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
<?php if ($this->config->item('stsp_beta')) : // BETA only ?>
          You are viewing the BETA site. All data may be deleted or changed at any time. Visit the <a href="http://studentspreadsheet.com/">live site</a> to store real data!
<?php else : ?>
          You are viewing this site in DEMO mode. Create an account for yourself <a href="http://studentspreadsheet.com/">here</a>!
<?php endif; // DEMO ?>
        </div>

<?php endif; // DEMO or BETA?>

<?php foreach (array('success', 'error', 'info', 'warning') as $alert_type) : ?>
<?php if ($this->session->flashdata($alert_type)) : ?> 
        <div class="alert alert-<?php echo $alert_type; ?>">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php echo $this->session->flashdata($alert_type); ?>
<?php if ($alert_type === 'error') : ?>
          <br />Please <a href="<?php echo site_url('help/contact'); ?>">get in touch</a> if you need help with this.
<?php endif; ?>
        </div>
<?php endif ?>
<?php endforeach; ?>

<h2><?php echo isset($title) ? $title : ucfirst($this->uri->segment(1, 'Home')); ?></h2>

<?php if (isset($subnav)) : ?>
    <p>
<?php foreach ($subnav as $k => $v) : ?>
        <a href="<?php echo site_url($this->uri->segment(1).'/'.$k); ?>" class="btn btn-small<?php echo (($k == $this->uri->segment(2)) ? ' disabled' : ''); ?>" type="button">
            <?php echo $v; ?>
        </a>
<?php endforeach; ?>
    </p>
<?php endif; ?>
            
            
<?php // include relevant page view

$view = ((isset($view)) ? $view : $this->uri->segment(1));
switch ($view) {
case 'purchases':
    $this->load->view('pages/'.$view.'_view', $purchases);
    break;
case '':
    $view = 'home';
    $this->load->view('pages/home_view');
    break;
default:
    $this->load->view('pages/'.$view.'_view');
}

?>

<?php $this->load->view('includes/footer_view'); ?>
