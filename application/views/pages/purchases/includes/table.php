<?php
//var_dump($purchases);
//var_dump($housemates);
?>

<?php
foreach ($housemates as $housemate) {
	$housemate_name[$housemate['user_id']] = $housemate['user_name'];
}
?>


<table id="purchases" class="table table-hover table-bordered table-condensed" >
<thead class="success">
	<tr class="warning">
		<th>Date</th>
		<th>Description</th>
		<th>Payer</th>
		<th>TOTAL</th>
<?php foreach ($housemates as $housemate) : ?>
		<th><?php echo $housemate['user_name']; ?></th>
<?php endforeach; ?>
	</tr>
</thead>

<!--
<tfoot class="success">
<tr>
	<th>ID</th>
	<th>Date</th>
	<th>Description</th>
	<th>Price</th>
	<th>Payer</th>
</tr>
</tfoot>
-->

<tbody>

<?php $colspan = 4 + count($housemates); ?>
<?php foreach ($purchases as $purchase_id => $purchase) : ?>
	<?php $isNew = (strtotime($purchase['added_time']) > (strtotime('now') - 60*60*2)); ?>
	<tr class="purchase-overview<?php echo ($purchase['status'] == 'deleted') ? ' error' : ''; ?>">
		<td title="Date of Purchase: <?php echo date("l jS F Y", strtotime($purchase['date'])); ?>"><?php echo date("d/m/y", strtotime($purchase['date'])); ?></td>
		<td><?php echo ($isNew) ? '<i title="Recently Added" class="icon-star"></i> ' : '' ?><?php echo $purchase['description']; ?></td>
		<td><?php echo $housemate_name[$purchase['payer']]; ?></td>
		<td><?php echo render_price($purchase['total_price']); ?></td>
<?php foreach ($housemates as $housemate) : ?>
	<td>£ <?php echo number_format((isset($purchase['payees'][$housemate['user_id']]) ? $purchase['payees'][$housemate['user_id']] : '0'), 2); ?></td>
<?php endforeach; // housemates ?>
	</tr>
	<tr class="purchase-details warning" data-id="<?php echo $purchase_id; ?>">
		<td colspan="<?php echo $colspan; ?>">
			<span class="pull-left">
<?php if ($purchase['status'] == 'deleted') : ?>
<span class="text-error">
  Deleted <time class="timeago" datetime="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', strtotime($purchase['deleted_time'])); ?>"><?php echo strftime('%a %d %b %Y at %H:%M', strtotime($purchase['deleted_time'])); ?></time> by <?php echo ($user['user_id'] == $purchase['deleted_by']) ? '<em>you</em>' : $housemate_name[$purchase['deleted_by']]; ?>.
</span>
<?php else : // not deleted ?>
<?php echo ($purchase['status'] == 'edited') ? 'Last edited' : 'Added' ?> <time class="timeago" datetime="<?php echo strftime('%Y-%m-%dT%H:%M:%SZ', strtotime($purchase['added_time'])); ?>"><?php echo strftime('%a %d %b %Y at %H:%M', strtotime($purchase['added_time'])); ?></time> by <?php echo ($user['user_id'] == $purchase['added_by']) ? '<em>you</em>' : $housemate_name[$purchase['added_by']]; ?>.
        <!--(<?php //echo $purchase['comment_count'] . ' ' . ($purchase['comment_count'] == 1 ? 'comment' : 'comments'); ?>)-->
<?php endif; // deleted ?>
			</span>
			<span class="pull-right">
        <?php echo anchor('purchases/view/'.$purchase_id, '<i class="icon-eye-open"></i> view'); ?>
<?php if (isset($purchase['perm_userCanModify']) && $purchase['perm_userCanModify'] === TRUE) : ?>
<?php if ($purchase['status'] == 'ok') : ?> 
        <?php echo anchor('purchases/edit/'.$purchase_id, '<i class="icon-edit"></i> edit'); ?>
        <?php echo anchor('purchases/delete/'.$purchase_id, '<i class="icon-remove"></i> delete'); ?>
        <!--<?php echo anchor('purchases/dispute/'.$purchase_id, '<i class="icon-legal"></i> dispute'); ?>-->
        <!--<?php echo anchor('purchases/comment/'.$purchase_id, '<i class="icon-comment"></i> comment'); ?>-->
<?php elseif ($purchase['status'] == 'deleted') : ?> 
        <?php echo anchor('purchases/restore/'.$purchase_id, '<i class="icon-undo"></i> restore'); ?>
<?php endif; // status OK ?>
<?php endif; // userCanModify ?>
			</span>
		</td>
	</tr>



<?php endforeach; // purchases ?>

</tbody>
</table><!-- #purchases -->
