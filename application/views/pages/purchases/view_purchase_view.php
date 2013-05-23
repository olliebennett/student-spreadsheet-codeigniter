<?php
d($purchases, 'purchases'); // plural, as we also receive old versions of purchase
//d($purchase_id, 'purchase_id');
//d($purchases[$purchase_id], 'this actual purchase');
//d($housemates, 'housemates');
//d($user,'user');
?>

<?php foreach ($purchases as $purchase) : ?>

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
		<tr>
			<th>Total Price </th>
			<td><strong>£ <?php echo number_format($purchase['total_price'], 2); ?></strong></td>
		</tr>
<?php foreach ($purchase['payees'] as $payee_id => $payee_price) : ?>
		<tr>
			<th><?php echo $housemates[$payee_id]['user_name']; ?></th>
			<td>£ <?php echo number_format($payee_price, 2); ?></td>
		</tr>
<?php endforeach; ?>
	</table>

	<p>This purchase was added using <?php echo ($purchase['split_type'] == 'even') ? 'an <em>even</em>' : (($purchase['split_type'] == 'custom') ? 'a <em>custom</em>' : 'an <b>UNKNOWN</b>'); ?> split. <?php echo helptip('When adding purchases, the price can be split either evenly between payers, or manually.'); ?></p>

<?php endforeach; // end purchases loop ?>
  
<?php if(isset($purchase['comments']) && count($purchase['comments']) >= 1): ?>

	<table class="purchase-comments" class="table table-bordered">

		<thead>
			<tr>
				<th>Author</th>
				<th>Comment</th>
				<th>Date</th>
			</tr>
		</thead>

		<tbody>

<?php foreach ($purchase['comments'] as $comment) : ?>
<?php $t = strtotime($comment['added_time']); ?>

			<tr>
				<td><?php echo $comment['added_by']; ?></td>
				<td><?php echo $comment['text']; ?></td>
				<td>
					<time class="timeago" datetime="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', $t); ?>"><?php echo strftime('%a %d %b %Y at %H:%M', $t); ?></time>
				</td>
			</tr>

<?php endforeach; ?>

		</tbody>

	</table>

<?php else : ?>

	<p>No comments have been made on this purchase.</p>

<?php endif ?>

<?php echo form_open('purchases/addcomment', array('id'=>'comment_form','class'=>'')); ?>

  <div class="control-group">
  	<?php //echo form_err('comment'); ?>
    <label class="control-label" for="comment">Comment or Dispute</label>
    <div class="controls">
      <textarea rows="3" name="comment" id="comment" class="span5" placeholder="Enter comment or dispute text" ></textarea>
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
      <a class="btn btn-warning"><i class="icon-legal"></i> Add Dispute</a>
      <a class="btn btn-inverse"><i class="icon-comment"></i> Add Comment</a>
      <span class="help-inline"><?php echo helptip('Dispute vs. Comment.', 'right'); ?></span>
    </div>
  </div>
  
  <div class="control-group">
    <div class="controls">
      <a href="<?php echo site_url('purchases/edit/'.$purchase_id, 'Edit'); ?>" class="btn btn-primary" ><i class="icon-edit"></i> Edit Purchase</a>
      <a href="<?php echo site_url('purchases/delete/'.$purchase_id, 'Edit'); ?>" class="btn btn-danger" ><i class="icon-remove"></i> Delete Purchase</a>
      <a href="<?php echo site_url('purchases/undelete/'.$purchase_id, 'Edit'); ?>" class="btn btn-success"><i class="icon-undo"></i> Undelete Purchase</a>
    </div>
  </div>

</form>

