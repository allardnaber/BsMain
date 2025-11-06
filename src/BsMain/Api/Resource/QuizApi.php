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
		return $this->client->fetchArray(
			QuizReadData::get()
			->description('list of quizzes')
			->leUrl('%d/quizzes/', $courseId)
		);
	}

	public function getQuiz(int $courseId, int $quizId): QuizReadData {
		return $this->client->fetch(
			QuizReadData::get()
			->description('quiz')
			->leUrl('%d/quizzes/%d', $courseId, $quizId));
	}

	/**
	 * @param int $courseId
	 * @param int $quizId
	 * @return QuestionData[]
	 */
	public function getQuizQuestions(int $courseId, int $quizId): array {
		return $this->client->fetchArray(
			QuestionData::get()
			->description('list of quiz questions')
			->leUrl('%d/quizzes/%d/questions/', $courseId, $quizId)
		);
	}

	/**
	 * @param int $courseId
	 * @param int $quizId
 	 * @return QuizAttemptData[]
	 */
	public function getAllQuizAttempts(int $courseId, int $quizId): array {
		return $this->client->fetchArray(
			QuizAttemptData::get()
			->description('list of attempts')
			->leUrl('%d/quizzes/%d/attempts/', $courseId, $quizId)
		);
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
