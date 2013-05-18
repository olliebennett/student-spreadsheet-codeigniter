    </div><!-- #content -->

    <footer>
      <span class="pull-left">&copy; <a href="http://olliebennett.co.uk/">Ollie Bennett</a> | v <?php echo $this->config->item('stsp_version') . (($this->config->item('stsp_beta')) ? ' <strong>BETA</strong>' : '') . (($this->config->item('stsp_demo')) ? ' DEMO' : ''); ?> | <a href="<?php echo base_url(); ?>help/disclaimer">Disclaimer</a> | <a href="<?php echo base_url(); ?>help/privacy">Privacy</a></span>
      <?php if (ENVIRONMENT != 'production') { ?>
        <span class="pull-right"><strong>{elapsed_time}</strong> sec : <?php echo ENVIRONMENT; ?></span>
      <?php } ?>
      <div class="clearfix"></div>
    </footer>
  </div><!-- #container -->

  <!-- Draw Cufon Fonts -->
  <script>Cufon.now();</script>

  <!-- jQuery -->
  <script>

  // Initially hide username
  $('#user_name').hide();

  // Show name when hovering
  $("#user_info").hover(
    function () {
      $('#user_name').show();
    },
    function () {
      $('#user_name').hide();
    }
  );

  $(document).ready(function() {

    // Display times as time ago
    $("time.timeago").timeago();

    // Show tooltips when hovering
    $('a.helptip').tooltip();

<?php if ($this->uri->segment(1) == 'purchases') : ?>

    // Initially hide all purchase details
    $('table#purchases tr.purchase-details').hide();
    // Show/Hide details when clicking overview
    $("table#purchases tr.purchase-overview").click(function() {
      $(this).next().toggle();
    });
    $("table#purchases tr.purchase-details").click(function() {
      // Send user to view this purchase
      window.location.href = "<?php echo site_url('purchases/view'); ?>/" + $(this).data('id');
    });

    // Activate tooltips on balances graph
    $('div#balances-graph .baltip').hover(
      function () {
        //alert('showing');
        //alert(this);
        $(this).children().tooltip('show');
      },
      function () {
        $(this).children().tooltip('hide');
      }
    );

<?php endif; ?>

<?php if ($this->uri->segment(1) == 'register') : ?>

    // Handle multi-select user registration
    $("#register-select").select2();

<?php endif; ?>

      $('.helptip').click(function(e) {
        e.preventDefault();
      });
     });
  </script>

</body>
</html>
