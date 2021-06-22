<?php
declare(strict_types=1);
/**
 * This file is part of the cloudplaydev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\ConfluenceClient\Tests;

use CloudPlayDev\ConfluenceClient\Client;
use CloudPlayDev\ConfluenceClient\Curl;
use CloudPlayDev\ConfluenceClient\Entity\ConfluencePage;
use CloudPlayDev\ConfluenceClient\Exception\Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientTest
 */
class ClientTest extends TestCase
{
    /**
     * Test create page
     */
    public function testCreatePage(): void
    {
        $url = 'some/url';
        $username = 'username';
        $password = 'password';
        $curl = $this->getMockBuilder(Curl::class)
            ->setConstructorArgs([$url, $username, $password])
            ->getMock();
        $curl->expects(self::once())
            ->method('setOptions')
            ->willReturnSelf();
        $curl->expects(self::once())
            ->method('execute')
            ->willReturn('{"result":true}');
        $client = new Client($curl);
        $page = new ConfluencePage();
        $response = $client->createPage($page);

        static::assertIsArray($response);
        self::assertEquals(['result' => true], $response);
    }

    /**
     * Test search page
     */
    public function testSelectBy(): void
    {
        $url = 'some/url';
        $username = 'username';
        $password = 'password';
        $curl = $this->getMockBuilder(Curl::class)
            ->setConstructorArgs([$url, $username, $password])
            ->getMock();
        $curl->expects(self::once())
            ->method('setOptions')
            ->willReturnSelf();
        $curl->expects(self::once())
            ->method('execute')
            ->willReturn('{"result":true}');
        $response = (new Client($curl))->selectPageBy(['title' => 'test']);

        self::assertNull($response);
    }

    /**
     * Test request function
     */
    public function testRequest(): void
    {
        $url = 'some/url';
        $username = 'username';
        $password = 'password';
        $curl = $this->getMockBuilder(Curl::class)
            ->setConstructorArgs([$url, $username, $password])
            ->getMock();
        $curl->expects(self::once())
            ->method('setOptions')
            ->willReturnSelf();
        $curl->expects(self::once())
            ->method('setOption')
            ->willReturnSelf();
        $curl->expects(self::once())
            ->method('execute')
            ->willReturn('{"result":true}');
        $response = (new Client($curl))->request('POST', $url, ['id' => 123]);

        self::assertIsArray($response);
        self::assertEquals(['result'=>true], $response);
    }

    /**
     * Test the exception when put invalid method
     */
    public function testRequestWithInvalidMethod(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('#.*Invalid method.*#');
        $url = 'some/url';
        $username = 'username';
        $password = 'password';
        $curl = $this->getMockBuilder(Curl::class)
            ->setConstructorArgs([$url, $username, $password])
            ->getMock();
        $curl
            ->method('setOptions')
            ->willReturnSelf();
        $curl
            ->method('setOption')
            ->willReturnSelf();
        $curl
            ->method('execute')
            ->willReturn(['result' => true]);
        $client = new Client($curl);
        $client->request('TEST', $url, ['id' => 123]);
    }
}