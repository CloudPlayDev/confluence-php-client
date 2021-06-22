<?php
declare(strict_types=1);
/**
 * This file is part of the cloudplaydev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\ConfluenceClient\Entity;

class ConfluencePage
{
    private ?int $id = null;
    private ?string $title = null;
    private ?string $space = null;

    /**
     * @var array<string, string> $ancestors
     */
    private array $ancestors = [];

    private ?string $content = null;

    private int $version = 1;
    /**
     * @var array<string, string> $children
     */
    private array $children = [];
    private ?string $url = null;
    private ?string $type = null;
    private ?string $createdDate = null;

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ConfluencePage
     */
    public function setType(string $type): ConfluencePage
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ConfluencePage
     */
    public function setId(int $id): ConfluencePage
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ConfluencePage
     */
    public function setTitle(string $title): ConfluencePage
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSpace(): ?string
    {
        return $this->space;
    }

    /**
     * @param string $space
     * @return ConfluencePage
     */
    public function setSpace(string $space): ConfluencePage
    {
        $this->space = $space;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAncestors()
    {
        return $this->ancestors;
    }

    /**
     * @param array<string, string> $ancestors
     * @return ConfluencePage
     */
    public function setAncestors(array $ancestors): ConfluencePage
    {
        $this->ancestors = $ancestors;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return ConfluencePage
     */
    public function setContent(string $content): ConfluencePage
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     * @return ConfluencePage
     */
    public function setVersion(int $version): ConfluencePage
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array<string, string> $children
     * @return ConfluencePage
     */
    public function setChildren(array $children): ConfluencePage
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return ConfluencePage
     */
    public function setUrl(string $url): ConfluencePage
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param string $createdDate
     * @return ConfluencePage
     */
    public function setCreatedDate(string $createdDate): ConfluencePage
    {
        $this->createdDate = $createdDate;
        return $this;
    }
}