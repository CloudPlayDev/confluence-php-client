<?php
declare(strict_types=1);

namespace CloudPlayDev\Tests\ConfluenceClient\Entity;

use CloudPlayDev\ConfluenceClient\Entity\ContentHistory;
use PHPUnit\Framework\TestCase;

class ContentHistoryTest extends TestCase
{

    public function testLoad()
    {
        $apiJson = '{"previousVersion":{"by":{"type":"known","accountId":"598058:5c617f13-29ad-4667-a874-4371dba57509","accountType":"atlassian","email":"somemail@gmail.com","publicName":"myusername","profilePicture":{"path":"/wiki/aa-avatar/598058:5c617f13-29ad-4667-a874-4371dba57509","width":48,"height":48,"isDefault":false},"displayName":"Artem Stepin","isExternalCollaborator":false,"_expandable":{"operations":"","personalSpace":""},"_links":{"self":"https://xxxxxxxxxxxxxxxxxxxxx.atlassian.net/wiki/rest/api/user?accountId=598058:5c617f13-29ad-4667-a874-4371dba57509"}},"when":"2023-10-02T09:25:53.469Z","friendlyWhen":"Okt. 02, 2023","message":"","number":2,"minorEdit":false,"ncsStepVersion":"2","ncsStepVersionSource":"ncs-ack","confRev":"confluence$content$160464897.12","contentTypeModified":false,"_expandable":{"collaborators":"","content":"/rest/api/content/160006145?status=historical&version=2"},"_links":{"self":"https://xxxxxxxxxxxxxxxxxxxxx.atlassian.net/wiki/rest/api/content/160006145/version/2"}},"lastUpdated":{"by":{"type":"known","accountId":"598058:5c617f13-29ad-4667-a874-4371dba57509","accountType":"atlassian","email":"somemail@gmail.com","publicName":"myusername","profilePicture":{"path":"/wiki/aa-avatar/598058:5c617f13-29ad-4667-a874-4371dba57509","width":48,"height":48,"isDefault":false},"displayName":"Artem Stepin","isExternalCollaborator":false,"_expandable":{"operations":"","personalSpace":""},"_links":{"self":"https://xxxxxxxxxxxxxxxxxxxxx.atlassian.net/wiki/rest/api/user?accountId=598058:5c617f13-29ad-4667-a874-4371dba57509"}},"when":"2023-10-05T21:05:10.153Z","friendlyWhen":"vor etwa einer Stunde","message":"","number":3,"minorEdit":false,"ncsStepVersion":"16","ncsStepVersionSource":"ncs-ack","confRev":"confluence$content$160006145.15","contentTypeModified":false,"_expandable":{"collaborators":"","content":"/rest/api/content/160006145"},"_links":{"self":"https://xxxxxxxxxxxxxxxxxxxxx.atlassian.net/wiki/rest/api/content/160006145/version/3"}},"latest":true,"createdBy":{"type":"known","accountId":"598058:5c617f13-29ad-4667-a874-4371dba57509","accountType":"atlassian","email":"somemail@gmail.com","publicName":"myusername","profilePicture":{"path":"/wiki/aa-avatar/598058:5c617f13-29ad-4667-a874-4371dba57509","width":48,"height":48,"isDefault":false},"displayName":"Artem Stepin","isExternalCollaborator":false,"_expandable":{"operations":"","personalSpace":""},"_links":{"self":"https://xxxxxxxxxxxxxxxxxxxxx.atlassian.net/wiki/rest/api/user?accountId=598058:5c617f13-29ad-4667-a874-4371dba57509"}},"createdDate":"2023-10-02T09:23:58.344Z","_expandable":{"lastOwnedBy":"","contributors":"","ownedBy":""},"_links":{"self":"https://xxxxxxxxxxxxxxxxxxxxx.atlassian.net/wiki/rest/api/content/160006145/history","base":"https://xxxxxxxxxxxxxxxxxxxxx.atlassian.net/wiki","context":"/wiki"}}';

        $data = json_decode($apiJson, true);

        $contentHistory = ContentHistory::load($data);

        self::assertInstanceOf(ContentHistory::class, $contentHistory);
        self::assertInstanceOf(\DateTimeImmutable::class, $contentHistory->getCreatedDate());
        self::assertInstanceOf(\DateTimeImmutable::class, $contentHistory->getUpdatedDate());
        self::assertTrue($contentHistory->isLatest());

        self::assertSame('atlassian', $contentHistory->getCreatedBy()->getAccountType());
        self::assertSame('myusername', $contentHistory->getCreatedBy()->getPublicName());
        self::assertSame('myusername', $contentHistory->getUpdatedBy()->getPublicName());
        self::assertSame(3, $contentHistory->getLastVersionNumber());



    }
}
