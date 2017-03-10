<?php

/***************************************************************************
 Extension Name  : Magento Import Export Categories Extension
 Extension URL   : http://www.magebees.com/magento-import-export-categories-extension.html
 Copyright    : Copyright (c) 2015 MageBees, http://www.magebees.com
 Support Email   : support@magebees.com 
 ***************************************************************************/ 

ini_set("memory_limit","1024M");
class CapacityWebSolutions_Importcategory_Model_Convert_Parser_Exportcategory extends Mage_Catalog_Model_Convert_Parser_Product
{
    public function unparse()
    {
		$include_sku = Mage::getStoreConfig('importcategory/general/sku'); 
		$categories = Mage::getModel('catalog/category')
                    ->getCollection();
		
		foreach($categories as $cat_data) {
			
			$cat_path = $cat_data->getData('path');
			$cat_path_data = explode('/',$cat_path);
			$cat_path_name_first = array();
			for($i=2;$i<count($cat_path_data);$i++)	{
				$cat_path_id = Mage::getModel('catalog/category')->load($cat_path_data[$i]);
				$cat_path_name  = $cat_path_id->getData('name');
				$cat_path_name_first[] = $cat_path_name;
			}
		   	$cat_path_name_final = '';  
			for($i=0;$i<count($cat_path_name_first); $i++) {
				if($i < count($cat_path_name_first)-1){
					$cat_path_name_final .= trim($cat_path_name_first[$i])."/";
				}else{
					$cat_path_name_final .= trim($cat_path_name_first[$i]);
				}
			}  
		 		 
			if($cat_path_data[0] != "" && $cat_data->getData('level') > 1  )
			{
				if($this->getStore()->getId() == 0) {	
					$store_view_id  = array(); 
					$store_view_data  = array(); 
					$store_view_root  = array(); 
					foreach (Mage::app()->getWebsites() as $website) {
						foreach ($website->getGroups() as $group) {
							$stores = $group->getStores();
							foreach ($stores as $store) {
								$store_view_id[] = $store->getId();
								$store_view_data[] = $store->getCode();
								$store_view_root[] = $store->getRootCategoryId();
							}
						}
					}
				}else{
					$store_view_id  = array(); 
					$store_view_data  = array(); 
					$store_view_root  = array();  
					$store_view_id[] = $this->getStore()->getId();
					$store_view_data[] = $this->getStore()->getCode();
					$store_view_root[] = $this->getStore()->getRootCategoryId();
				}
				
				for($i=0;$i<count($store_view_id);$i++){
									
					$category_id = Mage::getModel('catalog/category');
					$category_id->setStoreId($store_view_id[$i]);
					$category_id->load($cat_data->getData('entity_id'));
				
					if($include_sku){
						$category = Mage::getModel('catalog/category')->load($cat_data->getData('entity_id'));
						$productCollection = $category->getProductCollection();
						$d =0;	
						$productid = '';
						foreach($productCollection as $_product) {
							$product = Mage::getModel('catalog/product')->load($_product->getId());
							if($d < count($productCollection)-1){
								$productid .= $_product->getSku()."|";
							}else{
								$productid .= $_product->getSku();
							}
							$d++;
						}
					}
					
					$path = explode('/', $category_id->getData('path'));	
								
					if($path[1] == $store_view_root[$i]){
					
						$paths = Count($path);	
					
						if($paths > 3) {
							$cat_path_name_first = array();
							for($c=1;$c<count($path);$c++)	{
								$cat_path_id = Mage::getModel('catalog/category')->setStoreId($store_view_id[$i])->load($path[$c]);
								$cat_path_name  = $cat_path_id->getData('name');
								$cat_path_name_first[] = $cat_path_name;
							}
							$cat_path_name_finals = '';  
							for($d=1;$d<count($cat_path_name_first); $d++) {
								if($d < count($cat_path_name_first)-1){
									$cat_path_name_finals .= trim($cat_path_name_first[$d])."/";
								}else{
									$cat_path_name_finals .= trim($cat_path_name_first[$d]);
								}
							} 
						}else {
							$cat_path_name_finals = trim($category_id->getData('name'));
						}
						if($include_sku){
							$row = array(
								"store_code"=>$store_view_data[$i],
								"all_store_category_name"=>$cat_path_name_final,
								"category_name"=>$cat_path_name_finals,
								"is_active"=>$category_id->getData('is_active'),
								"url_key"=>$category_id->getData('url_key'),
								"thumbnail_image"=>$category_id->getData('thumbnail'),
								"description"=>$category_id->getData('description'),
								"image"=>$category_id->getData('image'),
								"page_title"=>$category_id->getData('meta_title'),
								"meta_keywords"=>$category_id->getData('meta_keywords'),
								"meta_description"=>$category_id->getData('meta_description'),
								"include_in_navigation_menu"=>$category_id->getData('include_in_menu'),
								"display_mode"=>$category_id->getData('display_mode'),
								"cms_block"=>$category_id->getData('landing_page'), 
								"is_anchor"=>$category_id->getData('is_anchor'), 
								"default_product_listing"=>$category_id->getData('default_sort_by'), 
								"price_step"=>$category_id->getData('filter_price_range'), 
								"use_parent_category"=>$category_id->getData('custom_use_parent_settings'), 
								"apply_to_products"=>$category_id->getData('custom_apply_to_products'), 
								"custom_design"=>$category_id->getData('custom_design'), 
								"active_from"=>$category_id->getData('custom_design_from'), 
								"active_to"=>$category_id->getData('custom_design_to'), 
								"page_layout"=>$category_id->getData('page_layout'), 
								"custom_layout"=>$category_id->getData('custom_layout_update'), 
								"sku"=>$productid, 
							);
							$productid = '';
						}else{
							$row = array(
								"store_code"=>$store_view_data[$i],
								"all_store_category_name"=>$cat_path_name_final,
								"category_name"=>$cat_path_name_finals,
								"is_active"=>$category_id->getData('is_active'),
								"url_key"=>$category_id->getData('url_key'),
								"thumbnail_image"=>$category_id->getData('thumbnail'),
								"description"=>$category_id->getData('description'),
								"image"=>$category_id->getData('image'),
								"page_title"=>$category_id->getData('meta_title'),
								"meta_keywords"=>$category_id->getData('meta_keywords'),
								"meta_description"=>$category_id->getData('meta_description'),
								"include_in_navigation_menu"=>$category_id->getData('include_in_menu'),
								"display_mode"=>$category_id->getData('display_mode'),
								"cms_block"=>$category_id->getData('landing_page'), 
								"is_anchor"=>$category_id->getData('is_anchor'), 
								"default_product_listing"=>$category_id->getData('default_sort_by'), 
								"price_step"=>$category_id->getData('filter_price_range'), 
								"use_parent_category"=>$category_id->getData('custom_use_parent_settings'), 
								"apply_to_products"=>$category_id->getData('custom_apply_to_products'), 
								"custom_design"=>$category_id->getData('custom_design'), 
								"active_from"=>$category_id->getData('custom_design_from'), 
								"active_to"=>$category_id->getData('custom_design_to'), 
								"page_layout"=>$category_id->getData('page_layout'), 
								"custom_layout"=>$category_id->getData('custom_layout_update'), 
							
							);
						}	
						
						$batchExport = $this->getBatchExportModel()
							->setId(null)
							->setBatchId($this->getBatchModel()->getId())
							->setBatchData($row)
							->setStatus(1)
							->save();
					}
				}
			}
		}			
	}
}