<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Sonata\UserBundle\Document\BaseUser as BaseUser;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * @MongoDB\Document
 * @MongoDBUnique(fields={"phone"}, message="Номер телефона занят")
 */
class User extends BaseUser //implements JWTUserInterface
{
    const USER_TYPE_ENTITY = 'ENTIYY';
    const USER_TYPE_INDIVIDUAL = 'INDIVIDUAL';

    public static $userTypes = [
        'Юридическое лицо' => self::USER_TYPE_ENTITY,
        'Физическое лицо'  => self::USER_TYPE_INDIVIDUAL
    ];
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $firstName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $secondName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $lastName;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $company;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $inn;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $kpp;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $city;

    /**
     * @Assert\Email()
     * @MongoDB\Field(type="string")
     */
    protected $email;

    /**
     * @AssertPhoneNumber
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
     * @MongoDB\Field(type="string")
     */
    protected $type;

    /**
     * @MongoDB\ReferenceMany(targetDocument="App\Application\Sonata\UserBundle\Document\Group")
     */
    protected $groups;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\PersonalAccount")
     */
    protected $personalAccount;

    /** TODO добавить личную наценку исходя из которой будет устанавливаться цена покупки*/

    /** TODO должны приходить уведомления о смене статуса на нет в наличии или Отказ
     * Тема писма Произошло изменение по вашим заказам
     */
    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $sendToEmail;

    /** TODO должны приходить уведомления о смене статуса на нет в наличии или Отказ
     * в СМС должно быть написано “ заказ № 000001 (Бренд Артикул Кол-во шт) Статус.” Отправитель должен быть LEEMANCAR.RU
     */

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $sendToPhone;
    /**
     * @return mixed
     */
    public function getRoles()
    {
        if(empty($this->roles)){
            return ['ROLE_USER'];
        }
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
        $this->setUsername('');
    }

    public function setUsername($username)
    {
        $username = $this->getPhone();
        $this->username = $username;

        return $this;
    }

    /*public function getUsername()
    {
        return $this->phone;
    }*/

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

    /**
     * @return array
     */
    public function getUserTypes(): array
    {
        return $this->userTypes;
    }

    /**
     * @return mixed
     */
    public function getSecondName()
    {
        return $this->secondName;
    }

    /**
     * @param mixed $secondName
     */
    public function setSecondName($secondName): void
    {
        $this->secondName = $secondName;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company): void
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * @param mixed $inn
     */
    public function setInn($inn): void
    {
        $this->inn = $inn;
    }

    /**
     * @return mixed
     */
    public function getKpp()
    {
        return $this->kpp;
    }

    /**
     * @param mixed $kpp
     */
    public function setKpp($kpp): void
    {
        $this->kpp = $kpp;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSendToEmail()
    {
        return $this->sendToEmail;
    }

    /**
     * @param mixed $sendToEmail
     */
    public function setSendToEmail($sendToEmail): void
    {
        $this->sendToEmail = $sendToEmail;
    }

    /**
     * @return mixed
     */
    public function getSendToPhone()
    {
        return $this->sendToPhone;
    }

    /**
     * @param mixed $sendToPhone
     */
    public function setSendToPhone($sendToPhone): void
    {
        $this->sendToPhone = $sendToPhone;
    }

    public function getFullname()
    {
        return sprintf('%s %s %s', $this->getFirstname(), $this->getSecondname(), $this->getLastname());
    }
}