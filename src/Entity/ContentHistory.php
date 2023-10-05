<?php
declare(strict_types=1);

namespace CloudPlayDev\ConfluenceClient\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Webmozart\Assert\Assert;

class ContentHistory implements Hydratable
{


    private DateTimeInterface $createdDate;
    private DateTimeInterface $updatedDate;
    private bool $isLatest = false;
    private User $createdBy;
    private User $updatedBy;

    private int $lastVersionNumber;

    public static function load(array $data): ContentHistory
    {
        $contentHistory = new self;
        Assert::keyExists($data, 'createdDate');
        Assert::keyExists($data, 'createdBy');
        Assert::keyExists($data, 'lastUpdated');
        Assert::isArray($data['createdBy']);
        Assert::isArray($data['lastUpdated']);
        Assert::boolean($data['latest']);

        Assert::keyExists($data['lastUpdated'], 'by');
        Assert::isArray($data['lastUpdated']['by']);

        if(isset($data['latest'])) {
            Assert::boolean($data['latest']);
            $contentHistory->setLatest($data['latest']);
        }

        $contentHistory->setCreatedDate(DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.vZ', $data['createdDate']));
        $contentHistory->setCreatedBy(User::load($data['createdBy']));
        $contentHistory->setUpdatedBy(User::load($data['lastUpdated']['by']));

        $contentHistory->setUpdatedDate(DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.vZ', $data['lastUpdated']['when']));

        $contentHistory->setLastVersionNumber($data['lastUpdated']['number']);

        return $contentHistory;
    }

    private function setLatest(bool $latest): ContentHistory
    {
        $this->isLatest = $latest;
        return $this;
    }

    private function setCreatedDate(DateTimeInterface $createFromFormat): ContentHistory
    {
        $this->createdDate = $createFromFormat;
        return $this;
    }

    private function setCreatedBy(User $user): ContentHistory
    {
        $this->createdBy = $user;
        return $this;
    }

    private function setUpdatedBy(User $user): ContentHistory
    {
        $this->updatedBy = $user;
        return $this;
    }

    public function setUpdatedDate(DateTimeInterface $updatedDate): ContentHistory
    {
        $this->updatedDate = $updatedDate;
        return $this;
    }

    public function getUpdatedDate(): DateTimeInterface
    {
        return $this->updatedDate;
    }

    public function getCreatedDate(): DateTimeInterface
    {
        return $this->createdDate;
    }

    public function isLatest(): bool
    {
        return $this->isLatest;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): User
    {
        return $this->updatedBy;
    }

    public function getLastVersionNumber(): int
    {
        return $this->lastVersionNumber;
    }

    public function setLastVersionNumber(int $lastVersionNumber): void
    {
        $this->lastVersionNumber = $lastVersionNumber;
    }


}
