<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HWI\Bundle\OAuthBundle\Tests\OAuth\ResourceOwner;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\EventbriteResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\Response\AbstractUserResponse;

final class EventbriteResourceOwnerTest extends GenericOAuth2ResourceOwnerTest
{
    protected string $resourceOwnerClass = EventbriteResourceOwner::class;
    protected string $userResponse = <<<json
{
    "user": {
        "user_id": "1",
        "first_name": "bar",
        "last_name": "foo"
    }
}
json;

    protected array $paths = [
        'identifier' => 'user.user_id',
        'nickname' => 'user.first_name',
        'firstname' => 'user.first_name',
        'lastname' => 'user.last_name',
        'realname' => ['user.first_name', 'user.last_name'],
        'email' => 'email',
    ];

    public function testGetUserInformationFirstAndLastName(): void
    {
        $resourceOwner = $this->createResourceOwner(
            [],
            [],
            [
                $this->createMockResponse($this->userResponse, 'application/json; charset=utf-8'),
            ]
        );

        /**
         * @var AbstractUserResponse
         */
        $userResponse = $resourceOwner->getUserInformation(['access_token' => 'token']);

        $this->assertEquals('bar', $userResponse->getFirstName());
        $this->assertEquals('foo', $userResponse->getLastName());
    }
}
