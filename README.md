# Confluence PHP Client
A Confluence RESTful API client in PHP

An Object Oriented wrapper for Confluence

## Requirements

* PHP >= 7.4.0

## Installation

```bash
$ php composer.phar require cloudplaydev/confluence-php-client
```

## Usage

```php
<?php
declare(strict_types=1);

use CloudPlayDev\ConfluenceClient\Client;
use CloudPlayDev\ConfluenceClient\Curl;
use CloudPlayDev\ConfluenceClient\Entity\ConfluencePage;

//Create and configure a curl web client
$curl = new Curl('confluence_host_url','username','password');
// $curl = new CurlTokenAuth('confluence_host_url','NjU9OTXA4NDI2MRY5OkBznOUO8YjaUF7KoOruZRXhILJ9');

//Create the Confluence Client
$client = new Client($curl);

//Create a confluence page
$page = new ConfluencePage();

//Configure your page
$page->setSpace('testSpaceKey')->setTitle('Test')->setContent('<p>test page</p>');

//Create the page in confluence in the test space
$client->createPage($page);

//Get the page we created
$client->selectPageBy([
    'spaceKey' => 'testSpaceKey',
    'title' => 'Test'
]);

```


### Get your development instance

Atlassian changed the way to work on Confluence/Jira, now in order to create your plugin, you have to get a [Developer Account](http://go.atlassian.com/cloud-dev) and create your own instance. All the steps to create your environment are defined on the [documentation page](https://developer.atlassian.com/static/connect/docs/latest/guides/development-setup.html).

Once you have access to your own Atlassian Cloud instance and you put it in developer mode, we can continue and let the instance contact us.

