<?php

namespace BsMain\Api\Fields;

use DateTimeInterface;

class DateTimeMapper extends FieldMapper {

	private const string DATE_FORMAT = 'Y-m-d\\TH:i:s.vp'; // yyyy-MM-ddTHH:mm:ss.fffZ

	public function map(array $input): ?DateTimeInterface {
		/**
		 * @var class-string<DateTimeInterface> $this->type The subclass to use (defaults to DateTimeImmutable)
		 */
		$value = $input[$this->name];
		return $value === null
			? null
			: $this->type::createFromFormat(self::DATE_FORMAT, $input[$this->name]);
	}
}
