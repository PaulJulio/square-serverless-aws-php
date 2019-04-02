<?php
namespace SquareServerless;

class Locations {

	private $locations;
	private $error;

	public function __construct()
	{
		$this->fetch();
	}

	public function fetch() : void
	{
		$sqSecret = new SquareSecrets();
		\SquareConnect\Configuration::getDefaultConfiguration()->setAccessToken($sqSecret->getClientSecret());
		$locations_api = new \SquareConnect\Api\LocationsApi();
		$response = $locations_api->listLocations();
		$this->error = $response->getErrors();
		$this->locations = [];
		foreach($response->getLocations() as $location) {
			$this->locations[] = $location;
		}
	}

	public function getAsArray() : array
	{
		$locations = [];
		foreach ($this->locations as $locationObject) {
			$addressObject = $locationObject->getAddress();
			if (isset($addressObject)) {
				$address = [
					'address_line_1' => $addressObject->getAddressLine1(),
					'locality' => $addressObject->getLocality(),
					'administrative_district_level_1' => $addressObject->getAdministrativeDistrictLevel1(),
					'postal_code' => $addressObject->getPostalCode(),
					'country' => $addressObject->getCountry(),
				];
			} else {
				$address = null;
			}
			$location = [
				'id' => $locationObject->getId(),
				'name' => $locationObject->getName(),
				'address' => $address,
				'timezone' => $locationObject->getTimezone(),
				'capabilities' => $locationObject->getCapabilities(),
				'status' => $locationObject->getStatus(),
				'created_at' => $locationObject->getCreatedAt(),
				'merchant_id' => $locationObject->getMerchantId(),
				'country' => $locationObject->getCountry(),
				'language_code' => $locationObject->getLanguageCode(),
				'currency' => $locationObject->getCurrency(),
				'phone_number' => $locationObject->getPhoneNumber(),
				'business_name' => $locationObject->getBusinessName(),
				// see more attributes at https://github.com/square/connect-php-sdk/blob/cbea6864893c4c2a6baf35c414f532bdf88640f9/docs/Model/V1Merchant.md
			];
			$locations[] = $location;
		}
		return $locations;
	}

	public function getError() : string
	{
		if (!isset($this->error)) {
			return '';
		}
		return $this->error;
	}
}
