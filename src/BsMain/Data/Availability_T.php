<?php

namespace BsMain\Data;

/**
 * Availability dates can be used to restrict an activity’s access in different ways, classified by type. We use the
 * term AVAILABILITY_T to stand in for an appropriate integer value.
 * {@see https://docs.valence.desire2learn.com/res/apiprop.html#term-AVAILABILITY_T}
 */
enum Availability_T: int {

	case AccessRestricted = 0;
	case SubmissionRestricted = 1;
	case Hidden = 2;

}
