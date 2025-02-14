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
 * @author Thomas Bretzke <tb@itembase.biz>
 */
final class ItembaseResourceOwner extends GenericOAuth2ResourceOwner
{
    public const ITEMBASE_AUTH_URL = 'https://accounts.itembase.com/oauth/v2/auth';
    public const ITEMBASE_TOKEN_URL = 'https://accounts.itembase.com/oauth/v2/token';
    public const ITEMBASE_INFOS_URL = 'https://users.itembase.com/v1/me';

    /**
     * {@inheritdoc}
     */
    protected array $paths = [
        'identifier' => 'uuid',
        'nickname' => 'username',
        'firstname' => 'first_name',
        'lastname' => 'last_name',
        'email' => 'email',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'authorization_url' => self::ITEMBASE_AUTH_URL,
            'access_token_url' => self::ITEMBASE_TOKEN_URL,
            'infos_url' => self::ITEMBASE_INFOS_URL,
        ]);
    }
}
