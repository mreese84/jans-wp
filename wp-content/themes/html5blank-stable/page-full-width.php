<?php /* Template Name: Full Width Page */ get_header(); ?>

  <main role="main">
    <!-- section -->
    <section>

    <section class="hero full-" style="background-image: url('<?php echo $image[0]; ?>');">
      <h2 class="hero-copy"><?php the_title(); ?></h2>
    </section>

    <?php if (have_posts()): while (have_posts()) : the_post(); ?>

      <!-- article -->
      <section class="article" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <aside class="share-tools">
          <ul>
            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa fa-twitter" ></i></a></li>
            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
          </ul>
        </aside>
        <article>
          <?php the_content(); ?>
        </article>
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
