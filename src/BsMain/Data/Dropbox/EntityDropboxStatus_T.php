<?php

namespace BsMain\Data\Dropbox;

/**
 * @see https://docs.valence.desire2learn.com/res/dropbox.html#term-ENTITYDROPBOXSTATUS_T
 */
enum EntityDropboxStatus_T: int {
	case Unsubmitted = 0;
	case Submitted = 1;
	case Draft = 2;
	case Published = 3;

}

