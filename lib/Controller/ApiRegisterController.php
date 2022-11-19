<?php namespace Vankosoft\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
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
    
    // TranslatorInterface
    protected $translator;
    
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
        TranslatorInterface $translator,
        array $parameters
    ) {
        $this->doctrine             = $doctrine;
        $this->userManager          = $userManager;
        $this->usersRepository      = $usersRepository;
        $this->usersFactory         = $usersFactory;
        $this->userRolesRepository  = $userRolesRepository;
        $this->translator           = $translator;
        $this->params               = $parameters;
    }
    
    public function  __invoke( Request $request ): JsonResponse
    {
        // $request->get( "token" )
        $requestBody    = \json_decode( $request->getContent(), true );
        
        $createdUser    = $this->register( $requestBody, $request->getLocale() );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'message'   => $this->translator->trans( 'vs_api.messages.register_success', [], 'VSApiBundle' ),
            'data'      => $createdUser,
        ]);
    }
    
    public function register( array $requestBody, string $preferedLocale ): UserInterface
    {
        $em             = $this->doctrine->getManager();
        $oUser          = $this->userManager->createUser(
            $requestBody['username'],
            $requestBody['email'],
            $requestBody['password'],   // $plainPassword
        );
        
        //$oUser->setApiToken( $this->tokenGenerator->createToken( strval( time() ), $oUser->getEmail() ) );
        $oUser->addRole( $this->userRolesRepository->findByTaxonCode( $this->params['registerRole'] ) );
        
        $oUser->setPreferedLocale( $preferedLocale );
        $oUser->setVerified( true );
        $oUser->setEnabled( true );
        
        $oUser->getInfo()->setFirstName( $requestBody['firstName'] );
        $oUser->getInfo()->setLastName( $requestBody['lastName'] );
        
        $em->persist( $oUser );
        $em->flush();
        
        return $oUser;
    }
}
