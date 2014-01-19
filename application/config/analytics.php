<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Analytics
|--------------------------------------------------------------------------
|
| Custom code here is inserted before closing '</head>' tag.
| For example, use the Google Analytics tracking code.
|
*/
$config['analytics_enabled'] = FALSE;

$config['analytics_code'] = <<<EOT
<script>
// paste your analytics code here...
</script>
EOT;
// new line required here