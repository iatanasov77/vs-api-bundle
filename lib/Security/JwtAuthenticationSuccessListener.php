<?php namespace Vankosoft\ApiBundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Doctrine\ORM\EntityManager;
use Vankosoft\UsersBundle\Model\UserInterface;

/**
 * Manual: https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/2-data-customization.html#events-authentication-success-adding-public-data-to-the-jwt-response
 */
class JwtAuthenticationSuccessListener
{
    /** @var EntityManager */
    private $entityManager;
    
    public function __construct( EntityManager $entityManager )
    {
        $this->entityManager    = $entityManager;
    }
        
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse( AuthenticationSuccessEvent $event ): void
    {
        $data = $event->getData();
        $user = $event->getUser();
        
        if ( ! $user instanceof UserInterface ) {
            return;
        }
        
        $user->setLastLogin( new \DateTime() );
        $this->entityManager->persist( $user );
        $this->entityManager->flush();
        
        
        
        $event->setData([
            'code'      => $event->getResponse()->getStatusCode(),
            'payload'   => $event->getData(),
        ]);
        //$this->modifyResponse( $user, $event );
    }
    
    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse( AuthenticationFailureEvent $event ): void
    {
        $response = new JWTAuthenticationFailureResponse( 'Authentication failed', 401 );
        
        $event->setResponse($response);
    }
    
    private function modifyResponse( UserInterface $user,  AuthenticationSuccessEvent &$event ): void
    {
        $data['data'] = [
            'roles' => $user->getRoles(),
        ];
        
        $event->setData( $data );
    }
}