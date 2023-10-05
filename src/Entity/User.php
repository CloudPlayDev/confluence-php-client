<?php

namespace CloudPlayDev\ConfluenceClient\Entity;

use Webmozart\Assert\Assert;

class User implements Hydratable
{

    private string $type = 'unknown';
    private string $accountId;
    private string $accountType;
    private string $email;
    private string $publicName;
    private UserPicture $profilePicture;
    private string $displayName;
    private bool $isExternalCollaborator;

    public static function load(array $data): User
    {
        $user = new self;
        Assert::string($data['type']);
        Assert::string($data['accountId']);
        Assert::string($data['accountType']);
        Assert::string($data['email']);
        Assert::string($data['publicName']);
        Assert::string($data['displayName']);
        Assert::boolean($data['isExternalCollaborator']);
        Assert::isArray($data['profilePicture']);

        $user->setType($data['type']);
        $user->setAccountId($data['accountId']);
        $user->setAccountType($data['accountType']);
        $user->setEmail($data['email']);
        $user->setPublicName($data['publicName']);
        $user->setDisplayName($data['displayName']);
        $user->setIsExternalCollaborator($data['isExternalCollaborator']);

        $user->setProfilePicture(UserPicture::load($data['profilePicture']));

        return $user;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): User
    {
        $this->type = $type;
        return $this;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): User
    {
        $this->accountId = $accountId;
        return $this;
    }

    public function getAccountType(): string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): User
    {
        $this->accountType = $accountType;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getPublicName(): string
    {
        return $this->publicName;
    }

    public function setPublicName(string $publicName): User
    {
        $this->publicName = $publicName;
        return $this;
    }

    public function getProfilePicture(): UserPicture
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(UserPicture $profilePicture): User
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): User
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function isExternalCollaborator(): bool
    {
        return $this->isExternalCollaborator;
    }

    public function setIsExternalCollaborator(bool $isExternalCollaborator): User
    {
        $this->isExternalCollaborator = $isExternalCollaborator;
        return $this;
    }





}
