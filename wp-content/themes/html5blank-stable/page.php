<?php get_header(); ?>

  <main role="main">
    <!-- section -->
    <section>

      <!--<h1><?php the_title(); ?></h1>-->

    <!--<?php if (have_posts()): while (have_posts()) : the_post(); ?>-->

      <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>

      <section class="hero" style="background-image: url('<?php echo $image[0]; ?>');">
        <h2 class="hero-copy"><?php echo get_post_meta($post->ID, 'header-tag', true); ?>
</h2>
      </section>

      <!-- article -->
      <section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <?php the_content(); ?>

      </section>
      <!-- /article -->

    <?php endwhile; ?>

    <?php else: ?>

      <!-- article -->
      <article>

        <h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>

      </article>
      <!-- /article -->

    <?php endif; ?>

    </section>
    <!-- /section -->
  </main>

<?php get_footer(); ?>
