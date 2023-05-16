<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Entity;


use Webmozart\Assert\Assert;
use function in_array;

class ContentBody implements Hydratable
{
    /**
     * confluence representation (markup html)
     */
    public const REPRESENTATION_STORAGE = 'storage';

    /**
     * confluence representation (markup)
     */
    public const REPRESENTATION_EDITOR = 'editor';

    /**
     * confluence representation  (xhtml)
     */
    public const REPRESENTATION_VIEW = 'view';

    /**
     * confluence representation
     */
    public const REPRESENTATION_EXPORT_VIEW = 'export_view';

    /**
     * confluence representation
     */
    public const REPRESENTATION_STYLED_VIEW = 'styled_view';
    
    /**
     * confluence representation (wiki markup)
     */
    public const REPRESENTATION_WIKI = 'wiki';

    /**
     * @example <ac:link><ri:user ri:userkey="a-valid-account-id" /></ac:link>
     * @var string
     */
    private string $value = '';
    private string $representation = self::REPRESENTATION_STORAGE;

    public static function load(array $data): Hydratable
    {
        $contentBody = new self;
        Assert::keyExists($data, 'value');
        Assert::keyExists($data, 'representation');
        Assert::string($data['value']);
        Assert::string($data['representation']);

        $contentBody->setValue($data['value']);
        $contentBody->setRepresentation($data['representation']);

        return $contentBody;

    }

    public static function isSupported(string $representation): bool
    {
        return (in_array($representation, [
            self::REPRESENTATION_STORAGE,
            self::REPRESENTATION_EDITOR,
            self::REPRESENTATION_VIEW,
            self::REPRESENTATION_EXPORT_VIEW,
            self::REPRESENTATION_STYLED_VIEW,
            self::REPRESENTATION_WIKI,
        ], true));
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getRepresentation(): string
    {
        return $this->representation;
    }

    /**
     * @param string $representation
     * @return self
     */
    public function setRepresentation(string $representation): self
    {
        $this->representation = $representation;
        return $this;
    }


}
