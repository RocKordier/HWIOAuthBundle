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

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\FlickrResourceOwner;
use HWI\Bundle\OAuthBundle\Tests\Fixtures\CustomUserResponse;

final class FlickrResourceOwnerTest extends GenericOAuth1ResourceOwnerTest
{
    protected string $resourceOwnerClass = FlickrResourceOwner::class;
    protected array $paths = [
        'identifier' => 'user_nsid',
        'nickname' => 'username',
        'realname' => 'fullname',
    ];

    /**
     * Together with OAuth token Flickr sends user data.
     */
    public function testGetUserInformation(): void
    {
        $resourceOwner = $this->createResourceOwner(
            [],
            [],
            [
                $this->createMockResponse($this->userResponse),
            ]
        );

        $accessToken = [
            'oauth_token' => 'token',
            'oauth_token_secret' => 'secret',
            'fullname' => 'Dmitri Lakachuskis',
            'user_nsid' => '15362483@N08',
            'username' => 'lakiboy83',
        ];
        $userResponse = $resourceOwner->getUserInformation($accessToken);

        $this->assertEquals('15362483@N08', $userResponse->getUsername());
        $this->assertEquals('lakiboy83', $userResponse->getNickname());
        $this->assertEquals('Dmitri Lakachuskis', $userResponse->getRealName());
        $this->assertEquals($accessToken['oauth_token'], $userResponse->getAccessToken());
        $this->assertNull($userResponse->getRefreshToken());
        $this->assertNull($userResponse->getExpiresIn());
    }

    /**
     * Ensure permissions level is appended to authorization URL. Do not wrap JSON response with padding.
     */
    public function testGetAuthorizationUrlContainOAuthTokenAndSecret(): void
    {
        $resourceOwner = $this->createResourceOwner(
            [],
            [],
            [
                $this->createMockResponse('{"oauth_token": "token", "oauth_token_secret": "secret"}', 'application/json; charset=utf-8'),
            ]
        );

        $this->storage->expects($this->once())
            ->method('save')
            ->with($resourceOwner, ['oauth_token' => 'token', 'oauth_token_secret' => 'secret', 'timestamp' => time()])
        ;

        $this->assertEquals(
            $this->options['authorization_url'].'&oauth_token=token&perms=read&nojsoncallback=1',
            $resourceOwner->getAuthorizationUrl('http://redirect.to/')
        );
    }

    /**
     * Flickr resource owner relies on user data sent with OAuth token, hence no request is made to get user information.
     */
    public function testCustomResponseClass(): void
    {
        $class = CustomUserResponse::class;
        $resourceOwner = $this->createResourceOwner(['user_response_class' => $class]);

        /* @var $userResponse CustomUserResponse */
        $userResponse = $resourceOwner->getUserInformation(['oauth_token' => 'token', 'oauth_token_secret' => 'secret']);

        $this->assertInstanceOf($class, $userResponse);
        $this->assertEquals('foo666', $userResponse->getUsername());
        $this->assertEquals('foo', $userResponse->getNickname());
    }
}
