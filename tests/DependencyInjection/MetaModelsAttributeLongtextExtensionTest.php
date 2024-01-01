<?php

/**
 * This file is part of MetaModels/attribute_longtext.
 *
 * (c) 2012-2024 The MetaModels team.
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
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_longtext/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeLongtextBundle\Test\DependencyInjection;

use MetaModels\AttributeLongtextBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeLongtextBundle\DependencyInjection\MetaModelsAttributeLongtextExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test case test the extension.
 *
 * @covers \MetaModels\AttributeLongtextBundle\DependencyInjection\AttributeLongtextExtension
 */
class MetaModelsAttributeLongtextExtensionTest extends TestCase
{
    /**
     * Test that extension can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $extension = new MetaModelsAttributeLongtextExtension();

        $this->assertInstanceOf(MetaModelsAttributeLongtextExtension::class, $extension);
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    /**
     * Test that the services are loaded.
     *
     * @return void
     */
    public function testFactoryIsRegistered()
    {
        $container = new ContainerBuilder();

        $extension = new MetaModelsAttributeLongtextExtension();
        $extension->load([], $container);

        self::assertTrue($container->hasDefinition('metamodels.attribute_longtext.factory'));
        $definition = $container->getDefinition('metamodels.attribute_longtext.factory');
        self::assertCount(1, $definition->getTag('metamodels.attribute_factory'));
    }
}
