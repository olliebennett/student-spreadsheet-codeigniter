<table class="table table-bordered table-condensed">

  <thead>
  	<tr class="info">
      <th>Housemate</th>
      <th>Balance</th>
  	</tr>
  </thead>
  
  <tbody>
<?php foreach ($balances as $balance_user => $balance) : ?>
  	<tr class="<?php echo ($balance < -0.005) ? 'error' : (($balance >= 0.005) ? 'success' : 'info') ?>">
      <td><?php echo $housemates[$balance_user]['user_name']; ?></td>
      <td>£ <?php echo number_format($balance, 2); ?></td>
  	</tr>
<?php endforeach; ?>
  </tbody>

</table>
