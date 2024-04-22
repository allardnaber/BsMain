<?php

namespace BsMain\Data;

/**
 * See https://docs.valence.desire2learn.com/res/content.html#term-TOPIC_T
 *
Content topic type | Value
-------------------+------
File               | 1
Link               | 3
SCORM_2004         | 5 ^
SCORM_2004_ROOT    | 6 ^
SCORM_1_2          | 7 ^
SCORM_1_2_ROOT     | 8 ^
 */
class Topic_T {
	const File = 1;
	const Link = 3;
	const SCORM_2004 = 5;
	const SCORM_2004_ROOT = 6;
	const SCORM_1_2 = 7;
	const SCORM_1_2_ROOT = 8;
}
