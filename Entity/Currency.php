<?php

namespace Lexik\Bundle\CurrencyBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class Currency
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @Assert\Length(min=3)
     * @Assert\Length(max=3)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     *
     * @var string
     */
    protected $code;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     *
     * @var string
     */
    protected $rate;

    /**
     * Get ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get rate
     *
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set rate
     *
     * @param string $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * Convert currency rate
     *
     * @param float $rate
     */
    public function convert($rate)
    {
        $this->rate /= $rate;
    }
}
