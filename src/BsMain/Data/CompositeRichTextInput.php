<?php

namespace BsMain\Data;

/**
 * D2L expects:
 * see https://docs.valence.desire2learn.com/basic/conventions.html#term-RichTextInput
 * {
"Content": <string>,
"Type": "Text|Html"
}
 */
class CompositeRichTextInput extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Type', 'Content' ];
	}

	public static function getBlankInput() {
		return self::getObject('Text', '');
	}

	public static function html(?string $html) {
		return self::getObject('Html', $html);
	}

	public static function text(?string $text) {
		return self::getObject('Text', $text);
	}

	private static function getObject(string $type, ?string $content) {
		$result = self::instance();
		$result->Type = $type;
		$result->Content = $content;
		return $result;
	}
}
