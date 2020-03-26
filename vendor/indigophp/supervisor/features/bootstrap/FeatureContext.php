<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Indigo\Supervisor\Configuration\Parser\File as Parser;
use Indigo\Supervisor\Configuration\Writer\File as Writer;
use Indigo\Supervisor\Configuration\Section;
use Indigo\Supervisor\Connector\XmlRpc;
use Indigo\Supervisor\Supervisor;
use fXmlRpc\Client;
use fXmlRpc\Transport\Guzzle4Bridge;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($bin = 'supervisord')
    {
        $this->bin = $bin;
    }

    /**
     * @BeforeScenario
     */
    public function setUpSupervisor(BeforeScenarioScope $scope)
    {
        $parser = new Parser(__DIR__.'/../../resources/supervisord.conf');
        $this->configuration = $parser->parse();

        $this->setUpConnector();
    }

    protected function setUpConnector()
    {
        $client = new Client(
            'http://127.0.0.1:9001/RPC2',
            new Guzzle4Bridge(new GuzzleClient(['defaults' => ['auth' => ['user', '123']]]))
        );

        $connector = new XmlRpc($client);
        $this->supervisor = new Supervisor($connector);
    }

    /**
     * @AfterScenario
     */
    public function stopSupervisor(AfterScenarioScope $scope)
    {
        isset($this->process) and posix_kill($this->process, SIGKILL);
    }

    /**
     * @Given I have Supervisor running
     */
    public function iHaveSupervisorRunning()
    {
        $writer = new Writer($file = tempnam(sys_get_temp_dir(), 'supervisord_'));
        $writer->write($this->configuration);

        if ($this->supervisor->isConnected()) {
            posix_kill($this->supervisor->getPID(), SIGKILL);
        }

        $command = sprintf('(%s --configuration %s > /dev/null 2>&1 & echo $!)&', $this->bin, $file);
        exec($command, $op);
        $this->process = (int)$op[0];

        $c = 0;
        while (!$this->supervisor->isConnected() and $c < 100) {
            usleep(10000);
            $c++;
        }

        if ($c >= 100) {
            throw new \RuntimeException('Could not connect to supervisord');
        }

        if ($this->process !== $this->supervisor->getPID()) {
            throw new \RuntimeException('Connected to supervisord with a different PID');
        }
    }

    /**
     * @When I ask for the API version
     */
    public function iAskForTheApiVersion()
    {
        $this->version = $this->supervisor->getAPIVersion();
    }

    /**
     * @Then I should get at least :ver version
     */
    public function iShouldGetAtLeastVersion($ver)
    {
        if (version_compare($this->version, $ver) == -1) {
            throw new \Exception(sprintf('Version "%s" does not match the minimum required "%s"', $this->version, $ver));
        }
    }

    /**
     * @When I ask for Supervisor version
     */
    public function iAskForSupervisorVersion()
    {
        $this->version = $this->supervisor->getVersion();
    }

    /**
     * @Given my Supervisor instance is called :identifier
     */
    public function mySupervisorInstanceIsCalled($identifier)
    {
        $supervisord = $this->configuration->getSection('supervisord');
        $supervisord->setProperty('identifier', $identifier);
    }

    /**
     * @When I ask for Supervisor identification
     */
    public function iAskForSupervisorIdentification()
    {
        $this->identifier = $this->supervisor->getIdentification();
    }

    /**
     * @Then I should get :identifier as identifier
     */
    public function iShouldGetAsIdentifier($identifier)
    {
        if ($this->identifier !== $identifier) {
            throw new \Exception(sprintf('Identification "%s" does not match the required "%s"', $this->identifier, $identifier));
        }
    }

    /**
     * @When I ask for the state
     */
    public function iAskForTheState()
    {
        $this->state = $this->supervisor->getState();
    }

    /**
     * @Then I should get :code as statecode and :name as statename
     */
    public function iShouldGetAsStatecodeAndAsStatename($code, $name)
    {
        if ($this->state['statecode'] != $code) {
            throw new \Exception(sprintf('State code "%s" does not match the required "%s"', $this->state['statecode'], $code));
        }

        if ($this->state['statename'] !== $name) {
            throw new \Exception(sprintf('Statename "%s" does not match the required "%s"', $this->state['statename'], $name));
        }
    }

    /**
     * @When I ask for the PID
     */
    public function iAskForThePid()
    {
        $this->pid = $this->supervisor->getPID();
    }

    /**
     * @Then I should get the real PID
     */
    public function iShouldGetTheRealPid()
    {
        if ($this->process !== $this->pid) {
            throw new \Exception(sprintf('PID "%s" does not match the real "%s"', $this->pid, $this->process));
        }
    }

    /**
     * @When I ask for the log
     */
    public function iAskForTheLog()
    {
        $this->log = trim($this->supervisor->readLog(-(35 + strlen($this->process)), 0));
    }

    /**
     * @Then I should get an INFO about supervisord started
     */
    public function iShouldGetAnInfoAboutSupervisordStarted()
    {
        if ($this->log !== 'INFO supervisord started with pid '.$this->process) {
            throw new \Exception(sprintf('The following log entry was expected: "%s", but we got this: "%s"', 'INFO supervisord started with pid '.$this->process, $this->log));
        }
    }

    /**
     * @When I try to call :action action
     */
    public function iTryToCallAction($action)
    {
        $this->action = $action;
        $this->response = call_user_func([$this->supervisor, $action]);
    }

    /**
     * @When I check if the log is really empty
     */
    public function iCheckIfTheLogIsReallyEmpty()
    {
        $this->log = trim($this->supervisor->readLog(-24, 0));
    }

    /**
     * @Then I should get a success response
     */
    public function iShouldGetASuccessResponse()
    {
        if ($this->response !== true) {
            throw new \Exception(sprintf('Action "%s" was unsuccessful', $this->action));
        }
    }

    /**
     * @Then I should get a cleared log
     */
    public function iShouldGetAClearedLog()
    {
        if ($this->log !== 'INFO reopening log file') {
            throw new \Exception('Empty log cannot be confirmed');
        }
    }

    /**
     * @Then it should be stopped
     */
    public function itShouldBeStopped()
    {
        if ($this->supervisor->isConnected() === true) {
            throw new \Exception('Supervisor is still available');
        }
    }

    /**
     * @Then it should be running again
     */
    public function itShouldBeRunningAgain()
    {
        if ($this->supervisor->isConnected() !== true) {
            throw new \Exception('Supervisor is unavailable');
        }
    }

    /**
     * @Given I have a process called :process
     */
    public function iHaveAProcessCalled($process)
    {
        $this->processName = $this->processes[] = $process;

        $program = new Section\Program($process, [
            'command' => exec('which '.$process),
        ]);

        $this->configuration->addSection($program);
    }

    /**
     * @When I wait for start
     */
    public function iWaitForStart()
    {
        usleep(100000);
    }

    /**
     * @When I get information about the processes
     */
    public function iGetInformationAboutTheProcesses()
    {
        $processInfo = $this->supervisor->getAllProcessInfo();
        $processNames = array_column($processInfo, 'name');
        $this->processInfo = array_combine($processNames, $processInfo);
    }

    /**
     * @Then I should see running
     */
    public function iShouldSeeRunning()
    {
        foreach ($this->processes as $process) {
            if (!isset($this->processInfo[$process]) or $this->processInfo[$process]['state'] < 10) {
                throw new \Exception(sprintf('Process "%s" is not running', $process));
            }
        }
    }

    /**
     * @Given autostart is disabled
     */
    public function autostartIsDisabled()
    {
        $program = $this->configuration->getSection('program:'.$this->processName);

        $program->setProperty('autostart', false);
    }

    /**
     * @When I get information about the processes before action
     */
    public function iGetInformationAboutTheProcessesBeforeAction()
    {
        $processInfo = $this->supervisor->getAllProcessInfo();
        $processNames = array_column($processInfo, 'name');
        $this->firstProcessInfo = array_combine($processNames, $processInfo);
    }

    /**
     * @When I :action the process
     */
    public function iTheProcess($action)
    {
        $this->action = $action.'Process';
        $this->response = call_user_func([$this->supervisor, $this->action], $this->processName, false);
    }

    /**
     * @Then I should see not running first
     */
    public function iShouldSeeNotRunningFirst()
    {
        foreach ($this->processes as $process) {
            if (!isset($this->firstProcessInfo[$process]) or $this->firstProcessInfo[$process]['state'] > 0) {
                throw new \Exception(sprintf('Process "%s" is running', $process));
            }
        }
    }

    /**
     * @When I :action the processes
     */
    public function iTheProcesses($action)
    {
        $this->action = $action.'AllProcesses';
        $this->response = call_user_func([$this->supervisor, $this->action], false);
    }

    /**
     * @Then I should get a success response for all
     */
    public function iShouldGetASuccessResponseForAll()
    {
        foreach ($this->response as $response) {
            if ($response['description'] !== 'OK') {
                throw new \Exception(sprintf('Action "%s" was unsuccessful', $this->action));
            }
        }
    }

    /**
     * @Then I should see running first
     */
    public function iShouldSeeRunningFirst()
    {
        foreach ($this->processes as $process) {
            if (!isset($this->firstProcessInfo[$process]) or $this->firstProcessInfo[$process]['state'] < 10) {
                throw new \Exception(sprintf('Process "%s" is not running before "%s"', $process, $this->action));
            }
        }
    }

    /**
     * @Then I should see not running
     */
    public function iShouldSeeNotRunning()
    {
        foreach ($this->processes as $process) {
            if (!isset($this->processInfo[$process]) or $this->processInfo[$process]['state'] > 0) {
                throw new \Exception(sprintf('Process "%s" is running', $process));
            }
        }
    }

    /**
     * @Given it is part of group called :grp
     */
    public function itIsPartOfGroupCalled($grp)
    {
        $this->groupName = $grp;

        $program = $this->configuration->getSection('program:'.$this->processName);
        $group = $this->configuration->getSection('group:'.$grp);

        if (is_null($group)) {
            $group = new Section\Group($grp, ['programs' => $this->processName]);
            $this->configuration->addSection($group);
        } else {
            $programs = $group->getProperty('programs');
            $programs[] = $this->processName;
            $group->setProperty('programs', $programs);
        }
    }

    /**
     * @When I :action the processes in the group
     */
    public function iTheProcessesInTheGroup($action)
    {
        $this->action = $action.'ProcessGroup';
        $this->response = call_user_func([$this->supervisor, $this->action], $this->groupName, false);
    }

    /**
     * @Then I should see them as part of the group
     */
    public function iShouldSeeThemAsPartOfTheGroup()
    {
        foreach ($this->response as $response) {
            if ($response['group'] !== $this->groupName) {
                throw new \Exception(sprintf('Process "%s" is not part of the group "%s"', $response['name'], $this->groupName));
            }
        }
    }
}
