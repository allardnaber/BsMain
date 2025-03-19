<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/course.html#term-COI_IMPORTLOGMESSAGE_T
 */
class CoiImportLogMessage_T {
	const System = 0;
	const Progress = 1;
	const Warning = 2;
	const Error = 4;
}
