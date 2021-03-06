<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="subsubsub_section">
    <br class="clear" />
    <div class="section">
        <h3><?php printf(__('Products Filter Options v.%s', 'woocommerce-currency-switcher'), $this->version) ?></h3>
        <input type="hidden" name="woof_settings" value="" />
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1"><?php _e("Taxonomies", 'woocommerce-currency-switcher') ?></a></li>
                <li><a href="#tabs-2"><?php _e("Options", 'woocommerce-currency-switcher') ?></a></li>
                <li><a href="#tabs-3"><?php _e("Miscellaneous", 'woocommerce-currency-switcher') ?></a></li>
            </ul>

            <div id="tabs-1">
                <ul id="woof_options">
                    <?php
                    $taxonomies_tmp = $this->get_taxonomies();
                    $taxonomies = array();
                    //sort them as in options
                    if (!empty($this->settings['tax']))
                    {
                        foreach ($this->settings['tax'] as $key => $value)
                        {
                            $taxonomies[$key] = $taxonomies_tmp[$key];
                        }
                    }
                    //check for absent
                    foreach ($taxonomies_tmp as $key => $value)
                    {
                        if (!in_array(@$taxonomies[$key], $taxonomies_tmp))
                        {
                            $taxonomies[$key] = $taxonomies_tmp[$key];
                        }
                    }
                    //+++
                    foreach ($taxonomies as $key => $tax):
                        ?>
                        <li data-key="<?php echo $key ?>">
                            <a href="#" class="help_tip" data-tip="<?php _e("drag and drope", 'woocommerce-products-filter'); ?>"><img style="width: 22px; vertical-align: middle;" src="<?php echo WOOF_LINK ?>img/move.png" alt="<?php _e("move", 'woocommerce-products-filter'); ?>" /></a>&nbsp;
                            <select name="woof_settings[tax_type][<?php echo $key ?>]">
                                <?php foreach ($this->html_types as $type => $type_text) : ?>
                                    <option value="<?php echo $type ?>" <?php if (isset($woof_settings['tax_type'][$key])) echo selected($woof_settings['tax_type'][$key], $type) ?>><?php echo $type_text ?></option>
                                <?php endforeach; ?>
                            </select><img class="help_tip" data-tip="<?php _e('View of the taxonomies terms on the front', 'woocommerce-products-filter') ?>" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png" height="16" width="16" />&nbsp;
                            <?php
                            $max_height = 0;
                            if (isset($woof_settings['tax_block_height'][$key]))
                            {
                                $max_height = $woof_settings['tax_block_height'][$key];
                            }
                            ?>
                            <input type="text" name="woof_settings[tax_block_height][<?php echo $key ?>]" placeholder="<?php _e('Max height of  the block', 'woocommerce-products-filter') ?>" value="<?php echo $max_height ?>" />&nbsp;<img class="help_tip" data-tip="<?php _e('Max-height (px). Works if the taxonomy view is radio or checkbox. 0 means no max-height.', 'woocommerce-products-filter') ?>" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png" height="16" width="16" />&nbsp;
                            <input <?php echo(@in_array($key, @array_keys($this->settings['tax'])) ? 'checked="checked"' : '') ?> type="checkbox" name="woof_settings[tax][<?php echo $key ?>]" value="1" />&nbsp;
                            <?php echo $tax->labels->name ?>&nbsp;
                        </li>
                    <?php endforeach; ?>
                </ul><br />
            </div>

            <div id="tabs-2">
                <?php woocommerce_admin_fields($this->get_options()); ?>
            </div>

            <div id="tabs-3">


                <?php
                $skins = array(
                    'flat' => array(
                        'flat_aero',
                        'flat_blue',
                        'flat_flat',
                        'flat_green',
                        'flat_grey',
                        'flat_orange',
                        'flat_pink',
                        'flat_purple',
                        'flat_red',
                        'flat_yellow'
                    ),
                    'minimal' => array(
                        'minimal_aero',
                        'minimal_blue',
                        'minimal_green',
                        'minimal_grey',
                        'minimal_minimal',
                        'minimal_orange',
                        'minimal_pink',
                        'minimal_purple',
                        'minimal_red',
                        'minimal_yellow'
                    ),
                    'square' => array(
                        'square_aero',
                        'square_blue',
                        'square_green',
                        'square_grey',
                        'square_orange',
                        'square_pink',
                        'square_purple',
                        'square_red',
                        'square_yellow',
                        'square_square'
                    )
                );
                $skin = $woof_settings['icheck_skin'];
                ?>
                <h4 style="margin-bottom: 5px;"><?php _e('Radio and checkboxes skin', 'woocommerce-products-filter') ?></h4>
                <select name="woof_settings[icheck_skin]">
                    <?php foreach ($skins as $key => $schemes) : ?>
                        <optgroup label="<?php echo $key ?>">
                            <?php foreach ($schemes as $scheme) : ?>
                                <option value="<?php echo $scheme; ?>" <?php if ($skin == $scheme): ?>selected="selected"<?php endif; ?>><?php echo $scheme; ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>&nbsp;

                <?php if (class_exists('SitePress')): ?>
                <br />
                    <?php
                    $wpml_tax_labels = "";
                    if (isset($woof_settings['wpml_tax_labels']) AND is_array($woof_settings['wpml_tax_labels']))
                    {
                        foreach ($woof_settings['wpml_tax_labels'] as $lang => $words)
                        {
                            if (!empty($words) AND is_array($words))
                            {
                                foreach ($words as $key_word => $translation)
                                {
                                    $wpml_tax_labels.=$lang . ':' . $key_word . '^' . $translation . PHP_EOL;
                                }
                            }
                            //$first_value = reset($value); // First Element's Value
                            //$first_key = key($value); // First Element's Key
                        }
                    }
                    ?>

                    <h4 style="margin-bottom: 5px;"><?php _e('WPML taxonomies labels translations', 'woocommerce-products-filter') ?> <img class="help_tip" data-tip="Syntax:
                                                                                                                                           es:Locations^Ubicaciones
                                                                                                                                           es:Size^Tamaño
                                                                                                                                           de:Locations^Lage
                                                                                                                                           de:Size^Größe" src="<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/help.png" height="16" width="16" /></h4>
                    <textarea class="wide" style="height: 300px; width: 50%;" name="woof_settings[wpml_tax_labels]"><?php echo $wpml_tax_labels ?></textarea><br />
                    <i><?php _e('Use it if you can not translate your custom taxonomies labels and attributes labels by another plugins.', 'woocommerce-products-filter') ?></i>
                <?php endif; ?>
                <br /><br />
            </div>

        </div>

        <br />
        <a href="http://www.woocommerce-filter.com/" target="_blank" class="button">Look Demo site</a><br />
        <br /><br />

        <a href="http://codecanyon.net/item/woocommerce-currency-switcher/8085217?ref=realmag777" target="_blank" class="help_tip" data-tip="<?php _e("WooCommerce Currency Switcher", 'woocommerce-products-filter'); ?>"><img src="<?php echo WOOF_LINK ?>img/woocs_banner.jpg" alt="" /></a>&nbsp;<a href="http://codecanyon.net/item/wordpress-meta-data-taxonomies-filter/7002700?ref=realmag777" target="_blank" class="help_tip" data-tip="<?php _e("Wordpress Meta Data & Taxonomies Filter", 'woocommerce-products-filter'); ?>"><img src="<?php echo WOOF_LINK ?>img/mdtf_banner.jpg" alt="" /></a><br />

        <hr />


        <script charset="utf-8" type="text/javascript">
            jQuery(function () {
                jQuery("#tabs").tabs();
            });
            //***adv
            amzn_assoc_ad_type = "responsive_search_widget";
            amzn_assoc_tracking_id = "plugnet-20";
            amzn_assoc_link_id = "QXJKYATMAW467FXH";
            amzn_assoc_marketplace = "amazon";
            amzn_assoc_region = "US";
            amzn_assoc_placement = "";
            amzn_assoc_search_type = "search_widget";
            amzn_assoc_width = 900;
            amzn_assoc_height = 1500;
            amzn_assoc_default_search_category = "Software";
            amzn_assoc_default_search_key = "";
            amzn_assoc_theme = "light";
            amzn_assoc_bg_color = "FFFFFF";
        </script>
        <script src="//z-na.amazon-adsystem.com/widgets/q?ServiceVersion=20070822&Operation=GetScript&ID=OneJS&WS=1&MarketPlace=US"></script>

    </div>



</div>

