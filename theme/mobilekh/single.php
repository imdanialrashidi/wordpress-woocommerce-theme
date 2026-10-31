<?php
/** Single post. @package mobilekh */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="container section single-post">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'page-article' ); ?>>
			<h1><?php the_title(); ?></h1>
			<div class="post-meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<span>·</span>
				<span><?php the_category( '، ' ); ?></span>
			</div>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="post-thumb"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>
			<div class="post-body"><?php the_content(); ?></div>
			<?php the_tags( '<div class="post-tags">', ' ', '</div>' ); ?>
		</article>
		<?php comments_template(); ?>
	<?php endwhile; ?>
</div>
<?php
get_footer();
