<?php $this->load->view('includes/header_view'); ?>

<?php foreach (array('success', 'error', 'info') as $alert_type) : ?>
<?php if ($this->session->flashdata($alert_type)) : ?> 
        <div class="alert alert-<?php echo $alert_type; ?>">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <?php echo $this->session->flashdata($alert_type); ?>
<?php if ($alert_type === 'error') : ?>
          <br />Please <a href="<?php echo site_url('help/contact'); ?>">get in touch</a> if you require assistance or help with this.
<?php endif; ?>
        </div>
<?php endif ?>
<?php endforeach; ?>

<h2><?php echo isset($title) ? $title : ucfirst($this->uri->segment(1, 'home')); ?></h2>

<?php if (isset($subnav)) : ?>
    <p>
<?php foreach ($subnav as $k => $v) : ?>
        <a href="<?php echo site_url($this->uri->segment(1).'/'.$k); ?>" class="btn btn-small" type="button">
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
