<?php

namespace Ojs\JournalBundle\Entity;

use APY\DataGridBundle\Grid\Mapping as GRID;
use Ojs\CoreBundle\Entity\GenericEntityTrait;
use Ojs\LocationBundle\Entity\Country;
use Ojs\LocationBundle\Entity\Province;

/**
 * JournalContact.
 *
 * @GRID\Source(columns="id, title, fullName")
 */
class JournalContact
{
    use GenericEntityTrait;

    /**
     * @var int
     * @GRID\Column(title="id")
     */
    protected $id;

    /**
     * @var string
     * @GRID\Column(title="title")
     */
    private $title;

    /**
     * @var string
     * @GRID\Column(title="firstname")
     */
    private $fullName;

    /**
     * @var string
     */
    protected $affiliation;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var ContactTypes
     */
    private $contactType;

    /**
     * @var Province
     */
    protected $city;

    /**
     * @var Country
     */
    protected $country;

    /**
     * @var Journal
     */
    private $journal;

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return JournalContact
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set firstName.
     *
     * @param string $fullName
     *
     * @return JournalContact
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return JournalContact
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return JournalContact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return JournalContact
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Province
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param Province $city
     *
     * @return $this
     */
    public function setCity(Province $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     *
     * @return $this
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getAffiliation()
    {
        return $this->affiliation;
    }

    /**
     * @param string $affiliation
     *
     * @return $this
     */
    public function setAffiliation($affiliation)
    {
        $this->affiliation = $affiliation;

        return $this;
    }

    /**
     * Get contactType.
     *
     * @return ContactTypes
     */
    public function getContactType()
    {
        return $this->contactType;
    }

    /**
     * @param ContactTypes $contactType
     *
     * @return $this
     */
    public function setContactType(ContactTypes $contactType)
    {
        $this->contactType = $contactType;

        return $this;
    }

    /**
     * @return Journal
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * @param Journal $journal
     *
     * @return $this
     */
    public function setJournal(Journal $journal)
    {
        $this->journal = $journal;

        return $this;
    }

    public function __toString()
    {
        return $this->getFullName();
    }
}
