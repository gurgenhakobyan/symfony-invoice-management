<?php

namespace App\Entity;

use App\Repository\UploadErrorRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UploadErrorRepository::class)
 * @ORM\Table(name="si_upload_errors", indexes={@ORM\Index(name="upload_date_idx", columns={"upload_date"})})
 */
class UploadError
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $fileName;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private $rowNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $rowText;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private $highlightStart;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private $highlightEnd;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     */
    private $uploadDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getRowNumber(): ?int
    {
        return $this->rowNumber;
    }

    public function setRowNumber(int $rowNumber): self
    {
        $this->rowNumber = $rowNumber;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }


    public function getRowText(): ?string
    {
        return $this->rowText;
    }

    public function setRowText(string $rowText): self
    {
        $this->rowText = $rowText;

        return $this;
    }
    

    public function getUploadDate(): ?\DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function setUploadDate(\DateTimeInterface $uploadDate): self
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    public function getHighlightStart(): ?int
    {
        return $this->highlightStart;
    }

    public function setHighlightStart(?int $highlightStart): self
    {
        $this->highlightStart = $highlightStart;

        return $this;
    }

    public function getHighlightEnd(): ?int
    {
        return $this->highlightEnd;
    }

    public function setHighlightEnd(?int $highlightEnd): self
    {
        $this->highlightEnd = $highlightEnd;

        return $this;
    }
}
