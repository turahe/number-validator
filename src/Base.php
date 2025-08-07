<?php
namespace Turahe\Validator;

/**
 * Base class for number validation with optimized performance
 */
abstract class Base
{
    /**
     * Location data from JSON file
     */
    public readonly array $location;
    
    /**
     * The number to validate
     */
    public readonly string $number;

    /**
     * Cached values for performance
     */
    private ?object $cachedBornDate = null;
    private ?object $cachedAge = null;
    private ?object $cachedNextBirthday = null;
    private ?string $cachedGender = null;

    /**
     * Constructor with property promotion and optimized file loading
     */
    public function __construct(
        string $number,
        ?string $wilayahPath = null
    ) {
        $this->number = $number;
        
        // Optimized file loading with error handling
        $wilayahPath ??= dirname(__FILE__) . '/assets/wilayah.json';
        
        if (!file_exists($wilayahPath)) {
            throw new \InvalidArgumentException("Wilayah file not found: $wilayahPath");
        }
        
        $jsonContent = file_get_contents($wilayahPath);
        if ($jsonContent === false) {
            throw new \RuntimeException("Failed to read wilayah file: $wilayahPath");
        }
        
        $this->location = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR) ?? [];
    }

    /**
     * Get last 2 digits number at the current year (cached)
     */
    public function getCurrentYear(): int
    {
        static $currentYear = null;
        
        if ($currentYear === null) {
            $currentYear = (int) substr(date('Y'), -2);
        }
        
        return $currentYear;
    }

    /**
     * Get year in NIK (optimized with direct access)
     */
    public function getNIKYear(): int
    {
        return (int) ($this->number[10] . $this->number[11]);
    }

    /**
     * Get date in number (optimized with direct access)
     */
    public function getNIKDate(): int
    {
        return (int) ($this->number[6] . $this->number[7]);
    }

    /**
     * Get born date from NIK (cached for performance)
     */
    public function getBornDate(): object
    {
        if ($this->cachedBornDate !== null) {
            return $this->cachedBornDate;
        }

        $nikDate = $this->getNIKDate();
        $nikYear = $this->getNIKYear();
        $currYear = $this->getCurrentYear();
        $isFemale = $this->getGender() === 'PEREMPUAN';

        // Optimized date calculation
        if ($isFemale) {
            $nikDate -= 40;
        }
        
        $date = $nikDate >= 10 ? (string) $nikDate : "0$nikDate";
        $month = $this->number[8] . $this->number[9];
        $year = (string) ($nikYear < $currYear ? 2000 + $nikYear : 1900 + $nikYear);
        $full = "$date-$month-$year";

        return $this->cachedBornDate = (object) compact('date', 'month', 'year', 'full');
    }

    /**
     * Get age data from born date (cached for performance)
     */
    public function getAge(): object
    {
        if ($this->cachedAge !== null) {
            return $this->cachedAge;
        }

        $bornDate = $this->getBornDate()->full;
        $bornTimestamp = strtotime($bornDate);
        
        if ($bornTimestamp === false) {
            throw new \RuntimeException("Invalid birth date format: $bornDate");
        }
        
        $ageDate = time() - $bornTimestamp;

        $year = abs(gmdate('Y', $ageDate) - 1970);
        $month = abs(gmdate('m', $ageDate));
        $day = abs(gmdate('d', $ageDate) - 1);

        return $this->cachedAge = (object) compact('year', 'month', 'day');
    }

    /**
     * Get next birthday from born date (cached for performance)
     */
    public function getNextBirthday(): object
    {
        if ($this->cachedNextBirthday !== null) {
            return $this->cachedNextBirthday;
        }

        $bornDate = $this->getBornDate()->full;
        $bornTimestamp = strtotime($bornDate);
        
        if ($bornTimestamp === false) {
            throw new \RuntimeException("Invalid birth date format: $bornDate");
        }
        
        $diff = $bornTimestamp - time();

        $month = abs(gmdate('m', $diff));
        $day = abs(gmdate('d', $diff) - 1);

        return $this->cachedNextBirthday = (object) compact('month', 'day');
    }

    /**
     * Get zodiac from born date (optimized with early return)
     */
    public function getZodiac(): string
    {
        $bornDate = $this->getBornDate();
        $month = (int) $bornDate->month;
        $date = (int) $bornDate->date;

        // Optimized zodiac calculation with early returns
        if ($month === 1) {
            return $date >= 20 ? 'Aquarius' : 'Capricorn';
        }
        
        if ($month === 2) {
            return $date >= 19 ? 'Pisces' : 'Aquarius';
        }
        
        if ($month === 3) {
            return $date >= 21 ? 'Aries' : 'Pisces';
        }
        
        if ($month === 4) {
            return $date >= 20 ? 'Taurus' : 'Aries';
        }
        
        if ($month === 5) {
            return $date >= 21 ? 'Gemini' : 'Taurus';
        }
        
        if ($month === 6) {
            return $date >= 21 ? 'Cancer' : 'Gemini';
        }
        
        if ($month === 7) {
            return $date >= 23 ? 'Leo' : 'Cancer';
        }
        
        if ($month === 8) {
            return $date >= 23 ? 'Virgo' : 'Leo';
        }
        
        if ($month === 9) {
            return $date >= 23 ? 'Libra' : 'Virgo';
        }
        
        if ($month === 10) {
            return $date >= 24 ? 'Scorpio' : 'Libra';
        }
        
        if ($month === 11) {
            return $date >= 23 ? 'Sagittarius' : 'Scorpio';
        }
        
        // December
        return $date >= 22 ? 'Capricorn' : 'Sagittarius';
    }

    /**
     * Get the province from NIK (optimized with direct access)
     */
    public function getProvince(): ?string
    {
        $provinceCode = $this->number[0] . $this->number[1];
        return $this->location['provinsi'][$provinceCode] ?? null;
    }

    /**
     * Get the city from NIK (optimized with direct access)
     */
    public function getCity(): ?string
    {
        $cityCode = $this->number[0] . $this->number[1] . $this->number[2] . $this->number[3];
        return $this->location['kabkot'][$cityCode] ?? null;
    }

    /**
     * Get the sub-district from NIK (optimized with direct access)
     */
    public function getSubDistrict(): ?string
    {
        $subDistrictCode = $this->number[0] . $this->number[1] . $this->number[2] . 
                           $this->number[3] . $this->number[4] . $this->number[5];
        
        $result = $this->location['kecamatan'][$subDistrictCode] ?? null;
        
        if ($result === null) {
            return null;
        }

        $parts = explode('--', $result, 2);
        return trim($parts[0]);
    }

    /**
     * Get postal code (optimized with direct access)
     */
    public function getPostalCode(): ?string
    {
        $subDistrictCode = $this->number[0] . $this->number[1] . $this->number[2] . 
                           $this->number[3] . $this->number[4] . $this->number[5];
        
        $result = $this->location['kecamatan'][$subDistrictCode] ?? null;
        
        if ($result === null) {
            return null;
        }

        $parts = explode('--', $result, 2);
        return isset($parts[1]) ? trim($parts[1]) : null;
    }

    /**
     * Get gender (cached for performance)
     */
    public function getGender(): string
    {
        if ($this->cachedGender !== null) {
            return $this->cachedGender;
        }

        $date = $this->getNIKDate();
        return $this->cachedGender = ($date > 40) ? 'PEREMPUAN' : 'LAKI-LAKI';
    }

    /**
     * Clear cached values (useful for testing or when data changes)
     */
    protected function clearCache(): void
    {
        $this->cachedBornDate = null;
        $this->cachedAge = null;
        $this->cachedNextBirthday = null;
        $this->cachedGender = null;
    }
}
