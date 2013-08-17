<p class="lead">Welcome to The Student Spreadsheet...</p>

<?php if (ENVIRONMENT == 'demo') : ?>

<p class="text-info">
<i class="icon-asterisk"></i> This is the <strong>DEMO</strong> version.<br />
<i class="icon-asterisk"></i> You have been logged in as a sample user.<br />
<i class="icon-asterisk"></i> Any changes you make will be wiped hourly (<time class="timeago" datetime="<?php $now = time(); echo strftime('%Y-%m-%dT%H:%M:%SZ', $now + (3600 - $now % 3600)); ?>">on the hour, every hour</time>).<br />
<i class="icon-asterisk"></i> Explore the site's features with no hassle!
</p>

<?php elseif (ENVIRONMENT == 'testing') : ?>

<p class="text-error">This is the <strong>BETA</strong> version; all data may be deleted at any time.</p>

<p>Please use the <a href="http://studentspreadsheet.com/">live version</a> instead.</p>

<?php endif; ?>

<ul>
	<li>a free, easy-to-use online expenses manager.</li>
	<li>removes the hassle of remembering who owes what.</li>
	<li>share costs with your housemates - without the maths!</li>
</ul>

<p>It's as simple as...</p>
<ol>
	<li>Record payments such as utility bills, including the price and which friends should contribute.</li>
	<li>Track how much you owe (or are owed) in total.</li>
	<li>Automatically calculate out who owes what, and settle-up in simple payments at the end!</li>
</ol>

<p>
<?php if (ENVIRONMENT == 'demo') : ?>
Use the navigation links above to explore the demo, or 
<?php else : ?>
<a href="http://demo.studentspreadsheet.com/" class="btn btn-primary">Explore the DEMO</a> or 
<?php endif; ?>
<a href="http://studentspreadsheet.com/register" class="btn btn-success">Get Started!</a></p>

<p>An easy example:<p>
<ul>
	<li>Three flatmates - Alice, Bob and Charlie - live together in shared accommodation.</li>
	<li>Bob pays a £60 electricity bill.</li>
	<li>Alice buys the TV license for £120.</li>
	<li>Charlie pays £30/month for internet.</li>
	<li>Three months later, they move out. Student Spreadsheet tells them the quickest way to repay:
		<ul>
			<li>Bob pays Alice £30.</li>
		</ul>
	</li>
	<li>All housemates are now even!</li>
</ul>
