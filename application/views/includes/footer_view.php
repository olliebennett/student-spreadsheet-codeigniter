		</div><!-- #content -->

		<footer>
			<span class="left">&copy; <a href="http://olliebennett.co.uk/">Ollie Bennett</a> | v <?php echo $this->config->item('stsp_version') . (($this->config->item('stsp_beta')) ? ' <strong>BETA</strong>' : '') . (($this->config->item('stsp_demo')) ? ' DEMO' : ''); ?> | <a href="http://stsp.info/" title="Shortlink: STSP.info">stsp.info</a> | <a href="<?php echo base_url(); ?>help/disclaimer">Disclaimer</a> <a href="<?php echo base_url(); ?>help/privacy">Privacy</a></span>
			<span class="right">Page rendered in <strong>{elapsed_time}</strong> seconds.</span>
			<div class="clearfix"></div>
		</footer>
	</div><!-- #container -->
	
	<!-- Draw Cufon Fonts -->
	<script>Cufon.now();</script>
	
	<!-- jQuery Magic -->
	<script>
	// Initially hide username
	$('#user_name').hide();
	
	// Show name when hovering on #user_pic
	$("#user_info").hover(
		function () {
			$('#user_name').show();
		}, 
		function () {
			$('#user_name').hide();
		}
	);
	</script>
	
<?php d($user,'$user'); ?>
	
</body>
</html>