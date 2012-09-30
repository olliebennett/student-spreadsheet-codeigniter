<?php $this->load->view('includes/header_view',$slug); ?>

			<?php 
				//echo validation_errors('<div class="error">', '</div>');
				

				
				// Flash Errors
				if ($this->session->flashdata('error')) {
					echo '<div class="error">';
					echo $this->session->flashdata('error');
					echo '</div>';
				}
				if ($this->session->flashdata('notice')) {
					echo '<div class="notice">';
					echo $this->session->flashdata('notice');
					echo '</div>';
				}
				if ($this->session->flashdata('info')) {
					echo '<div class="info">';
					echo $this->session->flashdata('info');
					echo '</div>';
				}
				if ($this->session->flashdata('success')) {
					echo '<div class="success">';
					echo $this->session->flashdata('success');
					echo '</div>';
				}
				
				

			?>
			


			<h2><?php echo (isset($title) ? $title : ucwords(str_replace('_',' ',$slug))); ?></h2>
			

			<?php // subnav
				if (isset($subnav)) {
					echo '<nav id="sub">
					';
					foreach ($subnav as $k => $v) {
						echo '<a href="'. site_url($slug.'/'.$k) . '">'.$v.'</a> ';
					}
					echo '
					</nav>';
				}
							
			?>
			
			
	<?php // include relevant page view

	$view = ((isset($view)) ? $view : $slug);
	switch ($view) {
		case 'purchases':
		$this->load->view('pages/'.$view.'_view',$purchases);
		break;
		default:
		$this->load->view('pages/'.$view.'_view');
	}

	?>

<?php $this->load->view('includes/footer_view'); ?>