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
use Zend\Mvc\Controller\AbstractActionController;

class GenerateController extends AbstractActionController
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

    public function indexAction()
    {
        /** @var \Zend\Db\ResultSet\ResultSet $types */
        $types = $this->typeTable->fetchAll();
        // Populate Types
        if ($types->count() == 0) {
            $this->typeTable->getTableGateway()->insert([
                'name' => 'Thoroughbred'
            ]);
            $this->typeTable->getTableGateway()->insert([
                'name' => 'Greyhounds'
            ]);
            $this->typeTable->getTableGateway()->insert([
                'name' => 'Harness'
            ]);
            $types = $this->typeTable->fetchAll();
        }

        // prepare types array
        $arTypes = [];
        foreach ($types as $type) {
            $arTypes[$type['id']] = $type['name'];
        }

        /** @var \Zend\Db\ResultSet\ResultSet $meetings */
        $meetings = $this->meetingTable->fetchAll();

        // Populate Meetings
        if ($meetings->count() < 20) {
            for ($i = $meetings->count(); $i < 20; $i++) {
                $data = [
                    'name' => $this->faker->city,
                    'type_id' => array_rand($arTypes),
                ];
                $this->meetingTable->getTableGateway()->insert($data);
            }
            $meetings = $this->meetingTable->fetchAll();
        }

        /** @var \Zend\Db\ResultSet\ResultSet $competitors */
        $competitors = $this->competitorTable->fetchAll();

        // Populate Competitors
        if ($competitors->count() < 50) {
            for ($i = $competitors->count(); $i < 50; $i++) {
                $data = [
                    'name' => $this->faker->name
                ];
                $this->competitorTable->getTableGateway()->insert($data);
            }
            $competitors = $this->competitorTable->fetchAll();
        }

        // prepare competitors array
        $arCompetitors = [];
        foreach ($competitors as $competitor) {
            $arCompetitors[$competitor['id']] = $competitor['name'];
        }

        // prepare new races ids array
        $arNewRaces = [];

        // Populate New Races
        $arMeetings = $meetings->toArray();
        foreach ($arMeetings as $meeting) {
            for ($i = 0; $i < rand(1, 3); $i++) {

                $datetime = new \DateTime('now', new \DateTimeZone('UTC'));
                // add up to 2 hours to current time
                $datetime->add(new \DateInterval('PT' . rand(5, 120) . 'M'));

                $data = [
                    'meeting_id' => $meeting['id'],
                    'close_time' => $datetime->format('Y-m-d H:i:s')
                ];

                $this->raceTable->getTableGateway()->insert($data);
                $arNewRaces[] = $this->raceTable->getTableGateway()->getLastInsertValue();
            }
        }

        // Populate Competitors for New Races
        foreach ($arNewRaces as $newRaceId) {
            for ($i = 0; $i < rand(4, 6); $i++) {
                $data = [
                    'race_id' => $newRaceId,
                    'competitor_id' => array_rand($arCompetitors),
                    'position' => $i + 1
                ];
                $this->competitorRaceTable->getTableGateway()->insert($data);
            }
        }

        return [];
    }
}