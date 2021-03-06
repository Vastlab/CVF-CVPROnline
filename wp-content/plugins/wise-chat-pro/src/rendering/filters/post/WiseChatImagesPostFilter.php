<?php

/**
 * Wise Chat images post-filter.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatImagesPostFilter {
	const SHORTCODE_REGEXP = '/\[img id=&quot;(\d+)&quot; src=&quot;(.+?)&quot; src-th=&quot;(.+?)&quot; src-org=&quot;(.+?)&quot;\]/i';
	const URL_PROTOCOLS_REGEXP = "/^(https|http|ftp)\:\/\//i";
    const IMAGE_TAG_TEMPLATE = '<a href="%s" data-tag="%s" data-type="img" target="_blank" data-lightbox="wise_chat" rel="lightbox[wise_chat]"><img src="%s" class="wcImage" alt="Chat image" /></a>';
    const IMAGE_LINK_TAG_TEMPLATE = '<a href="%s" data-tag="%s" data-type="img" target="_blank" rel="noopener noreferrer nofollow">%s</a>';

	/**
	* Detects all images in shortcode format and converts them into images, clickable links or raw URLs
	*
	* @param string $text HTML-encoded string
	* @param boolean $imagesEnabled Whether to convert shortcodes into real images
	* @param boolean $linksEnabled Whether to convert shortcodes into real hyperlinks
	*
	* @return string
	*/
	public function filter($text, $imagesEnabled, $linksEnabled = true) {
		if (preg_match_all(self::SHORTCODE_REGEXP, $text, $matches)) {
			if (count($matches) < 3) {
				return $text;
			}
			
			foreach ($matches[0] as $key => $shortCode) {
				$tagEncrypted = base64_encode((WiseChatCrypt::encrypt(gzcompress($matches[0][$key]))));
				$imageSrc = $matches[2][$key];
				$imageThumbnailSrc = $matches[3][$key];
				$imageOrgSrc = $matches[4][$key];
				
				$replace = '';
				if ($imagesEnabled) {
					$replace = sprintf(self::IMAGE_TAG_TEMPLATE, $imageSrc, $tagEncrypted, $imageThumbnailSrc);
				} else if ($linksEnabled) {
					if ($imageOrgSrc == '_') {
						$imageOrgSrc = $imageSrc;
					}
				
					$url = (!preg_match(self::URL_PROTOCOLS_REGEXP, $imageOrgSrc) ? 'http://' : '').$imageOrgSrc;
					$linkBody = htmlentities(urldecode($imageOrgSrc), ENT_QUOTES, 'UTF-8', false);
					$replace = sprintf(self::IMAGE_LINK_TAG_TEMPLATE, $url, $tagEncrypted, $linkBody);
				} else {
					$replace = $imageOrgSrc != '_' ? $imageOrgSrc : $imageSrc;
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
	private function strReplaceFirst($needle, $replace, $haystack) {
		$pos = strpos($haystack, $needle);
		
		if ($pos !== false) {
			return substr_replace($haystack, $replace, $pos, strlen($needle));
		}
		
		return $haystack;
	}
}