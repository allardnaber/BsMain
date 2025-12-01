<?php

namespace BsMain\Data\Content;

/**
 * The back-end service lets you filter content items based on their exemption status.
 * {@see https://docs.valence.desire2learn.com/res/content.html#term-EXEMPTION_T}
 */
enum Exemption_T: int {

	case Any = 1;
	case ExemptedOnly = 2;
	case NotExemptedOnly = 3;

}
