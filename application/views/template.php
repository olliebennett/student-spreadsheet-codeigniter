<?php $this->load->view('includes/header_view'); ?>


<?php if (ENVIRONMENT == 'demo' || ENVIRONMENT == 'beta') : // DEMO or BETA ?>

        <div class="alert alert-info">
<?php if (ENVIRONMENT == 'demo') : ?>
          You are exploring the <strong>DEMO</strong> site, which contains sample data. Create a real account for yourself <a href="http://studentspreadsheet.com/">here</a>!
<?php else : // BETA ?>
          You are viewing the <strong>BETA</strong> site. All data may be deleted or changed at any time. Visit the <a href="http://studentspreadsheet.com/">live site</a> to store real data!
<?php endif; // DEMO or BETA ?>
        </div>

<?php elseif (ENVIRONMENT == 'production') : ?>

<? // hide this warning after a date...
$date_now = new DateTime('now');
$date_expiry = new DateTime('2013-09-01');
$date_time_remaining = $date_now->diff($date_expiry);
?>

<?php if ($date_time_remaining->format('%R') === "+") : // date in future ?>
        <div class="alert alert-success">
          <button type="button" class="close" data-dismiss="alert" onclick="alert('This message will be removed permanently in <?php echo $date_time_remaining->format('%a days'); ?>.');" >&times;</button>
          Accounts from the old Student Spreadsheet can be viewed <a href="http://studentspreadsheet.com/version/3-1/">here</a>.
        </div>
<?php endif; ?>

<?php endif; ?>

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

<?php if ($this->uri->segment(1, 'Home') != 'Home') : // don't display 'Home' title ?>
<h2><?php echo isset($title) ? $title : ucfirst($this->uri->segment(1)); ?></h2>
<?php endif; ?>

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
