<?php namespace Vankosoft\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
Symfony\Component\Security\Core\User\UserInterface;

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
