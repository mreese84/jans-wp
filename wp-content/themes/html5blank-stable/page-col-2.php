<?php /* Template Name: Commerce Two Column */ get_header(); ?>

  <main role="main">
    <!-- section -->
    <section>

    <section class="hero-solid internal">
      <h2 class="hero-copy"><?php the_title(); ?></h2>
    </section>

    <?php if (have_posts()): while (have_posts()) : the_post(); ?>

      <!-- article -->
      <section class="col-2" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <article class="the-content">
          <?php the_content(); ?>
        </article>
        <?php
          /**
           * woocommerce_sidebar hook
           *
           * @hooked woocommerce_get_sidebar - 10
           */
           do_action( 'woocommerce_sidebar' );
        ?>
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
