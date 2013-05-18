<?php //d(array_sum(array_values($balances)), 'sum of balances'); ?>

<div id="balances-graph">

<?php $absmax = round(abs(max($balances))*100)/100; ?>
<?php //d($absmax, 'absmax'); ?>
<?php foreach ($balances as $balance_id => $balance_price) : ?>
<?php $balance_price = round($balance_price*100)/100; ?>
<?php //d($balance_price, 'balance_price'); ?>
<?php $scaled_width = abs(50 * ($balance_price / $absmax)); ?>
<?php //d($scaled_width, 'scaled_width'); ?>
  <div class="row <?php echo (abs($balance_price) < 0.005) ? '' : ($balance_price > 0) ? 'text-success' : 'text-error'; ?>">
    <div class="span2">
    	<?php echo $housemates[$balance_id]['user_name']; ?> 
    </div>
    <div class="span1 text-right">
    	<?php echo render_price($balance_price); ?>
    </div> 
    <div class="progress progress-striped baltip span9">
<?php if ($balance_price < 0) : ?>
      <div class="bar bar-danger" data-toggle="tooltip" data-placement="right" title="£ <?php echo $balance_price; ?>" style="width: <?php echo number_format($scaled_width); ?>%; margin-left: <?php echo number_format(50 - $scaled_width); ?>%;"></div>
<?php else : ?>
      <div class="bar bar-success" data-toggle="tooltip" data-placement="left" title="£ <?php echo $balance_price; ?>" style="width: <?php echo number_format($scaled_width); ?>%; margin-left: 50%;"></div>
<?php endif; ?>
    </div>
  </div>

<?php endforeach; ?>
</div><!-- #balances-graph -->

