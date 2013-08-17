<?php //d($user, 'user'); ?>
<?php //d($houses, 'houses'); ?>
<?php //d($housemates, 'housemates'); ?>
<?php //d($purchases, 'purchases'); ?>
<?php //d($options, 'options'); ?>
<?php //d($balances, 'balances'); ?>
<?php //d($balances_after, 'balances_after'); ?>
<?php //d($payments, 'payments'); ?>

<p>The list below outlines a few simple transactions required in order for your housemates to repay each other, and settle any outstanding balances.</p>

<ul>
<?php
if (!isset($payments) || !is_array($payments)) {
	$payments[] = 'No transactions could be suggested.';
}
?>
<?php foreach ($payments as $pay) : ?>
<?php if (is_array($pay)) : ?>
	<li><strong><?php echo $housemates[$pay['payer']]['user_name']; ?></strong> pays <strong><?php echo $housemates[$pay['payee']]['user_name']; ?></strong> £ <?php echo number_format($pay['price'], 2); ?>. <a href="<?php echo site_url('purchases/add?s=custom&r&d=Repayment&payer=' . $pay['payer'] . '&payees=' . $pay['payee'] . ':' . number_format($pay['price'], 2)); ?>">Complete this repayment?</a></li>
<?php else : ?>
	<li><?php echo $pay; ?></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>

<h3>Balances Before Consolidation</h3>

<p>This is how your house owings currently stand. Each housemate is either owed money (green), or owes it (red).</p>

<?php $this->load->view('pages/purchases/includes/balances.php', array('balances' => $balances)); ?>

<h3>Balances After Consolidation</h3>

<p>After completing the suggested transactions, all housemates will be back to zero - they'll owe each other nothing!</p>

<?php //$balances['1'] = 0; ?>

<?php //$this->load->view('pages/purchases/includes/balances.php', array('balances' => $balances_after)); ?>

<p>Did you know, you can also <a href="<?php echo site_url('purchases/export'); ?>">export</a> your purchases?</p>
