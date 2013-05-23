<?php //d($user, 'user'); ?>
<?php //d($houses, 'houses'); ?>
<?php //d($housemates, 'housemates'); ?>
<?php //d($purchases, 'purchases'); ?>
<?php //d($options, 'options'); ?>
<?php //d($balances, 'balances'); ?>


<?php if (count($purchases) == 0) : ?>

<p class="warn">No purchases found. Add one <a href="<?php echo site_url('purchases/add'); ?>">here</a>.</p>

<p>Purchases are specific to your active house (currently "<strong><?php echo $houses[$user['house_id']]['house_name']; ?></strong>"). If you expected to see purchases here, and are members of other houses, review your <a href="<?php echo site_url('settings'); ?>">settings</a>.</p>

<?php else : ?>

<p>Showing <!--# to # of --><?php echo count($purchases); ?> purchases.</p>

<?php $this->load->view('pages/purchases/includes/table.php'); ?>

<!--
<div class="pagination">
  <ul>
    <li><a href="#">Prev</a></li>
    <li><a href="#">1</a></li>
    <li><a href="#">2</a></li>
    <li><a href="#">3</a></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li><a href="#">Next</a></li>
  </ul>
</div>
-->

<h3>Balances</h3>

<?php //$this->load->view('pages/purchases/includes/balances.php'); ?>


<?php $this->load->view('pages/purchases/includes/balances_graph.php'); ?>


<h3>Options</h3>

<a href="<?php echo site_url('purchases/export'); ?>"><i class="icon-download-alt"></i> Export Purchases</a> | <a href="<?php echo site_url('purchases/consolidate'); ?>"><i class="icon-magic"></i> Consolidate Purchases</a> <?php echo helptip('Find the quickest and easiest way to repay each other.'); ?>



<p>Note: Purchases are specific to your active house (currently "<strong><?php echo $houses[$user['house_id']]['house_name']; ?></strong>"). To see purchases from your other houses, switch house in your <a href="<?php echo site_url('settings'); ?>">settings</a>.</p>


<?php endif; ?>

<?php
// EOF
