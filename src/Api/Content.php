<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Api;

use CloudPlayDev\ConfluenceClient\Entity\Content as ContentEntity;
use CloudPlayDev\ConfluenceClient\Exception\Exception;
use CloudPlayDev\ConfluenceClient\Exception\RequestException;
use Http\Client\Exception as HttpClientException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use function count;
use function in_array;
use function is_array;

class Content extends AbstractApi
{
    /**
     * @param int|null $contentId
     * @return string
     */
    private static function getContentUri(?int $contentId = null): string
    {
        $uri = 'content';
        if (null !== $contentId) {
            $uri .= '/' . $contentId;
        }

        return $uri;
    }

    /**
     * @param int $contentId
     * @return ContentEntity
     * @throws Exception
     * @throws JsonException
     * @throws RequestException
     * @throws HttpClientException
     */
    public function findOneById(int $contentId): ContentEntity
    {
        $response = $this->get(self::getContentUri($contentId));

        if ($response->getStatusCode() !== 200) {
            throw new RequestException($response);
        }

        return $this->deserialize($response);
    }

    /**
     * @param ContentEntity $page
     * @return ResponseInterface
     * @throws Exception
     * @throws JsonException
     * @throws HttpClientException
     */
    public function update(ContentEntity $page): ResponseInterface
    {
        $contentId = $page->getId();
        if (null === $contentId) {
            throw new Exception('Only saved pages can be updated.');
        }
        $data = [
            'id' => $contentId,
            'type' => $page->getType(),
            'title' => $page->getTitle(),
            'space' => ['key' => $page->getSpace()],
            'body' => [
                'storage' => [
                    'value' => $page->getContent(),
                    'representation' => 'storage',
                ],
            ],
            'version' => ['number' => $page->getVersion() + 1]
        ];

        return $this->put(self::getContentUri($contentId), $data);

    }

    /**
     * @param ContentEntity $page
     * @return ContentEntity
     * @throws Exception
     * @throws HttpClientException
     * @throws JsonException
     */
    public function create(ContentEntity $page): ContentEntity
    {
        if (null !== $page->getId()) {
            throw new Exception('Only new pages can be created.');
        }

        $data = [
            'type' => $page->getType(),
            'title' => $page->getTitle(),
            'space' => ['key' => $page->getSpace()],
            'body' => [
                'storage' => [
                    'value' => $page->getContent(),
                    'representation' => 'storage',
                ],
            ],
        ];

        $response = $this->post(self::getContentUri(), $data);

        if ($response->getStatusCode() !== 200) {
            throw new RequestException($response);
        }

        return $this->deserialize($response);

    }

    /**
     * @param ContentEntity $page
     * @return ResponseInterface
     */
    public function remove(ContentEntity $page): ResponseInterface
    {
        $contentId = $page->getId();
        if (null === $contentId) {
            throw new Exception('Only saved pages can be removed.');
        }
        return $this->put(self::getContentUri($contentId));
    }

    /**
     * @param array{title?: string, spaceKey?: string, type?: string, id?: int|string, expand?: string} $searchParameter
     * @return ContentEntity|null
     * @throws Exception
     * @throws JsonException
     */
    public function findOneBy(array $searchParameter): ?ContentEntity
    {
        $allowedSearchParameter = ['title', 'spaceKey', 'type', 'id', 'expand'];
        $queryParameter = array_filter($searchParameter, static function (string $searchKey) use ($allowedSearchParameter) {
            return in_array($searchKey, $allowedSearchParameter, true);
        }, ARRAY_FILTER_USE_KEY);

        $searchResponse = $this->get('content?', $queryParameter);

        if ($searchResponse->getStatusCode() !== 200) {
            throw new RequestException($searchResponse);
        }

        $decodedSearchResponse = (array)json_decode($searchResponse->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($decodedSearchResponse['results'], $decodedSearchResponse['size']) && is_array($decodedSearchResponse['results']) && $decodedSearchResponse['size'] >= 1 && count($decodedSearchResponse['results']) >= 1) {
            $firstPage = (array)reset($decodedSearchResponse['results']);
            if (isset($firstPage['id'])) {
                return $this->findOneById((int)$firstPage['id']);
            }
        }

        return null;
    }

    /**
     * @param ResponseInterface $response
     * @return ContentEntity
     * @throws Exception
     * @throws JsonException
     */
    private function deserialize(ResponseInterface $response): ContentEntity
    {
        $responseData = $response->getBody()->getContents();

        $decodedData = json_decode($responseData, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decodedData)) {
            throw new Exception('Return value could not be decoded.');
        }

        return $this->deserializeContent($decodedData);
    }

    /**
     * @param mixed[] $decodedData
     * @return ContentEntity
     * @throws Exception
     */
    private function deserializeContent(array $decodedData): ContentEntity
    {
        if (!isset($decodedData['id'],
            $decodedData['type'],
            $decodedData['title'],
            $decodedData['_links']['self'],
            $decodedData['space']['key'],
            $decodedData['version']['number'])
        ) {
            throw new Exception('Invalid content data');
        }

        $page = new ContentEntity();
        $page->setId((int)$decodedData['id']);
        $page->setType((string)$decodedData['type']);
        $page->setTitle((string)$decodedData['title']);
        $page->setUrl((string)$decodedData['_links']['self']);
        $page->setSpace(str_replace('/rest/api/space/', '', (string)$decodedData['space']['key']));
        $page->setVersion((int)$decodedData['version']['number']);

        return $page;
    }
}