<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/basic/conventions.html#term-RichText
 *
 * Note. This structure does not guarantee that you’ll always get both versions of a string: with some instances of its
 * use, you might get only a text version, or only an HTML version, or both. Accordingly, callers should be prepared to
 * handle results that may not always contain both formats.
 * {
 * "Text": <string:plaintext_form_of_text>,
 * "Html": <string:HTML_form_of_text>|null
 * }
 */
class CompositeRichText extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Text', 'Html' ];
	}
}