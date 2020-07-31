<?php

/**
 * Shortcode that renders CVPR Chat basic statistics for given channel.
 *
 * @author Kainex <contact@kaine.pl>
 */
class CVPRChatStatsShortcode {
    /**
     * @var CVPRChatOptions
     */
    private $options;

    /**
     * @var CVPRChatService
     */
    private $service;

    /**
     * @var CVPRChatMessagesService
     */
    private $messagesService;

    /**
     * @var CVPRChatChannelsDAO
     */
    private $channelsDAO;

    /**
     * @var CVPRChatRenderer
     */
    private $renderer;

    /**
     * CVPRChatStatsShortcode constructor.
     */
    public function __construct() {
        $this->options = CVPRChatOptions::getInstance();
        $this->service = CVPRChatContainer::get('services/CVPRChatService');
        $this->messagesService = CVPRChatContainer::get('services/CVPRChatMessagesService');
        $this->channelsDAO = CVPRChatContainer::get('dao/CVPRChatChannelsDAO');
        $this->renderer = CVPRChatContainer::get('rendering/CVPRChatRenderer');
    }

    /**
     * Renders shortcode: [Cvpr-chat-channel-stats]
     *
     * @param array $attributes
     * @return string
     */
    public function getRenderedChannelStatsShortcode($attributes) {
        if (!is_array($attributes)) {
            $attributes = array();
        }

        $attributes['channel'] = $this->service->getValidChatChannelName(
            array_key_exists('channel', $attributes) ? $attributes['channel'] : ''
        );

        $channel = $this->channelsDAO->getByName($attributes['channel']);
        if ($channel !== null) {
            $this->options->replaceOptions($attributes);

            $this->messagesService->startUpMaintenance($channel);

            return $this->renderer->getRenderedChannelStats($channel);
        } else {
            return 'ERROR: channel does not exist';
        }
    }
}