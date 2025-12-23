<?php

namespace BsMain\Data\Dropbox;

enum DropboxCompletionType_T: int {
	case OnSubmission = 0;
	case DueDate = 1;
	case ManuallyByLearner = 2;
	case OnEvaluation = 3;
}
