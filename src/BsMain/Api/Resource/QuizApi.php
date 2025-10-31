<?php /** @noinspection PhpUnused */

namespace BsMain\Api\Resource;

use BsMain\Data\Quiz\QuestionData;
use BsMain\Data\Quiz\QuizAttemptData;
use BsMain\Data\Quiz\QuizReadData;

/**
 * API endpoints related to Quizzes, {@see https://docs.valence.desire2learn.com/res/quiz.html}.<br>
 * Completeness: 4 / 18
 */
class QuizApi extends ApiShell {

	/**
	 * @param int $courseId
	 * @return QuizReadData[]
	 */
	public function getAllQuizzes(int $courseId): array {
		return QuizReadData::get($this->client)
			->description('list of quizzes')
			->leUrl('%d/quizzes/', $courseId)
			->fetchArray();
	}

	public function getQuiz(int $courseId, int $quizId): QuizReadData {
		return QuizReadData::get($this->client)
			->description('quiz')
			->leUrl('%d/quizzes/%d', $courseId, $quizId)
			->fetch();
	}

	/**
	 * @param int $courseId
	 * @param int $quizId
	 * @return QuestionData[]
	 */
	public function getQuizQuestions(int $courseId, int $quizId): array {
		return QuestionData::get($this->client)
			->description('list of quiz questions')
			->leUrl('%d/quizzes/%d/questions/', $courseId, $quizId)
			->fetchArray();
	}

	/**
	 * @param int $courseId
	 * @param int $quizId
 	 * @return QuizAttemptData[]
	 */
	public function getAllQuizAttempts(int $courseId, int $quizId): array {
		return QuizAttemptData::get($this->client)
			->description('list of attempts')
			->leUrl('%d/quizzes/%d/attempts/', $courseId, $quizId)
			->fetchArray();
	}

	/*public function getQuizAttempt(int $courseId, int $quizId, int $userId) {
		$response = $this->request($this->url('/customization/1.0/quizzes/%d/%d/lastquizattempt/%d', $courseId, $quizId, $userId), 'user attempt');
		return \BsMain\Data\LastQuizAttempt::create($response);
	}*/



	/*public function deleteQuiz(int $courseId, int $quizId): void {
		$this->request(
			$this->url('/le/1.67/%d/quizzes/%d', $courseId, $quizId), null, 'the quiz', 'DELETE'
		);
	}
	 */
}
