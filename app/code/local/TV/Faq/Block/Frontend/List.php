<?php
/**
 * FAQ accordion for Magento

 */

/**
 * FAQ accordion for Magento
 *
 * Website: www.abc.com 
 * Email: honeyvishnoi@gmail.com
 */
class TV_Faq_Block_Frontend_List extends Mage_Core_Block_Template
{
	protected $_faqCollection;
	
	protected function _prepareLayout()
   {
        if ($head = $this->getLayout()->getBlock('head')){
            $head->setTitle($this->htmlEscape($this->__('Frequently Asked Questions')) . ' - ' . $head->getTitle());
        }
    }
	
	/**
	 * Returns collection of current FAQ entries
	 *
	 * @param int $pageSize
	 * @return TV_Faq_Model_Mysql_Faq_Collection collection of current FAQ entries
	 */
	public function getFaqCollection($pageSize = null)
	{
		if (!$this->_faqCollection || (intval($pageSize) > 0
			&& $this->_faqCollection->getSize() != intval($pageSize))
		){
			$this->_faqCollection = Mage :: getModel('tv_faq/faq')
				->getCollection()
				->addStoreFilter(Mage :: app()->getStore())
				->addIsActiveFilter();
			
			if (isset($pageSize) && intval($pageSize) && intval($pageSize) > 0){
				$this->_faqCollection->setPageSize(intval($pageSize));
			}
		}
		
		return $this->_faqCollection;
	}
	
	/**
	 * Returns all active categories
	 * 
	 * @return TV_Faq_Model_Mysql4_Category_Collection
	 */
	public function getCategoryCollection()
	{
	    $categories = $this->getData('category_collection');
	    if (is_null($categories)){
    	    $categories =  Mage::getResourceSingleton('tv_faq/category_collection')
    	       ->addStoreFilter(Mage::app()->getStore())
    	       ->addIsActiveFilter();
    	    $this->setData('category_collection', $categories);
	    }
	    return $categories;
	}
	
	/**
	 * Returns the item collection for the given category 
	 * 
	 * @param TV_Faq_Model_Category $category
	 * @return TV_Faq_Model_Mysql4_Faq_Collection
	 */
	public function getItemCollectionByCategory(TV_Faq_Model_Category $category)
	{
	    return $category->getItemCollection()->addIsActiveFilter()->addStoreFilter(Mage::app()->getStore());
	}
	
	/**
	 * Simple helper function to determine, whether there are FAQ entries or not.
	 *
	 * @return boolean True, if FAQ are given.
	 */
	public function hasFaq()
	{
		return $this->getFaqCollection()->getSize() > 0;
	}
	
	public function getIntro($faqItem)
	{
		$_intro = strip_tags($faqItem->getContent());
		$_intro = mb_substr($_intro, 0, mb_strpos($_intro, "\n"));
		
		$length = 100 - mb_strlen($faqItem->getQuestion());
		if ($length < 0){
			return '';
		}
		if (mb_strlen($_intro) > $length){
			$_intro = mb_substr($_intro, 0, $length);
			$_intro = mb_substr($_intro, 0, mb_strrpos($_intro, ' ')).'...';
		}
		
		return $_intro;
	}
	
	/**
	 * Returns 
	 *
	 * @return array
	 */
	public function getFaqJumplist()
	{
		if(is_null($this->_faqJumplist))
		{
			$this->_faqJumplist = Mage::helper('tv_faq/jumplist');
			$this->_faqJumplist->setFaqItems($this->getFaqCollection());
		}
		return $this->_faqJumplist;
	}
	
	/**
	 * Simple helper function to determine, whether we should display a jumplist or not.
	 *
	 * @return boolean True if the jumplist should be displayed
	 */
	public function hasFaqJumplist(){
		// TODO add configuration option to enable/disable jumplist
		return count($this->getFaqJumplist()) > 0;
	}
	
	public function encodeQuestionForUrl($question)
	{
		return 	urlencode(
					trim(
						str_replace(
							array(' ', 'ä',  'ö',  'ü',  'ß',  '.', '/', ';', ':', '=', '?', '__'),
							array('_', 'ae', 'oe', 'ue', 'ss', '_', '',  '',  '',  '',  '', '_'), 
							strtolower($question)
						), ' _'
					)
				);
	}
}
