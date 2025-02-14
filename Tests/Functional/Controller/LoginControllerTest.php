<?php

declare(strict_types=1);

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HWI\Bundle\OAuthBundle\Tests\Functional\Controller;

use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Tests\Fixtures\CustomOAuthToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class LoginControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $client->getContainer()->set('hwi_oauth.http_client', $this->createMock(HttpClientInterface::class));

        $crawler = $client->request('GET', '/login_hwi/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        $this->assertSame('google', $crawler->filter('a')->text(), $response->getContent());
    }

    public function testRedirectingToRegistrationFormWithError(): void
    {
        $client = static::createClient();

        $session = $this->getSession();
        $session->set(Security::AUTHENTICATION_ERROR, new AccountNotLinkedException());

        $client->request('GET', '/login_hwi/');

        $response = $client->getResponse();

        $this->assertSame(302, $response->getStatusCode(), $response->getContent());
        $this->assertSame(0, strpos($response->headers->get('Location'), '/connect/registration/'), $response->headers->get('Location'));
    }

    public function testLoginPageWithError(): void
    {
        $httpClient = new MockHttpClient();

        $client = static::createClient();
        $client->getContainer()->set('hwi_oauth.http_client', $httpClient);

        $session = $client->getContainer()->get('session');

        $this->logIn($client, $session);
        $exception = new UsernameNotFoundException();
        $session->set(Security::AUTHENTICATION_ERROR, $exception);

        $crawler = $client->request('GET', '/login_hwi/');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode(), $response->getContent());
        $this->assertSame($exception->getMessageKey(), $crawler->filter('span')->text(), $response->getContent());
    }

    private function logIn($client, SessionInterface $session): void
    {
        $firewallContext = 'hwi_context';

        $token = new CustomOAuthToken();
        $session->set('_security_'.$firewallContext, serialize($token));

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

    private function getSession(): SessionInterface
    {
        /** @var RequestStack $requestStack */
        $requestStack = self::$container->get('request_stack');

        $session = null;
        if (method_exists($requestStack, 'getSession')) {
            try {
                $session = $requestStack->getSession();
            } catch (SessionNotFoundException $e) {
                // Ignore & fallback to service
            }
        } elseif ((null !== $request = $requestStack->getCurrentRequest()) && $request->hasSession()) {
            $session = $request->getSession();
        }

        return $session ?: self::$container->get('session');
    }
}
