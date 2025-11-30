<?php

namespace BsMain\Data\Overview;

use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

/**
 * {@see https://docs.valence.desire2learn.com/res/content.html#Overview.Overview}
 */
class Overview extends ApiEntity {

	public RichText $Description;
	public bool $HasAttachment;

}
