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

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\WindowsLiveResourceOwner;

final class WindowsLiveResourceOwnerTest extends GenericOAuth2ResourceOwnerTest
{
    protected string $resourceOwnerClass = WindowsLiveResourceOwner::class;
    protected string $userResponse = <<<json
{
    "id": "1",
    "name": "bar"
}
json;

    protected array $paths = [
        'identifier' => 'id',
        'nickname' => 'name',
        'realname' => 'name',
    ];

    protected string $authorizationUrlBasePart = 'http://user.auth/?test=2&response_type=code&client_id=clientid&scope=wl.signin';
}
