<?php namespace Vankosoft\ApiBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

trait ApiUserEntity
{
    /** @var string */
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected $apiVerifySiganature;
    
    /** @var \DateTime | null */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected $apiVerifyExpiresAt;
    
    public function getApiVerifySiganature(): ?string
    {
        return $this->apiVerifySiganature;
    }
    
    public function setApiVerifySiganature( ?string $apiVerifySiganature ): self
    {
        $this->apiVerifySiganature = $apiVerifySiganature;
        
        return $this;
    }
    
    public function getApiVerifyExpiresAt(): ?\DateTime
    {
        return $this->apiVerifySiganature;
    }
    
    public function setApiVerifyExpiresAt( ?\DateTime $apiVerifyExpiresAt ): self
    {
        $this->apiVerifyExpiresAt = $apiVerifyExpiresAt;
        
        return $this;
    }
}
