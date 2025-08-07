<?php

declare(strict_types = 1);

namespace Turahe\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Turahe\Validator\KK;

/**
 * @small
 */
class KKTest extends TestCase
{
    private const VALID_KK = '3273012501990001';

    private const INVALID_KK = '1234567890123456';

    /**
     * @test
     */
    public function setMethodWithString(): void
    {
        $kk = KK::set(self::VALID_KK);

        $this->assertInstanceOf(KK::class, $kk);
        $this->assertSame(self::VALID_KK, $kk->number);
    }

    /**
     * @test
     */
    public function setMethodWithInteger(): void
    {
        $kk = KK::set(3273012501990001);

        $this->assertInstanceOf(KK::class, $kk);
        $this->assertSame('3273012501990001', $kk->number);
    }

    /**
     * @test
     */
    public function validateWithValidKK(): void
    {
        $kk = KK::set(self::VALID_KK);

        $this->assertTrue($kk->validate());
    }

    /**
     * @test
     */
    public function validateWithInvalidKK(): void
    {
        $kk = KK::set(self::INVALID_KK);

        $this->assertFalse($kk->validate());
    }

    /**
     * @test
     */
    public function parseWithValidKK(): void
    {
        $kk = KK::set(self::VALID_KK);
        $result = $kk->parse();

        $this->assertIsObject($result);
        $this->assertTrue($result->valid);
        $this->assertSame(self::VALID_KK, $result->number);
        $this->assertIsObject($result->address);
        $this->assertIsString($result->postalCode);
    }

    /**
     * @test
     */
    public function parseWithInvalidKK(): void
    {
        $kk = KK::set(self::INVALID_KK);
        $result = $kk->parse();

        $this->assertIsObject($result);
        $this->assertFalse($result->valid);
        $this->assertFalse(property_exists($result, 'number'));
    }

    /**
     * @test
     */
    public function getProvince(): void
    {
        $kk = KK::set(self::VALID_KK);
        $province = $kk->getProvince();

        $this->assertIsString($province);
        $this->assertNotEmpty($province);
    }

    /**
     * @test
     */
    public function getCity(): void
    {
        $kk = KK::set(self::VALID_KK);
        $city = $kk->getCity();

        $this->assertIsString($city);
        $this->assertNotEmpty($city);
    }

    /**
     * @test
     */
    public function getSubDistrict(): void
    {
        $kk = KK::set(self::VALID_KK);
        $subDistrict = $kk->getSubDistrict();

        $this->assertIsString($subDistrict);
        $this->assertNotEmpty($subDistrict);
    }

    /**
     * @test
     */
    public function getPostalCode(): void
    {
        $kk = KK::set(self::VALID_KK);
        $postalCode = $kk->getPostalCode();

        $this->assertIsString($postalCode);
        $this->assertNotEmpty($postalCode);
    }

    /**
     * @test
     */
    public function constructorWithCustomWilayahPath(): void
    {
        $customPath = __DIR__ . '/../src/assets/wilayah.json';
        $kk = new KK(self::VALID_KK, $customPath);

        $this->assertInstanceOf(KK::class, $kk);
        $this->assertSame(self::VALID_KK, $kk->number);
    }

    /**
     * @test
     */
    public function readonlyProperties(): void
    {
        $kk = KK::set(self::VALID_KK);

        // Test that properties are readonly (should not be able to modify them)
        $this->expectException(\Error::class);
        $kk->number = '1234567890123456';
    }

    /**
     * @test
     */
    public function typeSafety(): void
    {
        // Test that the class properly handles type safety
        $kk = KK::set(self::VALID_KK);

        $this->assertIsString($kk->number);
        $this->assertIsArray($kk->location);
    }

    /**
     * @test
     */
    public function addressObjectStructure(): void
    {
        $kk = KK::set(self::VALID_KK);
        $result = $kk->parse();

        $this->assertIsObject($result->address);
        $this->assertTrue(property_exists($result->address, 'province'));
        $this->assertTrue(property_exists($result->address, 'city'));
        $this->assertTrue(property_exists($result->address, 'subDistrict'));

        $this->assertIsString($result->address->province);
        $this->assertIsString($result->address->city);
        $this->assertIsString($result->address->subDistrict);
    }

    /**
     * @test
     */
    public function invalidKKWithShortLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KK::set('123456789012345');
    }

    /**
     * @test
     */
    public function invalidKKWithLongLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KK::set('12345678901234567');
    }

    /**
     * @test
     */
    public function invalidKKWithNonNumeric(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        KK::set('123456789012345a');
    }

    /**
     * @test
     */
    public function getValidationErrors(): void
    {
        $kk = KK::set(self::INVALID_KK);
        $errors = $kk->getValidationErrors();

        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }

    /**
     * @test
     */
    public function toArray(): void
    {
        $kk = KK::set(self::VALID_KK);
        $array = $kk->toArray();

        $this->assertIsArray($array);
        $this->assertTrue($array['valid']);
        $this->assertSame(self::VALID_KK, $array['number']);
    }

    /**
     * @test
     */
    public function getFormattedNumber(): void
    {
        $kk = KK::set(self::VALID_KK);
        $formatted = $kk->getFormattedNumber();

        $this->assertIsString($formatted);
        $this->assertSame('3273-0125-0199-0001', $formatted);
    }

    /**
     * @test
     */
    public function getRawNumber(): void
    {
        $kk = KK::set(self::VALID_KK);
        $raw = $kk->getRawNumber();

        $this->assertIsString($raw);
        $this->assertSame(self::VALID_KK, $raw);
    }
}
