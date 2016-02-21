<?php get_header();
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
?>

<div id="content">
	<div class="container">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php if(1 == $paged ): ?>
			<?php get_template_part( 'content', 'singular' ); ?>
		<?php endif; ?>
		<div class="tile masonry">

			<?php 	$post_type = get_post_type_object( 'vegetables' );

				$posts_per_page = get_option( 'posts_per_page' );
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
					<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

							<a href="<?php the_permalink(); ?>">
								<?php if( has_post_thumbnail() ): ?>
									<div class="entry-eyecatch"><?php the_post_thumbnail(  get_the_ID(), 'large' ); ?></div>
								<?php endif; ?>
								<header class="entry-header"><h3 class="entry-title"> <?php the_title(); ?> </h3></header>
							</a>
						</div>
					<?php endwhile; ?>

					<div class="pagenation more"><?php next_posts_link( 'もっとみる', $the_query->max_num_pages ) ?><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/loading.gif" alt="" class="loading"></div>


			<?php endif;
				wp_reset_postdata();
			?>

		</div>
	</article>

<?php endwhile; ?>

	</div>
</div>

<?php get_footer(); ?>
