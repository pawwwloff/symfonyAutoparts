<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Gesdinet\JWTRefreshTokenBundle\Document\AbstractRefreshToken;

/**
 * This class override Gesdinet\JWTRefreshTokenBundle\Document\RefreshToken to have another collection name.
 *
 * @MongoDB\Document(collection="UserRefreshToken")
 */
class JwtRefreshToken extends AbstractRefreshToken
{
    /**
     * @var string
     *
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
}