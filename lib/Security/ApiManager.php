<?php namespace Vankosoft\ApiBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Vankosoft\UsersBundle\Model\UserInterface;

class ApiManager
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
    
    /** @var JWTTokenManagerInterface */
    private $jwtManager;
    
    /** @var RefreshTokenGeneratorInterface */
    private $refreshTokenGenerator;
    
    /** @var RefreshTokenManagerInterface */
    private $refreshTokenManager;
    
    /** @var VerifyEmailHelperInterface */
    private $verifyEmailHelper;
    
    public function __construct(
        TokenStorageInterface $tokenStorage,
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenGeneratorInterface $refreshTokenGenerator,
        RefreshTokenManagerInterface $refreshTokenManager,
        VerifyEmailHelperInterface $helper
    ) {
        $this->tokenStorage             = $tokenStorage;
        $this->jwtManager               = $jwtManager;
        $this->refreshTokenGenerator    = $refreshTokenGenerator;
        $this->refreshTokenManager      = $refreshTokenManager;
        $this->verifyEmailHelper        = $helper;
    }
    
    public function getToken()
    {
        $token  = $this->tokenStorage->getToken();
        
        return $this->jwtManager->decode( $token );
    }
    
    public function invalidateToken()
    {
        $this->tokenStorage->reset();
    }
    
    public function getVerifySignature( UserInterface $oUser, string $signatureRoute ): VerifyEmailSignatureComponents
    {
        $signature  = $this->verifyEmailHelper->generateSignature(
            $signatureRoute,
            $oUser->getId(),
            $oUser->getEmail(),
            ['userId' => $oUser->getId()]
        );
        
        return $signature;
    }
    
    public function verifySignature( string $signedUrl, string $userId, string $userEmail ): bool
    {
        try {
            $this->verifyEmailHelper->validateEmailConfirmation( $signedUrl, $userId, $userEmail );
            
            return true;
        } catch ( VerifyEmailExceptionInterface $e ) {
            return false;
        }
    }
    
    public function createToken( UserInterface $oUser ): array
    {
        $tokenString    = $this->jwtManager->create( $oUser );
        
        /** EXAMPLE: https://github.com/markitosgv/JWTRefreshTokenBundle/issues/322 */
        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl( $user, SELF::TTL );
        $this->refreshTokenManager->save( $refreshToken );
        
        return [
            'tokenString'   => $tokenString,
            'token'         => $this->jwtManager->parse( $tokenString ),
            'refreshToken'  => $refreshToken,
        ];
    }
}
