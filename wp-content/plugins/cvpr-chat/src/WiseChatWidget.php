<?php

/**
 * CVPR Chat widget.
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatWidget extends WP_Widget {
	
	public function __construct() {
		$widgetOps = array('classname' => 'CVPRChatWidget', 'description' => 'Displays CVPR Chat' );
		parent::__construct('CVPRChatWidget', 'CVPR Chat Window', $widgetOps);
	}
 
	public function form($instance) {
		$instance = wp_parse_args((array) $instance, array('channel' => '', 'options' => ''));
		
		$channel = $instance['channel'];
		$options = $instance['options'];
		?>
			<p>
				<label for="<?php echo $this->get_field_id('channel'); ?>">
					Channel: <input class="widefat" id="<?php echo $this->get_field_id('channel'); ?>" 
								name="<?php echo $this->get_field_name('channel'); ?>" 
								type="text" value="<?php echo esc_attr($channel); ?>" />
				</label>
			</p>
            <p>
                <label for="<?php echo $this->get_field_id('options'); ?>">
                    Shortcode options: <input class="widefat" id="<?php echo $this->get_field_id('options'); ?>"
                                    name="<?php echo $this->get_field_name('options'); ?>"
                                    type="text" value="<?php echo esc_attr($options); ?>" />
                </label>
            </p>
		<?php
	}
 
	public function update($newInstance, $oldInstance) {
		$instance = $oldInstance;
		$instance['channel'] = $newInstance['channel'];
		$instance['options'] = $newInstance['options'];
		
		return $instance;
	}
	
	public function widget($args, $instance) {
		extract($args, EXTR_SKIP);
	
		echo $before_widget;

		$CvprChat = CVPRChatContainer::get('CVPRChat');
		$channel = $instance['channel'];
		$options = $instance['options'];

		$parsedOptions = shortcode_parse_atts($options);

		if (is_array($parsedOptions)) {
			$parsedOptions['channel'] = $channel;
			echo $CvprChat->getRenderedShortcode($parsedOptions);
		} else {
			echo $CvprChat->getRenderedChat($channel);
		}
	
		echo $after_widget;
		
		$CvprChat->registerResources();
	}
}