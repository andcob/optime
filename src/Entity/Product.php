<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 *  @ORM\Entity(repositoryClass=ProductRepository::class)
 *  @UniqueEntity("code")
 *  @UniqueEntity("name")
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Assert\NotBlank
     *  @Assert\Regex( 
     *    pattern = "/^[a-z0-9]+$/i", 
     *    htmlPattern = "^[a-zA-Z0-9]+$",
     *    message="Product code must not contain special chars or empty spaces"
     *  ) 
     *  @Assert\Length(
     *      min = 4,
     *      max = 10,
     *      minMessage = "Product Code must be at least {{ limit }} characters long",
     *      maxMessage = "Product Code cannot be longer than {{ limit }} characters"
     * )
     *
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     *  @Assert\NotBlank
     *  @Assert\Length(
     *      min = 4,
     *      minMessage = "Product Name must be at least {{ limit }} characters long",
     * )
     * 
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     *  @Assert\NotBlank(
     *     message = "This field is rquired."
     * )
     * 
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     *  @Assert\NotBlank(
     *     message = "This field is rquired."
     * )
     * 
     */
    private $brand;

    /**
     * @ORM\Column(type="float", nullable=true)
     * 
     *  @Assert\NotBlank(
     *     message = "This field is rquired."
     * )
     * @Assert\Type("float")
     * 
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * 
     *  @Assert\NotBlank(
     *     message = "This field is rquired."
     * )
     * 
     */
    private $category;

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
