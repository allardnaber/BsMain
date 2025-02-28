<?php

namespace BsMain\Data;

/**
 * See https://docs.valence.desire2learn.com/res/content.html#term-ACTIVITYTYPE_T
 */
class ActivityType_T {

	const UnknownActivity = -1;
	const Module = 0;
	const File = 1;
	const Link = 2;
	const Dropbox = 3;
	const Quiz = 4;
	const DiscussionForum = 5;
	const DiscussionTopic = 6;
	const LTI = 7; // Legacy LTI (v1.1)
	const Chat = 8; // Deprecated
	const Schedule = 9;
	const Checklist = 10;
	const SelfAssessment = 11;
	const Survey = 12;
	const OnlineRoom = 13; // Deprecated
	const CourseLink = 14;
	const Scorm_1_3 = 20;
	const Scorm_1_3_Root = 21;
	const Scorm_1_2 = 22;
	const Scorm_1_2_Root = 23;
	const Scorm = 24; // Content Service SCORM
	const Lor = 25;
	const LorScorm = 26;
	const LTIAdvantage = 27; // LTI version v1.3+
	const OrgUnit = 28;
	const ActivityInstance = 29;
}
