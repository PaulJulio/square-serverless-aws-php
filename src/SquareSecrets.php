<?php
namespace SquareServerless;

use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Aws\SecretsManager\SecretsManagerClient;

class SquareSecrets {

	private $clientId;
	private $clientSecret;

	public function __construct()
	{
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
		$this->clientId = $squareSecret['client-id'];
		$this->clientSecret = $squareSecret['client-secret'];
	}

	public function getClientID() : string
	{
		return $this->clientId;
	}

	public function getClientSecret() : string
	{
		return $this->clientSecret;
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
