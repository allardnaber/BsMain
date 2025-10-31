<?php

namespace BsMain\Data;

/**
 * Rich Text object used for reading HTML and/or Text content.
 * {@see https://docs.valence.desire2learn.com/basic/conventions.html#term-RichText}
 */
class RichText extends ApiEntity {

	public string $Text;
	public ?string $Html;

}
