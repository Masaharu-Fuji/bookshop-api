<?php

// src/Security/AccessTokenHandler.php
namespace App\Security;

use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private ApiTokenRepository $apiTokenRepository
    ) {
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        // e.g. query the "access token" database to search for this token
        $accessToken = $this->apiTokenRepository->findOneBy(['token' => $accessToken]);

        if (null === $accessToken) {
            throw new BadCredentialsException('Invalid credentials.hogehoge');
        }

        if (!$accessToken->isValid()) {
            throw new CustomUserMessageAuthenticationException('Token expired');
        }

        // and return a UserBadge object containing the user identifier from the found token
        return new UserBadge($accessToken->getWriter()->getUserIdentifier());
    }
}
