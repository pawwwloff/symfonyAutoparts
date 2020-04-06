<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
//use FOS\UserBundle\Model\User as BaseUser;
use Sonata\UserBundle\Document\BaseUser as BaseUser;
//use App\Application\Sonata\UserBundle\Document\User as BaseUser;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

/**
 * @MongoDB\Document
 */
class User extends BaseUser implements JWTUserInterface
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $firstname;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $lastname;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $email;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $phone;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $password;

    protected $confirmPassword;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $create_date;

    /**
     * @MongoDB\Field(type="collection")
     */
    protected $roles;

    /**
     * @MongoDB\ReferenceMany(targetDocument="App\Application\Sonata\UserBundle\Document\Group")
     */
    protected $groups;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\PersonalAccount")
     */
    protected $personalAccount;

    /** TODO добавить личную наценку исходя из которой будет устанавливаться цена покупки*/
    /** TODO добавить два типа покупателя */

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * @param mixed $create_date
     */
    public function setCreateDate($create_date): void
    {
        $this->create_date = $create_date;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
        $this->setUsername($phone);
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Creates a new instance from a given JWT payload.
     *
     * @param string $username
     * @param array $payload
     *
     * @return JWTUserInterface
     */
    public static function createFromPayload($username, array $payload)
    {
        return new self(
            $username,
            $payload['roles'], // Added by default
            $payload['email']  // Custom
        );
    }

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirmPassword;
    }

    /**
     * @param mixed $confirmPassword
     */
    public function setConfirmPassword($confirmPassword): void
    {
        $this->confirmPassword = $confirmPassword;
    }

    /**
     * @return mixed
     */
    public function getPersonalAccount()
    {
        return $this->personalAccount;
    }

    /**
     * @param mixed $personalAccount
     */
    public function setPersonalAccount($personalAccount): void
    {
        $this->personalAccount = $personalAccount;
    }
}