<?php
d($purchases, 'purchases'); // plural, as we also receive old versions of purchase
//d($purchase_id, 'purchase_id');
//d($purchases[$purchase_id], 'this actual purchase');
//d($housemates, 'housemates');
//d($user,'user');
?>

<?php $purchase = $purchases[max(array_keys($purchases))]; // get latest version to display ?>

<?php $this->load->view('pages/purchases/includes/purchase_info_table.php', array('purchase' => $purchase, 'housemates' => $housemates)); ?>


<h3>Purchase History and Comments</h3>

<div class="accordion" id="purchase-history">

<?php foreach ($purchases as $p_id => $p) : // purchases ?>

<?php if ($p_id == min(array_keys($purchases))) : // is original version ?>
<div class="accordion-group">
  <div class="accordion-heading">
    <a class="accordion-toggle" data-toggle="collapse" data-parent="#purchase-history" href="#purchase-history-<?php echo $p_id; ?>">
      Added by XYZ on YYYY-MM-DD
    </a>
  </div>
  <div id="purchase-history-<?php echo $p_id; ?>" class="accordion-body collapse in">
    <div class="accordion-inner">
      Anim pariatur cliche...
    </div>
  </div>
</div>
<?php else : // is more recent version ?>
<h4>Edited by XYZ</h4>
<div class="accordion-group">
<ul>
<?php foreach ($p['edit_changes'] as $change) : // edit changes ?>
  <li><?php echo $change; ?></li>
<?php endforeach; // edit changes ?>
</ul>
</div>
<?php endif; ?>

<?php foreach ($p['comments'] as $comment) : // comments ?>
<h4>Comment: <?php echo $comment['text']; ?></h4>
<?php endforeach; // comments ?>


<?php endforeach; // purchases ?>

</div><!-- #purchase-history -->

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

