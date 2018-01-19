<?php

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
     * @return TableGatewayInterface
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
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
