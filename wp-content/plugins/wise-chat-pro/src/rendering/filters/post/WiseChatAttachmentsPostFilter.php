<?php

/**
 * Wise Chat attachments post-filter.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatAttachmentsPostFilter {
	const SHORTCODE_REGEXP = '/\[attachment id=&quot;(.+?)&quot; src=&quot;(.+?)&quot; name-org=&quot;(.+?)&quot;\]/i';
    const URL_PROTOCOLS_REGEXP = "/^(https|http|ftp)\:\/\//i";
	const ATTACHMENT_TAG_TEMPLATE = '<a href="%s" data-tag="%s" data-type="attachment" target="_blank" rel="noopener noreferrer nofollow">%s</a>';

	/**
	* Detects all attachments in shortcode format and converts them into real hyperlinks or raw URLs
	*
	* @param string $text HTML-encoded string
	* @param boolean $attachmentsEnabled Whether to convert shortcodes into real hyperlinks
	* @param boolean $linksEnabled Whether to convert shortcodes into real hyperlinks
	*
	* @return string
	*/
	public function filter($text, $attachmentsEnabled, $linksEnabled = true) {
		if (preg_match_all(self::SHORTCODE_REGEXP, $text, $matches)) {
			if (count($matches) < 2) {
				return $text;
			}
			
			foreach ($matches[0] as $key => $shortCode) {
				$tagEncrypted = base64_encode((WiseChatCrypt::encrypt(gzcompress($matches[0][$key]))));
				$attachmentSrc = $matches[2][$key];
				$attachmentOrgName = $matches[3][$key];
				$linkBody = htmlentities(urldecode($attachmentOrgName), ENT_QUOTES, 'UTF-8', false);
				$replace = '';
				if ($attachmentsEnabled) {
					$linkTag = sprintf(self::ATTACHMENT_TAG_TEMPLATE, $attachmentSrc, $tagEncrypted, $linkBody);
                    $replace = $linkTag;
				} else if ($linksEnabled) {
                    $url = (!preg_match(self::URL_PROTOCOLS_REGEXP, $attachmentSrc) ? 'http://' : '').$attachmentSrc;
                    $linkBody = htmlentities(urldecode($attachmentOrgName), ENT_QUOTES, 'UTF-8', false);
                    $replace = sprintf(self::ATTACHMENT_TAG_TEMPLATE, $url, $tagEncrypted, $linkBody);
                } else {
                    $replace = $linkBody;
				}

                $text = $this->strReplaceFirst($shortCode, $replace, $text);
			}
		}
		
		return $text;
	}

    /**
     * Replaces first occurrence of the needle.
     *
     * @param string $needle
     * @param string $replace
     * @param string $haystack
     *
     * @return string
     */
	private static function strReplaceFirst($needle, $replace, $haystack) {
		$pos = strpos($haystack, $needle);
		
		if ($pos !== false) {
			return substr_replace($haystack, $replace, $pos, strlen($needle));
		}
		
		return $haystack;
	}
}	