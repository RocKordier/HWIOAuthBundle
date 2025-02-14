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

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\DropboxResourceOwner;

final class DropboxResourceOwnerTest extends GenericOAuth2ResourceOwnerTest
{
    protected string $resourceOwnerClass = DropboxResourceOwner::class;
    protected string $userResponse = '{"account_id": "1", "email": "bar"}';
    protected array $paths = [
        'identifier' => 'account_id',
        'nickname' => 'email',
        'realname' => 'email',
        'email' => 'email',
    ];

    public function testGetAuthorizationUrl(): void
    {
        $resourceOwner = $this->createResourceOwner();

        $this->assertEquals(
            $this->options['authorization_url'].'&response_type=code&client_id=clientid&state=eyJzdGF0ZSI6InJhbmRvbSJ9&redirect_uri=http%3A%2F%2Fredirect.to%2F',
            $resourceOwner->getAuthorizationUrl('http://redirect.to/')
        );
    }
}
