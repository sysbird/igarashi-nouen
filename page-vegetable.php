<?php get_header();
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>

<div id="content">
	<div class="container">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php if(1 == $paged ): ?>
			<?php get_template_part( 'content', 'singular' ); ?>
			<?php if ( wp_is_mobile() ): ?>
				<?php
					$page = get_page_by_path( 'calendar' );
				?>
				<p><a href="<?php echo get_the_permalink( $page->ID); ?>">&raquo; <?php echo $page->post_title; ?></a></p>
			<?php else: ?>
				<?php echo do_shortcode( '[igarashi_nouen_vegetables_calendar]' ); ?>
			<?php endif; ?>
		<?php endif; ?>

		<?php $posts_per_page = get_option( 'posts_per_page' );
			$offset = $posts_per_page * ( $paged -1 );

			$args = array(
				'posts_per_page'	=> $posts_per_page,
				'offset'			=> $offset,
				'post_type'		=> 'vegetables',
				'post_status'		=> 'publish'
			);

			$the_query = new WP_Query($args);
			if ( $the_query->have_posts() ) :
		?>
				<div class="tile masonry">

					<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<?php get_template_part( 'content', 'vegetables' ); ?>
					<?php endwhile; ?>

					<div class="pagenation more"><?php next_posts_link( 'もっとみる', $the_query->max_num_pages ) ?><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/loading.gif" alt="" class="loading"></div>
				</div>

		<?php endif;
			wp_reset_postdata();
		?>

	</article>

<?php endwhile; ?>

	</div>
</div>

<?php get_footer(); ?>
