<?php
declare(strict_types=1);

namespace CloudPlayDev\Tests\ConfluenceClient\Api;

use CloudPlayDev\ConfluenceClient\Api\Content;
use CloudPlayDev\ConfluenceClient\Entity\ContentComment;
use CloudPlayDev\ConfluenceClient\Entity\ContentHistory;
use CloudPlayDev\ConfluenceClient\Entity\ContentPage;
use CloudPlayDev\ConfluenceClient\Exception\ConfluencePhpClientException;
use CloudPlayDev\ConfluenceClient\Exception\HttpClientException;
use CloudPlayDev\ConfluenceClient\Exception\HttpServerException;
use Http\Client\Exception;
use Webmozart\Assert\InvalidArgumentException;
use function PHPUnit\Framework\assertEquals;

class ContentTest extends TestCase
{

    private const PAGE_CONTENT = [
        'id' => 1234556,
        'title' => 'Test title',
        '_links' => [
            'self' => 'https://example.com/content/1234556'
        ],
        'type' => 'page'
    ];
    private const PAGE_HISTORY= [
        'createdDate' => '2023-10-02T09:23:58.344Z',
        'createdBy' => [
            'type' => 'known',
            'accountId' => '123456',
            'accountType' => 'atlassian',
            'email' => 'demo@example.com',
            'publicName' => 'Demo User',
            'displayName' => 'Demo User',
            'isExternalCollaborator' => false,
            'profilePicture' => [
                'path' => 'https://example.com/profile/123456',
                'width' => 48,
                'height' => 48,
                'isDefault' => false
            ]

        ],
        'lastUpdated' => [
            'by' => [
                'type' => 'known',
                'accountId' => '123456',
                'accountType' => 'atlassian',
                'email' => 'demo@example.com',
                'publicName' => 'Demo User',
                'displayName' => 'Demo User',
                'isExternalCollaborator' => false,
                'profilePicture' => [
                    'path' => 'https://example.com/profile/123456',
                    'width' => 48,
                    'height' => 48,
                    'isDefault' => false
                ]
            ],
            'when' => '2023-10-02T09:23:58.344Z',
            'number' => 1234556
        ],
        'latest' => true
    ];
    private const COMMENT_CONTENT = [
        'id' => 1234556,
        'title' => 'Test title',
        '_links' => [
            'self' => 'https://example.com/content/1234556'
        ],
        'type' => 'comment'
    ];
    private const COMMENT_2_CONTENT = [
        'id' => 1234556,
        'title' => 'Test title',
        '_links' => [
            'self' => 'https://example.com/content/1234556'
        ],
        'type' => 'comment'
    ];
    private const PAGE_CONTENT_RESULTS = [
        'size' => 1,
        'results' => [self::PAGE_CONTENT]
    ];

    private const PAGE_CONTENT_RESULTS_PAGING = [
        'size' => 2,
        'limit' => 2,
        'start' => 0,
        'results' => [self::PAGE_CONTENT],
        '_links' => [
            'next' => 'https://example.com/content/1234556?start=2&limit=1'
        ]
    ];

    private const COMMENT_CONTENT_RESULTS = [
        'size' => 2,
        'results' => [self::COMMENT_CONTENT, self::COMMENT_2_CONTENT]
    ];


    protected function getApiClass(): string
    {
        return Content::class;
    }

    public function testCanGetContentById(): void
    {
        $api = $this->getApiMock();

        $api->expects(self::once())
            ->method('httpGet')
            ->with('content/123456')
            ->willReturn($this->createResponse(json_encode(self::PAGE_CONTENT, JSON_THROW_ON_ERROR)));

        $page = $api->get(123456);

        self::assertInstanceOf(ContentPage::class, $page);

        self::assertEquals(self::PAGE_CONTENT['id'], $page->getId());
        self::assertEquals(self::PAGE_CONTENT['title'], $page->getTitle());

    }

    public function testCanFindPages(): void
    {
        $api = $this->getApiMock();

        $queryParameter = [
            'spaceKey' => 'KEY',
            'title' => 'Content Name',
            'type' => 'comment',
            'expand' => 'space,version,body.storage,container'
        ];

        $api->expects(self::once())
            ->method('httpGet')
            ->with('content', $queryParameter)
            ->willReturn($this->createResponse(json_encode(self::PAGE_CONTENT_RESULTS, JSON_THROW_ON_ERROR)));

        $results = $api->find([
            'spaceKey' => 'KEY',
            'title' => 'Content Name',
            'type' => 'comment',
        ]);

        self::assertEquals(1, $results->getSize());

        self::assertInstanceOf(ContentPage::class, $results->getResultAt(0));
        self::assertTrue($results->isLastPage());

    }

    public function testCanDetectMoreResults(): void
    {
        $api = $this->getApiMock();

        $queryParameter = [
            'spaceKey' => 'KEY',
            'title' => 'Content Name',
            'type' => 'comment',
            'expand' => 'space,version,body.storage,container'
        ];

        $api->expects(self::once())
            ->method('httpGet')
            ->with('content', $queryParameter)
            ->willReturn($this->createResponse(json_encode(self::PAGE_CONTENT_RESULTS_PAGING, JSON_THROW_ON_ERROR)));

        $results = $api->find([
            'spaceKey' => 'KEY',
            'title' => 'Content Name',
            'type' => 'comment',
        ]);

        self::assertEquals(2, $results->getSize());
        self::assertEquals(2, $results->getLimit());
        self::assertEquals(0, $results->getStart());
        self::assertFalse($results->isLastPage());
    }

    public function testCanGetChildren(): void
    {
        $api = $this->getApiMock();

        $queryParameter = [
            'expand' => 'space,version,body.storage,container'
        ];

        $api->expects(self::once())
            ->method('httpGet')
            ->with('content/678/child/comment', $queryParameter)
            ->willReturn($this->createResponse(json_encode(self::COMMENT_CONTENT_RESULTS, JSON_THROW_ON_ERROR)));

        $page = new ContentPage();
        $page->setId(678);

        $results = $api->children($page, Content::CONTENT_TYPE_COMMENT);

        self::assertEquals(2, $results->getSize());

        self::assertInstanceOf(ContentComment::class, $results->getResultAt(0));
        self::assertInstanceOf(ContentComment::class, $results->getResultAt(1));

    }

    public function testCanGetDescendants(): void
    {
        $api = $this->getApiMock();

        $queryParameter = [
        ];

        $api->expects(self::once())
            ->method('httpGet')
            ->with('content/678/descendant/comment', $queryParameter)
            ->willReturn($this->createResponse(json_encode(self::COMMENT_CONTENT_RESULTS, JSON_THROW_ON_ERROR)));

        $page = new ContentPage();
        $page->setId(678);

        $results = $api->descendants($page, Content::CONTENT_TYPE_COMMENT);

        self::assertEquals(2, $results->getSize());

        self::assertInstanceOf(ContentComment::class, $results->getResultAt(0));
        self::assertInstanceOf(ContentComment::class, $results->getResultAt(1));

    }

    public function testCanUpdatePage(): void
    {
        $api = $this->getApiMock();

        $data = [
            'id' => 12,
            'type' => 'page',
            'title' => 'Test',
            'space' => [
                'key' => 'KEY'
            ],
            'body' => [
                'storage' => [
                    'value' => 'my text',
                    'representation' => 'storage'
                ]
            ],
            'version' => [
                'number' => 2
            ],
        ];

        $api->expects(self::once())
            ->method('httpPut')
            ->with('content/12', $data)
            ->willReturn($this->createResponse(json_encode(self::PAGE_CONTENT, JSON_THROW_ON_ERROR)));

        $content = new ContentPage();
        $content->setId(12);
        $content->setTitle('Test');
        $content->setVersion(1);
        $content->setContent('my text');
        $content->setSpace('KEY');

        $page = $api->update($content);

        self::assertInstanceOf(ContentPage::class, $page);
    }

    public function testCanCreatePage(): void
    {
        $api = $this->getApiMock();

        $data = [
            'type' => 'page',
            'title' => 'Test',
            'space' => [
                'key' => 'KEY'
            ],
            'body' => [
                'storage' => [
                    'value' => 'my text',
                    'representation' => 'storage'
                ]
            ],
        ];

        $api->expects(self::once())
            ->method('httpPost')
            ->with('content', [], $data)
            ->willReturn($this->createResponse(json_encode(self::PAGE_CONTENT, JSON_THROW_ON_ERROR)));

        $content = new ContentPage();
        $content->setTitle('Test');
        $content->setVersion(1);
        $content->setContent('my text');
        $content->setSpace('KEY');

        $page = $api->create($content);

        self::assertInstanceOf(ContentPage::class, $page);
    }

    public function testCantCreateSavedPage(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $api = $this->getApiMock();

        $content = new ContentPage();
        $content->setId(89);

        $api->create($content);

    }

    public function testCanDeletePage(): void
    {
        $api = $this->getApiMock();


        $api->expects(self::once())
            ->method('httpDelete')
            ->with('content/121')
            ->willReturn($this->createResponse(''));

        $content = new ContentPage();
        $content->setId(121);

        $api->delete($content);
    }

    /**
     * @dataProvider exceptionErrorCodes
     */
    public function testCanHandleHttpError400(int $errorCode, string $class): void
    {
        $this->expectException($class);
        $api = $this->getApiMock();

        $api->expects(self::once())
            ->method('httpGet')
            ->willReturn($this->createResponse('', $errorCode));

        $api->get(1);
    }

    /**
     * @throws Exception
     * @throws ConfluencePhpClientException
     * @throws \JsonException
     */
    public function testCanExtractErrorMessageFromResponse(): void
    {
        $this->expectException(HttpClientException::class);
        $this->expectExceptionMessage('Error message');

        $api = $this->getApiMock();

        $api->expects(self::once())
            ->method('httpGet')
            ->willReturn($this->createResponse('{"message": "Error message"}', 403));

        $api->get(1);
    }

    public static function exceptionErrorCodes(): array
    {
        return [
            400 => [400, HttpClientException::class],
            401 => [401, HttpClientException::class],
            402 => [402, HttpClientException::class],
            403 => [403, HttpClientException::class],
            404 => [404, HttpClientException::class],
            409 => [409, HttpClientException::class],
            413 => [413, HttpClientException::class],
            429 => [429, HttpClientException::class],
            500 => [500, HttpServerException::class],
            501 => [501, ConfluencePhpClientException::class],
        ];
    }


    public function testCanGetHistory(): void
    {
        $api = $this->getApiMock();

        $api->expects(self::once())
            ->method('httpGet')
            ->with('content/123456/history')
            ->willReturn($this->createResponse(json_encode(self::PAGE_HISTORY, JSON_THROW_ON_ERROR)));

        $page = $api->history(123456);

        self::assertInstanceOf(ContentHistory::class, $page);

        self::assertTrue($page->isLatest());
        self::assertEquals('Demo User', $page->getCreatedBy()->getDisplayName());


    }
}
