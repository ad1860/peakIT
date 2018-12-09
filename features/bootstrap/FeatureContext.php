<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use elkan\BehatFormatter\Context\BehatFormatterContext;

class FeatureContext extends BehatFormatterContext implements Context
{
    /**
     * General context class used for independent setup settings (no page objects)
     */


    /**
     * adds a breakpoints
     * stops the execution until you hit enter in the console
     * @Then /^breakpoint/
     */
    public static function breakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while (fgets(STDIN, 1024) == '') {}
        fwrite(STDOUT, "\033[u");

        return;
    }

//    public function getTestId(BeforeScenarioScope $scope)
//    {
//        $tags = $scope->getScenario()->getTags();
//
//        foreach ($tags as $tag){
//            if(strpos($tag, "id_") !== false){
//                $this->testId = "@".$tag;
//                break;
//            }
//        }
//    }
//
//    public function takeScreenshotAfterFailedStep(AfterStepScope $scope)
//    {
//        if (getenv('BROWSERSTACK_USER') !== false){
//            return;
//        }
//
//        if (99 === $scope->getTestResult()->getResultCode()) {
//            $this->takeScreenshot();
//        }
//    }

    private function takeScreenshot()
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof Selenium2Driver) {
            return;
        }
        $fileName = $this->testId .'_'. date('d-m_H-i-s') . '.png';

        $path = getcwd();
        $this->saveScreenshot($fileName, "$path/report/");
    }
}
