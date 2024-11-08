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
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_longtext/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeLongtextBundle\FileUsage;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use InspiredMinds\ContaoFileUsage\Provider\FileUsageProviderInterface;
use InspiredMinds\ContaoFileUsage\Result\ResultInterface;
use InspiredMinds\ContaoFileUsage\Result\ResultsCollection;
use MetaModels\AttributeLongtextBundle\Attribute\Longtext;
use MetaModels\CoreBundle\FileUsage\MetaModelsSingleResult;
use MetaModels\IFactory;
use MetaModels\IMetaModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This class supports the Contao extension 'file usage'.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FileUsageProvider implements FileUsageProviderInterface
{
    // phpcs:disable
    private const INSERT_TAG_PATTERN = '~{{(file|picture|figure)::([a-f0-9]{8}-[a-f0-9]{4}-1[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12})(([|?])[^}]+)?}}~';
    // phpcs:enable

    private string $refererId = '';

    public function __construct(
        private readonly IFactory $factory,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RequestStack $requestStack,
        private readonly ContaoCsrfTokenManager $csrfTokenManager,
        private readonly string $csrfTokenName,
    ) {
    }

    public function find(): ResultsCollection
    {
        $this->refererId = $this->requestStack->getCurrentRequest()?->attributes->get('_contao_referer_id') ?? '';

        $allTables = $this->factory->collectNames();

        $collection = new ResultsCollection();
        foreach ($allTables as $table) {
            $collection->mergeCollection($this->processTable($table));
        }

        return $collection;
    }

    private function processTable(string $table): ResultsCollection
    {
        $collection = new ResultsCollection();
        $metaModel  = $this->factory->getMetaModel($table);
        assert($metaModel instanceof IMetaModel);

        $attributeColumns = [];
        foreach ($metaModel->getAttributes() as $attribute) {
            if (!$attribute instanceof Longtext) {
                continue;
            }
            $attributeColumns[] = $attribute->getColName();
        }

        $items = $metaModel->findByFilter($metaModel->getEmptyFilter(), arrAttrOnly: $attributeColumns);
        foreach ($items as $item) {
            foreach ($attributeColumns as $attributeColumn) {
                if (empty($text = $item->get($attributeColumn))) {
                    continue;
                }
                \preg_match_all(self::INSERT_TAG_PATTERN, $text, $matches);
                foreach ($matches[2] ?? [] as $uuid) {
                    $collection->addResult(
                        $uuid,
                        $this->createFileResult($table, $attributeColumn, $item->get('id'))
                    );
                }
            }
        }

        return $collection;
    }

    private function createFileResult(
        string $tableName,
        string $attributeName,
        string $itemId,
    ): ResultInterface {
        return new MetaModelsSingleResult(
            $tableName,
            $attributeName,
            $itemId,
            $this->urlGenerator->generate(
                'metamodels.metamodel',
                [
                    'tableName' => $tableName,
                    'act'       => 'edit',
                    'id'        => ModelId::fromValues($tableName, $itemId)->getSerialized(),
                    'ref'       => $this->refererId,
                    'rt'        => $this->csrfTokenManager->getToken($this->csrfTokenName)->getValue(),
                ]
            )
        );
    }
}
