<?php

namespace BsMain\Data\Toc;

enum ContentCompletionType_T: int {

	/**
	 * Manual and Auto both indicate that the content topic is “required”.
	 */
	case Manual = 1;

	/**
	 * Manual and Auto both indicate that the content topic is “required”.
	 */
	case Auto = 2;

	/**
	 * This completion type indicates that the content topic is exempt, or "optional".
	 */
	case None = 3;
}
