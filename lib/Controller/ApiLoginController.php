<?php namespace Vankosoft\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Repository\UsersRepositoryInterface;

use Vankosoft\ApiBundle\Security\ApiManager;

class ApiLoginController extends AbstractController
{
    /** @var ApiManager */
    protected $apiManager;
    
    /** @var UsersRepositoryInterface */
    protected $usersRepository;
    
    // TranslatorInterface
    protected $translator;
    
    public function __construct(
        ApiManager $apiManager,
        UsersRepositoryInterface $usersRepository,
        TranslatorInterface $translator
    ) {
        $this->apiManager       = $apiManager;
        $this->usersRepository  = $usersRepository;
        $this->translator       = $translator;
    }
    
    public function getLoggedUser( Request $request ): Response
    {
        $token  = $this->apiManager->getToken();
        $user   = $this->usersRepository->findOneBy( ['username' => $token['username']] );
        
        $data   = [
            'tokenCreated'  => $token['iat'],
            'tokenExpired'  => $token['exp'],
            
            'user'                  => [
                'username'  => $user->getUsername(),
                'email'     => $user->getEmail(),
                'firstName' => $user->getInfo()->getFirstName(),
                'lastName'  => $user->getInfo()->getLastName(),
            ]
        ];
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data
        ]);
    }

    public function loginBySignature( $signature, Request $request ): Response
    {
        $user = $this->usersRepository->findOneBy( ['apiVerifySiganature' => $signature] );
        // Ensure the user exists in persistence
        if ( null === $user ) {
            return new JsonResponse([
                'status'    => Status::STATUS_ERROR,
                'message'   => $this->translator->trans( 'vs_api.messages.login_by_signature.invalid_user', [], 'VSApiBundle' ),
            ]);
        }
        
        // @TODO Check For Expired Signature
        
        $token  = $this->apiManager->createToken( $user );
        
        $data   = [
            'tokenString'   => $token['tokenString'],
            'token'         => $token['token'],
            
            'user'          => [
                'id'        => $user->getId(),
                'username'  => $user->getUsername(),
                'email'     => $user->getEmail(),
                'firstName' => $user->getInfo()->getFirstName(),
                'lastName'  => $user->getInfo()->getLastName(),
            ],
            
            'refreshToken'  => $token['refreshToken'],
        ];
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    /**
     * USED FOR DEVELOPEMENT ONLY
     */
    public function getSignature( $userId, Request $request )
    {
        $user       = $this->usersRepository->find( $userId );
        $signature  = $this->apiManager->getVerifySignature( $user, 'vs_api_login_by_signature' );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => [
                'signedUrl'             => $signature->getSignedUrl(),
                'expiresAtMessageKey'   => $signature->getExpirationMessageKey(),
                'expiresAtMessageData'  => $signature->getExpirationMessageData(),
            ],
        ]);
    }
}
