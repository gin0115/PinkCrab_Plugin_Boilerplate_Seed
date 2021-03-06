<?php

/**
 * Application container.
 */

namespace PinkCrab\Plugin_Boilerplate_Builder\Builder;

use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use PinkCrab\Plugin_Boilerplate_Builder\Builder\PluginDetails;
use PinkCrab\Plugin_Boilerplate_Builder\Builder\PluginSetting;

class PluginBuilder {

	protected PluginDetails $pluginDetails;

	public function __construct( PluginDetails $pluginDetails ) {
		$this->pluginDetails = $pluginDetails;
	}

	/**
	 * Iterates through all settings and populates with the values.
	 *
	 * @param \Symfony\Component\Console\Input\Input $input
	 * @param \Symfony\Component\Console\Output\OutputInterface $output
	 * @param \Symfony\Component\Console\Helper\HelperSet $questionHelper
	 * @return void
	 */
	public function askQuestions(
		Input $input,
		OutputInterface $output,
		HelperSet $questionHelper
	): void {

		foreach ( $this->pluginDetails->toArray()
			as $key => $setting ) {

			$output->writeln( "<darkpink> {$setting->getQuestion()} </>" );

			if ( $setting->hasSubline() ) {
				$output->writeln( "<lightpink> {$setting->getSubLine()} </>" );
			}

			$question = $questionHelper->get( 'question' );
			$response = $question->ask( $input, $output, new Question( '> ' ) );
			$this->pluginDetails->setResponse( $key, $response );
		}
	}

	/**
	 * Checks if any of the questions have errors.
	 *
	 * @return bool
	 */
	public function hasErrors() {
		return count( $this->getErrors() ) !== 0;
	}

	/**
	 * Gets all errors which may have been created.
	 *
	 * @return array
	 */
	public function getErrors(): array {
		$errors = array_filter(
			$this->pluginDetails->toArray(),
			fn( PluginSetting $setting ): bool => $setting->hasError()
		);
		return array_map( fn( $e): string => $e->getError(), $errors );
	}
}
