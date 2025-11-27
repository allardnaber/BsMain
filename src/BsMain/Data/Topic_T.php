<?php

namespace BsMain\Data;

/**
 * See https://docs.valence.desire2learn.com/res/content.html#term-TOPIC_T
 */
enum Topic_T: int {
	case File = 1;
	case Link = 3;
	case SCORM_2004 = 5;
	case SCORM_2004_ROOT = 6;
	case SCORM_1_2 = 7;
	case SCORM_1_2_ROOT = 8;
	case SCORM_2004_3 = 11; // undocumented, but seen in the wild
}
