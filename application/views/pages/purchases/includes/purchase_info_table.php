    <table id="purchase-details" class="table table-bordered">
      <tr>
        <th>Description</th>
        <td><?php echo $purchase['description']; ?></td>
      </tr>
      <tr>
        <th>Payer</th>
        <td><?php echo $housemates[$purchase['payer']]['user_name']; ?></td>
      </tr>
      <tr>
        <th>Purchase Date</th>
        <td><?php echo strftime('%a %d %b %Y', strtotime($purchase['date'])); ?></td>
      </tr>
      
      <tr>
        <th>Added by:</th>
        <td><?php echo ($purchase['added_by'] == $user['user_id']) ? 'You' : $housemates[$purchase['added_by']]['user_name']; ?><!-- <?php echo $purchase['added_by']; ?> --></td>
      </tr>
      <tr>
        <th>Added on:</th>
        <td><?php echo strftime('%a %d %b %Y', strtotime($purchase['added_time'])); ?> (<time class="timeago" datetime="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', strtotime($purchase['added_time'])); ?>"><?php echo strftime('%a %d %b %Y at %H:%M', strtotime($purchase['added_time'])); ?></time>)</td>
      </tr>
      <?php foreach ($purchase['payees'] as $payee_id => $payee_price) : ?>
      <tr>
        <th><i class="icon-chevron-right"></i> <?php echo $housemates[$payee_id]['user_name']; ?> pays:</th>
        <td>£ <?php echo number_format($payee_price, 2); ?></td>
      </tr>
<?php endforeach; ?>
      <tr>
        <th>Total Price </th>
        <td><strong>£ <?php echo number_format($purchase['total_price'], 2); ?></strong></td>
      </tr>
    </table>