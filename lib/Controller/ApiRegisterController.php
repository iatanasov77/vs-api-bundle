<?php namespace Vankosoft\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Security\UserManager;
use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\UsersBundle\Model\User;

class ApiRegisterController extends AbstractController
{
    /**
     * @var UserManager
     */
    private $userManager;
    
    /**
     * @var RepositoryInterface
     */
    private $usersRepository;
    
    /**
     * @var Factory
     */
    private $usersFactory;
    
    /**
     * @var RepositoryInterface
     */
    private $userRolesRepository;
    
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /**
     * @var array
     */
    protected $params;
    
    public function __construct(
        ManagerRegistry $doctrine,
        UserManager $userManager,
        RepositoryInterface $usersRepository,
        Factory $usersFactory,
        RepositoryInterface $userRolesRepository,
        array $parameters
    ) {
        $this->doctrine             = $doctrine;
        $this->userManager          = $userManager;
        $this->usersRepository      = $usersRepository;
        $this->usersFactory         = $usersFactory;
        $this->userRolesRepository  = $userRolesRepository;
        $this->params               = $parameters;
    }
    
    public function register( Request $request ): UserInterface
    {
        $requestBody    = \json_decode( $request->getContent(), true );
        
        $em             = $this->doctrine->getManager();
        $oUser          = $this->userManager->createUser(
            $requestBody['username'],
            $requestBody['email'],
            $requestBody['password'],   // $plainPassword
        );
        return $oUser;
        
        //$oUser->setApiToken( $this->tokenGenerator->createToken( strval( time() ), $oUser->getEmail() ) );
        
        //$oUser->setRoles( [$request->request->get( 'registerRole' )] );
        $oUser->addRole( $this->userRolesRepository->findByTaxonCode( $this->params['registerRole'] ) );
        
        $oUser->setPreferedLocale( $request->getLocale() );
        $oUser->setVerified( true );
        $oUser->setEnabled( true );
        
        $em->persist( $oUser );
        $em->flush();
        
        return $oUser;
    }
}
