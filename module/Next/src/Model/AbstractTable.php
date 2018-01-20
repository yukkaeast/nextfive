<?php
/**
 * @package Next
 * @author Yury Ostapenko
 */

namespace Next\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

abstract class AbstractTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @return TableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * @return \Zend\Db\Sql\Sql
     */
    public function getSql()
    {
        return $this->tableGateway->getSql();
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getById($id)
    {
        $id     = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row    = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }
}
