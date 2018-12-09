<?php
namespace Page;

use Behat\Mink\Exception\Exception;
use WebDriver\Exception\NoAlertOpenError;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
class BasePage extends Page
{

    /**
     * Main object
     * Contains general page actions to be used in other objects
     * Will be extended by each specific page
     */

    # -------------------- general methods --------------------

    /**
     * @param $selector
     * @throws \Exception
     */
    public function click($selector)
    {
        $this->waitForElement($selector)->click();
    }

    /**
     * @param $selector
     * @throws \Exception
     */
    public function mouseOver($selector)
    {
        $this->waitForElement($selector)->mouseOver();
    }

    /**
     * @param $option
     * @param $selector
     * @param bool $multiple
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     * @throws \Exception
     */
    public function selectFrom($option, $selector, $multiple = false)
    {
        $element = $this->waitForElement($selector);
        $element->selectOption($option, $multiple);
    }

    /**
     * @param string $selector
     * @param string $text
     * @return mixed
     * @throws \Exception
     */
    public function fillWith($selector, $text)
    {
        $this->waitForElement($selector)->setValue($text);
//        $this->javascriptFill($selector, $text);
        return $text;
    }

    public function javascriptFill($selector, $value){
        $function = <<<JS
        (function(){
        var element = document.querySelector("$selector");
        event = document.createEvent('Event');
        event.initEvent('change', true, true);
        element.value = "$value";
        element.dispatchEvent(event);
        })()
JS;
        try {
            $this->getDriver()->executeScript($function);
        } catch (Exception $e) {
            print_r($e->getMessage());
            throw new \Exception("Element $selector was NOT found." . PHP_EOL . $e->getMessage());
        }
    }

    /**
     * Confirms the popup alert
     */
    public function confirmThePopup()
    {
        $i = 0;
        while ($i < 5) {
            try {
                $this->getDriver()->getWebDriverSession()->accept_alert();
                break;
            } catch (NoAlertOpenError $e) {
                sleep(1);
                $i++;
            }
        }
    }

    /**
     * Extracts link/path from selector
     * @param $selector
     * @return null|string
     * @throws \Exception
     */
    public function extractLink($selector){
        $node = $this->waitForElement($selector);

        if ($node->hasAttribute('href')) {
            $link = $node->getAttribute('href');

        } elseif ($node->hasAttribute('onclick')) {
            $onclick = $node->getAttribute('onclick');
            $link = explode("'", $onclick)[1];

        } else {
            throw new \Exception("Unable to extract the link from $selector");
        }

        return $link;
    }

    /**
     * Checks the presence of element
     * @param $element
     * @throws \Exception
     */
    public function assertIsPresent($element)
    {
        $this->waitForElement($element);
    }

    /**
     * Checks the element is not present
     * @param string $element
     * @throws \Exception
     */
    public function assertIsNotPresent($element)
    {
        if ($this->find($element) !== null) {
            throw new \Exception("Element $element was found, but should not be available");
        }
    }

    /**
     * Checks the visibility of element
     * @param $element
     * @throws \Exception
     */
    public function assertIsVisible($element)
    {
        $this->waitUntilIsVisible($element);
    }

    /**
     * Checks the element is not visible
     * @param string $element
     * @throws \Exception
     */
    public function assertNotVisible($element)
    {
        $node = $this->waitForElement($element);
        if ($node->isVisible()) {
            throw new \Exception("The $element was visible, but should not be visible");
        }
    }

    /**
     * Checks if given text is found in the given element
     * @param $text
     * @param $element
     * @throws \Exception
     */
    public function assertHasText($text, $element)
    {
        $actual = $this->waitForElement($element)->getText();
        if (stripos($actual, $text) === false) {
            throw new \Exception("Expected text $text was NOT found in $element, actual is $actual");
        }
    }

    /**
     * Checks the element identified by selector has given text in the given attribute
     * @param string $element
     * @param string $text
     * @param string $attribute
     * @throws \Exception
     */
    public function assertHasTextInAttribute($element, $text, $attribute)
    {
        # check fot attribute
        $hasAttribute = $this->waitForElement($element)->hasAttribute($attribute);
        if ($hasAttribute === null) {
            throw new \Exception("Element $element does not have $attribute attribute.");
        }

        # check for value
        $attributeValue = $this->waitForElement($element)->getAttribute($attribute);
        if (stripos($attributeValue, $text) === false) {
            throw new \Exception("Element $element does not have $text value in $attribute attribute! Actual $attributeValue");
        }
    }

    /**
     * Check the current URL contains specified text/path
     * @param string $path
     * @throws \Exception
     */
    public function assertUrlContainsPath($path)
    {
        $currentUrl = $this->getDriver()->getCurrentUrl();
        if (strpos($currentUrl, $path) === false) {
            throw new \Exception("Current Url does not contain $path. Actual url is $currentUrl.");
        }
    }

    /**
     * Check the current URL does NOT contains specified text/path
     * @param string $path
     * @throws \Exception
     */
    public function assertUrlDoesNotContainPath($path)
    {
        $this->switchToNewWindow();
        $currentUrl = $this->getDriver()->getCurrentUrl();
        if (strpos($currentUrl, $path) !== false) {
            throw new \Exception("Current Url does not contain $path. Actual url is $currentUrl.");
        }
    }

    /**
     * @param $text
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    public function fillCkeditor($text, $selector)
    {
//        $this->getDriver()->executeScript("for(var i in CKEDITOR.instances){CKEDITOR.instances[i].insertHtml('$text');}");

        $id = $this->waitForElement($selector)->getAttribute('id');
        $this->getDriver()->wait(10000, "CKEDITOR.status == 'loaded'");
        sleep(1);
        $this->getDriver()->evaluateScript("CKEDITOR.instances['$id'].insertHtml('$text');");
    }

    # --------------- window and wait methods ------------------

    /**
     * Waits seconds for element
     * @param string $element
     * @param int $seconds
     * @return \Behat\Mink\Element\NodeElement|mixed|null
     * @throws \Exception
     */
    public function waitForElement($element, $seconds = 10)
    {
        $count = 0;
        do {
            $node = $this->find($element);

            if ($node !== null) {
                break;
            } else {
                $count++;
                sleep(1);
            }
        } while ($count < $seconds);

        if ($count >= $seconds) {
            throw new \Exception("Element $element was not found before $seconds timeout!");
        }

        return $node;
    }

    /**
     * Waits seconds until element is visible
     * @param string $element
     * @param int $seconds
     * @return \Behat\Mink\Element\NodeElement|mixed|null
     * @throws \Exception
     */
    public function waitUntilIsVisible($element, $seconds = 10)
    {
        $count = 0;
        do {
            $node = $this->find($element);

            if ($node !== null) {
                $status = 'was not visible';
                if ($node->isVisible()) {
                    break;
                }
            } else {
                $status = 'was not found';
            }
            $count++;
            sleep(1);
        } while ($count < $seconds);

        if ($count >= $seconds) {
            throw new \Exception("Element $element $status before $seconds seconds timeout!");
        }

        return $node;
    }

    /**
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function waitForThePageToBeLoaded(){
        $this->getDriver()->wait(20000, "document.readyState === 'complete'");
    }

    /**
     * @param $selector
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     * @throws \Exception
     */
    public function switchToIframeFromSelector($selector){
        $this->waitForElement($selector);
        $function = <<<JS
            (function(){
                 var elem = document.querySelector("$selector");
                 var iframe = elem.getElementsByTagName('iframe');
                 iframe[0].name = "myIframe";
            })()
JS;
        try {
            $this->getDriver()->executeScript($function);
        } catch (Exception $e) {
            print_r($e->getMessage());
            throw new \Exception("Element $selector was NOT found." . PHP_EOL . $e->getMessage());
        }
        $this->getDriver()->switchToIFrame("myIframe");
    }

    /**
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function switchToNewWindow()
    {
        $wdSession = $this->getDriver()->getWebDriverSession();
        $windows = $wdSession->window_handles();
        $this->getDriver()->switchToWindow(array_pop($windows));
    }

    /**
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function scrollToTop(){
        $this->getDriver()->executeScript('window.scrollTo(0,0);');
    }

    /**
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function scrollToBottom(){
        $this->getDriver()->executeScript('window.scrollTo(0,document.body.scrollHeight);');
    }

    /**
     * @param $file
     * @throws \Exception
     */
    public function attachFileTo($file, $selector) {
        # get the path of the file
        $path = $this->getParameter('files_path') .DIRECTORY_SEPARATOR. $file;
        $this->waitForElement($selector)->attachFile($path);
    }

    # --------------------- debug & logs ----------------------

    /**
     * Write message to console
     *
     * @param $message
     * @param null $description
     */
    public function log($message, $description = null)
    {
        $log = '';
        if (is_array($message)) {
            $log  = implode(' | ', $message);
        } else {
            $log = $message;
        }

        if ($description !== null)
        {
            if (is_array($description)) {
                $log  = $log . ": " .implode(' | ', $description);
            } else {
                $log = $log . ": " . $description;
            }
        }

        echo $log . "\n";
    }

    # ----------------------- override ------------------------

    /**
     * @param string $locator
     * @param null $selector
     * @return \Behat\Mink\Element\NodeElement|mixed|null
     */
    public function find($locator, $selector = null){
        return Page::find($locator, 'default');
    }

    /**
     * @param string $locator
     * @param null $selector
     * @return \Behat\Mink\Element\NodeElement[]
     */
    public function findAll($locator, $selector = null){
        if (strstr($locator, '//')) {
            $element = Page::findAll('xpath', $locator);
        } else {
            $element = Page::findAll('css', $locator);
        }
        return $element;
    }

    public function verify(array $urlParameters){
        $this->verifyResponse();
    }

    /**
     * @param null $path
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function visit($path = null){
        $url = $this->getParameter('base_url') . $path;
        $this->getDriver()->visit($url);
    }
}