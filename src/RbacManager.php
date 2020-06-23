<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidCallException;
use yii\caching\CacheInterface;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\di\Instance;
use yii\rbac\DbManager;

class RbacManager extends DbManager
{
    protected function addItem($item)
    {
        $item->description = serialize($item->description);
        return parent::addItem($item);
    }

    protected function updateItem($name, $item)
    {
        $item->description = serialize($item->description);
        return parent::updateItem($name, $item);
    }

    protected function populateItem($row)
    {
        $description = @unserialize($row['description'], ['allowed_classes' => false]);

        if ($description !== false) {
            $row['description'] = $description;
        }

        return parent::populateItem($row);
    }
}
