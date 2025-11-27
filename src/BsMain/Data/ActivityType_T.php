<?php

namespace BsMain\Data;

/**
 * See https://docs.valence.desire2learn.com/res/content.html#term-ACTIVITYTYPE_T
 */
enum ActivityType_T: int {

	case UnknownActivity = -1;
	case Module = 0;
	case File = 1;
	case Link = 2;
	case Dropbox = 3;
	case Quiz = 4;
	case DiscussionForum = 5;
	case DiscussionTopic = 6;
	case LTI = 7; // Legacy LTI (v1.1)
	case Chat = 8; // Deprecated
	case Schedule = 9;
	case Checklist = 10;
	case SelfAssessment = 11;
	case Survey = 12;
	case OnlineRoom = 13; // Deprecated
	case CourseLink = 14;
	case Scorm_1_3 = 20;
	case Scorm_1_3_Root = 21;
	case Scorm_1_2 = 22;
	case Scorm_1_2_Root = 23;
	case Scorm = 24; // Content Service SCORM
	case Lor = 25;
	case LorScorm = 26;
	case LTIAdvantage = 27; // LTI version v1.3+
	case OrgUnit = 28;
	case ActivityInstance = 29;
}
