<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Xigen\CleanCategoryUrl\Rewrite\Magento\Catalog\Model\Layer\Filter;

use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\CategoryFactory;

class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{
    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Html pager block
     *
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $_htmlPagerBlock;

    /**
     * Undocumented variable
     * @var [type]
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * Undocumented function
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     * @param \Magento\Catalog\Helper\Category $helper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        Category $helper,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_url = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->helper = $helper;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($url, $htmlPagerBlock, $data);
    }

    /**
     * Get filter item url
     * @return string
     */
    public function getUrl()
    {
        if ($this->getFilter()->getRequestVar() == "cat") {
            
            $category = $this->categoryFactory
                ->create()
                ->load($this->getValue());
            
            if ($category && $category->getId()) {
           
                $url = $this->helper->getCategoryUrl($category);
                $request = $this->_url->getUrl('*/*/*', ['_current'=> true, '_use_rewrite' => true]);

                if (strpos($request, '?') !== false) {
                    $query_string = substr($request, strpos($request, '?'));
                } else {
                    $query_string = '';
                }
                if (!empty($query_string)) {
                    $url .= $query_string;
                }

                return $url;
            }
        }

        $query = [
            $this->getFilter()->getRequestVar() => $this->getValue(),
            // exclude current page from urls
            $this->_htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }
}
