<?php

namespace Praveen\Plugin\Plugin\Catalog\Model;

use Praveen\Preference\Model\Product;

class ProductPlugin
{
	public function afterGetName(Product $subject, $result)
	{
	return $result. ' modified by After Plugin';
	}
}
