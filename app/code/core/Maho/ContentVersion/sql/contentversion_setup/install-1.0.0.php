<?php

/**
 * Maho
 *
 * @category   Maho
 * @package    Maho_ContentVersion
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('contentversion/version'))
    ->addColumn('version_id', Maho\Db\Ddl\Table::TYPE_INTEGER, null, [
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ], 'Version ID')
    ->addColumn('entity_type', Maho\Db\Ddl\Table::TYPE_VARCHAR, 50, [
        'nullable' => false,
    ], 'Entity Type (cms_page, cms_block, blog_post)')
    ->addColumn('entity_id', Maho\Db\Ddl\Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => false,
    ], 'Entity ID')
    ->addColumn('version_number', Maho\Db\Ddl\Table::TYPE_SMALLINT, null, [
        'unsigned' => true,
        'nullable' => false,
    ], 'Version Number')
    ->addColumn('content_data', Maho\Db\Ddl\Table::TYPE_TEXT, '16M', [
        'nullable' => false,
    ], 'JSON Snapshot of Content')
    ->addColumn('editor', Maho\Db\Ddl\Table::TYPE_VARCHAR, 100, [
        'nullable' => true,
    ], 'Who Made the Edit')
    ->addColumn('created_at', Maho\Db\Ddl\Table::TYPE_TIMESTAMP, null, [
        'nullable' => false,
        'default'  => Maho\Db\Ddl\Table::TIMESTAMP_INIT,
    ], 'Created At')
    ->addIndex(
        $installer->getIdxName(
            'contentversion/version',
            ['entity_type', 'entity_id', 'version_number'],
            Maho\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE,
        ),
        ['entity_type', 'entity_id', 'version_number'],
        ['type' => Maho\Db\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE],
    )
    ->addIndex(
        $installer->getIdxName('contentversion/version', ['entity_type', 'entity_id']),
        ['entity_type', 'entity_id'],
    )
    ->addIndex(
        $installer->getIdxName('contentversion/version', ['created_at']),
        ['created_at'],
    )
    ->setComment('Content Version History');

$installer->getConnection()->createTable($table);

$installer->endSetup();
