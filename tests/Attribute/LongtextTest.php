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
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_longtext/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeLongtextBundle\Test\Attribute;

use Doctrine\DBAL\Connection;
use MetaModels\AttributeLongtextBundle\Attribute\Longtext;
use MetaModels\Helper\TableManipulator;
use PHPUnit\Framework\TestCase;
use MetaModels\IMetaModel;

/**
 * Unit tests to test class longtext.
 *
 * @covers \MetaModels\AttributeLongtextBundle\Attribute\Longtext
 */
class LongtextTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $language         The language.
     * @param string $fallbackLanguage The fallback language.
     *
     * @return \MetaModels\IMetaModel
     */
    protected function mockMetaModel($language, $fallbackLanguage)
    {
        $metaModel = $this->getMockBuilder(IMetaModel::class)->getMock();

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('mm_unittest'));

        $metaModel
            ->expects($this->any())
            ->method('getActiveLanguage')
            ->will($this->returnValue($language));

        $metaModel
            ->expects($this->any())
            ->method('getFallbackLanguage')
            ->will($this->returnValue($fallbackLanguage));

        return $metaModel;
    }

    /**
     * Mock the database connection.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Connection
     */
    private function mockConnection()
    {
        return $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Mock the table manipulator.
     *
     * @param Connection $connection The database connection mock.
     *
     * @return TableManipulator|\PHPUnit_Framework_MockObject_MockObject
     */
    private function mockTableManipulator(Connection $connection)
    {
        return $this->getMockBuilder(TableManipulator::class)
            ->setConstructorArgs([$connection, []])
            ->getMock();
    }

    /**
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $connection  = $this->mockConnection();
        $manipulator = $this->mockTableManipulator($connection);

        $text = new Longtext($this->mockMetaModel('en', 'en'), [], $connection, $manipulator);
        $this->assertInstanceOf(Longtext::class, $text);
    }


    /**
     * Test that the attribute does not accept config keys not specified via getAttributeSettingNames().
     *
     * @return void
     */
    public function testGetFieldDefinition()
    {
        $attributes = [
            'id'             => 1,
            'pid'            => 1,
            'tstamp'         => 0,
            'name'           => [
                'en'         => 'name English',
                'de'         => 'name German',
            ],
            'description'    => [
                'en'         => 'description English',
                'de'         => 'description German',
            ],
            'type'           => 'base',
            'colname'        => 'baseattribute',
            'isvariant'      => 1,
            // Settings originating from tl_metamodel_dcasetting.
            'tl_class'       => 'custom_class',
            'readonly'       => 1,
            'allowHtml'      => null,
            'mandatory'      => null,
            'preserveTags'   => null,
            'decodeEntities' => null,
            'rows'           => null,
            'cols'           => null,
        ];

        $serialized = [];
        foreach ($attributes as $key => $value) {
            if (\is_array($value)) {
                $serialized[$key] = \serialize($value);
            } else {
                $serialized[$key] = $value;
            }
        }

        $connection      = $this->mockConnection();
        $manipulator     = $this->mockTableManipulator($connection);
        $attribute       = new Longtext($this->mockMetaModel('en', 'en'), $serialized, $connection, $manipulator);
        $fieldDefinition = $attribute->getFieldDefinition(
            [
                'tl_class' => 'some_widget_class',
                'readonly' => true,
                'rte'      => 'tinyMCE',
            ]
        );

        $this->assertTrue(\array_key_exists('filter', $fieldDefinition));
        $this->assertTrue(\array_key_exists('search', $fieldDefinition));
        $this->assertEquals('textarea', $fieldDefinition['inputType']);
        $this->assertEquals('some_widget_class', $fieldDefinition['eval']['tl_class']);
        $this->assertEquals(true, $fieldDefinition['eval']['readonly']);
        $this->assertEquals('tinyMCE', $fieldDefinition['eval']['rte']);
    }
}
