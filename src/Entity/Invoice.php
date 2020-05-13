<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 * @ORM\Table(name="si_invoices", indexes={@ORM\Index(name="invoice_id_idx", columns={"invoice_id"}), @ORM\Index(name="dueOn_idx", columns={"due_on"}), @ORM\Index(name="sellingPrice_idx", columns={"selling_price"})})
 */
class Invoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $invoiceId;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     * @Assert\DateTime
     */
    private $dueOn;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     */
    private $sellingPrice;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"fileUpload"})
     * @Assert\File(groups={"fileUpload"},
     *     mimeTypes = {"text/plain", "text/x-csv", "application/vnd.ms-excel"},
     *     mimeTypesMessage = "Please upload a valid CSV file"
     * )
     */
    private $fileName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoiceId(): ?int
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(int $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDueOn(): ?\DateTimeInterface
    {
        return $this->dueOn;
    }

    public function setDueOn(\DateTimeInterface $dueOn): self
    {
        $this->dueOn = $dueOn;

        return $this;
    }

    public function getSellingPrice(): ?string
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(string $sellingPrice): self
    {
        $this->sellingPrice = $sellingPrice;

        return $this;
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

}
