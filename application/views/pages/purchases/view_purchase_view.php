<?php
//d($purchases, 'purchases'); // plural, as we also receive old versions of purchase
//d($purchase_id, 'purchase_id');
//d($purchases[$purchase_id], 'this actual purchase');
//d($housemates, 'housemates');
//d($user,'user');
?>

<?php //$purchase = $purchases[max(array_keys($purchases))]; // get latest version to display ?>
<?php $purchase = $purchases[$purchase_id]; // get latest version to display ?>

<?php if ($purchase['status'] == 'deleted') : ?>
<h3 class="text-error">This purchase was deleted by <?php echo $housemates[$purchase['deleted_by']]['user_name']; ?> on <?php echo $purchase['deleted_time']; ?>.</h3>
<?php endif; ?>

<?php $this->load->view('pages/purchases/includes/purchase_info_table.php', array('purchase' => $purchase, 'housemates' => $housemates)); ?>

<p>This purchase was added using <?php echo ($purchase['split_type'] == 'even') ? 'an <em>even</em>' : (($purchase['split_type'] == 'custom') ? 'a <em>custom</em>' : 'an <b>UNKNOWN</b>'); ?> split. <?php echo helptip('When adding purchases, the price can be split either evenly between payers, or manually.'); ?></p>

<?php if (isset($purchase['perm_userCanModify']) && $purchase['perm_userCanModify'] === TRUE) : ?>
<?php if ($purchase['status'] != 'deleted') : ?>
<a href="<?php echo site_url('purchases/edit/'.$purchase_id, 'Edit'); ?>" class="btn btn-primary" ><i class="icon-edit"></i> Edit Purchase</a>
<?php endif; ?>
<?php if ($purchase['status'] == 'deleted') : ?>
<a href="<?php echo site_url('purchases/restore/'.$purchase_id, 'Restore'); ?>" class="btn btn-success"><i class="icon-undo"></i> Restore Purchase</a>
<?php else : ?>
<a href="<?php echo site_url('purchases/delete/'.$purchase_id, 'Delete'); ?>" class="btn btn-danger" ><i class="icon-remove"></i> Delete Purchase</a>
<?php endif; // deleted ?>
<?php endif; // userCanModify ?>

<h3>Purchase History and Comments</h3>

<?php
$html_expand = '<i class="icon-chevron-down"></i> expand all';
$html_collapse = '<i class="icon-chevron-up"></i> collapse all';
?>

<p>[ <span id="accordion-toggle-all"><?php echo $html_expand; ?></span> ]</p>
<script>
$('#accordion-toggle-all').click(function() {
  if ($('#accordion-toggle-all').html() === '<?php echo $html_expand; ?>') {
    $('.accordion-body').collapse('show');
    $('#accordion-toggle-all').html('<?php echo $html_collapse; ?>');
  } else {
    $('.accordion-body').collapse('hide');
    $('#accordion-toggle-all').html('<?php echo $html_expand; ?>');
  }
});
</script>
<div class="accordion" id="purchase-history">

<?php foreach ($purchases as $p_id => $p) : // purchases ?>

<?php if ($p_id == min(array_keys($purchases))) : // is original version ?>
<div class="accordion-group">
  <div class="accordion-heading">
    <a class="accordion-toggle" data-toggle="collapse" href="#purchase-history-p<?php echo $p_id; ?>">
      <i class="icon-plus-sign"></i> <?php echo $p['added_time']; ?> : Added by <?php echo $housemates[$p['added_by']]['user_name']; ?>
    </a>
  </div>
  <div id="purchase-history-p<?php echo $p_id; ?>" class="accordion-body collapse">
    <div class="accordion-inner">
      <ul class="unstyled">
        <li>Description: <b><?php echo $p['description']; ?></b></li>
        <li>Purchase Date: <?php echo $p['date']; ?></li>
        <li>Paid by: <?php echo $housemates[$p['payer']]['user_name']; ?></li>
        <li>Payees:
          <ul>
<?php foreach ($p['payees'] as $payee_id => $payee_val) : ?>
            <li><?php echo $housemates[$payee_id]['user_name']; ?> pays <?php echo render_price($payee_val); ?></li>
<?php endforeach; // payees ?>
          </ul>
        </li>
        <li>Total Price: <?php echo render_price($p['total_price']); ?></li>
      </ul>
    </div>
  </div>
</div>
<?php else : // is more recent version ?>
<div class="accordion-group">
  <div class="accordion-heading">
    <a class="accordion-toggle" data-toggle="collapse" href="#purchase-history-p<?php echo $p_id; ?>">
      <i class="icon-edit"></i> <?php echo $p['added_time']; ?> : Edited by <?php echo $housemates[$p['added_by']]['user_name']; ?>
    </a>
  </div>
  <div id="purchase-history-p<?php echo $p_id; ?>" class="accordion-body collapse">
    <div class="accordion-inner">
      <ul class="unstyled">
<?php foreach ($p['edit_changes'] as $change) : // edit changes ?>
        <li><?php echo $change; ?></li>
<?php endforeach; // edit changes ?>
      </ul>
    </div>
  </div>
</div>

<?php endif; // purchase version ?>

<?php if (isset($p['comments'])) : ?>
<?php foreach ($p['comments'] as $c_id => $comment) : // comments ?>
<div class="accordion-group">
  <div class="accordion-heading">
    <a class="accordion-toggle" data-toggle="collapse" href="#purchase-history-c<?php echo $c_id; ?>">
      <i class="icon-<?php echo ($comment['type'] == 'dispute') ? 'legal' : 'comment'; ?>"></i> <?php echo $comment['added_time']; ?> : <?php echo ($comment['type'] == 'dispute') ? 'Dispute' : 'Comment'; ?> by <?php echo $housemates[$comment['added_by']]['user_name']; ?>
    </a>
  </div>
  <div id="purchase-history-c<?php echo $c_id; ?>" class="accordion-body collapse in">
    <div class="accordion-inner">
      <?php echo $comment['text']; ?>
    </div>
  </div>
</div>
<?php endforeach; // comments ?>
<?php endif; // comments exist ?>

<?php if ($p['status'] == 'deleted') : ?>
<div class="accordion-group">
  <div class="accordion-heading">
    <a class="accordion-toggle" data-toggle="collapse" href="#purchase-history-d<?php echo $p_id; ?>">
      <i class="icon-remove"></i> <?php echo $p['deleted_time']; ?> : Deleted by <?php echo $housemates[$p['deleted_by']]['user_name']; ?>
    </a>
  </div>
  <div id="purchase-history-d<?php echo $p_id; ?>" class="accordion-body collapse">
    <div class="accordion-inner">
      This purchase was deleted.
    </div>
  </div>
</div>
<?php endif; // purchase deleted ?>

<?php endforeach; // purchases ?>

</div><!-- #purchase-history -->

<?php echo form_open("purchases/addcomment/$purchase_id", array('id'=>'comment_form','class'=>'')); ?>

<h3>Add New Comment or Dispute</h3>

<p>Enter your message below and create either a comment or dispute. <?php echo helptip('Dispute vs. Comment.', 'right'); ?></p>

  <div class="control-group">
    <div class="controls">
      <textarea rows="3" name="commenttext" id="commenttext" class="span5" placeholder="Enter comment or dispute text" ></textarea>
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
      <button class="btn btn-warning" type="submit" name="button_dispute"><i class="icon-legal"></i> Add Dispute</button>
      <button class="btn btn-inverse" type="submit" name="button_comment"><i class="icon-comment"></i> Add Comment</button>
    </div>
  </div>

</form>

