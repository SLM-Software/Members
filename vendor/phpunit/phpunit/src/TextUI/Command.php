<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A TestRunner for the Command Line Interface (CLI)
 * PHP SAPI Module.
 */
class PHPUnit_TextUI_Command
{
	/**
	 * @var array
	 */
	protected $arguments = [
		'listGroups'              => FALSE,
		'listSuites'              => FALSE,
		'loader'                  => NULL,
		'useDefaultConfiguration' => TRUE,
		'loadedExtensions'        => [],
		'notLoadedExtensions'     => []
	];

	/**
	 * @var array
	 */
	protected $options = [];

	/**
	 * @var array
	 */
	protected $longOptions = [
		'atleast-version='        => NULL,
		'bootstrap='              => NULL,
		'colors=='                => NULL,
		'columns='                => NULL,
		'configuration='          => NULL,
		'coverage-clover='        => NULL,
		'coverage-crap4j='        => NULL,
		'coverage-html='          => NULL,
		'coverage-php='           => NULL,
		'coverage-text=='         => NULL,
		'coverage-xml='           => NULL,
		'debug'                   => NULL,
		'disallow-test-output'    => NULL,
		'disallow-resource-usage' => NULL,
		'disallow-todo-tests'     => NULL,
		'enforce-time-limit'      => NULL,
		'exclude-group='          => NULL,
		'filter='                 => NULL,
		'generate-configuration'  => NULL,
		'group='                  => NULL,
		'help'                    => NULL,
		'include-path='           => NULL,
		'list-groups'             => NULL,
		'list-suites'             => NULL,
		'loader='                 => NULL,
		'log-json='               => NULL,
		'log-junit='              => NULL,
		'log-tap='                => NULL,
		'log-teamcity='           => NULL,
		'no-configuration'        => NULL,
		'no-coverage'             => NULL,
		'no-extensions'           => NULL,
		'no-globals-backup'       => NULL,
		'printer='                => NULL,
		'process-isolation'       => NULL,
		'repeat='                 => NULL,
		'report-useless-tests'    => NULL,
		'reverse-list'            => NULL,
		'static-backup'           => NULL,
		'stderr'                  => NULL,
		'stop-on-error'           => NULL,
		'stop-on-failure'         => NULL,
		'stop-on-warning'         => NULL,
		'stop-on-incomplete'      => NULL,
		'stop-on-risky'           => NULL,
		'stop-on-skipped'         => NULL,
		'fail-on-warning'         => NULL,
		'fail-on-risky'           => NULL,
		'strict-coverage'         => NULL,
		'disable-coverage-ignore' => NULL,
		'strict-global-state'     => NULL,
		'tap'                     => NULL,
		'teamcity'                => NULL,
		'testdox'                 => NULL,
		'testdox-group='          => NULL,
		'testdox-exclude-group='  => NULL,
		'testdox-html='           => NULL,
		'testdox-text='           => NULL,
		'testdox-xml='            => NULL,
		'test-suffix='            => NULL,
		'testsuite='              => NULL,
		'verbose'                 => NULL,
		'version'                 => NULL,
		'whitelist='              => NULL
	];

	/**
	 * @var bool
	 */
	private $versionStringPrinted = FALSE;

	/**
	 * @param bool $exit
	 */
	public static function main($exit = TRUE)
	{
		$command = new static;

		return $command->run($_SERVER['argv'], $exit);
	}

	/**
	 * @param array $argv
	 * @param bool  $exit
	 *
	 * @return int
	 */
	public function run(array $argv, $exit = TRUE)
	{
		$this->handleArguments($argv);

		$runner = $this->createRunner();

		if (is_object($this->arguments['test']) &&
			$this->arguments['test'] instanceof PHPUnit_Framework_Test
		)
		{
			$suite = $this->arguments['test'];
		} else
		{
			$suite = $runner->getTest(
				$this->arguments['test'],
				$this->arguments['testFile'],
				$this->arguments['testSuffixes']
			);
		}

		if ($this->arguments['listGroups'])
		{
			$this->printVersionString();

			print "Available test group(s):\n";

			$groups = $suite->getGroups();
			sort($groups);

			foreach ($groups as $group)
			{
				print " - $group\n";
			}

			if ($exit)
			{
				exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
			} else
			{
				return PHPUnit_TextUI_TestRunner::SUCCESS_EXIT;
			}
		}

		if ($this->arguments['listSuites'])
		{
			$this->printVersionString();

			print "Available test suite(s):\n";

			$configuration = PHPUnit_Util_Configuration::getInstance(
				$this->arguments['configuration']
			);

			$suiteNames = $configuration->getTestSuiteNames();
			foreach ($suiteNames as $suiteName)
			{
				print " - $suiteName\n";
			}

			if ($exit)
			{
				exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
			} else
			{
				return PHPUnit_TextUI_TestRunner::SUCCESS_EXIT;
			}
		}

		unset($this->arguments['test']);
		unset($this->arguments['testFile']);

		try
		{
			$result = $runner->doRun($suite, $this->arguments, $exit);
		} catch (PHPUnit_Framework_Exception $e)
		{
			print $e->getMessage() . "\n";
		}

		$return = PHPUnit_TextUI_TestRunner::FAILURE_EXIT;

		if (isset($result) && $result->wasSuccessful(FALSE))
		{
			$return = PHPUnit_TextUI_TestRunner::SUCCESS_EXIT;
		} else if (!isset($result) || $result->errorCount() > 0)
		{
			$return = PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT;
		}

		if ($exit)
		{
			exit($return);
		}

		return $return;
	}

	/**
	 * Create a TestRunner, override in subclasses.
	 *
	 * @return PHPUnit_TextUI_TestRunner
	 */
	protected function createRunner()
	{
		return new PHPUnit_TextUI_TestRunner($this->arguments['loader']);
	}

	/**
	 * Handles the command-line arguments.
	 *
	 * A child class of PHPUnit_TextUI_Command can hook into the argument
	 * parsing by adding the switch(es) to the $longOptions array and point to a
	 * callback method that handles the switch(es) in the child class like this
	 *
	 * <code>
	 * <?php
	 * class MyCommand extends PHPUnit_TextUI_Command
	 * {
	 *     public function __construct()
	 *     {
	 *         // my-switch won't accept a value, it's an on/off
	 *         $this->longOptions['my-switch'] = 'myHandler';
	 *         // my-secondswitch will accept a value - note the equals sign
	 *         $this->longOptions['my-secondswitch='] = 'myOtherHandler';
	 *     }
	 *
	 *     // --my-switch  -> myHandler()
	 *     protected function myHandler()
	 *     {
	 *     }
	 *
	 *     // --my-secondswitch foo -> myOtherHandler('foo')
	 *     protected function myOtherHandler ($value)
	 *     {
	 *     }
	 *
	 *     // You will also need this - the static keyword in the
	 *     // PHPUnit_TextUI_Command will mean that it'll be
	 *     // PHPUnit_TextUI_Command that gets instantiated,
	 *     // not MyCommand
	 *     public static function main($exit = true)
	 *     {
	 *         $command = new static;
	 *
	 *         return $command->run($_SERVER['argv'], $exit);
	 *     }
	 *
	 * }
	 * </code>
	 *
	 * @param array $argv
	 */
	protected function handleArguments(array $argv)
	{
		if (defined('__PHPUNIT_PHAR__'))
		{
			$this->longOptions['check-version'] = NULL;
			$this->longOptions['selfupdate'] = NULL;
			$this->longOptions['self-update'] = NULL;
			$this->longOptions['selfupgrade'] = NULL;
			$this->longOptions['self-upgrade'] = NULL;
		}

		try
		{
			$this->options = PHPUnit_Util_Getopt::getopt(
				$argv,
				'd:c:hv',
				array_keys($this->longOptions)
			);
		} catch (PHPUnit_Framework_Exception $e)
		{
			$this->showError($e->getMessage());
		}

		foreach ($this->options[0] as $option)
		{
			switch ($option[0])
			{
				case '--colors':
					$this->arguments['colors'] = $option[1] ?: PHPUnit_TextUI_ResultPrinter::COLOR_AUTO;
					break;

				case '--bootstrap':
					$this->arguments['bootstrap'] = $option[1];
					break;

				case '--columns':
					if (is_numeric($option[1]))
					{
						$this->arguments['columns'] = (int)$option[1];
					} else if ($option[1] == 'max')
					{
						$this->arguments['columns'] = 'max';
					}
					break;

				case 'c':
				case '--configuration':
					$this->arguments['configuration'] = $option[1];
					break;

				case '--coverage-clover':
					$this->arguments['coverageClover'] = $option[1];
					break;

				case '--coverage-crap4j':
					$this->arguments['coverageCrap4J'] = $option[1];
					break;

				case '--coverage-html':
					$this->arguments['coverageHtml'] = $option[1];
					break;

				case '--coverage-php':
					$this->arguments['coveragePHP'] = $option[1];
					break;

				case '--coverage-text':
					if ($option[1] === NULL)
					{
						$option[1] = 'php://stdout';
					}

					$this->arguments['coverageText'] = $option[1];
					$this->arguments['coverageTextShowUncoveredFiles'] = FALSE;
					$this->arguments['coverageTextShowOnlySummary'] = FALSE;
					break;

				case '--coverage-xml':
					$this->arguments['coverageXml'] = $option[1];
					break;

				case 'd':
					$ini = explode('=', $option[1]);

					if (isset($ini[0]))
					{
						if (isset($ini[1]))
						{
							ini_set($ini[0], $ini[1]);
						} else
						{
							ini_set($ini[0], TRUE);
						}
					}
					break;

				case '--debug':
					$this->arguments['debug'] = TRUE;
					break;

				case 'h':
				case '--help':
					$this->showHelp();
					exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
					break;

				case '--filter':
					$this->arguments['filter'] = $option[1];
					break;

				case '--testsuite':
					$this->arguments['testsuite'] = $option[1];
					break;

				case '--generate-configuration':
					$this->printVersionString();

					printf(
						"Generating phpunit.xml in %s\n\n",
						getcwd()
					);

					print 'Bootstrap script (relative to path shown above; default: vendor/autoload.php): ';
					$bootstrapScript = trim(fgets(STDIN));

					print 'Tests directory (relative to path shown above; default: tests): ';
					$testsDirectory = trim(fgets(STDIN));

					print 'Source directory (relative to path shown above; default: src): ';
					$src = trim(fgets(STDIN));

					if ($bootstrapScript == '')
					{
						$bootstrapScript = 'vendor/autoload.php';
					}

					if ($testsDirectory == '')
					{
						$testsDirectory = 'tests';
					}

					if ($src == '')
					{
						$src = 'src';
					}

					$generator = new PHPUnit_Util_ConfigurationGenerator;

					file_put_contents(
						'phpunit.xml',
						$generator->generateDefaultConfiguration(
							PHPUnit_Runner_Version::series(),
							$bootstrapScript,
							$testsDirectory,
							$src
						)
					);

					printf(
						"\nGenerated phpunit.xml in %s\n",
						getcwd()
					);

					exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
					break;

				case '--group':
					$this->arguments['groups'] = explode(',', $option[1]);
					break;

				case '--exclude-group':
					$this->arguments['excludeGroups'] = explode(
						',',
						$option[1]
					);
					break;

				case '--test-suffix':
					$this->arguments['testSuffixes'] = explode(
						',',
						$option[1]
					);
					break;

				case '--include-path':
					$includePath = $option[1];
					break;

				case '--list-groups':
					$this->arguments['listGroups'] = TRUE;
					break;

				case '--list-suites':
					$this->arguments['listSuites'] = TRUE;
					break;

				case '--printer':
					$this->arguments['printer'] = $option[1];
					break;

				case '--loader':
					$this->arguments['loader'] = $option[1];
					break;

				case '--log-json':
					$this->arguments['jsonLogfile'] = $option[1];
					break;

				case '--log-junit':
					$this->arguments['junitLogfile'] = $option[1];
					break;

				case '--log-tap':
					$this->arguments['tapLogfile'] = $option[1];
					break;

				case '--log-teamcity':
					$this->arguments['teamcityLogfile'] = $option[1];
					break;

				case '--process-isolation':
					$this->arguments['processIsolation'] = TRUE;
					break;

				case '--repeat':
					$this->arguments['repeat'] = (int)$option[1];
					break;

				case '--stderr':
					$this->arguments['stderr'] = TRUE;
					break;

				case '--stop-on-error':
					$this->arguments['stopOnError'] = TRUE;
					break;

				case '--stop-on-failure':
					$this->arguments['stopOnFailure'] = TRUE;
					break;

				case '--stop-on-warning':
					$this->arguments['stopOnWarning'] = TRUE;
					break;

				case '--stop-on-incomplete':
					$this->arguments['stopOnIncomplete'] = TRUE;
					break;

				case '--stop-on-risky':
					$this->arguments['stopOnRisky'] = TRUE;
					break;

				case '--stop-on-skipped':
					$this->arguments['stopOnSkipped'] = TRUE;
					break;

				case '--fail-on-warning':
					$this->arguments['failOnWarning'] = TRUE;
					break;

				case '--fail-on-risky':
					$this->arguments['failOnRisky'] = TRUE;
					break;

				case '--tap':
					$this->arguments['printer'] = 'PHPUnit_Util_Log_TAP';
					break;

				case '--teamcity':
					$this->arguments['printer'] = 'PHPUnit_Util_Log_TeamCity';
					break;

				case '--testdox':
					$this->arguments['printer'] = 'PHPUnit_Util_TestDox_ResultPrinter_Text';
					break;

				case '--testdox-group':
					$this->arguments['testdoxGroups'] = explode(
						',',
						$option[1]
					);
					break;

				case '--testdox-exclude-group':
					$this->arguments['testdoxExcludeGroups'] = explode(
						',',
						$option[1]
					);
					break;

				case '--testdox-html':
					$this->arguments['testdoxHTMLFile'] = $option[1];
					break;

				case '--testdox-text':
					$this->arguments['testdoxTextFile'] = $option[1];
					break;

				case '--testdox-xml':
					$this->arguments['testdoxXMLFile'] = $option[1];
					break;

				case '--no-configuration':
					$this->arguments['useDefaultConfiguration'] = FALSE;
					break;

				case '--no-extensions':
					$this->arguments['noExtensions'] = TRUE;
					break;

				case '--no-coverage':
					$this->arguments['noCoverage'] = TRUE;
					break;

				case '--no-globals-backup':
					$this->arguments['backupGlobals'] = FALSE;
					break;

				case '--static-backup':
					$this->arguments['backupStaticAttributes'] = TRUE;
					break;

				case 'v':
				case '--verbose':
					$this->arguments['verbose'] = TRUE;
					break;

				case '--atleast-version':
					exit(version_compare(PHPUnit_Runner_Version::id(), $option[1], '>=')
						? PHPUnit_TextUI_TestRunner::SUCCESS_EXIT
						: PHPUnit_TextUI_TestRunner::FAILURE_EXIT
					);
					break;

				case '--version':
					$this->printVersionString();
					exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
					break;

				case '--report-useless-tests':
					$this->arguments['reportUselessTests'] = TRUE;
					break;

				case '--strict-coverage':
					$this->arguments['strictCoverage'] = TRUE;
					break;

				case '--disable-coverage-ignore':
					$this->arguments['disableCodeCoverageIgnore'] = TRUE;
					break;

				case '--strict-global-state':
					$this->arguments['beStrictAboutChangesToGlobalState'] = TRUE;
					break;

				case '--disallow-test-output':
					$this->arguments['disallowTestOutput'] = TRUE;
					break;

				case '--disallow-resource-usage':
					$this->arguments['beStrictAboutResourceUsageDuringSmallTests'] = TRUE;
					break;

				case '--enforce-time-limit':
					$this->arguments['enforceTimeLimit'] = TRUE;
					break;

				case '--disallow-todo-tests':
					$this->arguments['disallowTodoAnnotatedTests'] = TRUE;
					break;

				case '--reverse-list':
					$this->arguments['reverseList'] = TRUE;
					break;

				case '--check-version':
					$this->handleVersionCheck();
					break;

				case '--selfupdate':
				case '--self-update':
					$this->handleSelfUpdate();
					break;

				case '--selfupgrade':
				case '--self-upgrade':
					$this->handleSelfUpdate(TRUE);
					break;

				case '--whitelist':
					$this->arguments['whitelist'] = $option[1];
					break;

				default:
					$optionName = str_replace('--', '', $option[0]);

					$handler = NULL;
					if (isset($this->longOptions[$optionName]))
					{
						$handler = $this->longOptions[$optionName];
					} else if (isset($this->longOptions[$optionName . '=']))
					{
						$handler = $this->longOptions[$optionName . '='];
					}

					if (isset($handler) && is_callable([$this, $handler]))
					{
						$this->$handler($option[1]);
					}
			}
		}

		$this->handleCustomTestSuite();

		if (!isset($this->arguments['test']))
		{
			if (isset($this->options[1][0]))
			{
				$this->arguments['test'] = $this->options[1][0];
			}

			if (isset($this->options[1][1]))
			{
				$this->arguments['testFile'] = realpath($this->options[1][1]);
			} else
			{
				$this->arguments['testFile'] = '';
			}

			if (isset($this->arguments['test']) &&
				is_file($this->arguments['test']) &&
				substr($this->arguments['test'], -5, 5) != '.phpt'
			)
			{
				$this->arguments['testFile'] = realpath($this->arguments['test']);
				$this->arguments['test'] = substr($this->arguments['test'], 0, strrpos($this->arguments['test'], '.'));
			}
		}

		if (!isset($this->arguments['testSuffixes']))
		{
			$this->arguments['testSuffixes'] = ['Test.php', '.phpt'];
		}

		if (isset($includePath))
		{
			ini_set(
				'include_path',
				$includePath . PATH_SEPARATOR . ini_get('include_path')
			);
		}

		if ($this->arguments['loader'] !== NULL)
		{
			$this->arguments['loader'] = $this->handleLoader($this->arguments['loader']);
		}

		if (isset($this->arguments['configuration']) &&
			is_dir($this->arguments['configuration'])
		)
		{
			$configurationFile = $this->arguments['configuration'] . '/phpunit.xml';

			if (file_exists($configurationFile))
			{
				$this->arguments['configuration'] = realpath(
					$configurationFile
				);
			} else if (file_exists($configurationFile . '.dist'))
			{
				$this->arguments['configuration'] = realpath(
					$configurationFile . '.dist'
				);
			}
		} else if (!isset($this->arguments['configuration']) &&
			$this->arguments['useDefaultConfiguration']
		)
		{
			if (file_exists('phpunit.xml'))
			{
				$this->arguments['configuration'] = realpath('phpunit.xml');
			} else if (file_exists('phpunit.xml.dist'))
			{
				$this->arguments['configuration'] = realpath(
					'phpunit.xml.dist'
				);
			}
		}

		if (isset($this->arguments['configuration']))
		{
			try
			{
				$configuration = PHPUnit_Util_Configuration::getInstance(
					$this->arguments['configuration']
				);
			} catch (Throwable $e)
			{
				print $e->getMessage() . "\n";
				exit(PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
			} catch (Exception $e)
			{
				print $e->getMessage() . "\n";
				exit(PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
			}

			$phpunitConfiguration = $configuration->getPHPUnitConfiguration();

			$configuration->handlePHPConfiguration();

			/*
			 * Issue #1216
			 */
			if (isset($this->arguments['bootstrap']))
			{
				$this->handleBootstrap($this->arguments['bootstrap']);
			} else if (isset($phpunitConfiguration['bootstrap']))
			{
				$this->handleBootstrap($phpunitConfiguration['bootstrap']);
			}

			/*
			 * Issue #657
			 */
			if (isset($phpunitConfiguration['stderr']) && !isset($this->arguments['stderr']))
			{
				$this->arguments['stderr'] = $phpunitConfiguration['stderr'];
			}

			if (isset($phpunitConfiguration['extensionsDirectory']) && !isset($this->arguments['noExtensions']) && extension_loaded('phar'))
			{
				$this->handleExtensions($phpunitConfiguration['extensionsDirectory']);
			}

			if (isset($phpunitConfiguration['columns']) && !isset($this->arguments['columns']))
			{
				$this->arguments['columns'] = $phpunitConfiguration['columns'];
			}

			if (!isset($this->arguments['printer']) && isset($phpunitConfiguration['printerClass']))
			{
				if (isset($phpunitConfiguration['printerFile']))
				{
					$file = $phpunitConfiguration['printerFile'];
				} else
				{
					$file = '';
				}

				$this->arguments['printer'] = $this->handlePrinter(
					$phpunitConfiguration['printerClass'],
					$file
				);
			}

			if (isset($phpunitConfiguration['testSuiteLoaderClass']))
			{
				if (isset($phpunitConfiguration['testSuiteLoaderFile']))
				{
					$file = $phpunitConfiguration['testSuiteLoaderFile'];
				} else
				{
					$file = '';
				}

				$this->arguments['loader'] = $this->handleLoader(
					$phpunitConfiguration['testSuiteLoaderClass'],
					$file
				);
			}

			if (!isset($this->arguments['test']))
			{
				$testSuite = $configuration->getTestSuiteConfiguration(isset($this->arguments['testsuite']) ? $this->arguments['testsuite'] : NULL);

				if ($testSuite !== NULL)
				{
					$this->arguments['test'] = $testSuite;
				}
			}
		} else if (isset($this->arguments['bootstrap']))
		{
			$this->handleBootstrap($this->arguments['bootstrap']);
		}

		if (isset($this->arguments['printer']) &&
			is_string($this->arguments['printer'])
		)
		{
			$this->arguments['printer'] = $this->handlePrinter($this->arguments['printer']);
		}

		if (isset($this->arguments['test']) && is_string($this->arguments['test']) && substr($this->arguments['test'], -5, 5) == '.phpt')
		{
			$test = new PHPUnit_Extensions_PhptTestCase($this->arguments['test']);

			$this->arguments['test'] = new PHPUnit_Framework_TestSuite;
			$this->arguments['test']->addTest($test);
		}

		if (!isset($this->arguments['test']) ||
			(isset($this->arguments['testDatabaseLogRevision']) && !isset($this->arguments['testDatabaseDSN']))
		)
		{
			$this->showHelp();
			exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
		}
	}

	/**
	 * Handles the loading of the PHPUnit_Runner_TestSuiteLoader implementation.
	 *
	 * @param string $loaderClass
	 * @param string $loaderFile
	 *
	 * @return PHPUnit_Runner_TestSuiteLoader
	 */
	protected function handleLoader($loaderClass, $loaderFile = '')
	{
		if (!class_exists($loaderClass, FALSE))
		{
			if ($loaderFile == '')
			{
				$loaderFile = PHPUnit_Util_Filesystem::classNameToFilename(
					$loaderClass
				);
			}

			$loaderFile = stream_resolve_include_path($loaderFile);

			if ($loaderFile)
			{
				require $loaderFile;
			}
		}

		if (class_exists($loaderClass, FALSE))
		{
			$class = new ReflectionClass($loaderClass);

			if ($class->implementsInterface('PHPUnit_Runner_TestSuiteLoader') &&
				$class->isInstantiable()
			)
			{
				return $class->newInstance();
			}
		}

		if ($loaderClass == 'PHPUnit_Runner_StandardTestSuiteLoader')
		{
			return;
		}

		$this->showError(
			sprintf(
				'Could not use "%s" as loader.',
				$loaderClass
			)
		);
	}

	/**
	 * Handles the loading of the PHPUnit_Util_Printer implementation.
	 *
	 * @param string $printerClass
	 * @param string $printerFile
	 *
	 * @return PHPUnit_Util_Printer|string
	 */
	protected function handlePrinter($printerClass, $printerFile = '')
	{
		if (!class_exists($printerClass, FALSE))
		{
			if ($printerFile == '')
			{
				$printerFile = PHPUnit_Util_Filesystem::classNameToFilename(
					$printerClass
				);
			}

			$printerFile = stream_resolve_include_path($printerFile);

			if ($printerFile)
			{
				require $printerFile;
			}
		}

		if (class_exists($printerClass))
		{
			$class = new ReflectionClass($printerClass);

			if ($class->implementsInterface('PHPUnit_Framework_TestListener') &&
				$class->isSubclassOf('PHPUnit_Util_Printer') &&
				$class->isInstantiable()
			)
			{
				if ($class->isSubclassOf('PHPUnit_TextUI_ResultPrinter'))
				{
					return $printerClass;
				}

				$outputStream = isset($this->arguments['stderr']) ? 'php://stderr' : NULL;

				return $class->newInstance($outputStream);
			}
		}

		$this->showError(
			sprintf(
				'Could not use "%s" as printer.',
				$printerClass
			)
		);
	}

	/**
	 * Loads a bootstrap file.
	 *
	 * @param string $filename
	 */
	protected function handleBootstrap($filename)
	{
		try
		{
			PHPUnit_Util_Fileloader::checkAndLoad($filename);
		} catch (PHPUnit_Framework_Exception $e)
		{
			$this->showError($e->getMessage());
		}
	}

	protected function handleSelfUpdate($upgrade = FALSE)
	{
		$this->printVersionString();

		if ($upgrade)
		{
			print "Warning: Deprecated --self-upgrade used\n\n";
		} else
		{
			print "Warning: Deprecated --self-update used\n\n";
		}

		$localFilename = realpath($_SERVER['argv'][0]);

		if (!is_writable($localFilename))
		{
			print 'No write permission to update ' . $localFilename . "\n";
			exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
		}

		if (!extension_loaded('openssl'))
		{
			print "The OpenSSL extension is not loaded.\n";
			exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
		}

		if (!$upgrade)
		{
			$remoteFilename = sprintf(
				'https://phar.phpunit.de/phpunit-%s.phar',
				file_get_contents(
					sprintf(
						'https://phar.phpunit.de/latest-version-of/phpunit-%s',
						PHPUnit_Runner_Version::series()
					)
				)
			);
		} else
		{
			$remoteFilename = sprintf(
				'https://phar.phpunit.de/phpunit%s.phar',
				PHPUnit_Runner_Version::getReleaseChannel()
			);
		}

		$tempFilename = tempnam(sys_get_temp_dir(), 'phpunit') . '.phar';

		// Workaround for https://bugs.php.net/bug.php?id=65538
		$caFile = dirname($tempFilename) . '/ca.pem';
		copy(__PHPUNIT_PHAR_ROOT__ . '/ca.pem', $caFile);

		print 'Updating the PHPUnit PHAR ... ';

		$options = [
			'ssl' => [
				'allow_self_signed' => FALSE,
				'cafile'            => $caFile,
				'verify_peer'       => TRUE
			]
		];

		file_put_contents(
			$tempFilename,
			file_get_contents(
				$remoteFilename,
				FALSE,
				stream_context_create($options)
			)
		);

		chmod($tempFilename, 0777 & ~umask());

		try
		{
			$phar = new Phar($tempFilename);
			unset($phar);
			rename($tempFilename, $localFilename);
			unlink($caFile);
		} catch (Throwable $_e)
		{
			$e = $_e;
		} catch (Exception $_e)
		{
			$e = $_e;
		}

		if (isset($e))
		{
			unlink($caFile);
			unlink($tempFilename);
			print " done\n\n" . $e->getMessage() . "\n";
			exit(2);
		}

		print " done\n";
		exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
	}

	protected function handleVersionCheck()
	{
		$this->printVersionString();

		$latestVersion = file_get_contents('https://phar.phpunit.de/latest-version-of/phpunit');
		$isOutdated = version_compare($latestVersion, PHPUnit_Runner_Version::id(), '>');

		if ($isOutdated)
		{
			print "You are not using the latest version of PHPUnit.\n";
			print 'Use "phpunit --self-upgrade" to install PHPUnit ' . $latestVersion . "\n";
		} else
		{
			print "You are using the latest version of PHPUnit.\n";
		}

		exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
	}

	/**
	 * Show the help message.
	 */
	protected function showHelp()
	{
		$this->printVersionString();

		print <<<EOT
Usage: phpunit [options] UnitTest [UnitTest.php]
       phpunit [options] <directory>

Code Coverage Options:

  --coverage-clover <file>  Generate code coverage report in Clover XML format.
  --coverage-crap4j <file>  Generate code coverage report in Crap4J XML format.
  --coverage-html <dir>     Generate code coverage report in HTML format.
  --coverage-php <file>     Export PHP_CodeCoverage object to file.
  --coverage-text=<file>    Generate code coverage report in text format.
                            Default: Standard output.
  --coverage-xml <dir>      Generate code coverage report in PHPUnit XML format.
  --whitelist <dir>         Whitelist <dir> for code coverage analysis.
  --disable-coverage-ignore Disable annotations for ignoring code coverage.

Logging Options:

  --log-junit <file>        Log test execution in JUnit XML format to file.
  --log-teamcity <file>     Log test execution in TeamCity format to file.
  --testdox-html <file>     Write agile documentation in HTML format to file.
  --testdox-text <file>     Write agile documentation in Text format to file.
  --testdox-xml <file>      Write agile documentation in XML format to file.
  --reverse-list            Print defects in reverse order

Test Selection Options:

  --filter <pattern>        Filter which tests to run.
  --testsuite <name>        Filter which testsuite to run.
  --group ...               Only runs tests from the specified group(s).
  --exclude-group ...       Exclude tests from the specified group(s).
  --list-groups             List available test groups.
  --list-suites             List available test suites.
  --test-suffix ...         Only search for test in files with specified
                            suffix(es). Default: Test.php,.phpt

Test Execution Options:

  --report-useless-tests    Be strict about tests that do not test anything.
  --strict-coverage         Be strict about @covers annotation usage.
  --strict-global-state     Be strict about changes to global state
  --disallow-test-output    Be strict about output during tests.
  --disallow-resource-usage Be strict about resource usage during small tests.
  --enforce-time-limit      Enforce time limit based on test size.
  --disallow-todo-tests     Disallow @todo-annotated tests.

  --process-isolation       Run each test in a separate PHP process.
  --no-globals-backup       Do not backup and restore \$GLOBALS for each test.
  --static-backup           Backup and restore static attributes for each test.

  --colors=<flag>           Use colors in output ("never", "auto" or "always").
  --columns <n>             Number of columns to use for progress output.
  --columns max             Use maximum number of columns for progress output.
  --stderr                  Write to STDERR instead of STDOUT.
  --stop-on-error           Stop execution upon first error.
  --stop-on-failure         Stop execution upon first error or failure.
  --stop-on-warning         Stop execution upon first warning.
  --stop-on-risky           Stop execution upon first risky test.
  --stop-on-skipped         Stop execution upon first skipped test.
  --stop-on-incomplete      Stop execution upon first incomplete test.
  --fail-on-warning         Treat tests with warnings as failures.
  --fail-on-risky           Treat risky tests as failures.
  -v|--verbose              Output more verbose information.
  --debug                   Display debugging information during test execution.

  --loader <loader>         TestSuiteLoader implementation to use.
  --repeat <times>          Runs the test(s) repeatedly.
  --teamcity                Report test execution progress in TeamCity format.
  --testdox                 Report test execution progress in TestDox format.
  --testdox-group           Only include tests from the specified group(s).
  --testdox-exclude-group   Exclude tests from the specified group(s).
  --printer <printer>       TestListener implementation to use.

Configuration Options:

  --bootstrap <file>        A "bootstrap" PHP file that is run before the tests.
  -c|--configuration <file> Read configuration from XML file.
  --no-configuration        Ignore default configuration file (phpunit.xml).
  --no-coverage             Ignore code coverage configuration.
  --no-extensions           Do not load PHPUnit extensions.
  --include-path <path(s)>  Prepend PHP's include_path with given path(s).
  -d key[=value]            Sets a php.ini value.
  --generate-configuration  Generate configuration file with suggested settings.

Miscellaneous Options:

  -h|--help                 Prints this usage information.
  --version                 Prints the version and exits.
  --atleast-version <min>   Checks that version is greater than min and exits.

EOT;

		if (defined('__PHPUNIT_PHAR__'))
		{
			print "\n  --check-version           Check whether PHPUnit is the latest version.";
		}
	}

	/**
	 * Custom callback for test suite discovery.
	 */
	protected function handleCustomTestSuite()
	{
	}

	private function printVersionString()
	{
		if ($this->versionStringPrinted)
		{
			return;
		}

		print PHPUnit_Runner_Version::getVersionString() . "\n\n";

		$this->versionStringPrinted = TRUE;
	}

	/**
	 * @param string $message
	 */
	private function showError($message)
	{
		$this->printVersionString();

		print $message . "\n";

		exit(PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
	}

	/**
	 * @param string $directory
	 */
	private function handleExtensions($directory)
	{
		$facade = new File_Iterator_Facade;

		foreach ($facade->getFilesAsArray($directory, '.phar') as $file)
		{
			require $file;

			$this->arguments['loadedExtensions'][] = $file;
		}
	}
}
