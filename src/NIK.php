<?php
namespace Turahe\Validator;

/**
 *
 */
class NIK extends Base
{
    /**
     * @param $number
     * @return self
     */
    public static function set($number)
    {
        return new self(strval($number));
    }

    /**
     * @return object
     */
    public function parse(): object
    {
        if ($this->validate()) {
            $born = $this->getBornDate();

            return (object) [
                'number'          => $this->number,
                'uniqueCode'      => $this->getUniqueCode(),
                'gender'          => $this->getGender(),
                'born'            => $this->getBornDate(),
                'age'             => $this->getAge(),
                'nextBirthday'    => $this->getNextBirthday(),
                'zodiac'          => $this->getZodiac(),
                'address'         => (object) [
                    'province'    => $this->getProvince(),
                    'city'        => $this->getCity(),
                    'subDistrict' => $this->getSubDistrict(),
                ],
                'postalCode' => $this->getPostalCode(),
                'valid'      => true,
            ];
        }

        return (object) [
            'valid' => false,
        ];
    }

    /**
     * Get unique code from NIK
     *
     * @return string|null
     */
    private function getUniqueCode(): ?string
    {
        return substr($this->number, 12, 4) ?? null;
    }
    
    /**
     * Get gender from NIK Date
     *
     * @return string
     */
    public function getGender(): string
    {
        $date = $this->getNIKDate();
        
        return ($date > 40) ? 'PEREMPUAN' : 'LAKI-LAKI';
    }
    
    /**
     * Make sure NIK is valid
     *
     * @return bool
     */
    public function validate(): bool
    {
        $length = (strlen($this->number) == 16);
        
        return $length &&
        $this->getProvince() &&
        $this->getCity() &&
        $this->getSubDistrict();
    }
}
