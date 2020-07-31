<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: upload
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'SP_WPCP_Field_upload' ) ) {
  class SP_WPCP_Field_upload extends SP_WPCP_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'library'      => array(),
        'button_title' => esc_html__( 'Upload', 'wp-carousel-pro' ),
        'remove_title' => esc_html__( 'Remove', 'wp-carousel-pro' ),
      ) );

      echo $this->field_before();

      $library = ( is_array( $args['library'] ) ) ? $args['library'] : array_filter( (array) $args['library'] );
      $library = ( ! empty( $library ) ) ? implode(',', $library ) : '';
      $hidden  = ( empty( $this->value ) ) ? ' hidden' : '';

      echo '<div class="spf--wrap">';
        echo '<input type="text" name="'. $this->field_name() .'" value="'. $this->value .'"'. $this->field_attributes() .'/>';
        echo '<div class="spf--buttons">';
        echo '<a href="#" class="button button-primary spf--button" data-library="'. esc_attr( $library ) .'">'. $args['button_title'] .'</a>';
        echo '<a href="#" class="button button-secondary spf-warning-primary spf--remove'. $hidden .'">'. $args['remove_title'] .'</a>';
        echo '</div>';
      echo '</div>';

      echo $this->field_after();

    }
  }
}
