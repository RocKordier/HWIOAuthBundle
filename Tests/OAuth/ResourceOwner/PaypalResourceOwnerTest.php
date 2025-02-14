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

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\PaypalResourceOwner;

/**
 * @author Berny Cantos <be@rny.cc>
 */
final class PaypalResourceOwnerTest extends GenericOAuth2ResourceOwnerTest
{
    protected string $resourceOwnerClass = PaypalResourceOwner::class;
    protected string $userResponse = <<<json
{
    "user_id": "1",
    "email": "bar",
    "name": "Example Default"
}
json;
    protected array $paths = [
        'identifier' => 'user_id',
        'nickname' => 'email',
        'realname' => 'name',
    ];

    protected string $authorizationUrlBasePart = 'http://user.auth/?test=2&response_type=code&client_id=clientid&scope=openid+email';
}
