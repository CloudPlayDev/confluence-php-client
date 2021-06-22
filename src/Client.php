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
use CloudPlayDev\ConfluenceClient\Exception\Exception;
use function count;
use function in_array;
use function is_array;
use function is_string;

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

        return $this->request('GET', $this->curl->getHost() . '/rest/api/content', $data);
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
            'version' => ['number' => $page->getVersion()+1]
        ];

        return $this->request('PUT', $this->curl->getHost() . "/rest/api/content/{$page->getId()}", $data);
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
        return $this->request('DELETE', $this->curl->getHost() . "/rest/api/content/$id");
    }

    /**
     * Search page by title, space key, type or id
     * @param array<string, string> $parameters
     * @return ConfluencePage|null
     * @throws Exception
     */
    public function selectPageBy(array $parameters = []): ?ConfluencePage
    {
        $url = $this->curl->getHost() . '/rest/api/content?';
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
            return $this->getPageById((int)$parameters['id']);
        }
        if (isset($parameters['expand'])) {
            $url .= 'expand=' . $parameters['expand'];
        }

        $searchResponse = $this->request('GET', $url);

        if (is_array($searchResponse) && isset($searchResponse['results'], $searchResponse['size']) && is_array($searchResponse['results']) && $searchResponse['size'] >= 1 && count($searchResponse['results']) >= 1) {
            $firstPage = (array)reset($searchResponse['results']);
            if (isset($firstPage['id'])) {
                return $this->getPageById((int)$firstPage['id']);
            }
        }
        return null;
    }

    public function getPageById(int $pageId): ?ConfluencePage
    {
        $url = $this->curl->getHost() . '/rest/api/content/'.$pageId;
        $firstPage = $this->request('GET', $url);

        if (!isset($firstPage['id'],
            $firstPage['type'],
            $firstPage['title'],
            $firstPage['_links']['self'],
            $firstPage['space']['key'],
            $firstPage['version']['number'])
        ) {
            return null;
        }

        $page = new ConfluencePage();
        $page->setId((int)$firstPage['id']);
        $page->setType((string)$firstPage['type']);
        $page->setTitle((string)$firstPage['title']);
        $page->setUrl((string)$firstPage['_links']['self']);
        $page->setSpace(str_replace('/rest/api/space/', '', (string)$firstPage['space']['key']));
        $page->setVersion((int)$firstPage['version']['number']);
        return $page;
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
            $this->curl->getHost() . "/rest/api/content/$parentPageId/child/attachment",
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
        return $this->request('GET', $this->curl->getHost() . "/rest/api/content/$pageId/child/attachment");
    }

    /**
     * @param string $pageId
     * @param mixed[] $labels [['name'=>'example_tag'],...]
     * @return mixed
     * @throws Exception
     */
    public function addLabel(string $pageId, array $labels)
    {
        return $this->request('POST', $this->curl->getHost() . "/rest/api/content/$pageId/label", $labels);
    }

    /**
     * Make request.
     *
     * @param string $method
     * @param string $url
     * @param mixed[] $data
     * @param array<string, string> $headers
     *
     * @return mixed[]|null
     *
     * @throws Exception
     */
    public function request(string $method, string $url, array $data = [], array $headers = ['Content-Type' => 'application/json']): ?array
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
            $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));
        }

        $serverOutput = $this->curl->execute();

        if (!is_string($serverOutput)) {
            throw new InvalidArgumentException('Unexpected return value');
        }

        if(empty($serverOutput)) {
            return null;
        }

        $decodedData = json_decode($serverOutput, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decodedData)) {
            throw new InvalidArgumentException('Return value could not be decoded.');
        }

        return $decodedData;
    }
}