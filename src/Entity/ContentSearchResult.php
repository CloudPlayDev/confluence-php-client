<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Entity;


use CloudPlayDev\ConfluenceClient\Exception\HydrationException;
use Webmozart\Assert\Assert;
use function count;

class ContentSearchResult implements Hydratable
{

    private int $size = 0;

    private int $start = 0;

    private int $limit = 0;

    /**
     * @var AbstractContent[]
     */
    private array $results = [];

    private bool $lastPage = true;

    /**
     * @param mixed[] $data
     * @return ContentSearchResult
     * @throws HydrationException
     */
    public static function load(array $data): self
    {

        $searchResult = new self;

        Assert::true(isset($data['results'], $data['size']));
        Assert::isArray($data['results']);
        Assert::integer($data['size']);

        $searchResult->setSize($data['size']);

        if(isset($data['start']) && isset($data['limit'])) {
            Assert::integer($data['start']);
            Assert::integer($data['limit']);
            $searchResult->setStart($data['start']);
            $searchResult->setLimit($data['limit']);
        }

        if ($data['size'] >= 1 && count($data['results']) >= 1) {

            foreach ($data['results'] as $resultEntity) {
                Assert::isArray($resultEntity);
                $content = AbstractContent::load($resultEntity);
                $searchResult->addResult($content);
            }
        }

        /* if there is a next link, then it is not the last page */
        if(isset($data['_links']['next'])) {
            $searchResult->setLastPage(false);
        }

        return $searchResult;
    }

    /**
     * @param AbstractContent[] $results
     * @return ContentSearchResult
     */
    public function setResults(array $results): ContentSearchResult
    {
        $this->results = $results;
        return $this;
    }

    /**
     * @param int $size
     * @return ContentSearchResult
     */
    public function setSize(int $size): ContentSearchResult
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return AbstractContent[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function getResultAt(int $position): ?AbstractContent
    {
        return $this->results[$position] ?? null;
    }

    /**
     * @param AbstractContent $content
     * @return self
     */
    public function addResult(AbstractContent $content): self
    {
        $this->results[] = $content;
        return $this;

    }

    public function isLastPage(): bool
    {
        return $this->lastPage;
    }

    public function setLastPage(bool $lastPage): void
    {
        $this->lastPage = $lastPage;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function setStart(int $start): void
    {
        $this->start = $start;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }


}
