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
		</div>
		<div class="cntSearch">
			<h2 id="titleSearch"><? echo($title); ?></h2>
			<? echo($str); ?>	
		</div>
	</div>
</section>
