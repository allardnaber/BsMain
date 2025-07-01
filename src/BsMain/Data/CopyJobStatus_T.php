<?php

namespace BsMain\Data;

/**
 * See https://docs.valence.desire2learn.com/res/course.html#term-COPYJOBSTATUS_T
 */
class CopyJobStatus_T {
	const PENDING = 'PENDING'; //Waiting to be picked up for processing.
	const PROCESSING = 'PROCESSING'; //Currently in process.
	const COMPLETE = 'COMPLETE'; // Processing finished (may contain processing errors: see Conversion history page for details).
	const FAILED = 'FAILED'; // Processing halted before finish, owing to errors.
	const CANCELLED = 'CANCELLED'; // Job was cancelled before finish.
}
