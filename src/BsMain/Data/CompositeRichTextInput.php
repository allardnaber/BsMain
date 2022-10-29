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
	
	protected function getAvailableFields() {
		return [ 'Type', 'Content' ];
	}

	public static function getBlankInput() {
		$result = self::create();
		$result->Type = 'Text';
		$result->Content = '';
		return $result;
	}
}
