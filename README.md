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

use CloudPlayDev\ConfluenceClient\ConfluenceClient;
use CloudPlayDev\ConfluenceClient\Entity\Content as ContentEntity;

//Create the Confluence Client
$client = new ConfluenceClient('https://url-to-conluence');

//authenticate with a private access token
$client->authenticate('NjU2OTA4NDI2MTY5OkBznOUO8YjaUF7KoOruZRXhILJ9');

//Create a confluence content
$page = new ContentEntity();

//Configure your page
$page->setSpace('testSpaceKey')
    ->setTitle('Test')
    ->setContent('<p>test page</p>');

//Create the page in confluence in the test space
$client->content()->create($page);

//Get the page we created
$createdPage = $client->content()->findOneBy([
    'spaceKey' => 'testSpaceKey',
    'title' => 'Test'
]);

//Update page content
$createdPage->setContent('some new content');
$client->content()->update($createdPage);



```


### Get your development instance

Atlassian changed the way to work on Confluence/Jira, now in order to create your plugin, you have to get a [Developer Account](http://go.atlassian.com/cloud-dev) and create your own instance. All the steps to create your environment are defined on the [documentation page](https://developer.atlassian.com/static/connect/docs/latest/guides/development-setup.html).

Once you have access to your own Atlassian Cloud instance and you put it in developer mode, we can continue and let the instance contact us.

