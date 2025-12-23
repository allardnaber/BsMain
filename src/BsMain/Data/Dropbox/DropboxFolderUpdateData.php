<?php

namespace BsMain\Data\Dropbox;

use BsMain\Data\RichTextInput;

class DropboxFolderUpdateData extends _DropboxFolder_Base {

	public RichTextInput $CustomInstructions;
	public DropboxAssessmentUpdate $Assessment;
}
