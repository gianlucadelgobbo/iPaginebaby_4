<section id="explore">
	<div class="wrapper">
		<div id="loading"></div>
		<nav id="main">
			<ul>
				<li><a class="active" href="/">home</a></li>
				<li><a href="/support"><? echo( $site->dataObj->getLabel("Support")); ?></a></li>
				<li><a href="/about-us"><? echo( $site->dataObj->getLabel("About Us")); ?></a></li>
				<li><a href="/blog"><? echo( $site->dataObj->getLabel("Blog")); ?></a></li>
			</ul>
		</nav>
		<div class="titles">
			<h1><? echo( $site->dataObj->getLabel("PagineBaby")); ?></h1>
			<p class="abstract"><? echo( $site->dataObj->getLabel("Because You’re Going Places")); ?></p>
			<nav id="searchtools">
				<ul>
					<li><a class="active" href="/explore/"><? echo( $site->dataObj->getLabel("Find by city")); ?></a></li>
					<li><a href="#" onclick="$('#titleSearch').html($(this).html());$('#search2').hide();$('#search').hide();nearMe(0);"><? echo( $site->dataObj->getLabel("Find Near Me")); ?></a></li>
					<li><a href="#" onclick="$('#titleSearch').html($(this).html());$('#search').show();$('#search2').hide();$('#search-results').empty();"><? echo( $site->dataObj->getLabel("Find by keyword")); ?></a></li>
					<li><a href="#" onclick="$('#titleSearch').html($(this).html());$('#search2').show();$('#search').hide();$('#search-results').empty();"><? echo( $site->dataObj->getLabel("Find by category")); ?></a></li>
				</ul>
			</nav>
		</div>
		<div class="cntSearch">
			<h2 id="titleSearch"><? echo($title); ?></h2>
			<form id="search" action="#" style="display:none;">
				<ul class="edgetoedge">
			  		<li><input type="text" name="search-text" placeholder="Cerca" id="search-text" /></li>
					<script type="text/javascript">document.getElementById("search-text").placeholder = getLabel("Cerca");</script>
				</ul>
			</form>
			<form id="search2" action="#" style="display:none;">
				<script type="text/javascript">document.write(searchCat)</script>
			</form>
			<ul id="search-results">
				<? echo($str); ?>	
			</ul>
		</div>
	</div>
</section>
