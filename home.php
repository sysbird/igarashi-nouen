<?php
/*
The home template file.
*/
get_header(); ?>

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
				<?php get_template_part( 'content', get_post_format() ); ?>
			<?php endwhile; ?>
			</ul>

			<?php if( is_paged()  ): ?>
				<?php $birdfield_pagination = get_the_posts_pagination( array(
						'mid_size'	=> 3,
						'prev_text'	=> esc_html__( 'Previous page', 'birdfield' ),
						'next_text'	=> esc_html__( 'Next page', 'birdfield' ),
						'screen_reader_text'	=> 'pagination',
					) );

				$birdfield_pagination = str_replace( '<h2 class="screen-reader-text">pagination</h2>', '', $birdfield_pagination );
				echo $birdfield_pagination; ?>
			<?php else: ?>
				<div class="more">
					<?php next_posts_link( 'お知らせをもっと見る' ); ?>
				</div>
			<?php endif; ?>

		</div>
	</section>

	<?php if( ! is_paged() ): ?>
		<section id="about">
			<div class="container">

				<?php
				$myposts = get_posts(array(
					'post_type' => 'page',
					'name' => 'about',
					'posts_per_page' => '1',
				));
				?>
				<?php
					foreach( $myposts as $post ):
						setup_postdata( $post );
				?>
					<div class="page">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_content(); ?>
						<div class="more"><a href="<?php the_permalink(); ?>">続きを見る</a>
					</div>
				<?php endforeach;
				wp_reset_postdata(); ?>

			</div>
		</section>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
