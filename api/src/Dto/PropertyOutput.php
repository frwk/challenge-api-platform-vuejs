<?php

namespace App\Dto;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
final class PropertyOutput
{
    public ?bool $isAlreadyApplied;
    public ?string $title;
    public ?string $address;
    public ?string $zipcode;
    public ?string $city;
    public ?string $country;
    public ?string $description;
    public ?string $type;
    public ?int $rooms;
    public ?int $surface;
    public ?bool $has_balcony;
    public ?bool $has_terrace;
    public ?bool $has_cave;
    public ?bool $has_elevator;
    public ?bool $has_parking;
    public ?int $price;
    public ?string $heat_type;
    public ?bool $is_furnished;
    public ?string $state;
    public ?int $level;
    public ?array $photos;


//    public function __construct(
//        bool $isAlreadyApplied, string $title, string $address,
//        string $zipcode, string $city, string $country,
//        string $description, string $type, int $rooms,
//        int $surface, bool $has_balcony, bool $has_terrace,
//        bool $has_cave, bool $has_elevator, bool $has_parking,
//        int $price, string $heat_type, bool $is_furnished,
//        string $state, int $level
//    )
//    {
//
//        $this->zipcode = $zipcode;
//
//    }
    /**
     * @param bool $isAlreadyApplied
     * @param string $title
     * @param string $address
     * @param string $zipcode
     * @param string $city
     * @param string $country
     * @param string $description
     * @param string $type
     * @param int $rooms
     * @param int $surface
     * @param bool $has_balcony
     * @param bool $has_terrace
     * @param bool $has_cave
     * @param bool $has_elevator
     * @param bool $has_parking
     * @param int $price
     * @param string $heat_type
     * @param bool $is_furnished
     * @param string $state
     * @param int $level
     * @param array $photos
     */
    public function __construct(
            ?bool $isAlreadyApplied, ?string $title, ?string $address,
            ?string $zipcode, ?string $city, ?string $country,
            ?string $description, ?string $type, ?int $rooms,
            ?int $surface, ?bool $has_balcony, ?bool $has_terrace,
            ?bool $has_cave, ?bool $has_elevator, ?bool $has_parking,
            ?int $price, ?string $heat_type, ?bool $is_furnished,
            ?string $state, ?int $level, ?array $photos
    )
    {
        $this->isAlreadyApplied = $isAlreadyApplied;
        $this->title = $title;
        $this->address = $address;
        $this->zipcode = $zipcode;
        $this->city = $city;
        $this->country = $country;
        $this->description = $description;
        $this->type = $type;
        $this->rooms = $rooms;
        $this->surface = $surface;
        $this->has_balcony = $has_balcony;
        $this->has_terrace = $has_terrace;
        $this->has_cave = $has_cave;
        $this->has_elevator = $has_elevator;
        $this->has_parking = $has_parking;
        $this->price = $price;
        $this->heat_type = $heat_type;
        $this->is_furnished = $is_furnished;
        $this->state = $state;
        $this->level = $level;
        $this->photos = $photos;
    }
}