<?php

namespace Stableaddon\RegionalManagement\Ui\Component\Listing\Column;

/**
 * Class SubDistrictNameActions
 *
 * @package Stableaddon\RegionalManagement\Ui\Component\Listing\Column
 */
class SubDistrictNameActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url path  to edit
     *
     * @var string
     */
    const URL_PATH_EDIT = 'regional_management/sub_district_name/edit';
    /**
     * Url path  to delete
     *
     * @var string
     */
    const URL_PATH_DELETE = 'regional_management/sub_district_name/delete';

    /**
     * URL builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * constructor
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['district_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->_urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'district_id' => $item['district_id'],
                                    'locale' => $item['locale']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->_urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'district_id' => $item['district_id'],
                                    'locale' => $item['locale']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.default_name }"'),
                                'message' => __('Are you sure you wan\'t to delete the Sub District "${ $.$data.default_name }" ?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
