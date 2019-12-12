<?php

namespace Stableaddon\RegionalManagement\Model\Import\Validator;

/**
 * Interface RowValidatorInterface
 *
 * @package Stableaddon\RegionalManagement\Model\Import\Validator
 */
interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
    /**
     * @var string
     */
    const ERROR_TITLE_IS_EMPTY= 'InvalidValueTITLE';

    /**
     * @var string
     */
    const ERROR_NAME_IS_EMPTY = 'EmptyName';

    /**
     * Initialize validator
     *
     * @return $this
     */
    public function init($context);
}