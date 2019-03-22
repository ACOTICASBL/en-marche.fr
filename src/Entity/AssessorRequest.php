<?php

namespace AppBundle\Entity;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use AppBundle\Validator\Recaptcha as AssertRecaptcha;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table
 * @ORM\Entity
 *
 * @Algolia\Index(autoIndex=false)
 */
class AssessorRequest
{
    use EntityTimestampableTrait;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=50)
     *
     * @Assert\NotBlank(message="assessor.last_name.not_blank")
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="assessor.last_name.min_length",
     *     maxMessage="assessor.last_name.max_length"
     * )
     */
    private $lastName = '';

    /**
     * @var string
     *
     * @ORM\Column(length=100)
     *
     * @Assert\NotBlank(message="assessor.first_name.not_blank")
     * @Assert\Length(
     *     min=2,
     *     max=100,
     *     minMessage="assessor.first_name.min_length",
     *     maxMessage="assessor.first_name.max_length"
     * )
     */
    private $firstName = '';

    /**
     * @var string|null
     *
     * @ORM\Column(length=50, nullable=true)
     *
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="assessor.birth_name.min_length",
     *     maxMessage="assessor.birth_name.max_length"
     * )
     */
    private $birthName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank(message="assessor.birthdate.not_blank")
     * @Assert\Range(max="-30 years", maxMessage="assessor.birthdate.minimum_required_age")
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(length=15)
     *
     * @Assert\Length(max=15)
     */
    private $birthCity;

    /**
     * @var string
     *
     * @ORM\Column(length=150)
     *
     * @Assert\NotBlank(message="common.address.required")
     * @Assert\Length(max=150, maxMessage="common.address.max_length")
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(length=15)
     *
     * @Assert\Length(max=15)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(length=15, name="city_insee")
     *
     * @Assert\Length(max=15)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(length=15)
     *
     * @Assert\NotBlank(message="assessor.vote_city.not_blank")
     * @Assert\Length(max=15)
     */
    private $voteCity;

    /**
     * @var string
     *
     * @ORM\Column(length=10)
     *
     * @Assert\NotBlank(message="assessor.office_number.not_blank")
     * @Assert\Length(max=10)
     */
    private $officeNumber;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     * @Assert\Email(message="common.email.invalid")
     * @Assert\Length(max=255, maxMessage="common.email.max_length")
     */
    private $emailAddress;

    /**
     * @var PhoneNumber
     *
     * @ORM\Column(type="phone_number")
     *
     * @Assert\NotBlank(message="common.phone_number.required")
     * @AssertPhoneNumber(defaultRegion="FR")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(length=15)
     *
     * @Assert\NotBlank(message="assessor.assessor_city.not_blank")
     * @Assert\Length(max=15)
     */
    private $assessorCity;

    /**
     * @var string
     *
     * @ORM\Column(length=15)
     *
     * @Assert\NotBlank(message="assessor.office.invalid_choice")
     * @Assert\Choice(
     *     callback={"AppBundle\Entity\AssessorOfficeEnum", "toArray"},
     *     message="assessor.office.invalid_choice",
     *     strict=true
     * )
     */
    private $office = AssessorOfficeEnum::SUBSTITUTE;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="common.recaptcha.invalid_message")
     * @AssertRecaptcha
     */
    public $recaptcha = '';

    public function __construct()
    {
        $this->phone = static::createPhoneNumber();
    }

    private static function createPhoneNumber(int $countryCode = 33, string $number = null): PhoneNumber
    {
        $phone = new PhoneNumber();
        $phone->setCountryCode($countryCode);

        if ($number) {
            $phone->setNationalNumber($number);
        }

        return $phone;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFirstNames(): string
    {
        return $this->firstNames;
    }

    public function setFirstNames(string $firstNames): void
    {
        $this->firstNames = $firstNames;
    }

    public function getBirthName(): ?string
    {
        return $this->birthName;
    }

    public function setBirthName(?string $birthName): void
    {
        $this->birthName = $birthName;
    }

    public function getBirthdate(): \DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTime $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    public function getBirthCity(): ?string
    {
        return $this->birthCity;
    }

    public function setBirthCity(?string $birthCity): void
    {
        $this->birthCity = $birthCity;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getVoteCity(): string
    {
        return $this->voteCity;
    }

    public function setVoteCity(string $voteCity): void
    {
        $this->voteCity = $voteCity;
    }

    public function getOfficeNumber(): string
    {
        return $this->officeNumber;
    }

    public function setOfficeNumber(string $officeNumber): void
    {
        $this->officeNumber = $officeNumber;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    public function getPhone(): PhoneNumber
    {
        return $this->phone;
    }

    public function setPhone(PhoneNumber $phone): void
    {
        $this->phone = $phone;
    }

    public function getAssessorCity(): string
    {
        return $this->assessorCity;
    }

    public function setAssessorCity(string $assessorCity): void
    {
        $this->assessorCity = $assessorCity;
    }

    public function getOffice(): string
    {
        return $this->office;
    }

    public function setOffice(string $office): void
    {
        $this->office = $office;
    }

    public function getRecaptcha(): string
    {
        return $this->recaptcha;
    }

    public function setRecaptcha(string $recaptcha): void
    {
        $this->recaptcha = $recaptcha;
    }
}
