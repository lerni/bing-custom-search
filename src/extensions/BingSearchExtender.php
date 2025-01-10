<?php

namespace Kraftausdruck\Extensions;


use GuzzleHttp\Client;
use SilverStripe\Forms\Form;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Core\Environment;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\ViewableData;
use SilverStripe\Core\Config\Config;


class BingSearchExtender extends DataExtension
{

    private static $azure_timeout = 2.0;

    private static $allowed_actions = [
        'SearchForm'
    ];

    public function Search($data, Form $form)
    {

        $apiKey = Environment::getEnv('AZURE_COGNITIVE_SEARCH_KEY') ?: Config::inst()->get(static::class, 'AccessKey');
        $customConfig = Environment::getEnv('AZURE_COGNITIVE_SEARCH_CUSTOMCONFIG') ?: Config::inst()->get(static::class, 'CustomConfig');

        $saveQuery = Convert::raw2sql(trim($data['Search']));
        unset($decodedresponse);
        $results = ArrayList::create();
        $timeout = Config::inst()->get(static::class, 'azure_timeout');

        if (strlen($saveQuery)) {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => 'https://api.bing.microsoft.com',
                'timeout'  => $timeout
            ]);
            $response = $client->request('GET', '/v7.0/custom/search', [
                // 'debug' => true,
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $apiKey
                ],
                'query' => [
                    'q' => $saveQuery,
                    'customconfig' => $customConfig,
                    'mkt' => 'de-CH'
                ]
            ]);
            $decodedresponse = json_decode($response->getBody(), true);
        }

        if (isset($decodedresponse['webPages'])) {
            foreach ($decodedresponse['webPages']['value'] as $result) {
                $m = ViewableData::create();

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
                if (isset($result['openGraphImage']) && isset($result['openGraphImage']['contentUrl'])) {
                    // ensure https
                    $url = preg_replace("/^http:/i", "https:", $result['openGraphImage']['contentUrl']);
                    $m->OpenGraphImageURL = $url;
                }

                $results->push($m);
            }
        }
        $customize = ArrayList::create();
        $customize->SearchResults = $results;

        return $customize->renderWith('Kraftausdruck/SearchResults');
    }

    public function SearchResults()
    {
        if (isset($data) && array_key_exists('Search', $data)) {
            return $this->Search();
        }
    }

    public function SearchForm()
    {
        $fields = new FieldList(
            TextField::create('Search', '')
        );

        $actions = new FieldList(
            FormAction::create('Search')->setTitle('Suchen')
        );


        //$required = new RequiredFields('Search');

        $form = new Form($this->owner, 'SearchForm', $fields, $actions);
        $form->setTemplate('SearchForm');
        $form->setAttribute('data-hx-target', '.search-results');
        $form->setAttribute('data-hx-get', $form->FormAction());
        $form->setAttribute('data-hx-indicator', '.loader.search');

        $form->setFormMethod('GET')
            ->disableSecurityToken()
            ->loadDataFrom($this->owner->request->getVars());

        $form->setTemplate('Kraftausdruck/SearchForm');

        return $form;
    }
}
