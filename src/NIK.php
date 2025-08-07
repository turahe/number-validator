<?php

declare(strict_types = 1);

namespace Turahe\Validator;

/**
 * NIK (Nomor Induk Kependudukan) validator class with optimized performance.
 */
class NIK extends Base
{
    /**
     * Create a new NIK instance with optimized type handling.
     */
    public static function set(string | int $number): self
    {
        $numberString = (string) $number;

        // Pre-validate number format for better performance
        if (strlen($numberString) !== 16 || ! ctype_digit($numberString)) {
            throw new \InvalidArgumentException('NIK must be exactly 16 digits');
        }

        return new self($numberString);
    }

    /**
     * Parse and validate NIK data with optimized structure.
     */
    public function parse(): object
    {
        if (! $this->validate()) {
            return (object) ['valid' => false];
        }

        // Cache frequently accessed data
        $born = $this->getBornDate();
        $address = (object) [
            'province' => $this->getProvince(),
            'city' => $this->getCity(),
            'subDistrict' => $this->getSubDistrict(),
        ];

        return (object) [
            'number' => $this->number,
            'uniqueCode' => $this->getUniqueCode(),
            'gender' => $this->getGender(),
            'born' => $born,
            'age' => $this->getAge(),
            'nextBirthday' => $this->getNextBirthday(),
            'zodiac' => $this->getZodiac(),
            'address' => $address,
            'postalCode' => $this->getPostalCode(),
            'valid' => true,
        ];
    }

    /**
     * Validate NIK format and data with optimized checks.
     */
    public function validate(): bool
    {
        // Quick length check first
        if (strlen($this->number) !== 16) {
            return false;
        }

        // Check if all characters are digits
        if (! ctype_digit($this->number)) {
            return false;
        }

        // Validate location data exists
        $province = $this->getProvince();
        $city = $this->getCity();
        $subDistrict = $this->getSubDistrict();

        return null !== $province && null !== $city && null !== $subDistrict;
    }

    /**
     * Get detailed validation errors for debugging.
     */
    public function getValidationErrors(): array
    {
        $errors = [];

        if (strlen($this->number) !== 16) {
            $errors[] = 'NIK must be exactly 16 digits';
        }

        if (! ctype_digit($this->number)) {
            $errors[] = 'NIK must contain only digits';
        }

        if ($this->getProvince() === null) {
            $errors[] = 'Invalid province code';
        }

        if ($this->getCity() === null) {
            $errors[] = 'Invalid city code';
        }

        if ($this->getSubDistrict() === null) {
            $errors[] = 'Invalid sub-district code';
        }

        return $errors;
    }

    /**
     * Get NIK information as array (alternative to object).
     */
    public function toArray(): array
    {
        $result = $this->parse();

        if (! $result->valid) {
            return ['valid' => false];
        }

        return [
            'number' => $result->number,
            'uniqueCode' => $result->uniqueCode,
            'gender' => $result->gender,
            'born' => [
                'date' => $result->born->date,
                'month' => $result->born->month,
                'year' => $result->born->year,
                'full' => $result->born->full,
            ],
            'age' => [
                'year' => $result->age->year,
                'month' => $result->age->month,
                'day' => $result->age->day,
            ],
            'nextBirthday' => [
                'month' => $result->nextBirthday->month,
                'day' => $result->nextBirthday->day,
            ],
            'zodiac' => $result->zodiac,
            'address' => [
                'province' => $result->address->province,
                'city' => $result->address->city,
                'subDistrict' => $result->address->subDistrict,
            ],
            'postalCode' => $result->postalCode,
            'valid' => true,
        ];
    }

    /**
     * Get unique code from NIK (optimized with direct access).
     */
    private function getUniqueCode(): string
    {
        return $this->number[12] . $this->number[13] . $this->number[14] . $this->number[15];
    }
}
