<?php namespace Vankosoft\ApiBundle\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

class RefreshToken extends BaseRefreshToken implements ResourceInterface
{
}
