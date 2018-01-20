<?php
/**
 * @package Next
 * @author Yury Ostapenko
 */

namespace Next;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\TypeTable::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('type', $dbAdapter);
                    return new Model\TypeTable($tableGateway);
                },
                Model\MeetingTable::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('meeting', $dbAdapter);
                    return new Model\MeetingTable($tableGateway);
                },
                Model\CompetitorTable::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('competitor', $dbAdapter);
                    return new Model\CompetitorTable($tableGateway);
                },
                Model\RaceTable::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('race', $dbAdapter);
                    return new Model\RaceTable($tableGateway);
                },
                Model\CompetitorRaceTable::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $tableGateway = new TableGateway('competitor_race', $dbAdapter);
                    return new Model\CompetitorRaceTable($tableGateway);
                }

            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\GenerateController::class => function ($container) {
                    return new Controller\GenerateController(
                        $container->get(Model\TypeTable::class),
                        $container->get(Model\MeetingTable::class),
                        $container->get(Model\RaceTable::class),
                        $container->get(Model\CompetitorTable::class),
                        $container->get(Model\CompetitorRaceTable::class)
                    );
                },
                Controller\GetController::class => function ($container) {
                    return new Controller\GetController(
                        $container->get(Model\TypeTable::class),
                        $container->get(Model\MeetingTable::class),
                        $container->get(Model\RaceTable::class),
                        $container->get(Model\CompetitorTable::class),
                        $container->get(Model\CompetitorRaceTable::class)
                    );
                },
            ],
        ];
    }
}
