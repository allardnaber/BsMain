<?php

namespace BsMain\Api\Util;

use DateTimeInterface;
use DateTimeZone;

class DateTimeParam {

	private const string DATE_TIME_FORMAT_OUTPUT = 'Y-m-d\\TH:i:s.vp'; // yyyy-MM-ddTHH:mm:ss.fffZ.

	/**
	 * Converts a date/time value into a UTCDateTime parameter, to be used as a URL parameter. Returns null if input
	 * was null
	 * {@see https://docs.valence.desire2learn.com/basic/conventions.html#term-UTCDateTime}
	 * @param DateTimeInterface|null $value
	 * @return string|null
	 */
	public static function UTCDateTime(?DateTimeInterface $value): ?string {
		if ($value === null) return null;
		$value->setTimezone(new DateTimeZone('UTC'));
		return $value->format(self::DATE_TIME_FORMAT_OUTPUT);
	}
}
