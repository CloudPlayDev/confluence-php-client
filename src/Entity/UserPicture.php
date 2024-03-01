<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Entity;

use Webmozart\Assert\Assert;

class UserPicture implements Hydratable
{

    private string $path;
    private int $width;
    private int $height;
    private bool $isDefault;

    public static function load(array $data): UserPicture
    {
        $userPicture = new self;
        Assert::string($data['path']);
        Assert::integer($data['width']);
        Assert::integer($data['height']);
        Assert::boolean($data['isDefault']);

        $userPicture->setPath($data['path']);
        $userPicture->setWidth($data['width']);
        $userPicture->setHeight($data['height']);
        $userPicture->setIsDefault($data['isDefault']);


        return $userPicture;
    }

    private function setPath(string $path): UserPicture
    {
        $this->path = $path;
        return $this;
    }

    private function setWidth(int $width): UserPicture
    {
        $this->width = $width;
        return $this;
    }

    private function setHeight(int $height): UserPicture
    {
        $this->height = $height;
        return $this;
    }

    private function setIsDefault(bool $isDefault): UserPicture
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }


}
