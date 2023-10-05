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

    public static function load(array $data): Hydratable
    {
        $userPicture = new self;
        Assert::string($data['path']);
        Assert::numeric($data['width']);
        Assert::numeric($data['height']);
        Assert::boolean($data['isDefault']);

        $userPicture->setPath($data['path']);
        $userPicture->setWidth($data['width']);
        $userPicture->setHeight($data['height']);
        $userPicture->setIsDefault($data['isDefault']);


        return $userPicture;
    }

    private function setPath(string $path)
    {
        $this->path = $path;
    }

    private function setWidth(int $width)
    {
        $this->width = $width;
    }

    private function setHeight(int $height)
    {
        $this->height = $height;
    }

    private function setIsDefault(bool $isDefault)
    {
        $this->isDefault = $isDefault;
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
