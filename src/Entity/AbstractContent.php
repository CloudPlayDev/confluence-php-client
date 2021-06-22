<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Entity;


use CloudPlayDev\ConfluenceClient\Api\Content;

abstract class AbstractContent
{
    private ?int $id = null;
    private ?string $title = null;
    private ?string $space = null;

    private ?string $content = null;

    private int $version = 1;
    /**
     * @var array<string, string> $children
     */
    private array $children = [];
    private ?string $url = null;
    protected string $type = Content::CONTENT_TYPE_GLOBAL;

    private ?int $containerId = null;
    private string $containerType = Content::CONTENT_TYPE_PAGE;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
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
     * @return self
     */
    public function setId(int $id): self
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
     * @return self
     */
    public function setTitle(string $title): self
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
     * @return self
     */
    public function setSpace(string $space): self
    {
        $this->space = $space;
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
     * @return self
     */
    public function setContent(string $content): self
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
     * @return self
     */
    public function setVersion(int $version): self
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
     * @return self
     */
    public function setChildren(array $children): self
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
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string $comment
     * @return ContentComment
     */
    public function createComment(string $comment): ContentComment
    {
        $contentComment = new ContentComment();
        $contentComment->setContainerId($this->getId());
        $contentComment->setContainerType($this->getType());
        $contentComment->setContent($comment);
        return $contentComment;
    }

    /**
     * @param string $title
     * @param string $body
     * @return ContentPage
     */
    public function createSubpage(string $title, string $body): ContentPage
    {
        $contentPage = new ContentPage();
        $contentPage->setContainerId($this->getId());
        $contentPage->setContainerType($this->getType());
        $contentPage->setContent($body);
        $contentPage->setTitle($title);
        return $contentPage;
    }

    /**
     * @return int|null
     */
    public function getContainerId(): ?int
    {
        return $this->containerId;
    }

    /**
     * @param int|null $containerId
     */
    public function setContainerId(?int $containerId): void
    {
        $this->containerId = $containerId;
    }

    /**
     * @return string
     */
    public function getContainerType(): string
    {
        return $this->containerType;
    }

    /**
     * @param string $containerType
     */
    public function setContainerType(string $containerType): void
    {
        $this->containerType = $containerType;
    }

}