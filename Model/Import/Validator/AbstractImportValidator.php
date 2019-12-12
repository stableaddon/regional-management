<?php

namespace Stableaddon\RegionalManagement\Model\Import\Validator;

use Magento\Framework\Validator\AbstractValidator;

/**
 * Class AbstractImportValidator
 *
 * @package Stableaddon\RegionalManagement\Model\Import\Validator
 */
abstract class AbstractImportValidator extends AbstractValidator implements RowValidatorInterface
{
    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product
     */
    protected $context;

    /**
     * @param \Magento\CatalogImportExport\Model\Import\Product $context
     * @return $this
     */
    public function init($context)
    {
        $this->context = $context;

        return $this;
    }
}