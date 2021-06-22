<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Entity;


use CloudPlayDev\ConfluenceClient\Api\Content;

class ContentComment extends AbstractContent
{
    protected string $type = Content::CONTENT_TYPE_COMMENT;

}