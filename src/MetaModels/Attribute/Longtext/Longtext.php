<?php

/**
 * This file is part of MetaModels/attribute_longtext.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage AttributeLongtext
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Christopher Boelter <c.boelter@cogizz.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_longtext/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\Longtext;

use MetaModels\Attribute\BaseSimple;
use MetaModels\Helper\TableManipulation;

/**
 * This is the MetaModelAttribute class for handling long text fields.
 */
class Longtext extends BaseSimple
{
    

    /**
     * {@inheritDoc}
     */
    public function getSQLDataType()
    {
        return 'text NULL';
    }

    /**
     * @{inheritDoc}
     *
     * add a fulltext-index to column after creation
     */
    public function createColumn()
    {
        parent::createColumn();

        TableManipulation::addIndex(
                $this->getMetaModel()->getTableName(),
                'FULLTEXT',
                $this->getColName()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributeSettingNames()
    {
        return array_merge(parent::getAttributeSettingNames(), array(
            'allowHtml',
            'cols',
            'decodeEntities',
            'mandatory',
            'preserveTags',
            'rte',
            'rows',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldDefinition($arrOverrides = array())
    {
        $arrFieldDef              = parent::getFieldDefinition($arrOverrides);
        $arrFieldDef['inputType'] = 'textarea';

        return $arrFieldDef;
    }
}
