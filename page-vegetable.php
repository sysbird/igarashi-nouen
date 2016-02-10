<?php get_header();

global $wp_rewrite;
$infinite_timeline_next = 1;
if( isset( $_GET[ 'infinite_timeline_next' ] ) ) {
	$infinite_timeline_next = $_GET[ 'infinite_timeline_next' ];
}
?>

<div id="content">
	<div class="container">

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php if(1 == $infinite_timeline_next ): ?>
			<?php get_template_part( 'content', 'singular' ); ?>
		<?php endif; ?>

		<div id="all-vegetables">

			<?php 	$post_type = get_post_type_object( 'vegetables' );

				//$posts_per_page = get_option( 'posts_per_page' );
				$posts_per_page = 3;
				$offset = $posts_per_page * ( $infinite_timeline_next -1 );

				$args = array(
					'posts_per_page'	=> $posts_per_page,
					'offset'			=> $offset,
					'post_type'			=> 'vegetables',
					'post_status'		=> 'publish'
				);

				$the_query = new WP_Query($args);
				if ( $the_query->have_posts() ) :
			?>

				<div class="box">

					<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<h3 class="entry-title"><a href="<?php the_permalink(); ?>" ><?php the_title(); ?></a></h3>
							<?php the_content(); ?>
						</div>
					<?php endwhile; ?>

				</div>

			<?php endif;
				wp_reset_postdata();
			?>

			<?php
				$url = add_query_arg( array( 'infinite_timeline_next' => ( $infinite_timeline_next + 1 ) ) );
				$rewrite_url = ( $wp_rewrite->using_permalinks() ) ? '<div class="rewrite_url"></div>' : '';
			?>

			<div class="pagenation more"><a href="<?php echo $url; ?>">もっと見る</a><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/loading.gif" alt="" class="loading"><?php echo $rewrite_url; ?></div>

		</div>
	</article>

<?php endwhile; ?>
	</div>
</div>

<?php get_footer(); ?>
