# Confluence PHP Client
A Confluence RESTful API client in PHP

An Object Oriented wrapper for Confluence

## Requirements

* PHP >= 7.4.0

## Installation

```bash
$ composer require cloudplaydev/confluence-php-client
```

## Usage

```php
<?php
declare(strict_types=1);

use CloudPlayDev\ConfluenceClient\ConfluenceClient;
use CloudPlayDev\ConfluenceClient\Api\Content;
use CloudPlayDev\ConfluenceClient\Entity\ContentPage;

//Create the Confluence Client
$client = new ConfluenceClient('https://url-to-conluence');

//authenticate with a private access token
$client->authenticate('NjU2OTA4NDI2MTY5OkBznOUO8YjaUF7KoOruZRXhILJ9');

//Create a confluence content page
$page = new ContentPage();

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

//get child content
$childContent = $client->content()->children($createdPage, Content::CONTENT_TYPE_PAGE);

//create a comment
$commentContent = $createdPage->createComment('test comment');
$createdComment = $client->content()->create($commentContent);

//update a comment
$createdComment->setContent('new comment');
$client->content()->update($createdComment);

//delete a content 
$client->content()->remove($createdComment);


```

