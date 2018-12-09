# peakIT
# basicSetup


# PHP setup (windows)
----------

PHP libraries to enable:
(extension=php_curl.dll, extension=php_mbstring.dll, extension=php_openssl.dll)
 - locate php.ini or rename the php.ini-development to php.ini
 - remove semicolon(;) that is in front of the fallowing files: extension=php_curl.dll, extension=php_mbstring.dll, extension=php_openssl.dll
 - change path to the extension or copy them in the same directory with php.ini
 - save php.ini file


# Check all required tools are installed
git --version
java --version
php --version


# Behat install
---------------

1) clone repository
2) `cd` to peakIT directory
3) to install Behat run from command line:  
`composer install --prefer-dist`

`php compose.phar install --prefer-dist`

# Execute Automation Suite
--------------------------

### Local
Download selenium server standalone and chromedriver from [seleniumhq.org](http://www.seleniumhq.org/download/)

1) Start selenium server using from command line using:  
`java -Dwebdriver.chrome.driver=<path_to_chrome_driver> -jar <selenium_server_file>`
2) Run scenario/suite

### On BrowserStack from local
toAdd


### Behat command line

`bin/behat -p myProfile` - will execute all the scenario on myProfile

`bin/behat -p myProfile --tags @new` - will execute all the scenarios with the @new tag on myProfile

`bin/behat -p myProfile test.feature` - will execute test feature on myProfile

`bin/behat -p myProfile -vvv` - will show more details in the console/stacktrace

`bin/behat -dl` - prints all available definitions

`bin/behat --story-syntax` - prints a story syntax sample

`bin/behat -h` - behat help, shows all available commands and options

`bin/behat --config-file awesome.yml`