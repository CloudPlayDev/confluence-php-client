<?php
declare(strict_types=1);
/**
 * This file is part of the cloudplaydev/confluencePHPClient.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CloudPlayDev\ConfluenceClient\Entity;

use CloudPlayDev\ConfluenceClient\Api\Content;

class ContentPage extends AbstractContent {

    protected string $type = Content::CONTENT_TYPE_PAGE;
}