<?php
/**
 * @package Next
 * @author Yury Ostapenko
 */

namespace Next\Controller;

use Next\Model\CompetitorRaceTable;
use Next\Model\CompetitorTable;
use Next\Model\MeetingTable;
use Next\Model\RaceTable;
use Next\Model\TypeTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Join;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class GetController extends AbstractActionController
{
    protected $typeTable;
    protected $meetingTable;
    protected $competitorTable;
    protected $raceTable;
    protected $competitorRaceTable;

    public function __construct(
        TypeTable $typeTable,
        MeetingTable $meetingTable,
        RaceTable $raceTable,
        CompetitorTable $competitorTable,
        CompetitorRaceTable $competitorRaceTable
    )
    {
        $this->faker = \Faker\Factory::create();
        $this->typeTable = $typeTable;
        $this->meetingTable = $meetingTable;
        $this->raceTable = $raceTable;
        $this->competitorTable = $competitorTable;
        $this->competitorRaceTable = $competitorRaceTable;
    }

    public function indextAction()
    {
        return [];
    }

    public function nextAction()
    {
        /** @var \Zend\Db\Sql\Sql $nextSql */
        $nextSql = $this->raceTable->getTableGateway()->getSql();

        /** @var \Zend\Db\Sql\Select $nextSql */
        $nextSelect = $nextSql->select();
        $nextSelect->join(
            "meeting",
            "meeting.id = race.meeting_id",
            ["meeting_name" => "name"],
            Join::JOIN_LEFT
        );
        $nextSelect->join(
            "type",
            "type.id = meeting.type_id",
            ["type_name" => "name"],
            Join::JOIN_LEFT
        );
        $nextSelect->where(new \Zend\Db\Sql\Predicate\Expression('race.close_time > ?',  (new \DateTime())->format('Y-m-d H:i:s')));
        $nextSelect->order("race.close_time asc");
        $nextSelect->limit(5);

        /** @var \Zend\Db\ResultSet\ResultSet $resultSet */
        $resultSet = new ResultSet();
        $resultSet->initialize($nextSql->prepareStatementForSqlObject($nextSelect)->execute());

        return new JsonModel($resultSet->toArray());
    }
}