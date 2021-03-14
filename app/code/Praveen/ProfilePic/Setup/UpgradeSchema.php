<?php
namespace Praveen\ProfilePic\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface {
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$setup->startSetup();
		if (version_compare($context->getVersion(), '1.0.0', '<')) {
			$connection = $setup->getConnection();
			$table = $setup->getTable('customer_entity');
			$connection->addColumn(
					$table,
					'profile_pic',
					['type' => Table::TYPE_TEXT, 'nullable' => true, 'length' => '255', 'comment' => 'Profile Picture']
				);
		}
		$setup->endSetup();
	}
}
