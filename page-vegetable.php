<?php get_header(); ?>

<div id="content">
	<div class="container">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php get_template_part( 'content', 'singular' ); ?>

		<?php 	$post_type = get_post_type_object( 'vegetables' );
			$args = array(
				'posts_per_page' => -1,
				'post_type' => 'vegetables',
				'post_status' => 'publish'
			);
			$the_query = new WP_Query($args);
			if ( $the_query->have_posts() ) :
		?>
				<ul class="vegetables-list">

				<?php
					while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<h3 class="entry-title"><a href="<?php the_permalink(); ?>" ><?php the_title(); ?></a></h3>
							<?php the_content(); ?>
						</li>
				<?php	endwhile; ?>

				</ul>
		<?php	endif;
			wp_reset_postdata();
		?>
	</article>

<?php endwhile; ?>
	</div>
</div>

<?php get_footer(); ?>
