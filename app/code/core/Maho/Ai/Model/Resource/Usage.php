<?php

declare(strict_types=1);

/**
 * Maho
 *
 * @package    Maho_Ai
 * @copyright  Copyright (c) 2026 Maho (https://mahocommerce.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Maho_Ai_Model_Resource_Usage extends Mage_Core_Model_Resource_Db_Abstract
{
    #[\Override]
    protected function _construct(): void
    {
        $this->_init('ai/usage', 'usage_id');
    }

    /**
     * Upsert daily usage record
     */
    public function incrementUsage(
        string $consumer,
        string $platform,
        string $model,
        int $storeId,
        int $inputTokens,
        int $outputTokens,
    ): void {
        $connection = $this->_getWriteAdapter();
        $table = $this->getMainTable();
        $date = Mage::app()->getLocale()->formatDateForDb('now', withTime: false);

        $connection->insertOnDuplicate(
            $table,
            [
                'consumer'       => $consumer,
                'platform'       => $platform,
                'model'          => $model,
                'store_id'       => $storeId,
                'period_date'    => $date,
                'request_count'  => 1,
                'input_tokens'   => $inputTokens,
                'output_tokens'  => $outputTokens,
            ],
            ['request_count', 'input_tokens', 'output_tokens'],
        );
    }

    /**
     * Get today's total token count across all providers
     */
    public function getTodayTotalTokens(): int
    {
        $connection = $this->_getReadAdapter();
        $select = $connection->select()
            ->from($this->getMainTable(), [
                'total' => new Maho\Db\Expr('SUM(input_tokens + output_tokens)'),
            ])
            ->where('period_date = ?', Mage::app()->getLocale()->formatDateForDb('now', withTime: false));

        return (int) $connection->fetchOne($select);
    }
}
