<?php
declare(strict_types=1);
/**
 * This file is part of the CloudPlayDev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\ConfluenceClient;

use CloudPlayDev\ConfluenceClient\Entity\ConfluencePage;
use InvalidArgumentException;
use function in_array;
use function is_array;

class Client
{
    private Curl $curl;

    /**
     * Class constructor
     * @param Curl $curl
     */
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    /**
     * Create new page
     *
     * @param ConfluencePage $page
     * @return mixed
     * @throws Exception
     */
    public function createPage(ConfluencePage $page)
    {
        $data = [
            'type' => $page->getType(),
            'title' => $page->getTitle(),
            'ancestors' => $page->getAncestors(),
            'space' => ['key' => $page->getSpace()],
            'history' => [
                'createdDate' => $page->getCreatedDate() ?: date('Y-m-d\TH:i:s.uP')
            ],
            'body' => [
                'storage' => [
                    'value' => $page->getContent(),
                    'representation' => 'storage',
                ],
            ],
        ];

        return $this->request('GET', $this->curl->getHost() . '/content', $data);
    }


    /**
     * Update an existing page
     *
     * @param ConfluencePage $page
     * @return mixed
     * @throws Exception
     */
    public function updatePage(ConfluencePage $page)
    {
        $data = [
            'id' => $page->getId(),
            'type' => $page->getType(),
            'title' => $page->getTitle(),
            'space' => ['key' => $page->getSpace()],
            'body' => [
                'storage' => [
                    'value' => $page->getContent(),
                    'representation' => 'storage',
                ],
            ],
            'version' => ['number' => $page->getVersion()]
        ];

        return $this->request('POST', $this->curl->getHost() . "/content/{$page->getId()}", $data);
    }

    /**
     * Delete a page
     *
     * @param string $id
     * @return mixed
     * @throws Exception
     */
    public function deletePage(string $id)
    {
        return $this->request('DELETE', $this->curl->getHost() . "/content/$id");
    }

    /**
     * Search page by title, space key, type or id
     * @param array<string, string> $parameters
     * @return mixed
     * @throws Exception
     */
    public function selectPageBy(array $parameters = [])
    {
        $url = $this->curl->getHost() . '/content?';
        if (isset($parameters['title'])) {
            $url .= "title={$parameters['title']}&";
        }
        if (isset($parameters['spaceKey'])) {
            $url .= "spaceKey={$parameters['spaceKey']}&";
        }
        if (isset($parameters['type'])) {
            $url .= "type={$parameters['type']}&";
        }
        if (isset($parameters['id'])) {
            $url = $this->curl->getHost() . '/content/' . $parameters['id'] . '?';
        }
        if (isset($parameters['expand'])) {
            $url .= 'expand=' . $parameters['expand'];
        }

        return $this->request('GET', $url);
    }

    /**
     * Upload an attachment
     * @param string $path
     * @param string $parentPageId
     * @return mixed
     * @throws Exception
     */
    public function uploadAttachment(string $path, string $parentPageId)
    {
        $headers = [
            'Content-Type' => 'multipart/form-data',
            'X-Atlassian-Token' => 'no-check'
        ];
        $data = [
            'file' => '@' . $path
        ];
        return $this->request(
            'POST',
            $this->curl->getHost() . "/content/$parentPageId/child/attachment",
            $data,
            $headers
        );
    }

    /**
     * Get attachments from the page
     *
     * @param string $pageId
     * @return mixed
     * @throws Exception
     */
    public function selectAttachments(string $pageId)
    {
        return $this->request('GET', $this->curl->getHost() . "/content/$pageId/child/attachment");
    }

    /**
     * @param string $pageId
     * @param mixed[] $labels [['name'=>'example_tag'],...]
     * @return mixed
     * @throws Exception
     */
    public function addLabel(string $pageId, array $labels)
    {
        return $this->request('POST', $this->curl->getHost() . "/content/$pageId/label", $labels);
    }

    /**
     * Make request.
     *
     * @param string $method
     * @param string $url
     * @param mixed[] $data
     * @param array<string, string> $headers
     *
     * @return string|false
     *
     * @throws Exception
     */
    public function request(string $method, string $url, array $data = [], array $headers = ['Content-Type' => 'application/json'])
    {
        //Detect invalid method
        $method = strtoupper($method);
        $methods = ['DELETE', 'GET', 'POST', 'PUT'];
        if (!in_array($method, $methods)) {
            throw new Exception('Invalid method');
        }
        $this->curl->setOptions([
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CUSTOMREQUEST => $method,
        ])->setHeaders($headers);

        if ($data !== []) {
            $this->curl->setOption(CURLOPT_POSTFIELDS, $data);
        }

        $serverOutput = $this->curl->execute();
        $this->curl->close();

        if (!is_scalar($serverOutput) && !is_array($serverOutput)) {
            throw new InvalidArgumentException('Unexpected return value');
        }

        return json_encode($serverOutput, JSON_THROW_ON_ERROR);
    }
}