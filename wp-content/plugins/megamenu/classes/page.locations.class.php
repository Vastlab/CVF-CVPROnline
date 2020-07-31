<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists( 'Mega_Menu_Menu_Locations' ) ) :

/**
 * Handles the Mega Menu > Menu Settings page
 */
class Mega_Menu_Menu_Locations {


    /**
     * Constructor
     *
     * @since 2.8
     */
    public function __construct() {
        add_action( 'admin_post_megamenu_add_menu_location', array( $this, 'add_menu_location') );
        add_action( 'admin_post_megamenu_delete_menu_location', array( $this, 'delete_menu_location') );
        add_action( 'admin_post_megamenu_save_menu_location', array( $this, 'save_menu_location') );
        add_filter( 'megamenu_menu_tabs', array( $this, 'add_locations_tab' ), 999 );
        add_action( 'megamenu_page_menu_locations', array( $this, 'menu_locations_page'));
    }


    /**
     * Add a new menu location.
     *
     * @since 2.8
     */
    public function add_menu_location() {
        check_admin_referer( 'megamenu_add_menu_location' );

        $locations = get_option( 'megamenu_locations' );
        $next_id = $this->get_next_menu_location_id();
        $new_menu_location_id = "max_mega_menu_" . $next_id;
        $locations[$new_menu_location_id] = "Max Mega Menu Location " . $next_id;

        update_option( 'megamenu_locations', $locations );

        do_action("megamenu_after_add_menu_location");

        $redirect_url = add_query_arg(
            array(
                'page' => 'maxmegamenu_menu_locations',
                'add_location' => 'true'
            ), admin_url("admin.php")
        );

        $this->redirect( $redirect_url );

    }


    /**
     * Delete a menu location.
     *
     * @since 2.8
     */
    public function delete_menu_location() {
        check_admin_referer( 'megamenu_delete_menu_location' );

        $locations = get_option( 'megamenu_locations' );
        $location_to_delete = esc_attr( $_GET['location'] );

        if ( isset( $locations[ $location_to_delete ] ) ) {
            unset( $locations[ $location_to_delete ] );
            update_option( 'megamenu_locations', $locations );
        }

        do_action("megamenu_after_delete_menu_location");
        do_action("megamenu_delete_cache");

        $redirect_url = add_query_arg(
            array(
                'page' => 'maxmegamenu_menu_locations',
                'delete_location' => 'true'
            ), admin_url("admin.php")
        );

        $this->redirect( $redirect_url );

    }

    /**
     * Save a menu location
     *
     * @since 2.0
     */
    public function save_menu_location() {
        check_admin_referer( 'megamenu_save_menu_location' );

        $location = false;

        if ( isset( $_POST['location'] ) ) {
            $location = esc_attr( $_POST['location'] );
        }

        if ( $location ) {
            $submitted_settings = apply_filters( "megamenu_submitted_settings_meta", $_POST['megamenu_meta'] );

            if ( isset( $submitted_settings[$location]['enabled'] ) ) {
                $submitted_settings[$location]['enabled'] = '1';
            }
            
            if ( ! get_option( 'megamenu_settings' ) ) {
                update_option( 'megamenu_settings', $submitted_settings );
            } else {
                $existing_settings = get_option( 'megamenu_settings' );
                $new_settings = array_merge( $existing_settings, $submitted_settings );

                update_option( 'megamenu_settings', $new_settings );
            }

            do_action( "megamenu_after_save_settings" );
            do_action( "megamenu_delete_cache" );
        }

        /* Save custom location description **/
        if ( isset( $_POST['custom_location'] ) && is_array( $_POST['custom_location'] ) ) {
            $location = array_map( 'sanitize_text_field', $_POST['custom_location'] );
            $locations = get_option('megamenu_locations');
            $new_locations = array_merge( (array)$locations, $location );

            update_option( 'megamenu_locations', $new_locations );
        }

        $redirect_url = add_query_arg(
            array(
                'page' => 'maxmegamenu_menu_locations',
                'location' => urlencode( $location ),
                'save_location' => 'true'
            ), admin_url("admin.php")
        );

        $this->redirect( $redirect_url );

    }

    /**
     * Redirect and exit
     *
     * @since 2.8
     */
    public function redirect( $url ) {
        wp_redirect( $url );
        exit;
    }


    /**
     * Returns the next available menu location ID
     *
     * @since 2.8
     */
    public function get_next_menu_location_id() {
        $last_id = 0;

        if ( $locations = get_option( "megamenu_locations" ) ) {
            foreach ( $locations as $key => $value ) {
                if ( strpos( $key, 'max_mega_menu_' ) !== FALSE ) {
                    $parts = explode( "_", $key );
                    $menu_id = end( $parts );

                    if ($menu_id > $last_id) {
                        $last_id = $menu_id;
                    }
                }
            }
        }

        $next_id = $last_id + 1;

        return $next_id;
    }


    /**
     * Add the Menu Locations tab to our available tabs
     *
     * @param array $tabs
     * @since 2.8
     */
    public function add_locations_tab($tabs) {

        $new_tabs = array();
        $i = 0;

        // array_splice() does not preserve keys
        foreach ( $tabs as $index => $title ) {
            $new_tabs[$index] = $title;
            $i++;

            if ( $i == 2 ) {
                $new_tabs['menu_locations'] = __("Menu Locations", "megamenu");
            } 
        }

        return $new_tabs;
    }



    /**
     * Content for Menu Locations page
     *
     * @since 2.8
     */
    public function menu_locations_page( $saved_settings ) {
        $locations = $this->get_registered_locations();

        ?>

        <div class='menu_settings menu_settings_menu_locations'>

            <?php $this->print_messages(); ?>

            <h3 class='first'><?php _e("Menu Locations", "megamenu"); ?></h3>

            <table>
                <tr>
                    <td class='mega-name'>
                        <?php _e("Registered Menu Locations", "megamenu"); ?>
                        <div class='mega-description'><?php _e("This is an overview of the menu locations supported by your theme.", "megamenu"); ?></div>
                    </td>
                    <td class='mega-value'>
                        <?php
                        
                        if ( ! count( $locations ) ) {
                            echo "<p>";
                            _e("Your theme does not natively support menus, but you can add a new menu location using Max Mega Menu and display the menu using the Max Mega Menu widget or shortcode.", "megamenu");
                            echo "</p>";
                        } else {

                            echo "<div class='accordion-container'>";
                            echo "<ul class='outer-border'>";

                            foreach ( $locations as $location => $description ) {
                                $open_class = ( isset( $_GET['location'] ) && $_GET['location'] == $location ) ? "open" : "";
                                $is_enabled_class = "mega-location-disabled";

                                if ( max_mega_menu_is_enabled( $location ) ) {
                                    $is_enabled_class = "mega-location-enabled";
                                } else if ( ! has_nav_menu( $location ) ) {
                                    $is_enabled_class = "mega-location-disabled-assign-menu";
                                }

                                ?>

                                <li class='control-section accordion-section mega-location <?php echo $open_class ?> <?php echo $is_enabled_class ?>'>
                                    <h4 class='accordion-section-title hndle'>
                                        <span class='dashicons dashicons-location'></span><?php echo esc_attr( $description ) ?><span class='dashicons dashicons-yes'></span>
                                    </h4>
                                    <div class='accordion-section-content'>
                                        <?php 
                                            // if no menu has been assigned to the location
                                            if ( ! has_nav_menu( $location ) ) {
                                                echo "<p class='notice warning'>";
                                                echo __("This location does not have a menu assigned to it.", "megamenu");
                                                echo " <a href='" . admin_url("nav-menus.php?action=locations") . "'>" . __("Assign a menu", "megamenu") . "</a>";
                                                echo "</p>";
                                            } else {
                                                $this->show_assigned_menu( $location ); 
                                                $this->show_menu_locations_options( $locations, $location );
                                            }
                                        ?>
                                    </div>
                                </li>
                            <?php
                            }
                            echo "</div>";
                            echo "</div>";
                        }

                        $add_location_url = esc_url( add_query_arg(
                            array(
                                'action'=>'megamenu_add_menu_location'
                            ),
                            wp_nonce_url( admin_url("admin-post.php"), 'megamenu_add_menu_location' )
                        ) );

                        echo "<p><a class='mega-add-location' href='{$add_location_url}'><span class='dashicons dashicons-plus'></span>" . __("Add another menu location", "megamenu") . "</a></p>";
                        ?>

                    </td>
                </tr>
            </table>

            <?php do_action( "megamenu_menu_locations", $saved_settings ); ?>

        </div>

        <?php
    }


    /**
     * Display a link showing the menu assigned to the specified location
     *
     * @param string $location
     * @since 2.8
     */
    public function show_assigned_menu( $location ) {

        $menu_id = $this->get_menu_id_for_location( $location );

        if ($menu_id) {
            echo "<div class='mega-assigned-menu'>";
            echo "<a href='" . admin_url("nav-menus.php?action=edit&menu={$menu_id}") . "'><span class='dashicons dashicons-menu-alt2'></span>" . $this->get_menu_name_for_location( $location ) . "</a>";
            echo "</div>";
        } else {
            echo "<div class='mega-assigned-menu'>";
            echo "<a href='" . admin_url("nav-menus.php?action=locations") . "'><span class='dashicons dashicons-menu-alt2'></span>" . __("Assign a menu", "megamenu") . "</a>";
            echo "</div>";
        }
    }

    /**
     * Content for Menu Location options
     *
     * @since 2.8
     */
    public function show_menu_locations_options( $all_locations, $location ) {

        $description = $all_locations[$location];
        $menu_id = $this->get_menu_id_for_location( $location );
        $is_custom_location = strpos( $location, 'max_mega_menu_' ) !== FALSE;
        $plugin_settings = get_option( 'megamenu_settings' );
        $location_settings = isset( $plugin_settings[$location] ) ? $plugin_settings[$location] : array();

        ?>

        <form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
            <input type="hidden" name="action" value="megamenu_save_menu_location" />
            <input type="hidden" name="location" value="<?php echo esc_attr($location) ?>" />
            <?php wp_nonce_field( 'megamenu_save_menu_location' ); ?>

            <?php

                $settings = apply_filters( 'megamenu_location_settings', array(

                    'general' => array(
                        'priority' => 10,
                        'title' => __( "General Settings", "megamenu" ),
                        'settings' => array(
                            'enabled' => array(
                                'priority' => 10,
                                'title' => __( "Enabled", "megamenu" ),
                                'description' => __( "Enable Max Mega Menu for this menu location?", "megamenu" ),
                                'settings' => array(
                                    array(
                                        'type' => 'checkbox_enabled',
                                        'key' => 'enabled',
                                        'value' => isset ( $location_settings['enabled'] ) ? $location_settings['enabled'] : 0
                                    )
                                )
                            ),
                            'event' => array(
                                'priority' => 20,
                                'title' => __( "Event", "megamenu" ),
                                'description' => __( "Select the event to trigger sub menus", "megamenu" ),
                                'settings' => array(
                                    array(
                                        'type' => 'event',
                                        'key' => 'event',
                                        'value' => isset( $location_settings['event'] ) ? $location_settings['event'] : 'hover'
                                    )
                                )
                            ),
                            'effect' => array(
                                'priority' => 30,
                                'title' => __( "Effect", "megamenu" ),
                                'description' => __( "Select the sub menu animation type", "megamenu" ),
                                'settings' => array(
                                    array(
                                        'type' => 'effect',
                                        'key' => 'effect',
                                        'value' => isset( $location_settings['effect'] ) ? $location_settings['effect'] : 'fade_up',
                                        'title' => __("Animation")
                                    ),
                                    array(
                                        'type' => 'effect_speed',
                                        'key' => 'effect_speed',
                                        'value' => isset( $location_settings['effect_speed'] ) ? $location_settings['effect_speed'] : '200',
                                        'title' => __("Speed")
                                    )
                                )
                            ),
                            'effect_mobile' => array(
                                'priority' => 40,
                                'title' => __( "Effect (Mobile)", "megamenu" ),
                                'description' => __( "Choose a style for your mobile menu", "megamenu" ),
                                'settings' => array(
                                    array(
                                        'type' => 'effect_mobile',
                                        'key' => 'effect_mobile',
                                        'value' => isset( $location_settings['effect_mobile'] ) ? $location_settings['effect_mobile'] : 'none',
                                        'title' => __("Style")
                                    ),
                                    array(
                                        'type' => 'effect_speed_mobile',
                                        'key' => 'effect_speed_mobile',
                                        'value' => isset( $location_settings['effect_speed_mobile'] ) ? $location_settings['effect_speed_mobile'] : '200',
                                        'title' => __("Speed")
                                    )
                                )
                            ),
                            'theme' => array(
                                'priority' => 50,
                                'title' => __( "Theme", "megamenu" ),
                                'description' => __( "Select a theme to be applied to the menu", "megamenu" ),
                                'settings' => array(
                                    array(
                                        'type' => 'theme_selector',
                                        'key' => 'theme',
                                        'value' => isset( $location_settings['theme'] ) ? $location_settings['theme'] : 'default'
                                    )
                                )
                            )
                        )
                    ),
                    'output_options' => array(
                        'priority' => 30,
                        'title' => __( "Menu Output Options", "megamenu" ),
                        'settings' => array(
                            'location_php_function' => array(
                                'priority' => 10,
                                'title' => __( "PHP Function", "megamenu" ),
                                'description' => __( "For use in a theme template (usually header.php)", "megamenu" ),
                                'settings' => array(
                                    array(
                                        'type' => 'location_php_function',
                                        'key' => 'location_php_function',
                                        'value' => $location
                                    )
                                )
                            ),
                            'location_shortcode' => array(
                                'priority' => 20,
                                'title' => __( "Shortcode", "megamenu" ),
                                'description' => __( "For use in a post or page.", "megamenu" ),
                                'settings' => array(
                                    array(
                                        'type' => 'location_shortcode',
                                        'key' => 'location_shortcode',
                                        'value' => $location
                                    )
                                )
                            ),
                            'location_widget' => array(
                                'priority' => 30,
                                'title' => __( "Widget", "megamenu" ),
                                'description' => __( "For use in a widget area.", "megamenu" ),
                                'settings' => array(
                                    array(
                                        'type' => 'location_widget',
                                        'key' => 'location_widget',
                                        'value' => $location
                                    )
                                )
                            ),
                        )
                    )
                ), $location, $plugin_settings );


                if ( $is_custom_location ) {

                    $settings['general']['settings']['location_description'] = array(
                        'priority' => 15,
                        'title' => __( "Location Description", "megamenu" ),
                        'description' => __( "Update the custom location description", "megamenu" ),
                        'settings' => array(
                            array(
                                'type' => 'location_description',
                                'key' => 'location_description',
                                'value' => $description
                            )
                        )
                    );
                }

                echo "<div class='accordion-wrapper'>";

                echo "<h2 class='nav-tab-wrapper'>";

                $is_first = true;

                uasort( $settings, array( $this, "compare_elems" ) );

                foreach ( $settings as $section_id => $section ) {

                    if ($is_first) {
                        $active = 'nav-tab-active ';
                        $is_first = false;
                    } else {
                        $active = '';
                    }

                    echo "<a class='mega-tab nav-tab {$active}' data-tab='mega-tab-content-{$section_id}'>" . $section['title'] . "</a>";

                }

                echo "</h2>";

                $is_first = true;

                foreach ( $settings as $section_id => $section ) {

                   if ($is_first) {
                        $display = 'block';
                        $is_first = false;
                    } else {
                        $display = 'none';
                    }

                    echo "<div class='mega-tab-content mega-tab-content-{$section_id}' style='display: {$display}'>";

                    if ( $section_id == 'output_options' && ! $is_custom_location ) {
                         echo "<p class='notice warning'>" . __("These options are for advanced users only. Your theme should already include the code required to display this menu location on your site.", "megamenu") . "</p>";
                    }

                    echo "    <table class='{$section_id}'>";

                    // order the fields by priority
                    uasort( $section['settings'], array( $this, "compare_elems" ) );

                    foreach ( $section['settings'] as $group_id => $group ) {

                        echo "<tr class='" . esc_attr( "mega-" . $group_id ) . "'>";

                        if ( isset( $group['settings'] ) ) {

                            echo "<td class='mega-name'>" . esc_html( $group['title'] ) . "<div class='mega-description'>" . esc_html( $group['description'] ) . "</div></td>";
                            echo "<td class='mega-value'>";

                            foreach ( $group['settings'] as $setting_id => $setting ) {

                                echo "<label class='" . esc_attr( "mega-" . $setting['key'] ) . "'>";
                                
                                if ( isset( $setting['title'] ) ) {
                                    echo "<span class='mega-short-desc'>" . esc_html( $setting['title'] ) . "</span>";
                                }

                                switch ( $setting['type'] ) {
                                    case "freetext":
                                        $this->print_location_freetext_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "textarea":
                                        $this->print_location_textarea_option( $location, $setting['key'] );
                                        break;
                                    case "checkbox_enabled":
                                        $this->print_location_enabled_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "event":
                                        $this->print_location_event_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "effect":
                                        $this->print_location_effect_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "effect_speed":
                                        $this->print_location_effect_speed_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "effect_mobile":
                                        $this->print_location_effect_mobile_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "effect_speed_mobile":
                                        $this->print_location_effect_speed_mobile_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "theme_selector":
                                        $this->print_location_theme_selector_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "location_description":
                                        $this->print_location_description_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "checkbox":
                                        $this->print_location_checkbox_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    case "location_php_function":
                                        $this->print_location_php_function_option( $location, $setting['value'] );
                                        break;
                                    case "location_shortcode":
                                        $this->print_location_shortcode_option( $location, $setting['value'] );
                                        break;
                                    case "location_widget":
                                        $this->print_location_widget_option( $location, $setting['key'], $setting['value'] );
                                        break;
                                    default:
                                        do_action("megamenu_print_location_option_{$setting['type']}", $setting['key'], $this->id );
                                        break;
                                }

                                echo "</label>";

                            }

                            if ( isset( $group['info'] ) ) {
                                foreach ( $group['info'] as $paragraph ) {
                                    echo "<div class='mega-info'>{$paragraph}</div>";
                                }
                            }

                            echo "</td>";
                        } else {
                            echo "<td colspan='2'><h5>{$group['title']}</h5></td>";
                        }
                        echo "</tr>";

                    }
                    
                    if ( $section_id == 'general' ) {
                        do_action( 'megamenu_settings_table', $location, $plugin_settings );
                    }

                    echo "</table>";
                    echo "</div>";
                }

                ?>
            
            </div>
            <div class='megamenu_submit'>
                <div class='mega_left'>
                    <?php submit_button( $text = null ); ?>
                </div>
                <div class='mega_right'>
                    <?php
                    if ( $is_custom_location ) {

                        $delete_location_url = esc_url( add_query_arg(
                            array(
                                'action' => 'megamenu_delete_menu_location',
                                'location' => $location
                            ),
                            wp_nonce_url( admin_url("admin-post.php"), 'megamenu_delete_menu_location' )
                        ) );

                        echo "<a class='confirm mega-delete' href='{$delete_location_url}'>" . __("Delete location", "megamenu") . "</a>";

                    }
                    ?>
                </div>
            </div>
        </form>

        <?php
    }


    /**
     * Return a list of all registed menu locations
     *
     * @since 2.8
     * @return array
     */
    public function get_registered_locations() {
        $all_locations = get_registered_nav_menus();

        // PolyLang - remove auto created/translated menu locations
        if ( function_exists( 'pll_default_language' ) ) {
            $default_lang = pll_default_language( 'name' );

            foreach ( $all_locations as $loc => $description ) {
                if ( false !== strpos( $loc, '___' ) ) {
                    // Remove locations created by Polylang
                    unregister_nav_menu( $loc );
                } else {
                    // Remove the language name appended to the original locations
                    register_nav_menu( $loc, str_replace( ' ' . $default_lang, '', $description ) );
                }
            }

            $all_locations = get_registered_nav_menus();      
        }

        $locations = array();

        $custom_locations = get_option( 'megamenu_locations' );

        if ( is_array( $custom_locations ) ) {
            $all_locations = array_merge( $custom_locations, $all_locations );
        }
        
        if ( count( $all_locations ) ) {

            $megamenu_locations = array();

            // reorder locations so custom MMM locations are listed at the bottom
            foreach ( $all_locations as $location => $val ) {

                if ( strpos( $location, 'max_mega_menu_' ) === FALSE ) {
                    $locations[$location] = $val;
                } else {
                    $megamenu_locations[$location] = $val;
                }

            }

            $locations = array_merge( $locations, $megamenu_locations );
        }

        return $locations;
    }


    /**
     * Returns the menu ID for a specified menu location, defaults to 0
     *
     * @since 2.8
     * @param string $location
     */
    private function get_menu_id_for_location( $location ) {

        $locations = get_nav_menu_locations();

        $id = isset( $locations[ $location ] ) ? $locations[ $location ] : 0;

        return $id;

    }


    /**
     * Returns the menu name for a specified menu location
     *
     * @since 2.8
     * @param string $location
     */
    private function get_menu_name_for_location( $location ) {

        $id = $this->get_menu_id_for_location( $location );

        $menus = wp_get_nav_menus();

        foreach ( $menus as $menu ) {
            if ( $menu->term_id == $id ) {
                return $menu->name;
            }
        }

        return false;
    }


    /**
     * Display messages to the user
     *
     * @since 2.0
     */
    public function print_messages() {

        if ( isset( $_GET['add_location'] ) ) {
            echo "<p class='success'>" . __("New Menu Location Created", "megamenu") . "</p>";
        }

        if ( isset( $_GET['delete_location'] ) ) {
            echo "<p class='success'>" . __("Menu Location Deleted", "megamenu") . "</p>";
        }

        if ( isset( $_GET['save_location'] ) ) {
            echo "<p class='success'>" . __("Menu Location Saved", "megamenu") . "</p>";
        }

    }


    /**
     * Print a checkbox option for enabling/disabling MMM for a specific location
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_enabled_option( $location, $key, $value ) {
        ?>
            <input type='checkbox' name='megamenu_meta[<?php esc_attr_e($location) ?>][<?php esc_attr_e($key) ?>]' <?php checked( $value, '1' ); ?> />
        <?php
    }


    /**
     * Print a generic checkbox option
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_checkbox_option( $location, $key, $value ) {
        ?>
            <input type='checkbox' value='true' name='megamenu_meta[<?php esc_attr_e($location) ?>][<?php esc_attr_e($key) ?>]' <?php checked( $value, "true" ); ?> />
        <?php
    }


    /**
     * Print a select box containing all available sub menu trigger events
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_event_option( $location, $key, $value ) {

        $options = apply_filters( "megamenu_event_options", array(
            'hover' => __("Hover Intent", "megamenu"),
            'hover_' => __("Hover", "megamenu"),
            'click' => __("Click", "megamenu")
        ) );

        echo "<select name='megamenu_meta[$location][$key]'>";

        foreach ( $options as $type => $name ) {
            echo "<option value='" . esc_attr( $type ) . "' " . selected( $value, $type, false ) . ">" . esc_html( $name ) . "</option>";
        }

        echo "</select>";

    }

    /**
     * Print a select box containing all available sub menu animation options
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_effect_option( $location, $key, $value ) {
        
        echo "<select name='" . esc_attr( "megamenu_meta[$location][$key]" ) . "'>";

        $selected = strlen( $value ) ? $value : 'fade_up';

        $options = apply_filters("megamenu_transition_effects", array(
            "disabled" => array(
                'label' => __("None", "megamenu"),
                'selected' => $selected == 'disabled',
            ),
            "fade" => array(
                'label' => __("Fade", "megamenu"),
                'selected' => $selected == 'fade',
            ),
            "fade_up" => array(
                'label' => __("Fade Up", "megamenu"),
                'selected' => $selected == 'fade_up' || $selected == 'fadeUp',
            ),
            "slide" => array(
                'label' => __("Slide", "megamenu"),
                'selected' => $selected == 'slide',
            ),
            "slide_up" => array(
                'label' => __("Slide Up", "megamenu"),
                'selected' => $selected == 'slide_up',
            )
        ), $selected );

        foreach ( $options as $key => $value ) {
            echo "<option value='" . esc_attr( $key ) . "' " . selected( $value['selected'] ) . ">" . esc_html( $value['label'] ) . "</option>";
        }

        echo "</select>";

    }


    /**
     * Print a select box containing all available effect speeds (desktop)
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_effect_speed_option( $location, $key, $value ) {
        echo "<select name='" . esc_attr( "megamenu_meta[$location][$key]" ) . "'>";

        $selected = strlen( $value ) ? $value : '200';

        $options = apply_filters("megamenu_effect_speed", array(
            "600" => __("Slow", "megamenu"),
            "400" => __("Med", "megamenu"),
            "200" => __("Fast", "megamenu")
        ), $selected );

        ksort($options);

        foreach ( $options as $key => $value ) {
            echo "<option value='" . esc_attr( $key ) . "' " . selected( $selected, $key ) . ">" . esc_html( $value ) . "</option>";
        }

        echo "</select>";

    }


    /**
     * Print the textbox containing the various mobile menu options
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_effect_mobile_option( $location, $key, $value ) {
        echo "<select name='" . esc_attr( "megamenu_meta[$location][$key]" ) . "'>";

        $selected = strlen( $value ) ? $value : 'disabled';

        $options = apply_filters("megamenu_transition_effects_mobile", array(
            "disabled" => array(
                'label' => __("None", "megamenu"),
                'selected' => $selected == 'disabled',
            ),
            "slide" => array(
                'label' => __("Slide Down", "megamenu"),
                'selected' => $selected == 'slide',
            ),
            "slide_left" => array(
                'label' => __("Slide Left (Off Canvas)", "megamenu"),
                'selected' => $selected == 'slide_left',
            ),
            "slide_right" => array(
                'label' => __("Slide Right (Off Canvas)", "megamenu"),
                'selected' => $selected == 'slide_right',
            )
        ), $selected );

        foreach ( $options as $key => $value ) {
            echo "<option value='" . esc_attr( $key ) . "' " . selected( $value['selected'] ) . ">" . esc_html( $value['label'] ) . "</option>";
        }

        echo "</select>";

    }


    /**
     * Print a select box containing all available effect speeds (mobile)
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_effect_speed_mobile_option( $location, $key, $value ) {
        echo "<select name='" . esc_attr( "megamenu_meta[$location][$key]" ) . "'>";

        $selected = strlen( $value ) ? $value : '200';

        $options = apply_filters("megamenu_effect_speed_mobile", array(
            "600" => __("Slow", "megamenu"),
            "400" => __("Med", "megamenu"),
            "200" => __("Fast", "megamenu")
        ), $selected );

        ksort($options);

        foreach ( $options as $key => $value ) {
            echo "<option value='" . esc_attr( $key ) . "' " . selected( $selected, $key ) . ">" . esc_html( $value ) . "</option>";
        }

        echo "</select>";

    }


    /**
     * Print a select box containing all available menu themes
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_theme_selector_option( $location, $key, $value ) {
        echo "<select name='" . esc_attr( "megamenu_meta[$location][$key]" ) . "'>";

        $style_manager = new Mega_Menu_Style_Manager();
        $themes = $style_manager->get_themes();
        $selected_theme = strlen( $value ) ? $value : 'default';

        foreach ( $themes as $key => $theme ) {
            echo "<option value='" . esc_attr( $key ) . "' " . selected( $selected_theme, $key ) . ">" . esc_html( $theme['title'] ) . "</option>";
        }

        echo "</select>";
    }


    /**
     * Print the textbox containing the sample PHP code to output a menu location
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_php_function_option( $location, $value ) {
        ?>
        <textarea readonly="readonly">&lt;?php wp_nav_menu( array( 'theme_location' => '<?php echo esc_attr( $value ) ?>' ) ); ?&gt;</textarea>
        <?php
    }


    /**
     * Print the textbox containing the sample shortcode to output a menu location
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_shortcode_option( $location, $value ) {
        ?>
        <textarea readonly="readonly">[maxmegamenu location=<?php echo esc_attr( $value ) ?>]</textarea>
        <?php
    }


    /**
     * Print the textbox containing instructions on how to display this menu location using a widget
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_widget_option( $location, $value ) {
        ?>
        <textarea readonly="readonly"><?php _e("Add the 'Max Mega Menu' widget to a widget area.", "megamenu") ?></textarea>
        <?php
    }


    /**
     * Print a standard text input box
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_freetext_option( $location, $key, $value ) {
        echo "<input class='" . esc_attr( "mega-setting-" . $key ) . "' type='text' name='megamenu_meta[$location][$key]' value='" . esc_attr( $value ) . "' />";
    }


    /**
     * Print a text input box allowing the user to change the name of a custom menu location
     *
     * @since 2.8
     * @param string $key
     * @param string $value
     */
    public function print_location_description_option( $location, $key, $value ) {
        echo "<input class='" . esc_attr( "mega-setting-" . $key ) . "' type='text' name='custom_location[$location]' value='" . esc_attr( $value ) . "' />";
    }


    /**
     * Compare array values
     *
     * @since 2.8
     * @param array $elem1
     * @param array $elem2
     * @return bool
     */
    private function compare_elems( $elem1, $elem2 ) {
        return $elem1['priority'] > $elem2['priority'];
    }
}

endif;