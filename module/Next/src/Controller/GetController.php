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
        $nextSql = $this->raceTable->getSql();

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
        $datetime_now = (new \DateTime(null, new \DateTimeZone("UTC")))->format('Y-m-d H:i:s');
        $nextSelect->where(new \Zend\Db\Sql\Predicate\Expression('race.close_time > ?',  $datetime_now));
        $nextSelect->order("race.close_time asc");
        $nextSelect->limit(5);

        /** @var \Zend\Db\ResultSet\ResultSet $resultSet */
        $resultSet = new ResultSet();
        $resultSet->initialize($nextSql->prepareStatementForSqlObject($nextSelect)->execute());

        return new JsonModel([
            "races" => $resultSet->toArray()
        ]);
    }

    public function raceAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (0 === $id) {
            return new JsonModel([]);
        }

        /** @var \Zend\Db\Sql\Sql $nextSql */
        $raceSql = $this->raceTable->getSql();

        /** @var \Zend\Db\Sql\Select $nextSql */
        $raceSelect = $raceSql->select();
        $raceSelect->join(
            "meeting",
            "meeting.id = race.meeting_id",
            ["meeting_name" => "name"],
            Join::JOIN_LEFT
        );
        $raceSelect->join(
            "type",
            "type.id = meeting.type_id",
            ["type_name" => "name"],
            Join::JOIN_LEFT
        );
        $raceSelect->where(new \Zend\Db\Sql\Predicate\Expression('race.id = ?',  $id));

        /** @var \Zend\Db\ResultSet\ResultSet $resultSet */
        $raceResultSet = new ResultSet();
        $raceResultSet->initialize($raceSql->prepareStatementForSqlObject($raceSelect)->execute());


        /** @var \Zend\Db\Sql\Sql $nextSql */
        $competitorRaceSql = $this->competitorRaceTable->getSql();

        /** @var \Zend\Db\Sql\Select $nextSql */
        $competitorRaceSelect = $competitorRaceSql->select();
        $competitorRaceSelect->join(
            "competitor",
            "competitor.id = competitor_race.competitor_id",
            ["competitor_name" => "name"],
            Join::JOIN_LEFT
        );
        $competitorRaceSelect->where(new \Zend\Db\Sql\Predicate\Expression('competitor_race.race_id = ?',  $id));
        $competitorRaceSelect->order("competitor_race.position asc");

        /** @var \Zend\Db\ResultSet\ResultSet $resultSet */
        $competitorRaceResultSet = new ResultSet();
        $competitorRaceResultSet->initialize($competitorRaceSql->prepareStatementForSqlObject($competitorRaceSelect)->execute());

        return new JsonModel([
            "race" => $raceResultSet->toArray()[0],
            "competitors" => $competitorRaceResultSet->toArray()
        ]);
    }
}