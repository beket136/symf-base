<?php

namespace Bek\UZBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * UZSearchRequest
 *
 * @ORM\Table(name="uz_search_request")
 * @ORM\Entity(repositoryClass="Bek\UZBundle\Repository\UZSearchRequestRepository")
 */
class UZSearchRequest
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="station_id_from", type="integer")
     */
    private $stationIdFrom;

    /**
     * @var int
     *
     * @ORM\Column(name="station_id_till", type="integer")
     */
    private $stationIdTill;

    /**
     * @var string
     *
     * @ORM\Column(name="station_from", type="string", length=255)
     */
    private $stationFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="station_till", type="string", length=255)
     */
    private $stationTill;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_dep", type="date")
     */
    private $dateDep;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_dep", type="time")
     */
    private $timeDep;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return UZSearchRequest
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return UZSearchRequest
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set stationIdFrom
     *
     * @param integer $stationIdFrom
     *
     * @return UZSearchRequest
     */
    public function setStationIdFrom($stationIdFrom)
    {
        $this->stationIdFrom = $stationIdFrom;

        return $this;
    }

    /**
     * Get stationIdFrom
     *
     * @return int
     */
    public function getStationIdFrom()
    {
        return $this->stationIdFrom;
    }

    /**
     * Set stationIdTill
     *
     * @param integer $stationIdTill
     *
     * @return UZSearchRequest
     */
    public function setStationIdTill($stationIdTill)
    {
        $this->stationIdTill = $stationIdTill;

        return $this;
    }

    /**
     * Get stationIdTill
     *
     * @return int
     */
    public function getStationIdTill()
    {
        return $this->stationIdTill;
    }

    /**
     * Set stationFrom
     *
     * @param string $stationFrom
     *
     * @return UZSearchRequest
     */
    public function setStationFrom($stationFrom)
    {
        $this->stationFrom = $stationFrom;

        return $this;
    }

    /**
     * Get stationFrom
     *
     * @return string
     */
    public function getStationFrom()
    {
        return $this->stationFrom;
    }

    /**
     * Set stationTill
     *
     * @param string $stationTill
     *
     * @return UZSearchRequest
     */
    public function setStationTill($stationTill)
    {
        $this->stationTill = $stationTill;

        return $this;
    }

    /**
     * Get stationTill
     *
     * @return string
     */
    public function getStationTill()
    {
        return $this->stationTill;
    }

    /**
     * Set dateDep
     *
     * @param \DateTime $dateDep
     *
     * @return UZSearchRequest
     */
    public function setDateDep(\DateTime $dateDep)
    {
        $this->dateDep = $dateDep;

        return $this;
    }

    /**
     * Get dateDep
     *
     * @return \DateTime
     */
    public function getDateDep()
    {
        return $this->dateDep;
    }

    /**
     * Set time
     *
     * @param \DateTime $timeDep
     *
     * @return UZSearchRequest
     */
    public function setTimeDep($timeDep)
    {
        $this->timeDep = $timeDep;

        return $this;
    }

    /**
     * Get timeDep
     *
     * @return \DateTime
     */
    public function getTimeDep()
    {
        return $this->timeDep;
    }

    public function setParams(array $params)
    {

        if (!$this->validateParams($params)) {
            return false;
        }

        $this->stationTill = $params['station_till'];
        $this->stationFrom = $params['station_from'];
        $this->stationIdTill = $params['station_id_till'];
        $this->stationIdFrom = $params['station_id_from'];
        $this->email = $params['email'];
        $this->dateDep = new \DateTime($params['date_dep']);
        $this->timeDep = !empty($params['time_dep']) ? new \DateTime($params['time_dep']) : new \DateTime('00:00:00');
        $this->userId = !empty($params['userId']) ? $params['userId'] : null;

        return $this;
    }

    /**
     * @param array $params
     * @return bool
     */
    private function validateParams(array $params): bool
    {
        if (
            empty($params['station_from'])    || !strval($params['station_from'])    ||
            empty($params['station_id_from']) || !intval($params['station_id_from']) ||
            empty($params['station_till'])    || !strval($params['station_till'])    ||
            empty($params['station_id_till']) || !intval($params['station_id_till']) ||
            empty($params['user_id'])         || !intval($params['user_id'])         ||
            empty($params['email'])           || !strval($params['email'])           ||
            empty($params['date_dep'])        || !strval($params['date_dep'])
        ) {
            return false;
        }
        return true;
    }

}

