<?php
namespace Turahe\Validator;

/**
 * KK (Kartu Keluarga) validator class with optimized performance
 */
class KK extends Base
{
    /**
     * Create a new KK instance with optimized type handling
     */
    public static function set(string|int $number): self
    {
        $numberString = (string) $number;
        
        // Pre-validate number format for better performance
        if (strlen($numberString) !== 16 || !ctype_digit($numberString)) {
            throw new \InvalidArgumentException('KK number must be exactly 16 digits');
        }
        
        return new self($numberString);
    }

    /**
     * Parse and validate KK data with optimized structure
     */
    public function parse(): object
    {
        if (!$this->validate()) {
            return (object) ['valid' => false];
        }

        // Cache frequently accessed data
        $address = (object) [
            'province'    => $this->getProvince(),
            'city'        => $this->getCity(),
            'subDistrict' => $this->getSubDistrict(),
        ];

        return (object) [
            'number'      => $this->number,
            'address'     => $address,
            'postalCode'  => $this->getPostalCode(),
            'valid'       => true,
        ];
    }

    /**
     * Validate KK format and data with optimized checks
     */
    public function validate(): bool
    {
        // Quick length check first
        if (strlen($this->number) !== 16) {
            return false;
        }
        
        // Check if all characters are digits
        if (!ctype_digit($this->number)) {
            return false;
        }
        
        // Validate location data exists
        $province = $this->getProvince();
        $city = $this->getCity();
        $subDistrict = $this->getSubDistrict();
        
        return $province !== null && $city !== null && $subDistrict !== null;
    }

    /**
     * Get detailed validation errors for debugging
     */
    public function getValidationErrors(): array
    {
        $errors = [];
        
        if (strlen($this->number) !== 16) {
            $errors[] = 'KK number must be exactly 16 digits';
        }
        
        if (!ctype_digit($this->number)) {
            $errors[] = 'KK number must contain only digits';
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
     * Get KK information as array (alternative to object)
     */
    public function toArray(): array
    {
        $result = $this->parse();
        
        if (!$result->valid) {
            return ['valid' => false];
        }
        
        return [
            'number'      => $result->number,
            'address'     => [
                'province'    => $result->address->province,
                'city'        => $result->address->city,
                'subDistrict' => $result->address->subDistrict,
            ],
            'postalCode'  => $result->postalCode,
            'valid'       => true,
        ];
    }

    /**
     * Get KK number with formatting (e.g., "1234-5678-9012-3456")
     */
    public function getFormattedNumber(): string
    {
        return implode('-', str_split($this->number, 4));
    }

    /**
     * Get KK number without formatting
     */
    public function getRawNumber(): string
    {
        return $this->number;
    }
}
