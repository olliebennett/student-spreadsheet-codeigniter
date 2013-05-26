
<!--
<div class="accordion" id="accordion2">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
        Collapsible Group Item #1
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse in">
      <div class="accordion-inner">
        Anim pariatur cliche...
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
        Collapsible Group Item #2
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner">
        Anim pariatur cliche...
      </div>
    </div>
  </div>
</div>
-->

<div class="accordion-group">

  <div class="accordion-heading">
    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
<?php if (isset($latest)) : ?>
      Purchase Information
<?php elseif (isset($purchase['edit_changes'])) : ?>
      Edit on <?php echo $purchase['added_time']; ?> by <?php echo $housemates[$purchase['added_by']]['user_name']; ?>...
<?php else : ?>
      Purchase Information
<?php endif; ?>
    </a>
  </div>
  
  <div id="collapseOne" class="accordion-body collapse in">
  
    <div class="accordion-inner">
    
      <ul>
<?php foreach ($purchase['edit_changes'] as $change) : ?>
        <li><?php echo $change; ?></li>
<?php endforeach; ?>
      <ul>
      
<?php $this->load->view('pages/purchases/includes/purchase_info_table.php', array('purchase' => $purchase, 'housemates' => $housemates)); ?>
      
    <p>This purchase was added using <?php echo ($purchase['split_type'] == 'even') ? 'an <em>even</em>' : (($purchase['split_type'] == 'custom') ? 'a <em>custom</em>' : 'an <b>UNKNOWN</b>'); ?> split. <?php echo helptip('When adding purchases, the price can be split either evenly between payers, or manually.'); ?></p>
    
  <?php if(isset($purchase['comments']) && count($purchase['comments']) >= 1): ?>
    
    <?php $this->load->view('pages/purchases/includes/purchase_info_comments.php', array('comments' => $purchase['comments'], 'housemates' => $housemates)); ?>
    
  <?php else : ?>
    
    <p>No comments have been made on this purchase.</p>
    
  <?php endif ?> 
  
    </div>
  </div>

</div>

    
