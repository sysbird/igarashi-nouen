<?php get_header(); ?>

<div id="content">
	<div class="container">

		<article class="hentry">
			<header class="content-header">
				<h1 class="content-title">
				<?php $cat_news = get_category_by_slug( 'news' ) ?>
				<?php echo esc_html( get_post_type_object( 'news')->label ); ?>
				</h1>
			</header>

			<?php if ( have_posts() ) : ?>
				<ul class="article">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'content', get_post_format() ); ?>
					<?php endwhile; ?>
				</ul>

				<?php $igr_pagination = get_the_posts_pagination( array(
						'mid_size'	=> 3,
						'screen_reader_text'	=> 'pagination',
					) );

					$igr_pagination = str_replace( '<h2 class="screen-reader-text">pagination</h2>', '', $igr_pagination );
					echo $igr_pagination; ?>
			<?php endif; ?>
		</article>
	</div>
</div>

<?php get_footer(); ?>
