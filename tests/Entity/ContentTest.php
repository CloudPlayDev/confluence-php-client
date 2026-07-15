<?php
declare(strict_types=1);
/**
 * This file is part of the cloudplaydev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\Tests\ConfluenceClient\Entity;

use CloudPlayDev\ConfluenceClient\Entity\AbstractContent;
use CloudPlayDev\ConfluenceClient\Entity\ContentAttachment;
use CloudPlayDev\ConfluenceClient\Entity\ContentPage;
use CloudPlayDev\ConfluenceClient\Entity\ContentSearchResult;
use CloudPlayDev\ConfluenceClient\Exception\HydrationException;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfluencePageModelTest
 */
class ContentTest extends TestCase
{
    /**
     * Test get space
     */
    public function testGetSpace(): void
    {
        $confluencePage = new ContentPage();
        self::assertNull($confluencePage->getSpace());
    }

    /**
     * Test set space
     */
    public function testSetSpace(): void
    {
        $confluencePage = new ContentPage();
        $confluencePage->setSpace('TEST');
        static::assertSame('TEST', $confluencePage->getSpace());
    }

    /**
     * Test set id
     */
    public function testSetId(): void
    {
        $confluencePage = new ContentPage();
        $confluencePage->setId(123);
        self::assertSame(123, $confluencePage->getId());
    }

    public function testLoadAttachment(): void
    {
        $attachment = AbstractContent::load(self::attachmentPayload());

        self::assertInstanceOf(ContentAttachment::class, $attachment);
        self::assertSame('attachment', $attachment->getType());
    }

    public function testSearchResultLoadsAttachment(): void
    {
        $searchResult = ContentSearchResult::load([
            'size' => 1,
            'results' => [self::attachmentPayload()],
        ]);

        self::assertInstanceOf(ContentAttachment::class, $searchResult->getResultAt(0));
    }

    /**
     * @return mixed[]
     */
    private static function attachmentPayload(): array
    {
        return [
            'id' => '123456',
            'type' => 'attachment',
            'title' => 'example.pdf',
            '_links' => [
                'self' => 'https://example.test/rest/api/content/123456',
            ],
        ];
    }

}
