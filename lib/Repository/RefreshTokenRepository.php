<?php namespace Vankosoft\ApiBundle\Repository;

use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository as BaseRefreshTokenRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepositoryTrait;

class RefreshTokenRepository extends BaseRefreshTokenRepository implements RepositoryInterface
{
    use ResourceRepositoryTrait;
}