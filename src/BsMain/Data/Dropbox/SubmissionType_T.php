<?php

namespace BsMain\Data\Dropbox;

enum SubmissionType_T: int {
	case File = 0;
	case Text = 1;
	case OnPaper = 2;
	case Observed = 3;
	case FileOrText = 4;
}
