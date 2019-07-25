<?php
namespace SquareServerless;

use Aws\SecretsManager\SecretsManagerClient;

class SquareSecrets {

	private $clientId;
	private $clientSecret;

	public function __construct()
	{
	    // if the info is available in the environment, use it
	    if (isset($_ENV['SQUARE_APP_ID']) && isset($_ENV['SQUARE_SECRET'])) {
	        $this->clientSecret = $_ENV['SQUARE_SECRET'];
	        $this->clientId = $_ENV['SQUARE_APP_ID'];
        } else {
            // otherwise assume it is fetchable from AWS
            $client = new SecretsManagerClient([
                'version' => '2017-10-17',
                'region' => 'us-west-1',
            ]);
            $secretName = 'square_serverless_php';
            $result = $client->getSecretValue([
                'SecretId' => $secretName,
            ]);
            if (isset($result['SecretString'])) {
                $secret = $result['SecretString'];
            } else {
                $secret = base64_decode($result['SecretBinary']);
            }
            $squareSecret = json_decode($secret, true);
            if (isset($squareSecret['client-id'])) {
                $this->clientId = $squareSecret['client-id'];
            }
            if (isset($squareSecret['client-secret'])) {
                $this->clientSecret = $squareSecret['client-secret'];
            }
        }
	}

    /**
     * @return string
     */
	public function getClientID() : string
	{
		return $this->clientId;
	}

    /**
     * @return string
     */
	public function getClientSecret() : string
	{
		return $this->clientSecret;
	}

    /**
     * @return string
     */
    public function getOauthSecret() : string
    {
        return $this->oauthSecret;
    }

    /**
     * @param string $configuration
     * @throws \Exception
     */
	public function setAccessToken($configuration = 'default') : void
    {
        if ($configuration != 'default') {
            // todo: handle more than the default configuration
            throw new \Exception('unsupported configuration');
        }
        \SquareConnect\Configuration::getDefaultConfiguration()->setAccessToken($this->clientSecret);
    }
}
