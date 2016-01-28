<?php get_header(); ?>

<div id="content">
	<?php $birdfarm_header_image = get_header_image(); ?>
	<?php if( ! is_paged() && ! empty( $birdfarm_header_image ) ): ?>
		<section id="wall">
			<div class="headerimage">
				<img src="<?php header_image(); ?>" alt="<?php bloginfo( 'name' ); ?>" >
			</div>
			<?php dynamic_sidebar( 'widget-area-header' ); ?>
		</section>
	<?php endif; ?>

	<section id="blog">
		<div class="container">
			<?php if( ! is_paged()  ): ?>
				<h2>お知らせ</h2>
			<?php endif; ?>

			<ul class="article">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'home' ); ?>
			<?php endwhile; ?>
			</ul>

			<?php if( is_paged()  ): ?>
				<?php $birdfield_pagination = get_the_posts_pagination( array(
						'mid_size'	=> 3,
						'screen_reader_text'	=> 'pagination',
					) );

					$birdfield_pagination = str_replace( '<h2 class="screen-reader-text">pagination</h2>', '', $birdfield_pagination );
					echo $birdfield_pagination; ?>
			<?php else: ?>
				<div class="more"><a href="<?php echo esc_url( home_url( '/' ) ); ?>/news/"><?php echo esc_html( get_post_type_object( 'news')->label ); ?>をもっと見る</a></div>
			<?php endif; ?>

		</div>
	</section>

	<?php if( ! is_paged() ): ?>

		<?php
		$myposts = get_posts(array(
			'post_type' => 'page',
			'tag' => 'information',
			'ordey by' => 'date'
		));
		foreach( $myposts as $post ):
			setup_postdata( $post );
		?>

		<section class="information">
			<div class="container">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php the_content(''); ?>
				<div class="more"><a href="<?php the_permalink(); ?>">「<?php the_title(); ?>」を詳しく見る</a></div>

				<?php
					if( !( false === strpos( $post->post_name, 'access' ) ) ){
						echo do_shortcode('[igarashi_nouen_map]');
					}
				?>

			</div>
		</section>

		<?php endforeach;
		wp_reset_postdata(); ?>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
