<?php

/**
 * Shortcode that renders a button for downloading all messages from the channel.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatExportShortcode {
    const CHECKSUM_CONST = '3897t42983chf';

    /**
     * @var WiseChatOptions
     */
    private $options;

    /**
     * @var WiseChatUsersDAO
     */
    private $usersDAO;

    /**
     * @var WiseChatMessagesDAO
     */
    private $messagesDAO;

    /**
     * @var WiseChatRenderer
     */
    private $renderer;

    public function __construct() {
        $this->options = WiseChatOptions::getInstance();
        $this->usersDAO = WiseChatContainer::get('dao/user/WiseChatUsersDAO');
        $this->renderer = WiseChatContainer::get('rendering/WiseChatRenderer');
        $this->messagesDAO = WiseChatContainer::get('dao/WiseChatMessagesDAO');
        WiseChatContainer::load('WiseChatCrypt');
    }

    public function doExport() {
        if (isset($_POST['wcAction']) && isset($_POST['wcCheckSum']) && isset($_POST['wcChannel']) && $_POST['wcAction'] == 'wcExportMessages') {
            $checksum = WiseChatCrypt::decrypt(base64_decode($_POST['wcCheckSum']));
            $checksumParts = explode(',', $checksum);
            $channel = WiseChatCrypt::decrypt(base64_decode($_POST['wcChannel']));
            if (count($checksumParts) === 3 && $checksumParts[1] === self::CHECKSUM_CONST && $checksumParts[2] === $channel) {
                $this->backupChannel($channel);
            }
        }
    }

    public function renderShortcode($atts) {
        if (!is_array($atts)) {
            $atts = array();
        }
        $atts = array_merge(array(
            'channel' => 'global',
            'message_export_button_label' => 'Export Messages'
        ), $atts);
        extract($atts);

        $checksum = base64_encode(WiseChatCrypt::encrypt(date('His').','.self::CHECKSUM_CONST.','.$atts['channel']));
        $channel = base64_encode(WiseChatCrypt::encrypt($atts['channel']));
        $label = htmlentities($atts['message_export_button_label'], ENT_QUOTES, 'UTF-8');
        $accessEnabled = $atts['access_mode'] == 0 || $atts['access_mode'] == 1 && $this->usersDAO->isWpUserLogged();

        if ($accessEnabled) {
            return "
				<form method='post'>
					<input type='hidden' name='wcAction' value='wcExportMessages' />
					<input type='hidden' name='wcCheckSum' value='$checksum' />
					<input type='hidden' name='wcChannel' value='$channel' />
					<input type='submit' value='$label' />
				</form>
			";
        } else {
            return '';
        }
    }

    public function backupChannel($channel) {
        $channelStripped = preg_replace("/[^[:alnum:][:space:]]/ui", '', $channel);
        $filename = "WiseChatProChannelBackup-{$channelStripped}.csv";

        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");

        $criteria = new WiseChatMessagesCriteria();
        $criteria->setChannelName($channel);
        $criteria->setOffsetId(0);
        $criteria->setIncludeAdminMessages(false);
        $messages = $this->messagesDAO->getAllByCriteria($criteria);

        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array('ChatID', 'Time', 'User', 'Message', 'Approved', 'IP'));
        foreach ($messages as $message) {
            $messageArray = array(
                $message->getId(), date("Y-m-d H:i:s", $message->getTime()), $message->getUserName(), $message->getText(), $message->isHidden() ? '0' : '1', $message->getIp()
            );
            fputcsv($df, $messageArray);
        }
        fclose($df);

        echo ob_get_clean();

        die();
    }

}