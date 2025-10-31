<?php

namespace BsMain\Data;

/**
 * Rich Text object used for writing HTML or Text content.
 * {@see https://docs.valence.desire2learn.com/basic/conventions.html#term-RichTextInput}
 */
class RichTextInput extends ApiEntity {

	public string $Content;
	public RichTextContentType $Type;

	/**
	 * Create Text type input object.
	 * @param string $text Text content.
	 * @return self
	 */
	public static function Text(string $text): self {
		$result = self::newInstance();
		$result->Content = $text;
		$result->Type = RichTextContentType::Text;
		return $result;
	}

	/**
	 * Create HTML type input object.
	 * @param string $html HTML content.
	 * @return self
	 */
	public static function Html(string $html): self {
		$result = self::newInstance();
		$result->Content = $html;
		$result->Type = RichTextContentType::Html;
		return $result;
	}

}
