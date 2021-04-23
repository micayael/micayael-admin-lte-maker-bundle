<?php

namespace Micayael\AdminLteMakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Micayael\AdminLteMakerBundle\Traits\EntityRevisionTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="parametro",
 *     indexes={
 *          @ORM\Index(columns={"dominio", "codigo"})
 *     }
 * )
 *
 * @ORM\Entity(repositoryClass="Micayael\AdminLteMakerBundle\Repository\ParametroRepository")
 *
 * @UniqueEntity(
 *     fields={"dominio", "codigo"},
 *     message="Este código ya existe en este dominio",
 *     errorPath="codigo"
 * )
 */
class Parametro
{
    use EntityRevisionTrait;

    const TIPOS = [
        'Texto' => 'string',
        'Número Entero' => 'integer',
        'Número Decimal' => 'float',
        'Si/No' => 'boolean',
        'Lista' => 'array',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @Assert\Length(max="255")
     */
    private $dominio;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @Assert\Length(max="255")
     */
    private $codigo;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     *
     * @Assert\Length(max="2000")
     */
    private $valor;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orden;

    public function __toString()
    {
        if (!$this->dominio) {
            return '';
        }

        return $this->dominio.': '.$this->codigo;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDominio(): ?string
    {
        return $this->dominio;
    }

    public function setDominio(string $dominio): self
    {
        $this->dominio = $dominio;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getOrden(): ?int
    {
        return $this->orden;
    }

    public function setOrden(?int $orden): self
    {
        $this->orden = $orden;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function getTipoValor(): ?string
    {
        $tipos = array_flip(self::TIPOS);

        if (!$this->tipo) {
            return $tipos['string'];
        }

        if (!isset($tipos[$this->tipo])) {
            throw new \Exception('Tipo de dato no soportado');
        }

        return $tipos[$this->tipo];
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }
}
