<?php namespace Vankosoft\ApiBundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Doctrine\ORM\EntityManager;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Model\UserInterface;

/**
 * Manual: https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/2-data-customization.html#events-authentication-success-adding-public-data-to-the-jwt-response
 */
class JwtAuthenticationSuccessListener
{
    /** @var JWTTokenManagerInterface */
    private $jwtManager;
    
    /** @var EntityManager */
    private $entityManager;
    
    public function __construct( JWTTokenManagerInterface $jwtManager, EntityManager $entityManager )
    {
        $this->jwtManager       = $jwtManager;
        $this->entityManager    = $entityManager;
    }
        
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse( AuthenticationSuccessEvent $event ): void
    {
        $user = $event->getUser();
        
        if ( ! $user instanceof UserInterface ) {
            return;
        }
        
        /*
         * May be this should not be here because it's called on every api request
         */
        $user->setLastLogin( new \DateTime() );
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
        
        $this->modifyResponse( $user, $event );
    }
    
    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse( AuthenticationFailureEvent $event ): void
    {
        $response = new JWTAuthenticationFailureResponse( 'Authentication failed', 401 );
        
        $event->setResponse( $response );
    }
    
    private function modifyResponse( UserInterface $user,  AuthenticationSuccessEvent &$event ): void
    {
        $status         = $event->getResponse()->getStatusCode() == 200 ? Status::STATUS_OK : Status::STATUS_ERROR;
        $payload        = $event->getData();
        $decodedToken   = $this->jwtManager->parse( $payload['token'] );
        
        $payload['tokenCreated']   = $decodedToken['iat'];
        $payload['tokenExpired']   = $decodedToken['exp'];
        
        $payload['userId']          = $user->getId();
        $payload['userFullName']    = $user->getInfo()->getFullName();
        //$payload['userRoles']               = $user->getRoles();
        
        $event->setData([
            'status'    => $status,
            'payload'   => $payload,
        ]);
    }
}