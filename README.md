# Confluence PHP Client
[![CI](https://github.com/CloudPlayDev/confluence-php-client/actions/workflows/ci.yml/badge.svg)](https://github.com/CloudPlayDev/confluence-php-client/actions/workflows/ci.yml) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/CloudPlayDev/confluence-php-client/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/CloudPlayDev/confluence-php-client/?branch=main)

A Confluence RESTful API client in PHP

An Object Oriented wrapper for Confluence

## Requirements

* PHP >= 7.4.0

## Installation

```bash
$ composer require cloudplaydev/confluence-php-client
```

## Usage

### Authentication

#### Using Personal Access Tokens
```php
use CloudPlayDev\ConfluenceClient\ConfluenceClient;

$client = new ConfluenceClient('https://url-to-conluence');

//authenticate with a private access token
//@see https://confluence.atlassian.com/enterprise/using-personal-access-tokens-1026032365.html
$client->authenticate('NjU2OTA4NDI2MTY5OkBznOUO8YjaUF7KoOruZRXhILJ9');
```
#### Using BaseAuth
```php
$client = new ConfluenceClient('https://USERNAME:PASSWORD@url-to-conluence');
```
or
```php
use CloudPlayDev\ConfluenceClient\ConfluenceClient;

$client = new ConfluenceClient('https://url-to-conluence');
$client->authenticateBasicAuth('USERNAME', 'PASSWORD');
```

### Fetch pages, comments and attachments

#### Find pages by title and space key
```php
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */


//Get the page we created
$searchResults = $client->content()->find([
    'spaceKey' => 'testSpaceKey',
    'title' => 'Test'
]);

//first page
$createdPage = $searchResults->getResultAt(0);
```

#### Browse content with pagination
```php
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

$limit = 100;
$start = 10;

//Get the search results with pagination
$searchResults = $client->content()->find([
    'spaceKey' => 'testSpaceKey',
    'title' => 'Test'
], $limit, $start);

//check if there are more results
while(!$searchResults->isLastPage()) {
    //get the next pages
    $nextPages = $client->content()->find([
        'spaceKey' => 'testSpaceKey',
        'title' => 'Test'
    ], $limit, $searchResults->getStart() + $limit);
}
``` 

#### Fetch a page or comment by content id
```php
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

//Get a page or comment
$resultContent = $client->content()->get(1234567890);
```

#### Fetch old versions of a page or comment by content id
```php
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

//Get a page or comment in a specific version
$resultContentInVersion2 = $client->content()->get(1234567890, 2);
```

#### Fetch page descendants
```php
use CloudPlayDev\ConfluenceClient\Api\Content;
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */
/* @var $page CloudPlayDev\ConfluenceClient\Entity\ContentPage */

//get child content
$childContent = $client->content()->children($page, Content::CONTENT_TYPE_PAGE); //\CloudPlayDev\ConfluenceClient\Entity\ContentSearchResult
```

#### Fetch content history
```php
use CloudPlayDev\ConfluenceClient\Api\Content;
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

$pageId = 2323232323;
$historyData = $client->content()->history($pageId); // \CloudPlayDev\ConfluenceClient\Entity\ContentHistory
```

### Manipulating  content

#### Create new page
```php
use CloudPlayDev\ConfluenceClient\Entity\ContentPage;
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

//Create a confluence content page
$page = new ContentPage();

//Configure your page
$page->setSpace('testSpaceKey')
    ->setTitle('Test')
    ->setContent('<p>test page</p>');

//Create the page in confluence in the test space
$client->content()->create($page);
```

#### Create new comment
```php
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

//get a page by id
$page = $client->content()->get(123456789);

//attach a comment to the page
$comment = $page->createComment('my comment text');

//save the comment
$client->content()->create($comment);
```

#### Create subpage
```php
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

//get a page by id
$page = $client->content()->get(123456789);

//attach a subpage to page
$subPage = $page->createSubpage('subpage title', 'subpage content');

//save the page
$client->content()->create($subPage);
```

#### Update content
```php
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

//get content by id
$page = $client->content()->get(123456789);

//change content
$page->setContent('new content')
    ->setTitle('new title');

//save the changes
$client->content()->update($page);
```

#### Delete content
```php
/* @var $client CloudPlayDev\ConfluenceClient\ConfluenceClient */

//get content by id
$page = $client->content()->get(123456789);

//delete content
$client->content()->delete($page);
```


# FAQ
## How to authenticate with Confuence cloud?
You have to use basic auth with your username and password. You can also use a personal access token instead of your password. See [Using personal access tokens](https://confluence.atlassian.com/enterprise/using-personal-access-tokens-1026032365.html) for more information.

Create a new API token here: https://id.atlassian.com/manage-profile/security/api-tokens

```php
use CloudPlayDev\ConfluenceClient\ConfluenceClient;

$client = new ConfluenceClient('https://xxxxxxxx.atlassian.net/wiki/');
$client->authenticateBasicAuth('USERNAME', 'TOKEN');
```
