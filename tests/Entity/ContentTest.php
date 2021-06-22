<?php
declare(strict_types=1);
/**
 * This file is part of the cloudplaydev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\Tests\ConfluenceClient\Entity;

use CloudPlayDev\ConfluenceClient\Entity\Content;
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
        $confluencePage = new Content();
        self::assertNull($confluencePage->getSpace());
    }

    /**
     * Test set space
     */
    public function testSetSpace(): void
    {
        self::assertClassHasAttribute('space', Content::class);
        $confluencePage = new Content();
        $confluencePage->setSpace('TEST');
        static::assertSame('TEST', $confluencePage->getSpace());
    }

    /**
     * Test set id
     */
    public function testSetId(): void
    {
        self::assertClassHasAttribute('id', Content::class);
        $confluencePage = new Content();
        $confluencePage->setId(123);
        self::assertSame(123, $confluencePage->getId());
    }

}