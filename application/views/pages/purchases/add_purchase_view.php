<?php
  //d(isset($purchase_id) ? $purchase_id : 'N/A', 'purchase_id');
  //d($housemates, 'housemates');
  //d($user, 'user');
  //d(isset($edit) ? $edit : 'N/A', 'edit');
  //d(isset($repop) ? $repop : 'N/A', 'repop');
?>


<?php echo form_open('purchases/add', array('id'=>'purchase_form','class'=>'form-horizontal')); ?>

<?php
function form_err($err) {
    return '<div class="alert alert-error">'.$err.'</div>';
}
// TODO - print errors next to the appropriate field, instead of all at top
if (isset($error) && is_array($error)) {
  foreach ($error as $err_type => $err) {
    echo form_err($err);
  }
} ?>

<?php if (isset($edit)) : ?>

  <input type="hidden" name="edit_id" value="<?php echo $purchase_id; ?>" />

<?php endif; ?>

  <div class="control-group">
  	<?php //echo form_err('description'); ?>
    <label class="control-label" for="description">Description</label>
    <div class="controls">
      <input type="text" name="description" id="description" placeholder="e.g. Electricity Bill" value="<?php echo (isset($repop['description']) ? $repop['description'] : ''); ?>" size="50" />
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="payer">Who Paid?</label>
    <div class="controls">
      <?php //echo form_err('payer'); ?>
      <select name="payer" id="payer">
<?php foreach ($housemates as $housemate) : ?>
        <option value="<?php echo $housemate['user_id']; ?>" <?php echo ((isset($repop['payer'])) && (($housemate['user_id'] == $repop['payer']) || ($housemate['user_id'] == $user['user_id']))) ? ' selected="selected"' : ''; ?>><?php echo $housemate['user_name']; ?></option>
<?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="control-group">
    <label class="control-label" for="purchase_date">Purchase Date</label>
    <div class="controls">
      <div class="input-prepend">
        <!--<label for="purchase_date">TODO - this breaks the datepicker -->
          <span class="add-on"><i class="icon-calendar"></i></span><input type="text" name="purchase_date" class="datepicker" id="purchase_date" placeholder="Click to select date" value="" />
        <!--</label>-->
      </div>
    </div>
  </div>

  <div class="control-group">
    <div class="controls btn-group" id="btns" data-toggle="buttons-radio">
  	  <input type="button" class="btn active" id="btn-split-even" value="Even Split" />
      <input type="button" class="btn" id="btn-split-custom" value="Custom Split" />
      <!--<input type="button" class="btn" id="btn-split-date" value="Date Split" />-->
      <!--<input type="button" class="btn" id="btn-split-percent" value="Percentage Split" />-->
    </div>
  </div>
  <div style="display: none;">
      <input type="radio" name="split_type" id="split_type_even" value="even" checked>
      <input type="radio" name="split_type" id="split_type_custom" value="custom">
      <input type="radio" name="split_type" id="split_type_date" value="date">
      <input type="radio" name="split_type" id="split_type_percent" value="percent">
  </div>

  <div class="control-group">
    <div class="controls">
      <span id="split_info_even" class="help-inline">Each housemate pays an <strong>equal share</strong> of the total.</span>
      <span id="split_info_custom" class="help-inline">Each housemate pays a <strong>custom amount</strong>, as specified below.</span>
      <span id="split_info_percent" class="help-inline">Each housemate pays a <strong>percentage</strong> of the total.</span>
      <span id="split_info_date" class="help-inline">Each housemate pays for <strong>how long</strong> they were involved.</span>
    </div>
  </div>

  <div id="total_price_panel" class="control-group">
    <label class="control-label" for="total_price">Total Price</label>
    <div class="controls">
      <div class="input-prepend">
        <label for="total_price">
          <span class="add-on">£</span><input class="input-mini" type="text" name="total_price" id="total_price" placeholder="0.00" value="<?php echo set_value('total_price', (isset($repop['total_price']) && is_numeric($repop['total_price'])) ? number_format($repop['total_price'], 2) : ''); ?>" size="50" />
          <span class="help-inline" id="price_split_indicator"></span>
        </label>
      </div>
    </div>
  </div>

  <div id="tab-even">
    <div class="control-group">
      <label class="control-label" for="payees">Payees</label>
      <div class="controls">

<?php foreach ($housemates as $housemate) : ?>
<?php
// Determine whether or not to check this housemate by default
$checked = FALSE;
if (isset($repop['split_type'])) {
  if ($repop['split_type'] == 'even') {
    if (isset($repop['payees']) && is_array($repop['payees'])) {
      if (in_array($housemate['user_id'], array_keys($repop['payees']))) {
          $checked = TRUE;
      }
    }
  }
} else {
  // check all housemates by default
  $checked = TRUE;
}
?>
        <label class="checkbox inline">
          <input type="checkbox" name="payees[]" class="input_payees" id="check_<?php echo $housemate['user_id']; ?>" value="<?php echo $housemate['user_id']; ?>"<?php echo ($checked ? ' checked="checked"' : '') ?>> <?php echo $housemate['user_name']; ?>
        </label>
 <?php endforeach; ?>
      </div>
      <script>
        // Update per-person price display when ticking/unticking checkboxes.
        function updatePriceSplitIndicator(hideme) {
          var help_text = $("#price_split_indicator");
          var total_price = $("#total_price").val().trim();
          var num_payees = $(".input_payees:checked").length;
          if (total_price === '' || total_price == '0') {
            help_text.html('<span class="text-warning">Enter a total price to be paid.</span>');
          } else if ((parseFloat(total_price).toString() !== total_price) && (parseFloat(total_price).toFixed(2).toString() !== total_price)) {
            help_text.html('<span class="text-warning">Enter a numeric price value.</span>');
          } else if (num_payees === 0) {
            help_text.html('<span class="text-warning">Choose at least one payee.</span>');
          } else {
            help_text.html("(= <strong>£" + (total_price / num_payees).toFixed(2) + "</strong> per person)");
          }
        }
        $(".input_payees").change(function() {
          updatePriceSplitIndicator();
        });
        $("#total_price").change(function() {
          updatePriceSplitIndicator();
        });
<?php if (isset($repop)) : ?>
        updatePriceSplitIndicator();
<?php endif; ?>
      </script>
    </div>
  </div>

  <div id="tab-custom">
<?php foreach ($housemates as $housemate) : ?>
    <div class="control-group">
      <label class="control-label" for="price_custom_<?php echo $housemate['user_id']; ?>"><?php echo $housemate['user_name']; ?></label>
      <div class="controls">
        <div class="input-prepend">
          <label for="price_custom_<?php echo $housemate['user_id']; ?>">
            <span class="add-on">£</span><input class="input-mini" type="text" name="price_custom[<?php echo $housemate['user_id']; ?>]" id="price_custom_<?php echo $housemate['user_id']; ?>" placeholder="0.00" value="<?php echo (isset($repop['payees'][$housemate['user_id']]) ? number_format((double)$repop['payees'][$housemate['user_id']], 2) : ''); ?>" />
          </label>
        </div>
      </div>
    </div>
<?php endforeach; ?>
  </div>

  <div id="tab-date">
    <div class="control-group">
      <label class="control-label" for="date_from">Dates:</label>
      <div class="controls">
        From&nbsp;<input class="input-medium" type="date" name="date_from" id="date_from" value="..." />
        to&nbsp;<input class="input-medium" type="date" name="date_to" id="date_to" value="..." />
      </div>
    </div>
<?php foreach ($housemates as $housemate) : ?>
    <div>
      <label class="control-label" for="price_date_<?php echo $housemate['user_id']; ?>"><?php echo $housemate['user_name']; ?></label>
      <div class="controls">
        <div class="input-prepend">
          <label for="price_date_<?php echo $housemate['user_id']; ?>">
            <input type="text" name="price_date[<?php echo $housemate['user_id']; ?>]" id="price_date_<?php echo $housemate['user_id']; ?>" placeholder="0.00" value="..." />
          </label>
        </div>
      </div>
    </div>
<?php endforeach; ?>
  </div>

  <div id="tab-percent">
<?php foreach ($housemates as $housemate) : ?>
    <div class="control-group">
      <label class="control-label" for="price_percent_<?php echo $housemate['user_id']; ?>"><?php echo $housemate['user_name']; ?></label>
      <div class="controls">
        <div class="input-append">
          <label for="price_percent_<?php echo $housemate['user_id']; ?>">
            <input class="input-mini" type="text" name="percent[<?php echo $housemate['user_id']; ?>]" id="price_percent_<?php echo $housemate['user_id']; ?>" placeholder="0" value="" /><span class="add-on">%</span>
          </label>
        </div>
      </div>
    </div>
<?php endforeach; ?>
  </div>

  <script>

  function chooseSplitType(tabname) {
   
    // Show only corresponding options
    $("[id^='tab-']").hide();
    $("#tab-" + tabname).show();

    // Show only corresponding info
    $("[id^='split_info_']").hide();
    $("#split_info_" + tabname).show();

    // Check relevant radio box
    $("#split_type_" + tabname).prop('checked', true);

    // Show "total" field when required
    if (tabname === "custom") {
      $("#total_price_panel").hide();
    } else {
      $("#total_price_panel").show();
    }

  };

  chooseSplitType('even');

  $('#btn-split-even').click(function (e) {
    e.preventDefault();
    chooseSplitType("even");
  });
  $('#btn-split-custom').click(function (e) {
    e.preventDefault();
    chooseSplitType("custom");
  });
  $('#btn-split-percent').click(function (e) {
    e.preventDefault();
    chooseSplitType("percent");
  });
  $('#btn-split-date').click(function (e) {
    e.preventDefault();
    chooseSplitType("date");
  });

  // Initially show desired split type
<?php if (isset($repop['split_type'])) : ?>
  $("#btn-split-<?php echo $repop['split_type']; ?>").trigger('click');
<?php else : ?>
  $("#btn-split-even").trigger('click');
<?php endif; ?>

    // Enhance Date input with date picker
    var purchase_date_input = $('#purchase_date').pickadate({
      format: 'dddd, dd mmm, yyyy', // visible format
      formatSubmit: 'yyyy/mm/dd' // submitted format
    });

    // Set date to the current or pre-set value
    var purchase_date = purchase_date_input.data('pickadate');
<?php $d = (isset($repop['date']) && $repop['date'] != '') ? $repop['date'] : date('Y/m/d'); ?>
    purchase_date.setDate( <?php echo substr($d, 0, 4); ?>, <?php echo substr($d, 5, 2); ?>, <?php echo substr($d, 8, 2); ?> );
  </script>

  <div class="control-group">
  	<?php //echo form_err('comment'); ?>
    <label class="control-label" for="comment">Extra Comments</label>
    <div class="controls">
      <textarea rows="3" name="comment" id="comment" class="span5" placeholder="(optional)" ><?php echo set_value('comment'); ?></textarea>
      <!--<input type="text" name="comment" id="comment" placeholder="(optional)" value="<?php echo set_value('comment'); ?>" size="250" />-->
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
<?php if (isset($edit)) : ?>
      <a href="<?php echo site_url('purchases/view/' . $purchase_id); ?>" class="btn btn-inverse">Cancel</a>
<?php endif; ?>
      <input class="btn btn-primary" type="submit"  
<?php if (isset($edit)) : ?>
  value="Save Changes"
<?php elseif (isset($repop) && isset($repop['description']) && ($repop['description'] == 'Repayment')) : ?>
  value="Add Repayment"
<?php else : ?>
  value="Add Purchase"
<?php endif; ?>
/>
    </div>
  </div>

  <script>

    // On form reset, manually reset the "split_type" buttons to defaults

  $("#btn-reset").click(function() {

    "use strict";

<?php if (isset($repop['split_type']) && ($repop['split_type'] == 'custom')) : ?>

    $('#btn-split-custom').trigger('click');

<?php else : ?>

    $('#btn-split-even').trigger('click');

<?php endif; ?>

  });

  </script>

</form>
