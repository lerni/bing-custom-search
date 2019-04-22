<?php

namespace Kraftausdruck\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use GuzzleHttp\Client;

/**
 * Class BingSearchExtender
 */
class BingSearchExtender extends DataExtension
{
	private static $allowed_actions = array(
		'SeachForm'
	);

	public function Search($data, Form $form)
	{
		$ApiKey = Config::inst()->get(static::class, 'AccessKey');
		$CustomConfig = Config::inst()->get(static::class, 'CustomConfig');
		$saveQuery = Convert::raw2sql(trim($data['Search']));
		unset($decodedresponse);
		$results = ArrayList::create();

		if(strlen($saveQuery))
		{
			$client = new Client([
				// Base URI is used with relative requests
				'base_uri' => 'https://api.cognitive.microsoft.com',
				'timeout'  => 2.0
			]);

			$response = $client->request('GET','/bingcustomsearch/v7.0/search', [
				// 'debug' => true,
				'headers' => [
					'Ocp-Apim-Subscription-Key' => $ApiKey
				],
				'query' => [
					'q' => $saveQuery,
					'customconfig' => $CustomConfig,
					'mkt' => 'de-CH'
				]
			]);
			$decodedresponse = json_decode($response->getBody(), true);
		}

		if (isset($decodedresponse['webPages']))
		{
			foreach ($decodedresponse['webPages']['value'] as $result)
			{
				$m = DataObject::create();

				if (isset($result['id'])) {
					$m->ID = $result['id'];
				}
				if (isset($result['url'])) {
					$m->URL = $result['url'];
				}
				if (isset($result['name'])) {
					$m->Name = $result['name'];
				}
				if (isset($result['snippet'])) {
					$m->Description = $result['snippet'];
				}
				if (isset($result['url'])) {
					$m->URL = $result['url'];
				}

				$results->push($m);
			}
		}
		return $this->owner->customise(array('SearchResults' => $results));
	}

	public function SearchResults()
	{
		if (isset($data) && array_key_exists('Search',$data))
		{
			return $this->Search();
		}
	}

	public function SeachForm()
	{
		$fields = new FieldList(
			TextField::create('Search', '')
		);

		$actions = new FieldList(
			FormAction::create('Search')->setTitle('Suchen')
		);

		//$required = new RequiredFields('Search');

		$form = new Form($this->owner, 'SeachForm', $fields, $actions);
		$form->setTemplate('SearchForm');
		$form->setAttribute('up-target', '.floating-content .search .txt');

		$form->setFormMethod('GET')
			->disableSecurityToken()
			->loadDataFrom($this->owner->request->getVars());

		return $form;
	}
}