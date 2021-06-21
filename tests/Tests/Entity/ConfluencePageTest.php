<?php
declare(strict_types=1);
/**
 * This file is part of the CloudPlayDev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\ConfluenceClient\Tests\Entity;

use CloudPlayDev\ConfluenceClient\Entity\ConfluencePage;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfluencePageModelTest
 */
class ConfluencePageTest extends TestCase
{
    /**
     * Test get space
     */
    public function testGetSpace(): void
    {
        $confluencePage = new ConfluencePage();
        self::assertNull($confluencePage->getSpace());
    }

    /**
     * Test set space
     */
    public function testSetSpace(): void
    {
        self::assertClassHasAttribute('space', ConfluencePage::class);
        $confluencePage = new ConfluencePage();
        $confluencePage->setSpace('TEST');
        static::assertSame('TEST', $confluencePage->getSpace());
    }

    /**
     * Test set id
     */
    public function testSetId(): void
    {
        self::assertClassHasAttribute('id', ConfluencePage::class);
        $confluencePage = new ConfluencePage();
        $confluencePage->setId('123');
        self::assertSame('123', $confluencePage->getId());
    }

}