<?php

namespace BsMain\Data\Dropbox;

use BsMain\Data\GenericObject;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/dropbox.html#Dropbox.DropboxFolder
 * {
 * "Entity": { <composite:Dropbox.Entity> },
 * "Status": <string:ENTITYDROPBOXSTATUS_T>,
 * "Feedback": { <composite:Dropbox.DropboxFeedbackOut> },
 * "Submissions": [ // Array of Submission blocks
 * {
 * "Id": <number:D2LID>,
 * "SubmittedBy": {
 * "Id": <string>,
 * "DisplayName": <string>
 * },
 * "SubmissionDate": <string:UTCDateTime>|null,
 * "Comment": { <composite:RichText> },
 * "Files": [ // Array of File blocks
 * {
 * "FileId": <number:D2LID>,
 * "FileName": <string>,
 * "Size": <number:long>,
 * "isRead": <boolean>,
 * "isFlagged": <boolean>
 * },
 * { <composite:File> }, ...
 * ]
 * },
 * { <composite:Submission> }, ...
 * ],
 * "CompletionDate": <string:UTCDateTime>|null
 * }
 */
class EntityDropbox extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Entity', 'Status', 'Feedback', 'Submissions', 'CompletedDate' ];
	}

	protected function postCreationProcessing(): void {
		//$this->Role = RoleInfo::instance($this->Role);
		$this->Status = EntityDropboxStatus_T::from($this->Status);
	}
}
