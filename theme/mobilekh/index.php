<?php
/** Fallback template. @package mobilekh */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="container section">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<?php the_excerpt(); ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'موردی یافت نشد.', 'mobilekh' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
