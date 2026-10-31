<?php
/** Archive (categories, tags, dates). @package mobilekh */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="container section">
	<h1 class="shop-title"><?php the_archive_title(); ?></h1>
	<?php the_archive_description( '<p class="muted">', '</p>' ); ?>
	<?php if ( have_posts() ) : ?>
	<div class="blog-grid">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'blog-card' ); ?>>
				<a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) );
					}
					?>
				</a>
				<div class="blog-card-body">
					<div class="post-meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					</div>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<p class="muted"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
	<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'موردی یافت نشد.', 'mobilekh' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
