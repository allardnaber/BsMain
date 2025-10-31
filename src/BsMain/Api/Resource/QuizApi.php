<?php

namespace BsMain\Api\Resource;

use BsMain\Data\Quiz\QuestionData;

class QuizApi extends ApiShell {

	/**
	 * @param int $courseId
	 * @param int $quizId
	 * @return QuestionData[]
	 */
	public function getQuizQuestions(int $courseId, int $quizId): array {
		return QuestionData::get($this->client)
			->leUrl('%d/quizzes/%d/questions/', $courseId, $quizId)
			->paged()
			->fetchArray();
	}
}
