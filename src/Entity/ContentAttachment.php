<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Entity;


use CloudPlayDev\ConfluenceClient\Api\Content;

class ContentAttachment extends AbstractContent
{
    protected string $type = Content::CONTENT_TYPE_ATTACHMENT;

}
