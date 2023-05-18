<?php namespace Vankosoft\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    
    public function __construct( ApiManager $apiManager, UsersRepositoryInterface $usersRepository )
    {
        $this->apiManager       = $apiManager;
        $this->usersRepository  = $usersRepository;
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

    public function loginBySignature( Request $request ): Response
    {
        $id = $request->get( 'id' ); // retrieve the user id from the url
        // Verify the user id exists and is not null
        if( null === $id ) {
            return $this->redirectToRoute( 'app_home' );
        }
        
        $user = $this->usersRepository->find( $id );
        // Ensure the user exists in persistence
        if ( null === $user ) {
            return $this->redirectToRoute( 'app_home' );
        }
        
        try {
            $this->apiManager->verifySignature( $request->getUri(), $user->getId(), $user->getEmail() );
        } catch ( VerifyEmailExceptionInterface $e ) {
            $this->addFlash( 'verify_email_error', $e->getReason() );
            
            return $this->redirectToRoute( 'app_home' );
        }
        
        $token  = $this->apiManager->createToken( $user );
        
        $data   = [
            'token' => $token,
            //'tokenCreated'  => $token['iat'],
            //'tokenExpired'  => $token['exp'],
            
            'user'                  => [
                'username'  => $user->getUsername(),
                'email'     => $user->getEmail(),
                'firstName' => $user->getInfo()->getFirstName(),
                'lastName'  => $user->getInfo()->getLastName(),
            ]
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
