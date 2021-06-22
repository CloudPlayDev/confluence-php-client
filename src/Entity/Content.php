<?php
declare(strict_types=1);
/**
 * This file is part of the cloudplaydev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\ConfluenceClient\Entity;

class Content
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
     * @return Content
     */
    public function setType(string $type): Content
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
     * @return Content
     */
    public function setId(int $id): Content
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
     * @return Content
     */
    public function setTitle(string $title): Content
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
     * @return Content
     */
    public function setSpace(string $space): Content
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
     * @return Content
     */
    public function setAncestors(array $ancestors): Content
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
     * @return Content
     */
    public function setContent(string $content): Content
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
     * @return Content
     */
    public function setVersion(int $version): Content
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
     * @return Content
     */
    public function setChildren(array $children): Content
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
     * @return Content
     */
    public function setUrl(string $url): Content
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
     * @return Content
     */
    public function setCreatedDate(string $createdDate): Content
    {
        $this->createdDate = $createdDate;
        return $this;
    }
}