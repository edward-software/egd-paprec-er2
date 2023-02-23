<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Picture
 *
 * @ORM\Table(name="quoteRequestFiles")
 * @ORM\Entity(repositoryClass="App\Repository\QuoteRequestFileRepository")
 */
class QuoteRequestFile
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
     * @var string
     *
     * @ORM\Column(name="originalFileName", type="string", length=255, nullable=true)
     */
    private $originalFileName;

    /**
     * @var string
     *
     * @ORM\Column(name="mimeType", type="string", length=255, nullable=true)
     */
    private $mimeType;

    /**
     * @var string
     *
     * @ORM\Column(name="systemName", type="string", length=255, nullable=true)
     */
    private $systemName;

    /**
     * @var string
     *
     * @ORM\Column(name="systemSize", type="integer", nullable=true)
     */
    private $systemSize;

    /**
     * !! Attention, attribut non intégré dans la base de données, permettant de récupérer les infos du fichier
     */
    private $systemPath;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * #################################
     *              Relations
     * #################################
     */

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\QuoteRequest", inversedBy="quoteRequestFiles")
     * @ORM\JoinColumn(name="quoteRequestId")
     */
    private $quoteRequest;

    public function getId()
    {
        return $this->id;
    }

    public function getOriginalFileName()
    {
        return $this->originalFileName;
    }

    public function setOriginalFileName($originalFileName)
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSystemName()
    {
        return $this->systemName;
    }

    public function setSystemName($systemName)
    {
        $this->systemName = $systemName;

        return $this;
    }

    public function getQuoteRequest(): ?QuoteRequest
    {
        return $this->quoteRequest;
    }

    public function setQuoteRequest(?QuoteRequest $quoteRequest): self
    {
        $this->quoteRequest = $quoteRequest;

        return $this;
    }

    public function getSystemSize()
    {
        return $this->systemSize;
    }

    public function setSystemSize($systemSize)
    {
        $this->systemSize = $systemSize;

        return $this;
    }

    public function getSystemPath()
    {
        return $this->systemPath;
    }

    public function setSystemPath($systemPath)
    {
        $this->systemPath = $systemPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Cart
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
