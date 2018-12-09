<?php
namespace Page;

use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
class MagentoApi extends BasePage
{

    private $token = null;
    private $client;
    private $response;
    private $request;

    /**
     * Contains api url's in key => value pair
     *
     * @var array
     */
    private $uri = array(
        #'sample' => array('http_verb', 'uri')
        'token' => array('POST', '/rest/V1/integration/admin/token'),
        'customers' => array('POST', '/rest/default/V1/customers'),
        'delete customer' => array('DELETE', '/rest/default/V1/customers'),
    );

    /**
     * Set token for api calls
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setToken($credentials)
    {
        # create client
        $client = new Client(['base_uri' => $this->getParameter('base_url')]);
        // TODO - move credentials in data or as params in yml file
        $auth = array('username' => $credentials['user'], 'password' => $credentials['password']);

        # get token
        $response = $client->request($this->uri['token'][0], $this->uri['token'][1],
            [
                'body' => json_encode($auth),
                'headers' => [
                    'cache-control' => 'no-cache',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:53.0) Gecko/20100101 Firefox/53.0'
                ],
                'verify' => false,
                'debug' => true
            ]);

        $this->token = $response->getBody()->getContents();
    }

    /**
     * General method for api call
     * @param $method
     * @param $payload
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function call($method, $payload)
    {
        $this->log($payload);

        if (!array_key_exists($method, $this->uri)){
            throw new \Exception('Please use an existing path!');
        }

        if ($this->token === null){
            throw new \Exception('Please call setToken() before this method to set the token!');
        }

        # create client
        $client = new Client(['base_uri' => $this->getParameter('base_url')]);

        # set the request
        $response = $client->request($this->uri[$method][0], $this->uri[$method][1], [
            'body' => json_encode($payload),
            'headers' => [
                'cache-control' => 'no-cache',
                'Accept' => 'application/json',
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . json_decode($this->token),
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:53.0) Gecko/20100101 Firefox/53.0'
            ],
            'allow_redirects' => true,
            'verify' => false,
            'debug' => true,
//            'on_stats' => function (TransferStats $stats) use (&$url) {
//                $this->request = $stats->getEffectiveUri()->getPath() ."?". $stats->getEffectiveUri()->getQuery();
//            }
        ]);

        $this->response = $response->getBody()->getContents();
    }

    /**
     * @param $details
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createCustomer($details)
    {
        // TODO - add try-catch
        $this->call('customers', $details);
    }
}