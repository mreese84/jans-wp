      <!-- footer -->
      <footer class="col-3 footer" role="contentinfo">

        <div class="col footer-resources">

          <?php
            $mailchimpreg = do_shortcode( '[mc4wp_form]' );
            echo $mailchimpreg;
          ?>

          <!--<form id="footer-newsletter" action="index.html" method="post">
            <input type="text" name="newsletter-signup" value="Sign up for our newsletter!">
            <button type="button" class="btn btn-green"><i class="fa fa-send"></i></button>
          </form>-->
          <div class="online-order">
            <a class="btn" href="#">Order Online!</a>
          </div>
        </div>
        <div class="col connect">
          <ul>
            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa fa-instagram" ></i></a></li>
            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
          </ul>
        </div>
        <div class="col contact-info">
          <dl>
            <dd itemprop="address" itemtype="http://data-vocabulary.org/Address">
              <span itemprop="name"><b>Jan's On 4th</b></span><br>
              <span itemprop="street-address">10004 4th Stree, NW</span><br>
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
    (function(f,i,r,e,s,h,l){i['GoogleAnalyticsObject']=s;f[s]=f[s]||function(){
    (f[s].q=f[s].q||[]).push(arguments)},f[s].l=1*new Date();h=i.createElement(r),
    l=i.getElementsByTagName(r)[0];h.async=1;h.src=e;l.parentNode.insertBefore(h,l)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-XXXXXXXX-XX', 'yourdomain.com');
    ga('send', 'pageview');
    </script>

  </body>
</html>
