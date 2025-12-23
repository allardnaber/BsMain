<?php

namespace BsMain\Data\Dropbox;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\File;
use BsMain\Data\Link;
use BsMain\Data\RichText;

/**
 * {@see https://docs.valence.desire2learn.com/res/dropbox.html#Dropbox.DropboxFolder}
 */
class DropboxFolder extends _DropboxFolder_Base {

	public int $Id;
	public RichText $CustomInstructions;

	/** @var File[] */
	#[ArrayOf(File::class)]
	public array $Attachments;
	public int $TotalFiles;
	public int $UnreadFiles;
	public int $FlaggedFiles;
	public int $TotalUsers;
	public int $TotalUsersWithSubmissions;
	public int $TotalUsersWithFeedback;
	public DropboxAssessmentRead $Assessment;

	/** @var Link[] */
	#[ArrayOf(Link::class)]
	public array $LinkAttachments;
	public ?string $ActivityId;

}
