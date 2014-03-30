<h3>Welcome to The Student Spreadsheet...</h3>

<?php if (ENVIRONMENT == 'demo') : ?>

<p class="text-info">
<i class="icon-asterisk"></i> This is the <strong>DEMO</strong> version.<br />
<i class="icon-asterisk"></i> You have been logged in as a sample user.<br />
<i class="icon-asterisk"></i> Any changes you make will be wiped hourly (<time class="timeago" datetime="<?php $now = time(); echo strftime('%Y-%m-%dT%H:%M:%SZ', $now + (3600 - $now % 3600)); ?>">on the hour, every hour</time>).<br />
<i class="icon-asterisk"></i> Explore the site's features with no hassle!
</p>

<?php elseif (ENVIRONMENT == 'beta') : ?>

<p class="text-error">This is the <strong>BETA</strong> version; all data may be deleted at any time.</p>

<p>Please use the <a href="http://studentspreadsheet.com/">live version</a> instead.</p>

<?php endif; ?>

<ul>
	<li>a free, easy-to-use online expenses manager.</li>
	<li>remove the hassle of remembering who owes what.</li>
	<li>share costs with your housemates - without the maths!</li>
</ul>

<p>
<?php if ((ENVIRONMENT != 'demo') && @is_null($user)) : ?>
<a href="http://studentspreadsheet.com/auth" class="btn btn-primary">Login</a> 
<?php endif; ?>
<?php if (ENVIRONMENT == 'demo') : ?>
<br />Use the navigation links above to explore the demo, or 
<?php else : ?>
<a href="http://demo.studentspreadsheet.com/purchases" class="btn btn-inverse">Explore the DEMO</a> 
<?php endif; ?>
<a href="http://studentspreadsheet.com/register" class="btn btn-success">Register!</a>
</p>

<p><strong>How it works:</strong></p>
<ol>
	<li>Record payments such as utility bills, including the price and which friends should contribute.</li>
	<li>Track how much you owe (or are owed) in total.</li>
	<li>Automatically calculate who owes what, and settle-up in simple payments at the end!</li>
</ol>

<p><strong>An example:</strong><p>
<ul>
	<li>Three flatmates - <em>Alice</em>, <em>Bob</em> and <em>Charlie</em> - live together in shared accommodation.</li>
	<li>Bob pays a <?php echo render_price(60); ?> electricity bill.</li>
	<li>Alice buys the TV license for <?php echo render_price(120); ?>.</li>
	<li>Charlie pays <?php echo render_price(30); ?>/month for internet.</li>
	<li>Three months later, they move out. Student Spreadsheet tells them the quickest way to repay:
		<ul>
			<li>Bob pays Alice <?php echo render_price(30); ?>.</li>
		</ul>
	</li>
	<li>All housemates are now even!</li>
</ul>
