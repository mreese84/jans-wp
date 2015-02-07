<?php /* Template Name: Event Page */ get_header(); ?>

  <main role="main">
    <!-- section -->
    <section class="event-page" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <!--<section class="hero full-" style="background-image: url('<?php echo $image[0]; ?>');">
      <h2 class="hero-copy"><?php the_title(); ?></h2>
    </section>-->

    <?php if (have_posts()): while (have_posts()) : the_post(); ?>


      <section class="full-bar" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php the_content(); ?>
      </section>


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
