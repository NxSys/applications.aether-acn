<?php

namespace NxSys\Library\Bridges\sfConsole;

use Symfony\Component\Console as sfConsole;

/**
 * ToolApplication
 *
 * @see http://symfony.com/doc/current/components/console/single_command_tool.html
 */
class ToolApplication extends sfConsole\Application
{
	protected $sCmdName;
	protected $oCmd;

	/**
	 * Constructor
	 * @param string Command   An instance of the command the application is based on
	 * @param string $name     The name of the application
	 * @param string $version  The version of the application
	 */
	public function __construct(sfConsole\Command\Command $oCommandInstance,
								$name = 'UNKNOWN', $version = 'UNKNOWN', $sDefaultCommandName=null)
	{
		$this->oCmd=$oCommandInstance;
		$this->sCmdName=$sDefaultCommandName ?: $oCommandInstance->getName();
		$this->oCmd->setName($this->sCmdName);
		parent::__construct($name, $version);
	}

	/**
	 * Gets the name of the command based on input.
	 *
	 * @param InputInterface $input The input interface
	 *
	 * @return string The command name
	 */
	protected function getCommandName(sfConsole\Input\InputInterface $input)
	{
		return $this->oCmd->getName();
	}

	/**
	 * Gets the default commands that should always be available.
	 *
	 * @return array An array of default Command instances
	 */
	protected function getDefaultCommands()
	{
		// Keep the core default commands to have the HelpCommand
		// which is used when using the --help option
		$defaultCommands = parent::getDefaultCommands();
		$defaultCommands[] = $this->oCmd;

		return $defaultCommands;
	}

	/**
	 * Overridden so that the application doesn't expect the command
	 * name to be the first argument.
	 */
	public function getDefinition()
	{
		$inputDefinition = parent::getDefinition();
		// clear out the normal first argument, which is the command name
		$inputDefinition->setArguments();

		return $inputDefinition;
	}
}