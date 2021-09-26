<?php

namespace Lexik\Bundle\CurrencyBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class Currency
{
    protected int $id;

    /**
     * @Assert\Length(min=3)
     * @Assert\Length(max=3)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    protected string $code;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     */
    protected float $rate;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }

    public function convert(float $rate): void
    {
        $this->rate /= $rate;
    }
}
