<?php

/***************************************************************************
 Extension Name  : Magento Import Export Categories Extension
 Extension URL   : http://www.magebees.com/magento-import-export-categories-extension.html
 Copyright    : Copyright (c) 2015 MageBees, http://www.magebees.com
 Support Email   : support@magebees.com 
 ***************************************************************************/ 

class CapacityWebSolutions_Importcategory_Model_Convert_Adapter_Importcategory
extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{

    public function load() {
     
    }

    public function save() {
     
    }
	
	public function saveRow(array $importData)
    {
		$store = Mage::app()->getStore($importData['store_code']);
		$store_id = $store->getStoreId();
		$rootId = $store->getRootCategoryId();
		$rootCategory = Mage::getModel('catalog/category')->load($rootId);
		$root_category =  trim($rootCategory->getData('name'));
		if(isset($importData['all_store_category_name'])) {
			$all_store_category_name = $importData['all_store_category_name'];
		}
		$categories = $importData['category_name'];
		if(isset($importData['is_active'])) {
			$is_active = $importData['is_active'];
		}
		if(isset($importData['url_key'])) {
			$url_key = $importData['url_key'];
		}
		if(isset($importData['description'])) {
			$description = $importData['description'];
		}
		if(isset($importData['page_title'])) {	
			$page_title = $importData['page_title'];
		}
		if(isset($importData['meta_keywords'])) {
			$meta_keywords = $importData['meta_keywords'];
		}
		if(isset($importData['meta_description'])) {	
			$meta_description = $importData['meta_description'];
		}
		if(isset($importData['include_in_navigation_menu'])) {	
			$include_in_navigation_menu = $importData['include_in_navigation_menu'];
		}
		if(isset($importData['display_mode'])) {
			$display_mode = $importData['display_mode'];
		}
		if(isset($importData['cms_block'])) {
			$cms_block = $importData['cms_block'];
		}
		if(isset($importData['is_anchor'])) {
			$is_anchor = $importData['is_anchor'];
		}
		if(isset($importData['default_product_listing'])) {
			$default_product_listing = $importData['default_product_listing'];
		}
		if(isset($importData['thumbnail_image'])) {
			$thumbnail_image = $importData['thumbnail_image'];
		}	
		if(isset($importData['image'])) {
			$image = $importData['image'];
		}	
		if(isset($importData['price_step'])) {
			$price_step = $importData['price_step'];
		}	
		if(isset($importData['use_parent_category'])) {
			$use_parent_category = $importData['use_parent_category'];
			if($use_parent_category == 0)
			{
				if(isset($importData['apply_to_products'])) {
					$apply_to_products = $importData['apply_to_products'];
				}
				if(isset($importData['custom_design'])) {
					$custom_design = $importData['custom_design'];
				}
				if(isset($importData['active_from'])) {
					$active_from = $importData['active_from'];
				}
				if(isset($importData['active_to'])) {
					$active_to = $importData['active_to'];
				}
				if(isset($importData['page_layout'])) {
					$page_layout = $importData['page_layout'];
				}
				if(isset($importData['custom_layout'])) {
					$custom_layout = $importData['custom_layout'];
				}	
			}
		}
		if(isset($importData['sku'])) {
			$product_sku = $importData['sku'];
		}
	    $_categoryCache = array();		
		if($categories=="")
		   return array();
        $rootPath = '1/'.$rootId;
        if (empty($this->_categoryCache[1])) {
            $collection = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name');
            $collection->getSelect()->where("path like '".$rootPath."/%'");
            foreach ($collection as $cat) {
                $pathArr = explode('/', $cat->getPath());
                $namePath = '';
                for ($i=2, $l=sizeof($pathArr); $i<$l; $i++) {
                    $name = $collection->getItemById($pathArr[$i])->getName();
                    $namePath .= (empty($namePath) ? '' : '/').trim($name);
                }
                $cat->setNamePath($namePath);
            }
            
            $cache = array();
            foreach ($collection as $cat) {
                $cache[strtolower($cat->getNamePath())] = $cat;
                $cat->unsNamePath();
            }
            $this->_categoryCache[1] = $cache;
        }
		
        $cache =& $this->_categoryCache[1];
		$catIds = array();
		$path = $rootPath;
        $namePath = '';
		
		$categories_update = Mage::getModel('catalog/category')
                    ->getCollection();
					
		foreach($categories_update as $cat_data){
		
			$cat_path = $cat_data->getData('path');
			$cat_path_data = explode('/',$cat_path);
	
			$cat_path_name_first = array();
			for($i=0;$i<count($cat_path_data);$i++)	{
				$cat_path_id = Mage::getModel('catalog/category')->load($cat_path_data[$i]);
				$cat_path_name  = $cat_path_id->getData('name');
				$cat_path_name_first[] = $cat_path_name;
			}
										
			$cat_path_name_final = '';  
			for($i=1;$i<count($cat_path_name_first); $i++) {
				if($i < count($cat_path_name_first)-1){
					$cat_path_name_final .= $cat_path_name_first[$i]."/";
				}else{
					$cat_path_name_final .= $cat_path_name_first[$i];
				}
			} 

			if(isset($all_store_category_name)) {
				$final_cat = $root_category."/".$all_store_category_name; 
			}else{
				$final_cat = $root_category."/".$categories; 
			}
					
			if($cat_path_name_final == $final_cat){
				$category_id = end($cat_path_data); 
				break;
			}else{
				$category_id = "";
			}
		} 
				 
			if($category_id != ""){
				$category_name = explode('/', $categories);
				$category_name = trim(end($category_name));
		  		$cat = Mage::getModel('catalog/category')->setStoreId($store_id)->load($category_id);
				$subcategory['name'] =  trim($category_name);
				if(isset($is_active)){
					$subcategory['is_active'] =  $is_active;
				}
				if(isset($url_key))	{
					$subcategory['url_key'] =  $url_key;
				}
				if(isset($description)) {
					$subcategory['description'] =  $description;
				}
				if(isset($page_title)) {
					$subcategory['meta_title'] =  $page_title;
				}
				if(isset($meta_keywords)){
					$subcategory['meta_keywords'] =  $meta_keywords;
				}
				if(isset($meta_description)){
					$subcategory['meta_description'] =  $meta_description;
				}
				if(isset($include_in_navigation_menu)){
					$subcategory['include_in_menu'] =  $include_in_navigation_menu;
				}
				if(isset($display_mode)){
					$subcategory['display_mode'] =  $display_mode;
				}
				if(isset($cms_block)) {
					$subcategory['landing_page'] =  $cms_block;
				}
				if(isset($default_product_listing)){
					$subcategory['default_sort_by'] =  $default_product_listing;
				}
				if(isset($is_anchor)){
					$subcategory['is_anchor'] =  $is_anchor;
				}										
				if(isset($thumbnail_image)){	
					$this->getCategoryImageFile($thumbnail_image);	
					$subcategory['thumbnail'] =  $thumbnail_image;
				}
				if(isset($image)){	
					$this->getCategoryImageFile($image);	
					$subcategory['image'] =  $image;
				}
				if(isset($price_step)){
					$subcategory['filter_price_range'] =  $price_step;
				}
				if(isset($use_parent_category)){
					$subcategory['custom_use_parent_settings']=	$use_parent_category;
				}
				if(isset($apply_to_products)){
					$subcategory['custom_apply_to_products'] =  $apply_to_products;
				}
				if(isset($custom_design)){
					$subcategory['custom_design'] =  $custom_design;
				}
				if(isset($active_from)){
					$subcategory['custom_design_from'] =  $active_from;
				}
				if(isset($active_to)){
					$subcategory['custom_design_to'] =  $active_to;
				}
				if(isset($page_layout)){
					$subcategory['page_layout'] =  $page_layout;
				}
				if(isset($custom_layout)){
					$subcategory['custom_layout_update'] =  $custom_layout;
				}
				$cat->addData($subcategory);	
				$cat->save(); 
				if(isset($product_sku))
				if($product_sku != "") {
					$product_data = explode("|",$product_sku);
					for($e=0; $e<count($product_data); $e++){  
						$product_id = Mage::getModel("catalog/product")->getIdBySku(trim($product_data[$e]));
						if($product_id != "") {
							$product = Mage::getModel('catalog/product')->load($product_id);
							$newCategories = $origCats = $product->getCategoryIds();
							
							if(!in_array($category_id, $origCats)) {
								$newCategories = array_merge($origCats, array($category_id));
								$product->setCategoryIds($newCategories)->save();
							}
						}
					}
				}
			} 
	
		if($category_id == "") {
			
			foreach (explode('/', $categories) as $catName) {
				$namePath .= (empty($namePath) ? '' : '/').strtolower($catName);
				if (empty($cache[$namePath])) {
					$cat = Mage::getModel('catalog/category')
							->setStoreId($store_id)
							->setName(trim($catName))
							->setPath($path);
							
							if(isset($is_active)){
								$subcategory['is_active'] =  $is_active;
							}
							if(isset($url_key)) {
								$subcategory['url_key'] =  $url_key;
							}	
							if(isset($description)) {
								$subcategory['description'] =  $description;
								}
							if(isset($page_title)) {
								$subcategory['meta_title'] =  $page_title;
							}
							if(isset($meta_keywords)){
								$subcategory['meta_keywords'] =  $meta_keywords;
							}
							if(isset($meta_description)){
								$subcategory['meta_description'] =  $meta_description;
							}
							
							if(isset($include_in_navigation_menu)){
								$subcategory['include_in_menu'] =  $include_in_navigation_menu;
							}
							if(isset($display_mode)){
								$subcategory['display_mode'] =  $display_mode;
							}
							if(isset($cms_block)) {
								$subcategory['landing_page'] =  $cms_block;
							}
							if(isset($default_product_listing)){
								$subcategory['default_sort_by'] =  $default_product_listing;
							}
							if(isset($is_anchor)){
								$subcategory['is_anchor'] =  $is_anchor;
							}										
							if(isset($thumbnail_image)){	
								$this->getCategoryImageFile($thumbnail_image);	
								$subcategory['thumbnail'] =  $thumbnail_image;
							}
							if(isset($image)){	
								$this->getCategoryImageFile($image);	
								$subcategory['image'] =  $image;
							}
							if(isset($price_step)){
								$subcategory['filter_price_range'] =  $price_step;
							}
							if(isset($use_parent_category)){
								$subcategory['custom_use_parent_settings']=	$use_parent_category;
							}
							if(isset($apply_to_products)){
								$subcategory['custom_apply_to_products'] =  $apply_to_products;
							}
							if(isset($custom_design)){
								$subcategory['custom_design'] =  $custom_design;
							}
							if(isset($active_from)){
								$subcategory['custom_design_from'] =  $active_from;
							}
							if(isset($active_to)){
								$subcategory['custom_design_to'] =  $active_to;
							}
							if(isset($page_layout)){
								$subcategory['page_layout'] =  $page_layout;
							}
							if(isset($custom_layout)){
								$subcategory['custom_layout_update'] =  $custom_layout;
							}
							$cat->addData($subcategory);	
							$cat->save(); 
							
							$cache[$namePath] = $cat;			
							$cate_id = $cache[$namePath]->getId();

							if(isset($product_sku))
							if($product_sku != "") {
								$product_data = explode("|",$product_sku);
								for($e=0; $e<count($product_data); $e++){  
									$product_id = Mage::getModel("catalog/product")->getIdBySku(trim($product_data[$e]));
									if($product_id != "") {
										$product = Mage::getModel('catalog/product')->load($product_id);
										$newCategories = $origCats = $product->getCategoryIds();
										if(!in_array($cate_id, $origCats)) {
											$newCategories = array_merge($origCats, array($cate_id));
											$product->setCategoryIds($newCategories)->save();
										}
									}
								}
							}
				}
								  
				$catId = $cache[$namePath]->getId();
				$path .= '/'.$catId;
			}
		}	
	}
	
	function getCategoryImageFile($filename)
	{    
		$cat_dir = "media/catalog/category";
		if(!is_dir($cat_dir)){
			mkdir($cat_dir, 0755, true);
		}
		$filePath = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS . $filename;
		$fileUrl = Mage::getBaseDir('media') . DS . 'import' . DS . 'category' . DS . $filename;
			
		try{
			if(rename($fileUrl, $filePath)){
			  //anything;
			} else {
			  throw new Exception('Can not rename file'.$fileUrl);
			}
		}catch (Exception $e) {
		   //do something
		}
		
		return  $filePath;
	}
}	