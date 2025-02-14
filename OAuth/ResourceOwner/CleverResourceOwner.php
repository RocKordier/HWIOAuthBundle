<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HWI\Bundle\OAuthBundle\OAuth\ResourceOwner;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Matt Farmer <work@mattfarmer.net>
 */
final class CleverResourceOwner extends GenericOAuth2ResourceOwner
{
    /**
     * {@inheritdoc}
     */
    protected array $paths = [
        'identifier' => 'data.id',
        'email' => 'data.email',
        'firstname' => 'data.name.first',
        'lastname' => 'data.name.last',
        'realname' => [
            'data.name.first',
            'data.name.middle',
            'data.name.last',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'authorization_url' => 'https://clever.com/oauth/authorize',
            'access_token_url' => 'https://clever.com/oauth/tokens',
            'infos_url' => 'https://api.clever.com/me',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetTokenRequest($url, array $parameters = [])
    {
        $authPreHash = $this->options['client_id'].':'.$this->options['client_secret'];
        $authHeader = 'Authorization: Basic '.base64_encode($authPreHash);

        return $this->httpRequest(
            $url,
            http_build_query($parameters, '', '&'),
            [
                $authHeader,
            ]
        );
    }
}
