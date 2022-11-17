<?php

/**
 * This file is part of MetaModels/attribute_longtext.
 *
 * (c) 2012-2022 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_longtext
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_longtext/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeLongtextBundle\Test;

use MetaModels\AttributeLongtextBundle\Attribute\Longtext;
use MetaModels\AttributeLongtextBundle\Attribute\AttributeTypeFactory;
use PHPUnit\Framework\TestCase;

/**
 * This class tests if the deprecated autoloader works.
 *
 * @covers \MetaModels\AttributeLongtextBundle\DeprecatedAutoloader
 */
class DeprecatedAutoloaderTest extends TestCase
{
    /**
     * Longtextes of old classes to the new one.
     *
     * @var array
     */
    private static $classes = [
        'MetaModels\Attribute\Longtext\Longtext' => Longtext::class,
        'MetaModels\Attribute\Longtext\AttributeTypeFactory' => AttributeTypeFactory::class
    ];

    /**
     * Provide the longtext class map.
     *
     * @return array
     */
    public function provideLongtextClassMap()
    {
        $values = [];

        foreach (static::$classes as $longtext => $class) {
            $values[] = [$longtext, $class];
        }

        return $values;
    }

    /**
     * Test if the deprecated classes are longtexted to the new one.
     *
     * @param string $oldClass Old class name.
     * @param string $newClass New class name.
     *
     * @dataProvider provideLongtextClassMap
     */
    public function testDeprecatedClassesAreLongtexted($oldClass, $newClass)
    {
        $this->assertTrue(class_exists($oldClass), sprintf('Class longtext "%s" is not found.', $oldClass));

        $oldClassReflection = new \ReflectionClass($oldClass);
        $newClassReflection = new \ReflectionClass($newClass);

        $this->assertSame($newClassReflection->getFileName(), $oldClassReflection->getFileName());
    }
}
