<?php namespace Vankosoft\ApiBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use Vankosoft\UsersBundle\Model\UserInterface;

class ApiManager
{
    /** @var TokenStorageInterface */
    private $tokenStorage;
    
    /** @var JWTTokenManagerInterface */
    private $jwtManager;
    
    /** @var VerifyEmailHelperInterface */
    private $verifyEmailHelper;
    
    public function __construct(
        TokenStorageInterface $tokenStorage,
        JWTTokenManagerInterface $jwtManager,
        VerifyEmailHelperInterface $helper
    ) {
        $this->tokenStorage         = $tokenStorage;
        $this->jwtManager           = $jwtManager;
        $this->verifyEmailHelper    = $helper;
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
            ['id' => $oUser->getId()]
        );
        
        return $signature;
    }
    
    public function verifySignature( string $signedUrl, string $userId, string $userEmail ): bool
    {
        $this->verifyEmailHelper->validateEmailConfirmation( $signedUrl, $userId, $userEmail );
        
        return true;
    }
    
    public function createToken( UserInterface $oUser ): array
    {
        $tokenString    = $this->jwtManager->create( $oUser );
        
        return [
            'tokenString'   => $tokenString,
            'token'         => $this->jwtManager->parse( $tokenString )
        ];
    }
}
