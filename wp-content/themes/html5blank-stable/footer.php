      <!-- footer -->
      <footer class="col-3 footer" role="contentinfo">

        <div class="col footer-resources">

          <?php
            $mailchimpreg = do_shortcode( '[mc4wp_form]' );
            echo $mailchimpreg;
          ?>

        </div>
        <div class="col connect">
          <ul>
            <li><a href="https://www.facebook.com/pages/Jans-on-4th/124479960967926" target="new"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa fa-instagram" ></i></a></li>
            <li><a href="https://twitter.com/JanCanCook4You"><i class="fa fa-twitter"></i></a></li>
          </ul>
        </div>
        <div class="col contact-info">
          <dl>
            <dd itemprop="address" itemtype="http://data-vocabulary.org/Address">
              <span itemprop="name"><b>Jan's On 4th</b></span><br>
              <span itemprop="street-address">10004 4th St, NW</span><br>
              <span itemprop="locality">Albuquerque</span>,
              <span itemprop="region">NM</span>
              <span itemprop="postal-code">87114</span><br>
              <span itemprop="tel">505-350-0600</span>
            </dd>
          </dl>
        </div>

        <!-- copyright
        <p class="copyright">
          &copy; <?php echo date('Y'); ?> Copyright <?php bloginfo('name'); ?>. <?php _e('Powered by', 'html5blank'); ?>
          <a href="//wordpress.org" title="WordPress">WordPress</a> &amp; <a href="//html5blank.com" title="HTML5 Blank">HTML5 Blank</a>.
        </p>
        /copyright -->

      </footer>
      <!-- /footer -->

    </div>
    <!-- /wrapper -->

    <?php wp_footer(); ?>

    <!-- analytics -->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-59729218-1', 'auto');
      ga('send', 'pageview');
    </script>

  </body>
</html>
