<?php

namespace BsMain\Data\Content;

use BsMain\Data\Content_T;
use DateTimeInterface;

class ContentObjectData_Module extends ContentObjectData {

	public ?DateTimeInterface $ModuleStartDate;
	public ?DateTimeInterface $ModuleEndDate;
	public ?DateTimeInterface $ModuleDueDate;

	public function onCreate(): void {
		$this->Type = Content_T::Module;
	}
}
