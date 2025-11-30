<?php

namespace BsMain\Data\Content;

enum Completion_T: int {

	case Any = 1;
	case CompletedOnly = 2;
	case NotCompletedOnly = 3;

}
