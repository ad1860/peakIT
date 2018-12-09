<?php
use Data\Data,
    Data\ApiData;

use Page\MagentoApi;
use Behat\Behat\Context\Context;
class MagentoApiContext implements Context
{

    private $magentoApi;

    public function __construct(MagentoApi $magentoApi)
    {
        $this->magentoApi = $magentoApi;
    }

    /**
     * @Given /^I have a new user( with (address|no address|newsletter subscription))?$/
     * @param null $details
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function iHaveANewUser($details = null)
    {
        $payload = ApiData::generateNewAccountDetails();
//        Data::$email = $payload['customer']['email'];

        $this->magentoApi->setToken(Data::getAdminCredentials());
        $this->magentoApi->call('customers', $payload);
    }
}