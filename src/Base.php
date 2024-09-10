<?php
namespace Turahe\Validator;

/**
 *
 */
abstract class Base
{
    /**
     * @var mixed
     */
    public $location;
    /**
     * @var
     */
    public $number;

    /**
     * @param $number
     */
    public function __construct($number)
    {
        $this->number = $number;
        // Get location from assets and convert it to array
        $wilayahPath = dirname(__FILE__) . '/assets/wilayah.json';
        $this->location = json_decode(file_get_contents($wilayahPath), true);
    }

    /**
     * Get last 2 digits number at the current year
     *
     * @return int
     */
    public function getCurrentYear(): int
    {
        return intval(substr(date('Y'), -2));
    }

    /**
     * Get year in NIK
     *
     * @return int
     */
    public function getNIKYear(): int
    {
        return intval(substr($this->number, 10, 2));
    }

    /**
     * Get date in number
     * @return int
     */
    public function getNIKDate(): int
    {
        return intval(substr($this->number, 6, 2));
    }

    /**
     * Get born date from NIK
     *
     * @return object
     */
    public function getBornDate(): object
    {
        $NIKdate = $this->getNIKDate();
        $NIKyear = $this->getNIKYear();
        $currYear = $this->getCurrentYear();
        $isFemale = ($this->getGender() == 'PEREMPUAN');

        // Get born date
        if ($isFemale) {
            $NIKdate -= 40;
        }
        $date = ($NIKdate >= 10) ? strval($NIKdate) : "0$NIKdate";
        // Get born month
        $month = substr($this->number, 8, 2);
        // Get born year
        $year = strval(($NIKyear < $currYear) ? 2000 + $NIKyear : 1900 + $NIKyear);
        // Generate to full date format (d-m-Y)
        $full = "$date-$month-$year";

        // return as object
        return (object) compact('date', 'month', 'year', 'full');
    }

    /**
     * Get age data from born date
     *
     * @return object
     */
    public function getAge(): object
    {
        $bornDate = $this->getBornDate()->full;
        $ageDate = time() - strtotime($bornDate);

        $year = abs(gmdate('Y', $ageDate) - 1970);
        $month = abs(gmdate('m', $ageDate));
        $day = abs(gmdate('d', $ageDate) - 1);

        return (object) compact('year', 'month', 'day');
    }

    /**
     * Get next birthday from born date
     *
     * @return object
     */
    public function getNextBirthday(): object
    {
        $bornDate = $this->getBornDate()->full;
        $diff = strtotime($bornDate) - time();

        $month = abs(gmdate('m', $diff));
        $day = abs(gmdate('d', $diff) - 1);

        return (object) compact('month', 'day');
    }

    /**
     * Get zodiac from born date
     *
     * @return string
     */
    public function getZodiac(): string
    {
        $bornDate = $this->getBornDate();
        $month = intval($bornDate->month);
        $date = intval($bornDate->date);

        if (($month == 1 && $date >= 20) || ($month == 2 && $date < 19)) {
            return 'Aquarius';
        }

        if (($month == 2 && $date >= 19) || ($month == 3 && $date < 21)) {
            return 'Pisces';
        }

        if (($month == 3 && $date >= 21) || ($month == 4 && $date < 20)) {
            return 'Aries';
        }

        if (($month == 4 && $date >= 20) || ($month == 5 && $date < 21)) {
            return 'Taurus';
        }

        if (($month == 5 && $date >= 21) || ($month == 6 && $date < 22)) {
            return 'Gemini';
        }

        if (($month == 6 && $date >= 21) || ($month == 7 && $date < 23)) {
            return 'Cancer';
        }

        if (($month == 7 && $date >= 23) || ($month == 8 && $date < 23)) {
            return 'Leo';
        }

        if (($month == 8 && $date >= 23) || ($month == 9 && $date < 23)) {
            return 'Virgo';
        }

        if (($month == 9 && $date >= 23) || ($month == 10 && $date < 24)) {
            return 'Libra';
        }

        if (($month == 10 && $date >= 24) || ($month == 11 && $date < 23)) {
            return 'Scorpio';
        }

        if (($month == 11 && $date >= 23) || ($month == 12 && $date < 22)) {
            return 'Sagittarius';
        }

        if (($month == 12 && $date >= 22) || ($month == 1 && $date < 20)) {
            return 'Capricorn';
        }

        return 'N/A';
    }

    /**
     * Get the province from NIK
     *
     * @return mixed|null
     */
    public function getProvince()
    {
        return $this->location['provinsi'][substr($this->number, 0, 2)] ?? null;
    }

    /**
     * Get the city from NIK
     *
     * @return mixed|null
     */
    public function getCity()
    {
        return $this->location['kabkot'][substr($this->number, 0, 4)] ?? null;
    }

    /**
     * Get the sub-district from NIK
     *
     * @return string|null
     */
    public function getSubDistrict(): ?string
    {
        $result = $this->location['kecamatan'][substr($this->number, 0, 6)];

        return trim(explode('--', $result)[0]) ?? null;
    }

    /**
     * Get postal code
     *
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        $result = $this->location['kecamatan'][substr($this->number, 0, 6)];

        return trim(explode('--', $result)[1]) ?? null;
    }
}
