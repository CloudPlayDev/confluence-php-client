<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Api;

use CloudPlayDev\ConfluenceClient\Entity\AbstractContent;
use CloudPlayDev\ConfluenceClient\Entity\ContentComment;
use CloudPlayDev\ConfluenceClient\Entity\ContentPage;
use CloudPlayDev\ConfluenceClient\Exception\Exception;
use CloudPlayDev\ConfluenceClient\Exception\RequestException;
use Http\Client\Exception as HttpClientException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use function assert;
use function count;
use function in_array;
use function is_array;
use function is_int;
use function is_string;

/**
 * Class Content
 * @package CloudPlayDev\ConfluenceClient\Api
 */
class Content extends AbstractApi
{
    /**
     * ContentType for confluence attachments
     */
    public const CONTENT_TYPE_ATTACHMENT = 'attachment';

    /**
     * ContentType for confluence comments
     */
    public const CONTENT_TYPE_COMMENT = 'comment';

    /**
     * ContentType for confluence page content
     */
    public const CONTENT_TYPE_PAGE = 'page';

    /**
     * @param string|int|null ...$parameter
     * @return string
     */
    private static function getContentUri(...$parameter): string
    {
        $uri = 'content';
        $parameterString = implode('/', array_filter($parameter));

        if (!empty($parameterString)) {
            $uri .= '/' . $parameterString;
        }

        return $uri . '?';
    }

    /**
     * @param int $contentId
     * @return AbstractContent
     * @throws Exception
     * @throws JsonException
     * @throws RequestException
     * @throws HttpClientException
     */
    public function findOneById(int $contentId): AbstractContent
    {
        $response = $this->get(self::getContentUri($contentId), ['expand' => 'space,version,body.storage']);

        if ($response->getStatusCode() !== 200) {
            throw new RequestException($response);
        }

        return $this->deserialize($response);
    }

    /**
     * @param AbstractContent $page
     * @return ResponseInterface
     * @throws Exception
     * @throws JsonException
     * @throws HttpClientException
     */
    public function update(AbstractContent $page): ResponseInterface
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
     * @param AbstractContent $page
     * @return AbstractContent
     * @throws Exception
     * @throws HttpClientException
     * @throws JsonException
     */
    public function create(AbstractContent $page): AbstractContent
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
     * @param AbstractContent $page
     * @return ResponseInterface
     */
    public function remove(AbstractContent $page): ResponseInterface
    {
        $contentId = $page->getId();
        if (null === $contentId) {
            throw new Exception('Only saved pages can be removed.');
        }
        return $this->put(self::getContentUri($contentId));
    }

    /**
     * @param AbstractContent $content
     * @param string|null $contentType
     * @return AbstractContent[]
     * @throws HttpClientException
     * @throws JsonException
     */
    public function children(AbstractContent $content, ?string $contentType = null): array
    {
        return $this->parseSearchResults(
            $this->get(
                self::getContentUri($content->getId(), 'child', $contentType),
                ['expand' => 'space,version,body.storage']
            ),
        );
    }

    /**
     * @param AbstractContent $content
     * @param string|null $contentType
     * @return AbstractContent[]
     * @throws HttpClientException
     * @throws JsonException
     */
    public function descendants(AbstractContent $content, ?string $contentType = null): array
    {
        return $this->parseSearchResults($this->get(self::getContentUri($content->getId(), 'descendant', $contentType)));
    }

    /**
     * @param array{title?: string, spaceKey?: string, type?: string, id?: int|string} $searchParameter
     * @return AbstractContent|null
     * @throws Exception
     * @throws JsonException
     */
    public function findOneBy(array $searchParameter): ?AbstractContent
    {
        $allowedSearchParameter = ['title', 'spaceKey', 'type', 'id'];
        $queryParameter = array_filter($searchParameter, static function (string $searchKey) use ($allowedSearchParameter) {
            return in_array($searchKey, $allowedSearchParameter, true);
        }, ARRAY_FILTER_USE_KEY);

        $queryParameter['expand'] = 'space,version,body.storage';

        $searchResponse = $this->get('content?', $queryParameter);

        if ($searchResponse->getStatusCode() !== 200) {
            throw new RequestException($searchResponse);
        }

        $searchResults = $this->parseSearchResults($searchResponse);
        if (count($searchResults) > 0) {
            return reset($searchResults);
        }

        return null;
    }

    /**
     * @param ResponseInterface $response
     * @return AbstractContent[]
     * @throws JsonException
     */
    private function parseSearchResults(ResponseInterface $response): array
    {
        $decodedSearchResponse = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($decodedSearchResponse));
        assert(isset($decodedSearchResponse['results'], $decodedSearchResponse['size']));
        assert(is_array($decodedSearchResponse['results']));
        assert(is_int($decodedSearchResponse['size']));

        $results = [];
        if ($decodedSearchResponse['size'] >= 1 && count($decodedSearchResponse['results']) >= 1) {

            foreach ($decodedSearchResponse['results'] as $resultEntity) {
                assert(is_array($resultEntity));
                $results[] = $this->deserializeContent($resultEntity);
            }
        }

        return $results;
    }

    /**
     * @param ResponseInterface $response
     * @return AbstractContent
     * @throws Exception
     * @throws JsonException
     */
    private function deserialize(ResponseInterface $response): AbstractContent
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
     * @return AbstractContent
     * @throws Exception
     */
    private function deserializeContent(array $decodedData): AbstractContent
    {
        assert(isset($decodedData['id'],
            $decodedData['type'],
            $decodedData['title'],
            $decodedData['_links']['self']));
        assert(is_string($decodedData['type']));

        switch ($decodedData['type']) {
            case self::CONTENT_TYPE_PAGE:
                $content = new ContentPage();
                break;
            case self::CONTENT_TYPE_COMMENT:
                $content = new ContentComment();
                break;
            default:
                throw new Exception('Invalid content type: ' . $decodedData['type']);
        }

        $content->setId((int)$decodedData['id']);
        $content->setType($decodedData['type']);
        $content->setTitle((string)$decodedData['title']);
        $content->setUrl((string)$decodedData['_links']['self']);
        if (isset($decodedData['space']['key'])) {
            $content->setSpace((string)$decodedData['space']['key']);
        }
        if (isset($decodedData['version']['number'])) {
            $content->setVersion((int)$decodedData['version']['number']);
        }
        if(isset($decodedData['body']['storage']['value'])) {
            $content->setContent($decodedData['body']['storage']['value']);
        }

        return $content;
    }
}