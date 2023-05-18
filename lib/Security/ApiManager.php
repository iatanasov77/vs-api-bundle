<?php namespace Vankosoft\ApiBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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
        $decodedJwtToken = $this->jwtManager->decode( $this->tokenStorage->getToken() );
        
        return $decodedJwtToken;
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
    
    public function createToken( UserInterface $oUser ): string
    {
        return $this->jwtManager->create( $oUser );
    }
}
