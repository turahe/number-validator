<?php

declare(strict_types = 1);

namespace Turahe\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Turahe\Validator\NIK;

/**
 * @small
 */
class NIKTest extends TestCase
{
    private const VALID_NIK = '3273012501990001';

    private const INVALID_NIK = '1234567890123456';

    /**
     * @test
     */
    public function setMethodWithString(): void
    {
        $nik = NIK::set(self::VALID_NIK);

        $this->assertInstanceOf(NIK::class, $nik);
        $this->assertSame(self::VALID_NIK, $nik->number);
    }

    /**
     * @test
     */
    public function setMethodWithInteger(): void
    {
        $nik = NIK::set(3273012501990001);

        $this->assertInstanceOf(NIK::class, $nik);
        $this->assertSame('3273012501990001', $nik->number);
    }

    /**
     * @test
     */
    public function validateWithValidNIK(): void
    {
        $nik = NIK::set(self::VALID_NIK);

        $this->assertTrue($nik->validate());
    }

    /**
     * @test
     */
    public function validateWithInvalidNIK(): void
    {
        $nik = NIK::set(self::INVALID_NIK);

        $this->assertFalse($nik->validate());
    }

    /**
     * @test
     */
    public function parseWithValidNIK(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $result = $nik->parse();

        $this->assertIsObject($result);
        $this->assertTrue($result->valid);
        $this->assertSame(self::VALID_NIK, $result->number);
        $this->assertIsString($result->uniqueCode);
        $this->assertIsString($result->gender);
        $this->assertIsObject($result->born);
        $this->assertIsObject($result->age);
        $this->assertIsObject($result->nextBirthday);
        $this->assertIsString($result->zodiac);
        $this->assertIsObject($result->address);
        $this->assertIsString($result->postalCode);
    }

    /**
     * @test
     */
    public function parseWithInvalidNIK(): void
    {
        $nik = NIK::set(self::INVALID_NIK);
        $result = $nik->parse();

        $this->assertIsObject($result);
        $this->assertFalse($result->valid);
        $this->assertFalse(property_exists($result, 'number'));
    }

    /**
     * @test
     */
    public function getGenderForMale(): void
    {
        // NIK with date 25 (male)
        $nik = NIK::set('3273012501990001');

        $this->assertSame('LAKI-LAKI', $nik->getGender());
    }

    /**
     * @test
     */
    public function getGenderForFemale(): void
    {
        // NIK with date 65 (female: 65-40=25)
        $nik = NIK::set('3273016501990001');

        $this->assertSame('PEREMPUAN', $nik->getGender());
    }

    /**
     * @test
     */
    public function getUniqueCode(): void
    {
        $nik = NIK::set(self::VALID_NIK);

        // Using reflection to test private method
        $reflection = new \ReflectionClass($nik);
        $method = $reflection->getMethod('getUniqueCode');
        $method->setAccessible(true);

        $uniqueCode = $method->invoke($nik);

        $this->assertSame('0001', $uniqueCode);
    }

    /**
     * @test
     */
    public function getBornDate(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $bornDate = $nik->getBornDate();

        $this->assertIsObject($bornDate);
        $this->assertTrue(property_exists($bornDate, 'date'));
        $this->assertTrue(property_exists($bornDate, 'month'));
        $this->assertTrue(property_exists($bornDate, 'year'));
        $this->assertTrue(property_exists($bornDate, 'full'));

        // Check specific values for the test NIK
        $this->assertSame('25', $bornDate->date);
        $this->assertSame('01', $bornDate->month);
        $this->assertSame('1999', $bornDate->year);
        $this->assertSame('25-01-1999', $bornDate->full);
    }

    /**
     * @test
     */
    public function getAge(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $age = $nik->getAge();

        $this->assertIsObject($age);
        $this->assertTrue(property_exists($age, 'year'));
        $this->assertTrue(property_exists($age, 'month'));
        $this->assertTrue(property_exists($age, 'day'));

        // Age should be reasonable (born in 1999, so should be around 25 years in 2024)
        $this->assertGreaterThan(20, $age->year);
        $this->assertLessThan(30, $age->year);
    }

    /**
     * @test
     */
    public function getNextBirthday(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $nextBirthday = $nik->getNextBirthday();

        $this->assertIsObject($nextBirthday);
        $this->assertTrue(property_exists($nextBirthday, 'month'));
        $this->assertTrue(property_exists($nextBirthday, 'day'));

        // Values should be non-negative
        $this->assertGreaterThanOrEqual(0, $nextBirthday->month);
        $this->assertGreaterThanOrEqual(0, $nextBirthday->day);
    }

    /**
     * @test
     */
    public function getZodiac(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $zodiac = $nik->getZodiac();

        $this->assertIsString($zodiac);
        $this->assertNotEmpty($zodiac);

        // For January 25th, should be Aquarius
        $this->assertSame('Aquarius', $zodiac);
    }

    /**
     * @test
     */
    public function getProvince(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $province = $nik->getProvince();

        $this->assertIsString($province);
        $this->assertNotEmpty($province);
    }

    /**
     * @test
     */
    public function getCity(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $city = $nik->getCity();

        $this->assertIsString($city);
        $this->assertNotEmpty($city);
    }

    /**
     * @test
     */
    public function getSubDistrict(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $subDistrict = $nik->getSubDistrict();

        $this->assertIsString($subDistrict);
        $this->assertNotEmpty($subDistrict);
    }

    /**
     * @test
     */
    public function getPostalCode(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $postalCode = $nik->getPostalCode();

        $this->assertIsString($postalCode);
        $this->assertNotEmpty($postalCode);
    }

    /**
     * @test
     */
    public function constructorWithCustomWilayahPath(): void
    {
        $customPath = __DIR__ . '/../src/assets/wilayah.json';
        $nik = new NIK(self::VALID_NIK, $customPath);

        $this->assertInstanceOf(NIK::class, $nik);
        $this->assertSame(self::VALID_NIK, $nik->number);
    }

    /**
     * @test
     */
    public function readonlyProperties(): void
    {
        $nik = NIK::set(self::VALID_NIK);

        // Test that properties are readonly (should not be able to modify them)
        $this->expectException(\Error::class);
        $nik->number = '1234567890123456';
    }

    /**
     * @test
     */
    public function typeSafety(): void
    {
        // Test that the class properly handles type safety
        $nik = NIK::set(self::VALID_NIK);

        $this->assertIsString($nik->number);
        $this->assertIsArray($nik->location);
    }

    /**
     * @test
     */
    public function getValidationErrors(): void
    {
        $nik = NIK::set(self::INVALID_NIK);
        $errors = $nik->getValidationErrors();

        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }

    /**
     * @test
     */
    public function toArray(): void
    {
        $nik = NIK::set(self::VALID_NIK);
        $array = $nik->toArray();

        $this->assertIsArray($array);
        $this->assertTrue($array['valid']);
        $this->assertSame(self::VALID_NIK, $array['number']);
        $this->assertIsString($array['uniqueCode']);
        $this->assertIsString($array['gender']);
        $this->assertIsArray($array['born']);
        $this->assertIsArray($array['age']);
        $this->assertIsArray($array['nextBirthday']);
        $this->assertIsString($array['zodiac']);
        $this->assertIsArray($array['address']);
        $this->assertIsString($array['postalCode']);
    }

    /**
     * @test
     */
    public function invalidNIKWithShortLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NIK::set('123456789012345');
    }

    /**
     * @test
     */
    public function invalidNIKWithLongLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NIK::set('12345678901234567');
    }

    /**
     * @test
     */
    public function invalidNIKWithNonNumeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        NIK::set('123456789012345a');
    }
}
