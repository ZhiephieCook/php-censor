<?php
/**
 * PHPCI - Continuous Integration for PHP
 *
 * @copyright    Copyright 2014, Block 8 Limited.
 * @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
 * @link         https://www.phptesting.org/
 */

namespace PHPCensor\Store;

use PHPCensor\Store\Base\BuildMetaStoreBase;
use b8\Database;
use PHPCensor\Model\BuildMeta;

/**
 * BuildMeta Store
 * @uses PHPCensor\Store\Base\BuildMetaStoreBase
 */
class BuildMetaStore extends BuildMetaStoreBase
{
    /**
     * Only used by an upgrade migration to move errors from build_meta to build_error
     * 
     * @param $limit
     * 
     * @return array
     */
    public function getErrorsForUpgrade($limit)
    {
        $query = 'SELECT * FROM {{build_meta}}
                    WHERE {{meta_key}} IN (\'phpmd-data\', \'phpcs-data\', \'phpdoccheck-data\', \'technical_debt - data\')
                    ORDER BY {{id}} ASC LIMIT :limit';

        $stmt = Database::getConnection('read')->prepareCommon($query);

        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);

        if ($stmt->execute()) {
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $map = function ($item) {
                return new BuildMeta($item);
            };
            $rtn = array_map($map, $res);

            return $rtn;
        } else {
            return [];
        }
    }
}
