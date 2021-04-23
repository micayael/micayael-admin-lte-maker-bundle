<?php

namespace Micayael\AdminLteMakerBundle\Traits;

trait EntityRevisionTrait
{
    /**
     * @ORM\Column(type="integer", options={"default":1})
     *
     * @ORM\Version()
     */
    private $revision;

    public function getRevision(): ?int
    {
        return $this->revision;
    }

    public function setRevision(int $revision): self
    {
        $this->revision = $revision;

        return $this;
    }
}
