<?php

/* This file is part of Jeedom
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

require_once __DIR__ . '/../../../../core/php/core.inc.php';
include __DIR__ . '/../php/viessmannApi.php';

class viessmannIot extends eqLogic
{
    public const REFRESH_TIME = 10;

    public const HEATING_CIRCUITS = "heating.circuits";
    public const HEATING_BURNERS = "heating.burners";
    public const HEATING_COMPRESSORS = "heating.compressors";

    public const OUTSIDE_TEMPERATURE = "heating.sensors.temperature.outside";
    public const HOT_WATER_STORAGE_TEMPERATURE = "heating.dhw.sensors.temperature.hotWaterStorage";
    public const DHW_TEMPERATURE = "heating.dhw.temperature.main";
    public const HEATING_DHW_ONETIMECHARGE = "heating.dhw.oneTimeCharge";
    public const HEATING_DHW_SCHEDULE = "heating.dhw.schedule";

    public const ACTIVE_MODE = "operating.modes.active";
    public const ACTIVE_DHW_MODE = "heating.dhw.operating.modes.active";
    public const ACTIVE_PROGRAM = "operating.programs.active";
    public const PUMP_STATUS = "circulation.pump";
    public const HEATING_BOILER_SENSORS_TEMPERATURE = "heating.boiler.sensors.temperature.commonSupply";
    public const HEATING_BOILER_SENSORS_TEMPERATURE_MAIN = "heating.boiler.sensors.temperature.main";

    public const STANDBY_MODE = "operating.modes.standby";
    public const HEATING_MODE = "operating.modes.heating";
    public const DHW_MODE = "operating.modes.dhw";
    public const DHW_AND_HEATING_MODE = "operating.modes.dhwAndHeating";
    public const COOLING_MODE = "operating.modes.cooling";
    public const DHW_AND_HEATING_COOLING_MODE = "operating.modes.dhwAndHeatingCooling";
    public const HEATING_COOLING_MODE = "operating.modes.heatingCooling";
    public const NORMAL_STANDBY_MODE = "operating.modes.normalStandby";
    public const HEATING_SCHEDULE = "heating.schedule";
    public const HEATING_FROSTPROTECTION = "frostprotection";

    public const SENSORS_TEMPERATURE_SUPPLY = "sensors.temperature.supply";
    public const HEATING_CURVE = "heating.curve";
    public const COMFORT_PROGRAM = "operating.programs.comfort";
    public const NORMAL_PROGRAM = "operating.programs.normal";
    public const REDUCED_PROGRAM = "operating.programs.reduced";
    public const COMFORT_PROGRAM_HEATING = "operating.programs.comfortHeating";
    public const NORMAL_PROGRAM_HEATING = "operating.programs.normalHeating";
    public const REDUCED_PROGRAM_HEATING = "operating.programs.reducedHeating";
    public const SENSORS_TEMPERATURE_ROOM = "sensors.temperature.room";
    public const ECO_PROGRAM = "operating.programs.eco";
    public const PRESSURE_SUPPLY = "heating.sensors.pressure.supply";

    public const HEATING_GAS_CONSUMPTION_DHW = "heating.gas.consumption.dhw";
    public const HEATING_GAS_CONSUMPTION_HEATING = "heating.gas.consumption.heating";
    public const HEATING_GAS_CONSUMPTION_TOTAL = "heating.gas.consumption.total";
    public const HEATING_POWER_CONSUMPTION_DHW = "heating.power.consumption.dhw";
    public const HEATING_POWER_CONSUMPTION_HEATING = "heating.power.consumption.heating";
    public const HEATING_POWER_CONSUMPTION_TOTAL = "heating.power.consumption.total";

    public const HEATING_GAS_CONSUMPTION_SUMMARY_DHW = "heating.gas.consumption.summary.dhw";
    public const HEATING_GAS_CONSUMPTION_SUMMARY_HEATING = "heating.gas.consumption.summary.heating";
    public const HEATING_GAS_CONSUMPTION_SUMMARY_TOTAL = "heating.gas.consumption.summary.total";
    public const HEATING_POWER_CONSUMPTION_SUMMARY_DHW = "heating.power.consumption.summary.dhw";
    public const HEATING_POWER_CONSUMPTION_SUMMARY_HEATING = "heating.power.consumption.summary.heating";
    public const HEATING_POWER_CONSUMPTION_SUMMARY_TOTAL = "heating.power.consumption.summary.total";

    public const HEATING_ERRORS_ACTIVE = "heating.errors.active";
    public const HEATING_ERRORS = "heating.errors";
    public const HEATING_ERRORS_HISTORY = "heating.errors.history";
    public const HEATING_SERVICE_TIMEBASED = "heating.service.timeBased";
    public const STATISTICS = "statistics";
    public const MODULATION = "modulation";
    public const HOLIDAY_PROGRAM = "heating.operating.programs.holiday";
    public const HOLIDAY_AT_HOME_PROGRAM = "heating.operating.programs.holidayAtHome";
    public const FORCED_LAST_FROM_SCHEDULE = "operating.programs.forcedLastFromSchedule";
    public const SOLAR_TEMPERATURE = "heating.solar.sensors.temperature.collector";
    public const SOLAR_DHW_TEMPERATURE = "heating.solar.sensors.temperature.dhw";
    public const VENTILATION = 'ventilation.operating.state';


    //    heating.buffer.sensors.temperature.main = température du tampon
//    heating.buffer.sensors.temperature.top= temperature du haut du tampon
//    heating.circuits.1.heating.curve = pente 
//    heating.circuits.1.operating.modes.heating = booleen, chauffe ou pas
//    heating.circuits.0.heating.curve => setcurve 
//    heating.compressors.0.statistics = stats compresseur -> 
//        .starts = nb de départs
//        .hours = nb heures de fct
//    heating.boiler.serial = S/N chaudiere
//    heating.controller.seria = S/N contrôleur
//   heating.circuits.1.temperature = température de l'eau dans le circuit
//   heating.circuits.1.operating.programs.standby = .active = booleen
    public const HEATPUMP_STATUS = "circulation.pump";
    //   heating.circuits.1.circulation.pump = .status = "on" ou "off" état du circulateur
//  heating.bufferCylinder.sensors.temperature.top = .value, température en haut du tampon
//  heating.bufferCylinder.sensors.temperature.main = .value, température au centre du tampon
//  heating.compressors.0 = compresseur 
//      .active = booléen en marche ou arrêté
//      .phase = état du compresseur, ex "pass defrost" => dégivrage
//  heating.sensors.temperature.outside = temperature exterieure via sonde PAC
//      .value = valeur
//      .status = connected si détectée
    //  heating.circuits.1.sensors.temperature.room = sonde de température vitotrol
//      .value = valeur
//      .status = connected si détectée
// heating.sensors.temperature.return = capteur du circuit de retour d'eau
//      .value = valeur
//      .status = connected si détectée
// heating.boiler.sensors.temperature.commonSupply = capteur de température de sortie vers le réseau
//      .value = valeur
//      .status = connected si détectée
//  heating.circuits.1.operating.modes.forcedReduced
//      .active = booléen en marche ou arrêté
public const HEATPUMP_SECONDARY = "heating.secondaryCircuit.sensors.temperature.supply";
//  heating.secondaryCircuit.sensors.temperature.supply = circuit de délestage secondaire
//      .value = valeur
//      .status = connected si détectée
//  heating.circuits.1.operating.modes.standby = .active , mode veille actif
//  heating.circuits.1.sensors.temperature.supply = sonde temperature en sortie de split
//      .value = valeur
//      .status = connected si détectée
// heating.circuits.1.operating.modes.forcedNormal = .active, mode normal forcé vs schedule
// heating.primaryCircuit.sensors.temperature.supply = température eau en entrée de split
//      .value = valeur
//      .status = connected si détectée
    // heating.circuits.1.operating.programs.active = .value, mode courant
    // heating.circuits.1.operating.programs.comfort  = mode confort
//          .active -> actif booleen
//          .demand -> "unknown" ????
//          .temperature -> température du mode
    //    heating.circuits.1.operating.programs.normal => setTemperature targetTemperature
//          .active -> actif booleen
//          .demand -> "unknown" ????
//          .temperature -> température du mode
    //  heating.circuits.1.operating.programs.reduced = programme éco
//          .active -> actif booleen
//          .demand -> "unknown" ????
//          .temperature -> température du mode

/*
    public static function deamon_info()
    {
        $return = array();
        $return['log'] = '';
        $return['state'] = 'nok';
        $cron = cron::byClassAndFunction(__CLASS__, 'salsa');
        if (is_object($cron) && $cron->running()) {
            $return['state'] = 'ok';
        }
        $return['launchable'] = 'ok';
        return $return;
    }

    public static function deamon_start()
    {
        self::deamon_stop();
        $deamon_info = self::deamon_info();
        if ($deamon_info['launchable'] != 'ok') {
            throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
        }
        $cron = cron::byClassAndFunction(__CLASS__, 'salsa');
        if (!is_object($cron)) {
            $cron = new cron();
            $cron->setClass(__CLASS__);
            $cron->setFunction('salsa');
            $cron->setEnable(1);
            $cron->setDeamon(1);
            $cron->setTimeout(1440);
            $cron->setSchedule('* * * * *');
            $cron->save();
        }
        $cron->run();
    }

    public static function deamon_stop()
    {
        $cron = cron::byClassAndFunction(__CLASS__, 'salsa');
        if (is_object($cron)) {
            $cron->halt();
        }
    }

    public static function salsa()
    {
        foreach (viessmannIot::byType('viessmannIot', true) as $viessmannIot) {
            if ($viessmannIot->getIsEnable() == 1) {
                $tempsRestant = $viessmannIot->getCache('tempsRestant', 10);
                if ($tempsRestant > 0) {
                    $tempsRestant--;
                    if ($tempsRestant == 0) {
                        log::add('viessmannIot', 'debug', "salsa viessmann api called");
                        $viessmannApi = $viessmannIot->callViessmannAPI();
                        if ($viessmannApi !== null) {
                            $viessmannIot->rafraichir($viessmannApi);
                            unset($viessmannApi);
                        }
                    }
                    $viessmannIot->setCache('tempsRestant', $tempsRestant);
                } else
                    $viessmannIot->setCache('tempsRestant', 10);
            }
        }
        sleep(1);
    }
*/

    public function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    // Créer les commandes
    //
    public function createCommands($viessmannApi)
    {
        $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $deviceId = trim($this->getConfiguration('deviceId', '0'));

        $features = $viessmannApi->getArrayFeatures();
        $n = count($features["data"]);
        log::add('viessmannIot', 'debug', "parse " . $n . " features");

        foreach ($features["data"] as $feature) {
            if ($feature["isEnabled"]) {
                log::add('viessmannIot', 'debug', $feature["feature"]);
                $this->createCommand($feature, $circuitId, $deviceId);
            }
        }

        log::add('viessmannIot', 'info', 'Commandes (re)créées');
    }

    private function createCommand($feature, $circuitId, $deviceId)
    {
        switch ($feature["feature"]) {
            case $this->buildFeature($circuitId, self::PUMP_STATUS):
            case $this->buildFeature($circuitId, self::HEATPUMP_STATUS):
                $this->createInfoCommand('pumpStatus', 'Statut circulateur', 'string');
                break;
            case self::HEATPUMP_SECONDARY:
                $this->createInfoCommand('secondaryCircuitTemperature', 'Circuit de délestage', 'numeric', '°C');
                break;
            case self::HOT_WATER_STORAGE_TEMPERATURE:
                $this->createInfoCommand('hotWaterStorageTemperature', 'Température eau chaude', 'numeric', '°C');
                break;
            case self::DHW_TEMPERATURE:
                $this->createDhwTemperatureCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::ACTIVE_MODE):
                $this->createActiveModeCommands($feature);
                break;
            case self::ACTIVE_DHW_MODE:
                $this->createActiveDhwModeCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::ACTIVE_PROGRAM):
                $this->createInfoCommand('activeProgram', 'Programme activé', 'string');
                break;
            case $this->buildFeatureBurner($deviceId, ''):
                $this->createInfoCommand('isHeatingBurnerActive', 'Bruleur activé', 'binary');
                break;
            case $this->buildFeatureCompressor($deviceId, ''):
                $this->createInfoCommand('isHeatingCompressorActive', 'Compresseur activé', 'binary');
                break;
            case $this->buildFeature($circuitId, self::STANDBY_MODE):
                $this->createInfoCommand('isStandbyModeActive', 'Veille activée', 'binary');
                break;
            case $this->buildFeature($circuitId, self::HEATING_MODE):
                $this->createInfoCommand('isHeatingModeActive', 'Chauffage activé', 'binary');
                break;
            case $this->buildFeature($circuitId, self::DHW_MODE):
                $this->createInfoCommand('isDhwModeActive', 'Eau chaude activée', 'binary');
                break;
            case $this->buildFeature($circuitId, self::DHW_AND_HEATING_MODE):
                $this->createInfoCommand('isDhwAndHeatingModeActive', 'Eau chaude et chauffage activés', 'binary');
                break;
            case $this->buildFeature($circuitId, self::COOLING_MODE):
                $this->createInfoCommand('isCoolingModeActive', 'Refroidissement activé', 'binary');
                break;
            case $this->buildFeature($circuitId, self::DHW_AND_HEATING_COOLING_MODE):
                $this->createInfoCommand('isDhwAndHeatingCoolingModeActive', 'Eau chaude, chauffage et refroidissement activés', 'binary');
                break;
            case $this->buildFeature($circuitId, self::HEATING_COOLING_MODE):
                $this->createInfoCommand('isHeatingCoolingModeActive', 'Chauffage et refroidissement activés', 'binary');
                break;
            case $this->buildFeature($circuitId, self::NORMAL_STANDBY_MODE):
                $this->createInfoCommand('isNormalStandbyModeActive', 'Veille normale activée', 'binary');
                break;
            case $this->buildFeature($circuitId, self::SENSORS_TEMPERATURE_SUPPLY):
                $this->createInfoCommand('supplyProgramTemperature', 'Température de départ', 'numeric', '°C');
                break;
            case $this->buildFeature($circuitId, self::COMFORT_PROGRAM):
            case $this->buildFeature($circuitId, self::COMFORT_PROGRAM_HEATING):
                $this->createComfortProgramCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::NORMAL_PROGRAM):
            case $this->buildFeature($circuitId, self::NORMAL_PROGRAM_HEATING):
                $this->createNormalProgramCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::REDUCED_PROGRAM):
            case $this->buildFeature($circuitId, self::REDUCED_PROGRAM_HEATING):
                $this->createReducedProgramCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::ECO_PROGRAM):
                $this->createEcoProgramCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::FORCED_LAST_FROM_SCHEDULE):
                $this->createLastScheduleCommands($feature);
                break;
            case self::HEATING_DHW_ONETIMECHARGE:
                $this->createOneTimeDhwChargeCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::HEATING_CURVE):
                $this->createHeatingCurveCommands($feature);
                break;
            case self::HEATING_DHW_SCHEDULE:
                $this->createDhwScheduleCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::HEATING_SCHEDULE):
                $this->createHeatingScheduleCommands($feature);
                break;
            case $this->buildFeature($circuitId, self::HEATING_FROSTPROTECTION):
                $this->createInfoCommand('frostProtection', 'Protection gel', 'string');
                break;
            case self::HEATING_BOILER_SENSORS_TEMPERATURE:
                $this->createInfoCommand('boilerTemperature', 'Température eau radiateur', 'numeric');
                break;
            case self::HEATING_BOILER_SENSORS_TEMPERATURE_MAIN:
                $this->createInfoCommand('boilerTemperatureMain', 'Température chaudière', 'numeric');
                break;
            case self::PRESSURE_SUPPLY:
                $this->createInfoCommand('pressureSupply', 'Pression installation', 'numeric');
                break;
            case self::SOLAR_TEMPERATURE:
                $this->createInfoCommand('solarTemperature', 'Température panneaux solaires', 'numeric');
                break;
            case self::SOLAR_DHW_TEMPERATURE:
                $this->createInfoCommand('solarDhwTemperature', 'Température eau chaude panneaux solaires', 'numeric');
                break;
            case self::HEATING_SERVICE_TIMEBASED:
                $this->createServiceTimeBasedCommands($feature);
                break;
            case $this->buildFeatureBurner($deviceId, self::STATISTICS):
                $this->createBurnerStatisticsCommands($feature);
                break;
            case $this->buildFeatureCompressor($deviceId, self::STATISTICS):
                $this->createCompressorStatisticsCommands($feature);
                break;
            case $this->buildFeatureBurner($deviceId, self::MODULATION):
                $this->createInfoCommand('heatingBurnerModulation', 'Modulation de puissance', 'numeric');
                break;
            case self::HOLIDAY_PROGRAM:
                $this->createHolidayProgramCommands($feature);
                break;
            case self::HOLIDAY_AT_HOME_PROGRAM:
                $this->createHolidayAtHomeProgramCommands($feature);
                break;
            case self::VENTILATION:
                $this->createInfoCommand('ventilation', 'Ventilation', 'string');
                break;
        }
    }

    private function createInfoCommand($logicalId, $name, $subType, $unit = '')
    {
        $obj = $this->getCmd(null, $logicalId);
        if (!is_object($obj)) {
            $obj = new viessmannIotCmd();
            $obj->setName(__($name, __FILE__));
            $obj->setIsVisible(1);
            $obj->setIsHistorized(0);
            if ($unit) {
                $obj->setUnite($unit);
            }
        }
        $obj->setEqLogic_id($this->getId());
        $obj->setType('info');
        $obj->setSubType($subType);
        $obj->setLogicalId($logicalId);
        $obj->save();
    }

    private function createDhwTemperatureCommands($feature)
    {
        $this->createInfoCommand('dhwTemperature', 'Consigne eau chaude', 'numeric');
        if (isset($feature["commands"]["setTargetTemperature"])) {
            $this->createSliderCommand('dhwSlider', 'Slider consigne eau chaude', 'dhwTemperature', 10, 60);
        }
    }

    private function createSliderCommand($logicalId, $name, $value, $min, $max)
    {
        $obj = $this->getCmd(null, $logicalId);
        if (!is_object($obj)) {
            $obj = new viessmannIotCmd();
            $obj->setName(__($name, __FILE__));
            $obj->setIsVisible(1);
            $obj->setIsHistorized(0);
            $obj->setUnite('°C');
        }
        $obj->setEqLogic_id($this->getId());
        $obj->setType('action');
        $obj->setSubType('slider');
        $obj->setLogicalId($logicalId);
        $obj->setValue($this->getCmd(null, $value)->getId());
        $obj->setConfiguration('minValue', $min);
        $obj->setConfiguration('maxValue', $max);
        $obj->save();
    }

    private function createActiveModeCommands($feature)
    {
        $this->createInfoCommand('activeMode', 'Mode activé', 'string');
        foreach ($feature["commands"]["setMode"]["params"]["mode"]["constraints"]["enum"] as $mode) {
            $this->createModeCommand($mode);
        }
    }

    private function createModeCommand($mode)
    {
        $logicalId = 'mode' . ucfirst($mode);
        $name = 'Mode ' . $mode;
        $this->createActionCommand($logicalId, $name);
    }

    private function createActionCommand($logicalId, $name)
    {
        $obj = $this->getCmd(null, $logicalId);
        if (!is_object($obj)) {
            $obj = new viessmannIotCmd();
            $obj->setName(__($name, __FILE__));
        }
        $obj->setEqLogic_id($this->getId());
        $obj->setLogicalId($logicalId);
        $obj->setType('action');
        $obj->setSubType('other');
        $obj->save();
    }

    private function createActiveDhwModeCommands($feature)
    {
        $this->createInfoCommand('activeDhwMode', 'Mode eau chaude activé', 'string');
        foreach ($feature["commands"]["setMode"]["params"]["mode"]["constraints"]["enum"] as $mode) {
            $this->createDhwModeCommand($mode);
        }
    }

    private function createDhwModeCommand($mode)
    {
        $logicalId = 'modeDhw' . ucfirst($mode);
        $name = 'Mode dhw ' . $mode;
        $this->createActionCommand($logicalId, $name);
    }

    private function createComfortProgramCommands($feature)
    {
        $this->setConfiguration('comfortProgram', $feature["feature"])->save();
        $this->createInfoCommand('comfortProgramTemperature', 'Consigne de confort', 'numeric', '°C');
        $this->createSliderCommand('comfortProgramSlider', 'Slider consigne de confort', 'comfortProgramTemperature', 3, 37);
        $this->createInfoCommand('isActivateComfortProgram', 'Programme comfort actif', 'binary');
        $this->createActionCommand('activateComfortProgram', 'Activer programme confort');
        $this->createActionCommand('deActivateComfortProgram', 'Désactiver programme confort');
    }

    private function createNormalProgramCommands($feature)
    {
        $this->setConfiguration('normalProgram', $feature["feature"])->save();
        $this->createInfoCommand('normalProgramTemperature', 'Consigne normale', 'numeric', '°C');
        $this->createSliderCommand('normalProgramSlider', 'Slider consigne normale', 'normalProgramTemperature', 3, 37);
    }

    private function createReducedProgramCommands($feature)
    {
        $this->setConfiguration('reducedProgram', $feature["feature"])->save();
        $this->createInfoCommand('reducedProgramTemperature', 'Consigne réduite', 'numeric', '°C');
        $this->createSliderCommand('reducedProgramSlider', 'Slider consigne réduite', 'reducedProgramTemperature', 3, 37);
    }

    private function createEcoProgramCommands($feature)
    {
        $this->createInfoCommand('isActivateEcoProgram', 'Programme éco actif', 'binary');
        $this->createActionCommand('activateEcoProgram', 'Activer programme éco');
        $this->createActionCommand('deActivateEcoProgram', 'Désactiver programme éco');
    }

    private function createLastScheduleCommands($feature)
    {
        $this->createInfoCommand('isActivateLastSchedule', 'Prolonger programme actif', 'binary');
        $this->createActionCommand('activateLastSchedule', 'Activer prolonger programme');
        $this->createActionCommand('deActivateLastSchedule', 'Désactiver prolonger programme');
    }

    private function createOneTimeDhwChargeCommands($feature)
    {
        $this->createInfoCommand('isOneTimeDhwCharge', 'Forcer Eau chaude', 'binary');
        $this->createActionCommand('startOneTimeDhwCharge', 'Activer demande eau chaude');
        $this->createActionCommand('stopOneTimeDhwCharge', 'Désactiver demande eau chaude');
    }

    private function createHeatingCurveCommands($feature)
    {
        $this->createInfoCommand('slope', 'Pente', 'numeric');
        $this->createInfoCommand('shift', 'Parallèle', 'numeric');
        $this->createSliderCommand('slopeSlider', 'Slider pente', 'slope', 0.2, 3.5);
        $this->createSliderCommand('shiftSlider', 'Slider parallèle', 'shift', -13, 40);
    }

    private function createDhwScheduleCommands($feature)
    {
        $this->createInfoCommand('dhwSchedule', 'Programmation eau chaude', 'string');
        $this->createActionCommand('setDhwSchedule', 'Set Prog Eau Chaude');
    }

    private function createHeatingScheduleCommands($feature)
    {
        $this->createInfoCommand('heatingSchedule', 'Programmation chauffage', 'string');
        $this->createActionCommand('setHeatingSchedule', 'Set Prog Chauffage');
    }

    private function createServiceTimeBasedCommands($feature)
    {
        $this->createInfoCommand('lastServiceDate', 'Date dernier entretien', 'string');
        $this->createInfoCommand('serviceInterval', 'Intervalle entretien', 'numeric');
        $this->createInfoCommand('monthSinceService', 'Mois entretien', 'numeric');
    }

    private function createBurnerStatisticsCommands($feature)
    {
        $this->createInfoCommand('heatingBurnerHoursPerDay', 'Heures fonctionnement brûleur par jour', 'numeric');
        $this->createInfoCommand('heatingBurnerHours', 'Heures fonctionnement brûleur', 'numeric');
        $this->createInfoCommand('heatingBurnerStartsPerDay', 'Démarrages du brûleur par jour', 'numeric');
        $this->createInfoCommand('heatingBurnerStarts', 'Démarrages du brûleur', 'numeric');
    }

    private function createCompressorStatisticsCommands($feature)
    {
        $this->createInfoCommand('heatingCompressorHoursPerDay', 'Heures fonctionnement compresseur par jour', 'numeric');
        $this->createInfoCommand('heatingCompressorHours', 'Heures fonctionnement compresseur', 'numeric');
        $this->createInfoCommand('heatingCompressorStartsPerDay', 'Démarrages du compresseur par jour', 'numeric');
        $this->createInfoCommand('heatingCompressorStarts', 'Démarrages du compresseur', 'numeric');
    }

    private function createHolidayProgramCommands($feature)
    {
        $this->createInfoCommand('startHoliday', 'Date début', 'string');
        $this->createInfoCommand('endHoliday', 'Date fin', 'string');
        $this->createActionCommand('startHolidayText', 'Date Début texte');
        $this->createActionCommand('endHolidayText', 'Date Fin texte');
        $this->createActionCommand('scheduleHolidayProgram', 'Activer programme vacances');
        $this->createActionCommand('unscheduleHolidayProgram', 'Désactiver programme vacances');
        $this->createInfoCommand('isScheduleHolidayProgram', 'Programme vacances actif', 'binary');
    }

    private function createHolidayAtHomeProgramCommands($feature)
    {
        $this->createInfoCommand('startHolidayAtHome', 'Date début maison', 'string');
        $this->createInfoCommand('endHolidayAtHome', 'Date fin maison', 'string');
        $this->createActionCommand('startHolidayAtHomeText', 'Date Début maison texte');
        $this->createActionCommand('endHolidayAtHomeText', 'Date Fin maison texte');
        $this->createActionCommand('scheduleHolidayAtHomeProgram', 'Activer programme vacances maison');
        $this->createActionCommand('unscheduleHolidayAtHomeProgram', 'Désactiver programme vacances maison');
        $this->createInfoCommand('isScheduleHolidayAtHomeProgram', 'Programme vacances maison actif', 'binary');
    }

    // Accès au serveur Viessmann
    //
    public function callViessmannAPI()
    {
        $clientId = trim($this->getConfiguration('clientId', ''));
        $codeChallenge = trim($this->getConfiguration('codeChallenge', ''));

        $userName = trim($this->getConfiguration('userName', ''));
        $password = trim($this->getConfiguration('password', ''));

        $numChaudiere = trim($this->getConfiguration('numChaudiere', '0'));

        $installationId = trim($this->getConfiguration('installationId', ''));
        $serial = trim($this->getConfiguration('serial', ''));

        $deviceId = trim($this->getConfiguration('deviceId', '0'));
        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        $logFeatures = $this->getConfiguration('logFeatures', '');
        if ($logFeatures === 'Oui') {
            $this->setConfiguration('logFeatures', '')->save();
        }
        $createCommands = $this->getConfiguration('createCommands', '');
        if ($createCommands === 'Oui') {
            $this->setConfiguration('createCommands', '')->save();
        }

        $expires_at = $this->getCache('expires_at', 0);
        $accessToken = $this->getCache('access_token', '');
        $refreshToken = $this->getCache('refresh_token', '');

        if (($userName === '') || ($password === '') || ($clientId === '') || ($codeChallenge === '')) {
            log::add('viessmannIot', 'debug', 'Configuration incomplète');
            return null;
        }

        $params = [
            "clientId" => $clientId,
            "codeChallenge" => $codeChallenge,
            "user" => $userName,
            "pwd" => $password,
            "installationId" => $installationId,
            "serial" => $serial,
            "deviceId" => $deviceId,
            "circuitId" => $circuitId,
            "expires_at" => $expires_at,
            "access_token" => $accessToken,
            "refresh_token" => $refreshToken,
            "logFeatures" => $logFeatures
        ];

        $viessmannApi = new ViessmannApi($params);

        if ((empty($installationId)) || (empty($serial)) || ($createCommands === "Oui")) {
            $return = $viessmannApi->getFeatures();
            if (is_string($return)) {
                unset($viessmannApi);
                log::add('viessmannIot', 'warning', $return);
                return null;
            }
            if ((empty($installationId)) || (empty($serial))) {
                $this->setConfiguration('installationId', $viessmannApi->getInstallationId($numChaudiere));
                $this->setConfiguration('serial', $viessmannApi->getSerial($numChaudiere))->save();
            }
            //              $this->deleteAllCommands();
            $this->createCommands($viessmannApi);
        }

        if ($viessmannApi->isNewToken()) {
            $this->setCache('expires_at', time() + $viessmannApi->getExpiresIn() - 300);
            $this->setCache('access_token', $viessmannApi->getAccessToken());
            $this->setCache('refresh_token', $viessmannApi->getRefreshToken());
        }

        return $viessmannApi;
    }

    /**
     * Summary of rafraichir
     * @param ViessmannApi $viessmannApi
     * @return void
     */
    public function rafraichir($viessmannApi)
    {
        $this->setCache('tempsRestant', 0);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $deviceId = trim($this->getConfiguration('deviceId', '0'));

        $facteurConversionGaz = floatval($this->getConfiguration('facteurConversionGaz', 1));
        if ($facteurConversionGaz == 0)  $facteurConversionGaz = 1;

        $nbr = 0;
        $erreurs = '';
        $erreurCourante = '';

        $outsideTemperature = 99;
        $roomTemperature = 99;
        $slope = 99;
        $shift = 99;

        $comfortProgramTemperature = 99;
        $normalProgramTemperature = 99;
        $reducedProgramTemperature = 99;
        $activeProgram = '';

        $heatingBurnerHours = -1;
        $heatingBurnerStarts = -1;

        $heatingCompressorHours = -1;
        $heatingCompressorStarts = -1;

        $return = $viessmannApi->getFeatures();
        if (is_string($return)) {
            log::add('viessmannIot', 'warning', $return);
            return;
        }

        $bConsumptionSeen = false;
        $features = $viessmannApi->getArrayFeatures();
        
        foreach ($features["data"] as $feature)
            if ($feature["isEnabled"] == true) {
                $val='';
                if ($feature["feature"] == self::OUTSIDE_TEMPERATURE) {
                    $val = $feature["properties"]["value"]["value"];
                    $outsideTemperature = $val;
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::PUMP_STATUS)) {
                    $val = $feature["properties"]["status"]["value"];
                    $this->checkAndUpdateCmd('pumpStatus', $val);
                } elseif ($feature["feature"] == self::HEATPUMP_SECONDARY) {
                    $val = $feature["properties"]["value"]["value"];
                    log::add('viessmannIot', 'debug', 'heatpump delestage '.$val);
                    $this->checkAndUpdateCmd('secondaryCircuitTemperature', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::HEATPUMP_STATUS)) {
                    log::add('viessmannIot', 'debug', 'heatpump status refresh');
                    $val = $feature["properties"]["status"]["value"];
                    $this->checkAndUpdateCmd('pumpStatus', $val);
                } elseif ($feature["feature"] == self::HOT_WATER_STORAGE_TEMPERATURE) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('hotWaterStorageTemperature', $val);
                } elseif ($feature["feature"] == self::DHW_TEMPERATURE) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('dhwTemperature', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::ACTIVE_MODE)) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('activeMode', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::ACTIVE_PROGRAM)) {
                    $val = $feature["properties"]["value"]["value"];
                    $activeProgram = $val;
                    $this->checkAndUpdateCmd('activeProgram', $val);
                } elseif ($feature["feature"] == $this->buildFeatureBurner($deviceId, '')) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isHeatingBurnerActive', $val);
                } elseif ($feature["feature"] == $this->buildFeatureCompressor($deviceId, '') && $feature["isEnabled"] == true) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isHeatingCompressorActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::STANDBY_MODE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isStandbyModeActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::HEATING_MODE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isHeatingModeActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::DHW_MODE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isDhwModeActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::DHW_AND_HEATING_MODE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isDhwAndHeatingModeActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::COOLING_MODE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isCoolingModeActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::DHW_AND_HEATING_COOLING_MODE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isDhwAndHeatingCoolingModeActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::HEATING_COOLING_MODE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isHeatingCoolingModeActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::NORMAL_STANDBY_MODE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isNormalStandbyModeActive', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::SENSORS_TEMPERATURE_SUPPLY)) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('supplyProgramTemperature', $val);
                } elseif (($feature["feature"] == $this->buildFeature($circuitId, self::COMFORT_PROGRAM) ||
                    $feature["feature"] == $this->buildFeature($circuitId, self::COMFORT_PROGRAM_HEATING))) {
                    $val = $feature["properties"]["temperature"]["value"];
                    $comfortProgramTemperature = $val;
                    $this->checkAndUpdateCmd('comfortProgramTemperature', $val);
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isActivateComfortProgram', $val);
                } elseif (
                    ($feature["feature"] == $this->buildFeature($circuitId, self::NORMAL_PROGRAM) ||
                     $feature["feature"] == $this->buildFeature($circuitId, self::NORMAL_PROGRAM_HEATING))
                ) {
                    $val = $feature["properties"]["temperature"]["value"];
                    $normalProgramTemperature = $val;
                    $this->checkAndUpdateCmd('normalProgramTemperature', $val);
                } elseif (
                    ($feature["feature"] == $this->buildFeature($circuitId, self::REDUCED_PROGRAM) ||
                     $feature["feature"] == $this->buildFeature($circuitId, self::REDUCED_PROGRAM_HEATING))
                ) {
                    $val = $feature["properties"]["temperature"]["value"];
                    $reducedProgramTemperature = $val;
                    $this->checkAndUpdateCmd('reducedProgramTemperature', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::ECO_PROGRAM)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isActivateEcoProgram', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::FORCED_LAST_FROM_SCHEDULE)) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isActivateLastSchedule', $val);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::SENSORS_TEMPERATURE_ROOM)) {
                    $val = $feature["properties"]["value"]["value"];
                    $roomTemperature = $val;
                } elseif ($feature["feature"] == self::HEATING_DHW_ONETIMECHARGE) {
                    $val = $feature["properties"]["active"]["value"];
                    $this->checkAndUpdateCmd('isOneTimeDhwCharge', $val);
                }
                 elseif ($feature["feature"] == $this->buildFeature($circuitId, self::HEATING_CURVE)) {
                    $shift = $feature["properties"]["shift"]["value"];
                    $this->checkAndUpdateCmd('shift', $shift);
                    $slope = $feature["properties"]["slope"]["value"];
                    $this->checkAndUpdateCmd('slope', $slope);
                } elseif ($feature["feature"] == self::HEATING_DHW_SCHEDULE) {
                    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                    $dhwSchedule = '';
                    foreach ($days as $day) {
                        $n = count($feature["properties"]['entries']['value'][$day]);
                        foreach ($feature["properties"]['entries']['value'][$day] as $index => $entry) {
                            $dhwSchedule .= 'n,' . $entry['start'] . ',' . $entry['end'];
                            if ($index < $n - 1) {
                                $dhwSchedule .= ',';
                            }
                        }
                        $dhwSchedule .= ';';
                    }
                    $this->checkAndUpdateCmd('dhwSchedule', $dhwSchedule);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::HEATING_SCHEDULE)) {
                    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                    $heatingSchedule = '';
                    foreach ($days as $day) {
                        $n = count($feature["properties"]['entries']['value'][$day]);
                        foreach ($feature["properties"]['entries']['value'][$day] as $entry) {
                            $heatingSchedule .= substr($entry['mode'], 0, 1) . ',' . $entry['start'] . ',' . $entry['end'];
                            if (next($feature["properties"]['entries']['value'][$day])) {
                                $heatingSchedule .= ',';
                            }
                        }
                        $heatingSchedule .= ';';
                    }
                    $this->checkAndUpdateCmd('heatingSchedule', $heatingSchedule);
                } elseif ($feature["feature"] == $this->buildFeature($circuitId, self::HEATING_FROSTPROTECTION)) {
                    $val = $feature["properties"]["status"]["value"];
                    $this->checkAndUpdateCmd('frostProtection', $val);
                } elseif ($feature["feature"] == self::HEATING_BOILER_SENSORS_TEMPERATURE) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('boilerTemperature', $val);
                } elseif ($feature["feature"] == self::HEATING_BOILER_SENSORS_TEMPERATURE_MAIN) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('boilerTemperatureMain', $val);
                } elseif ($feature["feature"] == self::PRESSURE_SUPPLY) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('pressureSupply', $val);
                } elseif ($feature["feature"] == self::SOLAR_TEMPERATURE) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('solarTemperature', $val);
                } elseif ($feature["feature"] == self::SOLAR_DHW_TEMPERATURE) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('solarDhwTemperature', $val);
                } elseif ($feature["feature"] == self::HEATING_GAS_CONSUMPTION_TOTAL) {
                    $bConsumptionSeen = true;
                    $this->handleConsumption($feature, 'totalGazConsumption', 'oldConsoTotal', 'totalGazHistorize', $facteurConversionGaz);
                } elseif ($feature["feature"] == self::HEATING_GAS_CONSUMPTION_DHW) {
                    $bConsumptionSeen = true;
                    $this->handleConsumption($feature, 'dhwGazConsumption', 'oldConsoDhw', 'dhwGazHistorize', $facteurConversionGaz);
                } elseif ($feature["feature"] == self::HEATING_GAS_CONSUMPTION_HEATING) {
                    $bConsumptionSeen = true;
                    $this->handleConsumption($feature, 'heatingGazConsumption', 'oldConsoHeating', 'heatingGazHistorize', $facteurConversionGaz);
                } elseif ($feature["feature"] == self::HEATING_POWER_CONSUMPTION_TOTAL) {
                    $bConsumptionSeen = true;
                    $this->handleConsumption($feature, 'totalPowerConsumption', 'oldConsoPowerTotal', 'totalPowerHistorize');
                } elseif ($feature["feature"] == self::HEATING_POWER_CONSUMPTION_DHW) {
                    $bConsumptionSeen = true;
                    $this->handleConsumption($feature, 'dhwPowerConsumption', 'oldConsoPowerDhw', 'dhwPowerHistorize');
                } elseif ($feature["feature"] == self::HEATING_POWER_CONSUMPTION_HEATING) {
                    $bConsumptionSeen = true;
                    $this->handleConsumption($feature, 'heatingPowerConsumption', 'oldConsoPowerHeating', 'heatingPowerHistorize');
                }
                elseif ($feature["feature"] == self::HEATING_SERVICE_TIMEBASED) {
                    $val = $feature["properties"]["lastService"]["value"];
                    $val = substr($val, 0, 19);
                    $val = str_replace('T', ' ', $val);
                    $this->checkAndUpdateCmd('lastServiceDate', $val);
                    $val = $feature["properties"]["serviceIntervalMonths"]["value"];
                    $this->checkAndUpdateCmd('serviceInterval', $val);
                    $val = $feature["properties"]["activeMonthSinceLastService"]["value"];
                    $this->checkAndUpdateCmd('monthSinceService', $val);
                } elseif ($feature["feature"] == $this->buildFeatureBurner($deviceId, self::STATISTICS)) {
                    $val = $feature["properties"]["hours"]["value"];
                    $heatingBurnerHours = $val;
                    $this->checkAndUpdateCmd('heatingBurnerHours', $val);
                    $val = $feature["properties"]["starts"]["value"];
                    $heatingBurnerStarts = $val;
                    $this->checkAndUpdateCmd('heatingBurnerStarts', $val);
                } elseif ($feature["feature"] == $this->buildFeatureCompressor($deviceId, self::STATISTICS) && $feature["isEnabled"] == true) {
                    $val = $feature["properties"]["hours"]["value"];
                    $heatingCompressorHours = $val;
                    $this->checkAndUpdateCmd('heatingCompressorHours', $val);
                    $val = $feature["properties"]["starts"]["value"];
                    $heatingCompressorStarts = $val;
                    $this->checkAndUpdateCmd('heatingCompressorStarts', $val);
                } elseif ($feature["feature"] == $this->buildFeatureBurner($deviceId, self::MODULATION)) {
                    $val = $feature["properties"]["value"]["value"];
                    $this->checkAndUpdateCmd('heatingBurnerModulation', $val);
                } elseif ($feature["feature"] == self::HOLIDAY_PROGRAM) {
                    $start = str_replace('"', '', $feature["properties"]["start"]["value"]);
                    $end = str_replace('"', '', $feature["properties"]["end"]["value"]);
                    if ($feature["properties"]["active"]["value"] == true) {
                        $this->checkAndUpdateCmd('isScheduleHolidayProgram', 1);
                        $this->checkAndUpdateCmd('startHoliday', $start);
                        $this->checkAndUpdateCmd('endHoliday', $end);
                    } else {
                        $this->checkAndUpdateCmd('isScheduleHolidayProgram', 0);
                    }
                } elseif ($feature["feature"] == self::HOLIDAY_AT_HOME_PROGRAM) {
                    $start = str_replace('"', '', $feature["properties"]["start"]["value"]);
                    $end = str_replace('"', '', $feature["properties"]["end"]["value"]);
                    if ($feature["properties"]["active"]["value"] == true) {
                        $this->checkAndUpdateCmd('isScheduleHolidayAtHomeProgram', 1);
                        $this->checkAndUpdateCmd('startHolidayAtHome', $start);
                        $this->checkAndUpdateCmd('endHolidayAtHome', $end);
                    } else {
                        $this->checkAndUpdateCmd('isScheduleHolidayAtHomeProgram', 0);
                    }
                }
                log::add('viessmannIot', 'debug', 'feature  '.$feature["feature"]. ' value '.$val);
        }

        // gestion de la consommation 
        if (!$bConsumptionSeen) 
            $this->handleConsumptionSummary($features, $facteurConversionGaz);

        // récupération des erreurs de la chaudière
        $maintenant = time();
        $minute = date("i", $maintenant);
        if ($minute == 0 || $viessmannApi->getLogFeatures() == 'Oui') {
            $viessmannApi->getEvents();
            $events = $viessmannApi->getArrayEvents();
            $timeZone = new DateTimeZone('Europe/Warsaw');  // +2 hours

            foreach (array_reverse($events["data"]) as $event) {
                if ($event["eventType"] == "device-error") {
                    $dateTime = new DateTime(str_replace('T', ' ', substr($event['eventTimestamp'], 0, 19)) . ' GMT');
                    $dateTime->setTimeZone($timeZone);
                    $timeStamp = $dateTime->format('d/m/Y H:i:s');

                    $errorCode = $event['body']['errorCode'];
                    $errorDescription = str_replace(["'", '"'], ["\'", '\"'], $event['body']['equipmentType'] . ':' . $event['body']['errorDescription']);

                    if ($nbr < 10) {
                        $erreurs .= ($nbr > 0 ? ';' : '') . ($event['body']['active'] ? 'AC;' : 'IN;') . $timeStamp . ';' . $errorDescription;
                        if ($event['body']['active']) {
                            $erreurCourante = $errorCode;
                        } elseif ($erreurCourante == $errorCode) {
                            $erreurCourante = '';
                        }
                    }
                    $nbr++;
                    }
                }
        
            $this->checkAndUpdateCmd('errors', $erreurs);
            $this->checkAndUpdateCmd('currentError', $erreurCourante);
        }

        // récupération des températures
        if ($outsideTemperature == 99) {
            $outsideTemperature = jeedom::evaluateExpression($this->getConfiguration('temperature_exterieure'));
            if (!is_numeric($outsideTemperature)) {
                $outsideTemperature = 99;
            } else {
                $outsideTemperature = round($outsideTemperature, 1);
            }
        }

        if ($outsideTemperature != 99) {
            $this->checkAndUpdateCmd('outsideTemperature', $outsideTemperature);
        }

        if ($roomTemperature == 99) {
            $roomTemperature = jeedom::evaluateExpression($this->getConfiguration('temperature_interieure'));
            if (!is_numeric($roomTemperature)) {
            $roomTemperature = 99;
            } else {
            $roomTemperature = round($roomTemperature, 1);
            }
        }

        if ($roomTemperature != 99) {
            $this->checkAndUpdateCmd('roomTemperature', $roomTemperature);
        }

        // gestion des consignes
        if (($activeProgram === 'comfort') || ($activeProgram === 'comfortHeating')) {
            $this->checkAndUpdateCmd('programTemperature', $comfortProgramTemperature);
            $consigneTemperature = $comfortProgramTemperature;
        } elseif (($activeProgram === 'normal') || ($activeProgram === 'normalHeating')) {
            $this->checkAndUpdateCmd('programTemperature', $normalProgramTemperature);
            $consigneTemperature = $normalProgramTemperature;
        } elseif ($activeProgram === 'normalEnergySaving') {
            $this->checkAndUpdateCmd('programTemperature', $normalProgramTemperature);
            $consigneTemperature = $normalProgramTemperature;
        } else {
            $this->checkAndUpdateCmd('programTemperature', $reducedProgramTemperature);
            $consigneTemperature = $reducedProgramTemperature;
        }

        // calcul de la courbe de chauffe
        if (($consigneTemperature != 99) && ($slope != 99) && ($shift != 99)) {
            $curve = '';
            for ($ot = 25; $ot >= -20; $ot -= 5) {
                $deltaT = $ot - $consigneTemperature;
                $tempDepart = $consigneTemperature + $shift - $slope * $deltaT * (1.4347 + 0.021 * $deltaT + 247.9e-6 * $deltaT * $deltaT);
                if ($curve == '') {
                    $curve = round($tempDepart, 0);
                } else {
                    $curve .= ',' . round($tempDepart, 0);
                }
            }
            $this->checkAndUpdateCmd('curve', $curve);
        }

        $now = time();

        // Historisation temperatures
        //
        $dateCron = time();
        $dateCron = date('Y-m-d H:i:00', $dateCron);
        if (($roomTemperature != 99) && ($consigneTemperature != 99) && ($outsideTemperature != 99)) {
            $this->checkAndUpdateCmd('histoTemperatureInt', $roomTemperature, $dateCron);
            $this->checkAndUpdateCmd('histoTemperatureCsg', $consigneTemperature, $dateCron);
            $this->checkAndUpdateCmd('histoTemperatureExt', $outsideTemperature, $dateCron);
        }
        $outsideMinTemperature = $this->getCache('outsideMinTemperature', 99);
        $outsideMaxTemperature = $this->getCache('outsideMaxTemperature', -99);

        if ($this->getCache('oldJour', -1) != date("d", $now)) { // changement de jour
            $dateVeille = date('Y-m-d 00:00:00', time() - strtotime('-1 day'));

            if (($heatingBurnerHours != -1) && ($heatingBurnerStarts != -1)) {
                if (($oldHours = $this->getCache('oldHours', -1)) != -1) 
                    $this->checkAndUpdateCmd('heatingBurnerHoursPerDay', round($heatingBurnerHours - $oldHours, 1), $dateVeille);
                
                if (($oldStarts = $this->getCache('oldStarts', -1)) != -1) 
                    $this->checkAndUpdateCmd('heatingBurnerStartsPerDay', $heatingBurnerStarts - $oldStarts, $dateVeille);
                
                $this->setCache('oldHours', $heatingBurnerHours);
                $this->setCache('oldStarts', $heatingBurnerStarts);
            }

            if (($heatingCompressorHours != -1) && ($heatingCompressorStarts != -1)) {
                if (($oldHours = $this->getCache('oldHoursComp', -1)) != -1) 
                    $this->checkAndUpdateCmd('heatingCompressorHoursPerDay', round($heatingCompressorHours - $oldHours, 1), $dateVeille);
                
                if (($oldStarts = $this->getCache('oldStartsComp', -1)) != -1) 
                    $this->checkAndUpdateCmd('heatingCompressorStartsPerDay', $heatingCompressorStarts - $oldStarts, $dateVeille);
                
                $this->setCache('oldHoursComp', $heatingCompressorHours);
                $this->setCache('oldStartsComp', $heatingCompressorStarts);
            }

            if ($outsideMinTemperature != 99) 
                $this->checkAndUpdateCmd('outsideMinTemperature', $outsideMinTemperature, $dateVeille);
            if ($outsideMaxTemperature != -99) 
                $this->checkAndUpdateCmd('outsideMaxTemperature', $outsideMaxTemperature, $dateVeille);
            
            $outsideMinTemperature = 99;
            $this->setCache('outsideMinTemperature', $outsideMinTemperature);
            $outsideMaxTemperature = -99;
            $this->setCache('outsideMaxTemperature', $outsideMaxTemperature);

            $this->setCache('oldJour', date("d", $now));
        }

        if ($outsideTemperature != 99) {
            if ($outsideTemperature < $outsideMinTemperature) 
                $this->setCache('outsideMinTemperature', $outsideTemperature);
            if ($outsideTemperature > $outsideMaxTemperature) 
                $this->setCache('outsideMaxTemperature', $outsideTemperature);
        }

        $date = new DateTime();
        $date = $date->format('d-m-Y H:i:s');
        $this->checkAndUpdateCmd('refreshDate', $date);

        return;
    }


    private function handleConsumption($feature, $consumptionType, $oldConsoCacheKey, $historizeCmd, $conversionFactor = 1) {
        $periods = ['day', 'week', 'month', 'year'];
        $consumptions = [];

        foreach ($periods as $period) {
            $values = $feature["properties"][$period]['value'];
            $consumptions[$period] = array_map(function($value) use ($conversionFactor) {
                return $value * $conversionFactor;
            }, $values);
        }

        $this->checkAndUpdateCmd($consumptionType, $consumptions['day'][0]);

        $conso = $consumptions['day'][0];
        $oldConso = $this->getCache($oldConsoCacheKey, -1);
        if ($oldConso >= $conso) {
            $dateVeille = date('Y-m-d 00:00:00', strtotime('-1 day'));
            $this->checkAndUpdateCmd($historizeCmd, $consumptions['day'][1], $dateVeille);
        }
        $this->setCache($oldConsoCacheKey, $conso);

        foreach ($periods as $period) {
            $values = implode(',', array_reverse($consumptions[$period]));
            $this->checkAndUpdateCmd($consumptionType . ucfirst($period), $values);
        }
    }

    /**
     * Summary of handleConsumptionSummary
     * @param mixed $features
     * @param int $facteurConversionGaz
     * @return void
     */
    private function handleConsumptionSummary($features, $facteurConversionGaz) {
            $gasSummary = [
            'dayTotal' => 0, 'weekTotal' => 0, 'monthTotal' => 0, 'yearTotal' => 0,
            'dayHeating' => 0, 'weekHeating' => 0, 'monthHeating' => 0, 'yearHeating' => 0,
            'dayDhw' => 0, 'weekDhw' => 0, 'monthDhw' => 0, 'yearDhw' => 0
            ];

            $powerSummary = [
            'dayTotal' => 0, 'weekTotal' => 0, 'monthTotal' => 0, 'yearTotal' => 0,
            'dayHeating' => 0, 'weekHeating' => 0, 'monthHeating' => 0, 'yearHeating' => 0,
            'dayDhw' => 0, 'weekDhw' => 0, 'monthDhw' => 0, 'yearDhw' => 0
            ];

            foreach ($features["data"] as $feature) {
            $properties = $feature["properties"];
            switch ($feature["feature"]) {
                case self::HEATING_GAS_CONSUMPTION_SUMMARY_TOTAL:
                $gasSummary['dayTotal'] = $properties['currentDay']['value'];
                $gasSummary['weekTotal'] = $properties['lastSevenDays']['value'];
                $gasSummary['monthTotal'] = $properties['currentMonth']['value'];
                $gasSummary['yearTotal'] = $properties['currentYear']['value'];
                break;
                case self::HEATING_GAS_CONSUMPTION_SUMMARY_HEATING:
                $gasSummary['dayHeating'] = $properties['currentDay']['value'];
                $gasSummary['weekHeating'] = $properties['lastSevenDays']['value'];
                $gasSummary['monthHeating'] = $properties['currentMonth']['value'];
                $gasSummary['yearHeating'] = $properties['currentYear']['value'];
                break;
                case self::HEATING_GAS_CONSUMPTION_SUMMARY_DHW:
                $gasSummary['dayDhw'] = $properties['currentDay']['value'];
                $gasSummary['weekDhw'] = $properties['lastSevenDays']['value'];
                $gasSummary['monthDhw'] = $properties['currentMonth']['value'];
                $gasSummary['yearDhw'] = $properties['currentYear']['value'];
                break;
                case self::HEATING_POWER_CONSUMPTION_SUMMARY_TOTAL:
                $powerSummary['dayTotal'] = $properties['currentDay']['value'];
                $powerSummary['weekTotal'] = $properties['lastSevenDays']['value'];
                $powerSummary['monthTotal'] = $properties['currentMonth']['value'];
                $powerSummary['yearTotal'] = $properties['currentYear']['value'];
                break;
                case self::HEATING_POWER_CONSUMPTION_SUMMARY_HEATING:
                $powerSummary['dayHeating'] = $properties['currentDay']['value'];
                $powerSummary['weekHeating'] = $properties['lastSevenDays']['value'];
                $powerSummary['monthHeating'] = $properties['currentMonth']['value'];
                $powerSummary['yearHeating'] = $properties['currentYear']['value'];
                break;
                case self::HEATING_POWER_CONSUMPTION_SUMMARY_DHW:
                $powerSummary['dayDhw'] = $properties['currentDay']['value'];
                $powerSummary['weekDhw'] = $properties['lastSevenDays']['value'];
                $powerSummary['monthDhw'] = $properties['currentMonth']['value'];
                $powerSummary['yearDhw'] = $properties['currentYear']['value'];
                break;
            }
            }

            foreach (['Total', 'Heating', 'Dhw'] as $type) {
                foreach (['day', 'week', 'month', 'year'] as $period) {
                    if ($gasSummary["{$period}{$type}"] == 0) {
                    $gasSummary["{$period}{$type}"] = $gasSummary["{$period}Dhw"] + $gasSummary["{$period}Heating"];
                    }
                    if ($powerSummary["{$period}{$type}"] == 0) {
                    $powerSummary["{$period}{$type}"] = $powerSummary["{$period}Dhw"] + $powerSummary["{$period}Heating"];
                    }
                }
            }

            foreach (['Dhw', 'Heating', 'Total'] as $type) {
                foreach (['day', 'week', 'month', 'year'] as $period) {
                    $gasSummary["{$period}{$type}"] *= $facteurConversionGaz;
                }
            }

            foreach (['Dhw', 'Heating', 'Total'] as $type) {
                $conso = $gasSummary["day{$type}"];
                $oldConso = $this->getCache("oldConso{$type}", -1);
                if ($oldConso >= $conso) {
                    $dateVeille = date('Y-m-d 00:00:00', strtotime('-1 day'));
                    $this->checkAndUpdateCmd("{$type}GazHistorize", $conso, $dateVeille);
                }
                $this->setCache("oldConso{$type}", $conso);

                $conso = $powerSummary["day{$type}"];
                $oldConso = $this->getCache("oldConsoPower{$type}", -1);
                if ($oldConso >= $conso) {
                    $dateVeille = date('Y-m-d 00:00:00', strtotime('-1 day'));
                    $this->checkAndUpdateCmd("{$type}PowerHistorize", $conso, $dateVeille);
                }
                $this->setCache("oldConsoPower{$type}", $conso);
            }

            foreach (['Total', 'Dhw', 'Heating'] as $type) {
                $this->checkAndUpdateCmd("total{$type}Consumption", $gasSummary["day{$type}"]);
                $this->checkAndUpdateCmd("totalPowerConsumption", $powerSummary["day{$type}"]);
                }

                foreach (['day', 'week', 'month', 'year'] as $period) {
                    foreach (['Dhw', 'Heating', 'Total'] as $type) {
                        $this->checkAndUpdateCmd("{$type}GazConsumption{$period}", $gasSummary["{$period}{$type}"]);
                        $this->checkAndUpdateCmd("{$type}PowerConsumption{$period}", $powerSummary["{$period}{$type}"]);
                    }
            }
    }

    public static function periodique()
    {
        log::add('viessmannIot', 'debug', 'Rafraichissement périodique');

        $credentials = [];
        foreach (self::byType('viessmannIot') as $viessmann) {
            if ($viessmann->getIsEnable() == 1) {
                $userName = trim($viessmann->getConfiguration('userName', ''));
                $password = trim($viessmann->getConfiguration('password', ''));
            }
            $credentials[] = ['userName' => $userName, 'password' => $password];
        }
        if (empty($credentials)) {
            return;
        }

        $uniqueCredentials = array_unique($credentials, SORT_REGULAR);
        if (count($uniqueCredentials) === 1) {
            $viessmannApi = null;
            foreach (self::byType('viessmannIot') as $viessmann) {
                if ($viessmann->getIsEnable() == 1) {
                    if ($viessmannApi === null) {
                        $viessmannApi = $viessmann->callViessmannAPI();
                    }
                    if ($viessmannApi !== null) {
                        $viessmann->rafraichir($viessmannApi);
                    }
                }
            }
            unset($viessmannApi);
        } else {
            foreach (self::byType('viessmannIot') as $viessmann) {
                if ($viessmann->getIsEnable() == 1) {
                    $viessmannApi = $viessmann->callViessmannAPI();
                    if ($viessmannApi !== null) {
                        $viessmann->rafraichir($viessmannApi);
                        unset($viessmannApi);
                    }
                }
            }
        }
    }

    // Set Dhw Temperature
    //
    public function setDhwTemperature($temperature)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{\"temperature\": $temperature}";
        $viessmannApi->setFeature(self::DHW_TEMPERATURE, "setTargetTemperature", $data);

        unset($viessmannApi);
    }

    // Set Mode
    //
    public function setMode($mode)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{\"mode\":\"" . $mode . "\"}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::ACTIVE_MODE), "setMode", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'activeMode')->event($mode);
    }

    // Set Dhw Mode
    //
    public function setDhwMode($mode)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{\"mode\":\"" . $mode . "\"}";
        $viessmannApi->setFeature(self::ACTIVE_DHW_MODE, "setMode", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('activeDhwMode', $mode);
    }

    // Set Comfort Program Temperature
    //
    public function setComfortProgramTemperature($temperature)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);
        $program = $this->getConfiguration('comfortProgram', '');
        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{\"targetTemperature\": $temperature}";
        $viessmannApi->setFeature($program, "setTemperature", $data);
        unset($viessmannApi);
        $obj = $this->getCmd(null, 'activeProgram');
        $activeProgram = '';
        if (is_object($obj)) 
            $activeProgram = $obj->execCmd();
        if ($activeProgram === 'comfort') 
            $this->checkAndUpdateCmd('programTemperature', $temperature);
    }

    // Set Normal Program Temperature
    //
    public function setNormalProgramTemperature($temperature)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $program = $this->getConfiguration('normalProgram', '');

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{\"targetTemperature\": $temperature}";
        $viessmannApi->setFeature($program, "setTemperature", $data);

        unset($viessmannApi);

        $obj = $this->getCmd(null, 'activeProgram');
        $activeProgram = '';
        if (is_object($obj)) {
            $activeProgram = $obj->execCmd();
        }
        if ($activeProgram === 'normal') {
            $this->checkAndUpdateCmd('programTemperature', $temperature);
        }
    }

    // Set Reduced Program Temperature
    //
    public function setReducedProgramTemperature($temperature)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $program = $this->getConfiguration('reducedProgram', '');

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{\"targetTemperature\": $temperature}";
        $viessmannApi->setFeature($program, "setTemperature", $data);

        unset($viessmannApi);
        $obj = $this->getCmd(null, 'activeProgram');
        $activeProgram = '';
        if (is_object($obj)) {
            $activeProgram = $obj->execCmd();
        }
        if (($activeProgram !== 'comfort') && ($activeProgram !== 'normal')) {
            $this->checkAndUpdateCmd('programTemperature', $temperature);
        }
    }

    // Start One Time Dhw Charge
    //
    public function startOneTimeDhwCharge()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature(self::HEATING_DHW_ONETIMECHARGE, "activate", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isOneTimeDhwCharge', 1);
    }

    // Stop One Time Dhw Charge
    //
    public function stopOneTimeDhwCharge()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature(self::HEATING_DHW_ONETIMECHARGE, "deactivate", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isOneTimeDhwCharge', 0);
    }

    // Activate Comfort Program
    //
    public function activateComfortProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $program = $this->getConfiguration('comfortProgram', '');

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($program, "activate", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isActivateComfortProgram', 1);
    }

    // deActivate Comfort Program
    //
    public function deActivateComfortProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $program = $this->getConfiguration('comfortProgram', '');

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($program, "deactivate", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isActivateComfortProgram', 0);
    }
    // Activate Eco Program
    //
    public function activateEcoProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::ECO_PROGRAM), "activate", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isActivateEcoProgram', 1);
    }

    // deActivate Eco Program
    //
    public function deActivateEcoProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::ECO_PROGRAM), "deactivate", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isActivateEcoProgram', 0);
    }

    // Activate Last Schedule
    //
    public function activateLastSchedule()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::FORCED_LAST_FROM_SCHEDULE), "activate", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isActivateLastSchedule', 1);
    }

    // deActivate Last Schedule
    //
    public function deActivateLastSchedule()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::FORCED_LAST_FROM_SCHEDULE), "deactivate", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isActivateLastSchedule', 0);
    }

    // Set Slope
    //
    public function setSlope($slope)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $obj = $this->getCmd(null, 'shift');
        $shift = $obj->execCmd();

        $data = "{\"shift\":" . $shift . ",\"slope\":" . round($slope, 1) . "}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::HEATING_CURVE), "setCurve", $data);

        unset($viessmannApi);
    }

    // Set Shift
    //
    public function setShift($shift)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $obj = $this->getCmd(null, 'slope');
        $slope = $obj->execCmd();

        $data = "{\"shift\":" . $shift . ",\"slope\":" . round($slope, 1) . "}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::HEATING_CURVE), "setCurve", $data);

        unset($viessmannApi);
    }

    // Schedule Holiday Program
    //
    public function scheduleHolidayProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $obj = $this->getCmd(null, 'startHoliday');
        $startHoliday = $obj->execCmd();
        if ($this->validateDate($startHoliday, 'Y-m-d') == false) {
            throw new Exception(__('Date de début invalide', __FILE__));
        }

        $obj = $this->getCmd(null, 'endHoliday');
        $endHoliday = $obj->execCmd();
        if ($this->validateDate($endHoliday, 'Y-m-d') == false) {
            throw new Exception(__('Date de fin invalide', __FILE__));
        }

        if ($startHoliday > $endHoliday) {
            throw new Exception(__('Date de début postérieure à la date de fin', __FILE__));
        }

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{\"start\":\"" . $startHoliday . "\",\"end\":\"" . $endHoliday . "\"}";
        $viessmannApi->setFeature(self::HOLIDAY_PROGRAM, "schedule", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isScheduleHolidayProgram', 1);
    }

    // Unschedule Holiday Program
    //
    public function unscheduleHolidayProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature(self::HOLIDAY_PROGRAM, "unschedule", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isScheduleHolidayProgram', 0);
    }

    // Schedule Holiday At Home Program
    //
    public function scheduleHolidayAtHomeProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $obj = $this->getCmd(null, 'startHolidayAtHome');
        $startHolidayAtHome = $obj->execCmd();
        if ($this->validateDate($startHolidayAtHome, 'Y-m-d') == false) {
            throw new Exception(__('Date de début invalide', __FILE__));
        }

        $obj = $this->getCmd(null, 'endHolidayAtHome');
        $endHolidayAtHome = $obj->execCmd();
        if ($this->validateDate($endHolidayAtHome, 'Y-m-d') == false) {
            throw new Exception(__('Date de fin invalide', __FILE__));
        }

        if ($startHolidayAtHome > $endHolidayAtHome) {
            throw new Exception(__('Date de début postérieure à la date de fin', __FILE__));
        }

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{\"start\":\"" . $startHolidayAtHome . "\",\"end\":\"" . $endHolidayAtHome . "\"}";
        $viessmannApi->setFeature(self::HOLIDAY_AT_HOME_PROGRAM, "schedule", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isScheduleHolidayAtHomeProgram', 1);
    }

    // Unschedule Holiday At Home Program
    //
    public function unscheduleHolidayAtHomeProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature(self::HOLIDAY_AT_HOME_PROGRAM, "unschedule", $data);
        unset($viessmannApi);

        $this->checkAndUpdateCmd('isScheduleHolidayAtHomeProgram', 0);
    }

    //
    //
    public function setHeatingSchedule($titre, $message)
    {
        $obj = $this->getCmd(null, 'heatingSchedule');
        if (!is_object($obj)) {
            return ('Object non trouvé');
        }
        $str = $obj->execCmd();
        $elements = explode(';', $str);
        if (count($elements) != 7) {
            return ('Nombre d\'élements <> 7');
        }

        $jours = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $schedule = [];

        for ($i = 0; $i < 7; $i++) {
            $subElements = ($titre == $jours[$i]) ? explode(',', $message) : explode(',', $elements[$i]);
            $n = count($subElements);
            if ($n % 3 != 0) {
            return 'Nombre de sous éléments <> 3';
            }

            $daySchedule = [];
            for ($j = 0; $j < $n; $j += 3) {
            $mode = $subElements[$j] == 'n' ? 'normal' : 'comfort';
            $daySchedule[] = [
                'mode' => $mode,
                'start' => $subElements[$j + 1],
                'end' => $subElements[$j + 2],
                'position' => $j / 3
            ];
            }
            $schedule[$jours[$i]] = $daySchedule;
        }

        $commande = json_encode(['newSchedule' => $schedule]);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->callViessmannAPI()) === null) return;

        $viessmannApi->setFeature($this->buildFeature($circuitId, self::HEATING_SCHEDULE), "setSchedule", $commande);
        unset($viessmannApi);

        return ($commande);
    }

    //
    //
    public function setDhwSchedule($titre, $message)
    {
        $obj = $this->getCmd(null, 'dhwSchedule');
        if (!is_object($obj)) {
            return ('Object non trouvé');
        }
        $str = $obj->execCmd();
        $elements = explode(';', $str);
        if (count($elements) != 7) {
            return ('Nombre d\'élements <> 7');
        }

        $jours = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        $schedule = [];

        for ($i = 0; $i < 7; $i++) {
            $subElements = ($titre == $jours[$i]) ? explode(',', $message) : explode(',', $elements[$i]);
            $n = count($subElements);
            if ($n % 3 != 0) {
            return 'Nombre de sous éléments <> 3';
            }

            $daySchedule = [];
            for ($j = 0; $j < $n; $j += 3) {
            $daySchedule[] = [
                'mode' => 'on',
                'start' => $subElements[$j + 1],
                'end' => $subElements[$j + 2],
                'position' => $j / 3
            ];
            }
            $schedule[$jours[$i]] = $daySchedule;
        }

        $commande = json_encode(['newSchedule' => $schedule]);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->callViessmannAPI()) === null) {
            return;
        }

        $viessmannApi->setFeature(self::HEATING_DHW_SCHEDULE, "setSchedule", $commande);
        unset($viessmannApi);

        return ($commande);
    }

    public static function cron()
    {
        log::add('viessmannIot', 'debug', "cron direct called");
        $maintenant = time();
        $minute = date("i", $maintenant);
        if (($minute % 2) == 0) {
            log::add('viessmannIot', 'debug', 'Cron toutes les 2 minutes');
            self::periodique();
        }
    }

    // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
    //
    public function postSave()
    {
        log::add('viessmannIot', 'debug', "postSave called");
        $commands = [
            'refresh' => ['name' => __('Rafraichir', __FILE__), 'type' => 'action', 'subType' => 'other'],
            'refreshDate' => ['name' => __('Date rafraichissement', __FILE__), 'type' => 'info', 'subType' => 'string', 'isVisible' => 1, 'isHistorized' => 0],
            'outsideTemperature' => ['name' => __('Température extérieure', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'unite' => '°C', 'isVisible' => 1, 'isHistorized' => 0],
            'outsideMinTemperature' => ['name' => __('Température extérieure minimum', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'unite' => '°C', 'isVisible' => 1, 'isHistorized' => 1],
            'outsideMaxTemperature' => ['name' => __('Température extérieure maximum', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'unite' => '°C', 'isVisible' => 1, 'isHistorized' => 1],
            'roomTemperature' => ['name' => __('Température intérieure', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0],
            'programTemperature' => ['name' => __('Consigne radiateurs', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'unite' => '°C', 'isVisible' => 1, 'isHistorized' => 0],
            'totalGazConsumption' => ['name' => __('Consommation gaz', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0],
            'dhwGazConsumption' => ['name' => __('Consommation gaz eau chaude', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0],
            'heatingGazConsumption' => ['name' => __('Consommation gaz chauffage', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0],
            'heatingPowerConsumption' => ['name' => __('Consommation électrique chauffage', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0],
            'dhwPowerConsumption' => ['name' => __('Consommation électrique eau chaude', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0],
            'totalPowerConsumption' => ['name' => __('Consommation électrique totale', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 0],
            'curve' => ['name' => __('Courbe de chauffe', __FILE__), 'type' => 'info', 'subType' => 'string', 'isVisible' => 1, 'isHistorized' => 0],
            'totalGazHistorize' => ['name' => __('Historisation gaz', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'heatingGazHistorize' => ['name' => __('Historisation gaz chauffage', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'dhwGazHistorize' => ['name' => __('Historisation gaz eau chaude', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'heatingPowerHistorize' => ['name' => __('Historisation électricité chauffage', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'dhwPowerHistorize' => ['name' => __('Historisation électricité eau chaude', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'totalPowerHistorize' => ['name' => __('Historisation électricité totale', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'histoTemperatureInt' => ['name' => __('Historique température intérieure', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'histoTemperatureCsg' => ['name' => __('Historique température consigne', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'histoTemperatureExt' => ['name' => __('Historique température extérieure', __FILE__), 'type' => 'info', 'subType' => 'numeric', 'isVisible' => 1, 'isHistorized' => 1],
            'errors' => ['name' => __('Erreurs', __FILE__), 'type' => 'info', 'subType' => 'string', 'isVisible' => 1, 'isHistorized' => 0],
            'currentError' => ['name' => __('Erreur courante', __FILE__), 'type' => 'info', 'subType' => 'string', 'isVisible' => 1, 'isHistorized' => 0],
            'ventilation' => ['name' => __('Ventilation', __FILE__), 'type' => 'info', 'subType' => 'string', 'isVisible' => 1, 'isHistorized' => 0],
        ];

        foreach ($commands as $logicalId => $command) {
            $obj = $this->getCmd($command['type'], $logicalId);
            if (!is_object($obj)) {
                log::add(  'viessmannIot', 'debug', "postSave called, create command $logicalId");
                $obj = new viessmannIotCmd();
                $obj->setName($command['name']);
                if (isset($command['unite'])) {
                    $obj->setUnite($command['unite']);
                }
                if (isset($command['isVisible'])) {
                    $obj->setIsVisible($command['isVisible']);
                }
                if (isset($command['isHistorized'])) {
                    $obj->setIsHistorized($command['isHistorized']);
                }
                $obj->setEqLogic_id($this->getId());
                $obj->setType($command['type']);
                $obj->setSubType($command['subType']);
                $obj->setLogicalId($logicalId);
                $obj->save();
            }
            else
            {
                log::add(  'viessmannIot', 'debug', "postSave called, command $logicalId already exists");
            }
        }
        
    }

    // Permet de modifier l'affichage du widget (également utilisable par les commandes)
    //
    public function toHtml($_version = 'dashboard')
    {
        log::add('viessmannIot', 'debug', "toHtml called");
        $isWidgetPlugin = $this->getConfiguration('isWidgetPlugin');
        $displayWater = $this->getConfiguration('displayWater', '1');
        $displayGas = $this->getConfiguration('displayGas', '1');
        $displayPower = $this->getConfiguration('displayPower', '1');
        $circuitName = $this->getConfiguration('circuitName', 'Radiateurs');
        $uniteGaz = $this->getConfiguration('uniteGaz', 'm3');

        if (!$isWidgetPlugin) {
            log::add('viessmannIot', 'debug', "toHtml called, not widget plugin");
            return eqLogic::toHtml($_version);
        }

        $replace = $this->preToHtml($_version);
        if (!is_array($replace)) {
            log::add('viessmannIot', 'debug', "toHtml called, preToHtml not array");
            return $replace;
        };
        log::add('viessmannIot', 'debug', "toHtml called, preToHtml array");
        $version = jeedom::versionAlias($_version);

        $obj = $this->getCmd(null, 'refresh');
        if (is_object($obj)) {
            $replace["#idRefresh#"] = $obj->getId();
        };

        $this->buildhtml($replace, 'isHeatingBurnerActive', 'idIsHeatingBurnerActive');
        $this->buildhtml($replace, 'isHeatingCompressorActive', 'idIsHeatingCompressorActive');
        $this->buildhtml($replace, 'currentError', 'idCurrentError', true, '');
        $this->buildhtml($replace, 'outsideTemperature', 'idOutsideTemperature', false);
        $this->buildhtml($replace, 'activeProgram', 'idActiveProgram');

        $replace["#circuitName#"] = $circuitName;
        $replace["#displayWater#"] = $displayWater;
        $replace["#displayGas#"] = $displayGas;
        $replace["#displayPower#"] = $displayPower;
        $replace["#uniteGaz#"] = $uniteGaz;

        $this->buildhtml($replace, 'roomTemperature', 'idRoomTemperature',false);
        $this->buildhtml($replace, 'secondaryCircuitTemperature', 'idSecondaryCircuitTemperature',false);
        $this->buildhtml($replace, 'programTemperature', 'idProgramTemperature',false);
        $this->buildhtml($replace, 'hotWaterStorageTemperature', 'idHotWaterStorageTemperature',true, 99);

        $obj = $this->getCmd(null, 'dhwTemperature');
        if (is_object($obj)) {
            $replace["#dhwTemperature#"] = $obj->execCmd();
            $replace["#idDhwTemperature#"] = $obj->getId();
            $replace["#minDhw#"] = $obj->getConfiguration('minValue');
            $replace["#maxDhw#"] = $obj->getConfiguration('maxValue');
            $replace["#stepDhw#"] = 1;
            $obj = $this->getCmd(null, 'dhwSlider');
            $replace["#idDhwSlider#"] = $obj->getId();
        } else {
            $replace["#dhwTemperature#"] = 99;
            $replace["#idDhwTemperature#"] = "#idDhwTemperature#";
        }

        $this->buildhtml($replace, 'dhwGazConsumption', 'idDhwGazConsumption',false);
        $this->buildhtml($replace, 'heatingGazConsumption', 'idHeatingGazConsumption',false);
        $this->buildhtml($replace, 'dhwPowerConsumption', 'idDhwPowerConsumption',false);
        $this->buildhtml($replace, 'heatingPowerConsumption', 'idHeatingPowerConsumption',false);
        $this->buildhtml($replace, 'refreshDate', 'idRefreshDate',false);
        $this->buildhtml($replace, 'heatingBurnerHours', 'idHeatingBurnerHours',true, -1);
        $this->buildhtml($replace, 'heatingBurnerHoursPerDay', 'idHeatingBurnerHoursPerDay',true, -1);
        $this->buildhtml($replace, 'heatingBurnerStarts', 'idHeatingBurnerStarts',true, -1);
        $this->buildhtml($replace, 'heatingBurnerStartsPerDay', 'idHeatingBurnerStartsPerDay',true, -1);
        $this->buildhtml($replace, 'heatingBurnerModulation', 'idHeatingBurnerModulation',true, -1);
        $this->buildhtml($replace, 'heatingCompressorHours', 'idHeatingCompressorHours',true, -1);
        $this->buildhtml($replace, 'heatingCompressorHoursPerDay', 'idHeatingCompressorHoursPerDay',true, -1);
        $this->buildhtml($replace, 'heatingCompressorStarts', 'idHeatingCompressorStarts',true, -1);
        $this->buildhtml($replace, 'heatingCompressorStartsPerDay', 'idHeatingCompressorStartsPerDay',true, -1);

        $obj = $this->getCmd(null, 'slope');
        if (is_object($obj)) {
            $replace["#slope#"] = $obj->execCmd();
            $replace["#idSlope#"] = $obj->getId();
            $replace["#minSlope#"] = $obj->getConfiguration('minValue');
            $replace["#maxSlope#"] = $obj->getConfiguration('maxValue');
            $replace["#stepSlope#"] = 0.1;
            $obj = $this->getCmd(null, 'slopeSlider');
            $replace["#idSlopeSlider#"] = $obj->getId();
        } else {
            $replace["#slope#"] = 99;
            $replace["#idSlope#"] = "#idSlope#";
        }

        $obj = $this->getCmd(null, 'shift');
        if (is_object($obj)) {
            $replace["#shift#"] = $obj->execCmd();
            $replace["#idShift#"] = $obj->getId();
            $replace["#minShift#"] = $obj->getConfiguration('minValue');
            $replace["#maxShift#"] = $obj->getConfiguration('maxValue');
            $replace["#stepShift#"] = 1;
            $obj = $this->getCmd(null, 'shiftSlider');
            $replace["#idShiftSlider#"] = $obj->getId();
        } else {
            $replace["#shift#"] = 99;
            $replace["#idShift#"] = "#idShift#";
        }

        $this->buildhtml($replace, 'pressureSupply', 'idPressureSupply',true, 99);
        $this->buildhtml($replace, 'solarTemperature', 'idSolarTemperature', true, 99);
        $this->buildhtml($replace, 'lastServiceDate', 'idLastServiceDate', true);
        $this->buildhtml($replace, 'serviceInterval', 'idServiceInterval', true, 99);
        $this->buildhtml($replace, 'monthSinceService', 'idMonthSinceService', true, 99);
        $this->buildhtml($replace, 'errors', 'idErrors', true);       

        $obj = $this->getCmd(null, 'activeMode');
        if (is_object($obj)) {
            $replace["#activeMode#"] = $obj->execCmd();
            $replace["#idActiveMode#"] = $obj->getId();
            $this->buildhtmlId($replace, 'modeStandby', 'idModeStandby','??');
            $this->buildhtmlId($replace, 'modeAuto', 'idModeAuto','??');
            $this->buildhtmlId($replace, 'modeHeating', 'idModeHeating','??');
            $this->buildhtmlId($replace, 'modeCooling', 'idModeCooling','??');
            $this->buildhtmlId($replace, 'modeHeatingCooling', 'idModeHeatingCooling','??');
            $this->buildhtmlId($replace, 'modeTestMode', 'idModeTestMode','??');
            $this->buildhtmlId($replace, 'modeDhw', 'idModeDhw','??');
            $this->buildhtmlId($replace, 'modeDhwAndHeating', 'idModeDhwAndHeating','??');
        } else {
            $replace["#activeMode#"] = '??';
            $replace["#idActiveMode#"] = "#idActiveMode#";
        }

        $obj = $this->getCmd(null, 'activeDhwMode');
        if (is_object($obj)) {
            $replace["#activeDhwMode#"] = $obj->execCmd();
            $replace["#idActiveDhwMode#"] = $obj->getId();

            $this->buildhtmlId($replace, 'modeDhwBalanced', 'idModeDhwBalanced','??');
            $this->buildhtmlId($replace, 'modeDhwComfort', 'idModeDhwComfort','??');
            $this->buildhtmlId($replace, 'modeDhwEco', 'idModeDhwEco','??');
            $this->buildhtmlId($replace, 'modeDhwOff', 'idModeDhwOff','??');    
        } else {
            $replace["#activeDhwMode#"] = '??';
            $replace["#idActiveDhwMode#"] = "#idActiveDhwMode#";
        }

        $obj = $this->getCmd(null, 'comfortProgramTemperature');
        if (is_object($obj)) {
            $replace["#comfortProgramTemperature#"] = $obj->execCmd();
            $replace["#idComfortProgramTemperature#"] = $obj->getId();
            $replace["#minComfort#"] = $obj->getConfiguration('minValue');
            $replace["#maxComfort#"] = $obj->getConfiguration('maxValue');
            $replace["#stepComfort#"] = 1;
            $obj = $this->getCmd(null, 'comfortProgramSlider');
            $replace["#idComfortProgramSlider#"] = $obj->getId();
        } else {
            $replace["#comfortProgramTemperature#"] = 99;
            $replace["#idComfortProgramTemperature#"] = "#idComfortProgramTemperature#";
        }
        $obj = $this->getCmd(null, 'normalProgramTemperature');
        if (is_object($obj)) {
            $replace["#normalProgramTemperature#"] = $obj->execCmd();
            $replace["#idNormalProgramTemperature#"] = $obj->getId();
            $replace["#minNormal#"] = $obj->getConfiguration('minValue');
            $replace["#maxNormal#"] = $obj->getConfiguration('maxValue');
            $replace["#stepNormal#"] = 1;
            $obj = $this->getCmd(null, 'normalProgramSlider');
            $replace["#idNormalProgramSlider#"] = $obj->getId();
        } else {
            $replace["#normalProgramTemperature#"] = 99;
            $replace["#idNormalProgramTemperature#"] = "#idNormalProgramTemperature#";
        }
        $obj = $this->getCmd(null, 'reducedProgramTemperature');
        if (is_object($obj)) {
            $replace["#reducedProgramTemperature#"] = $obj->execCmd();
            $replace["#idReducedProgramTemperature#"] = $obj->getId();
            $replace["#minReduced#"] = $obj->getConfiguration('minValue');
            $replace["#maxReduced#"] = $obj->getConfiguration('maxValue');
            $replace["#stepReduced#"] = 1;
            $obj = $this->getCmd(null, 'reducedProgramSlider');
            $replace["#idReducedProgramSlider#"] = $obj->getId();
        } else {
            $replace["#reducedProgramTemperature#"] = 99;
            $replace["#idReducedProgramTemperature#"] = "#idReducedProgramTemperature#";
        }

        $this->buildhtml($replace, 'supplyProgramTemperature', 'idSupplyProgramTemperature',true, 99);
        $this->buildhtml($replace, 'boilerTemperature', 'idBoilerTemperature',true, 99);
        $this->buildhtml($replace, 'boilerTemperatureMain', 'idBoilerTemperatureMain',true, 99);
        $this->buildhtml($replace, 'frostProtection', 'idFrostProtection',true, '??');
        $this->buildhtml($replace, 'pumpStatus', 'idPumpStatus',true, '??');
        $this->buildhtml($replace, 'heatingSchedule', 'idHeatingSchedule',true);
        $this->buildhtml($replace, 'dhwSchedule', 'idDhwSchedule',true);

        $obj = $this->getCmd(null, 'setHeatingSchedule');
        if (is_object($obj)) {
            $replace["#idSetHeatingSchedule#"] = $obj->getId();
        } else {
            $replace["#idSetHeatingSchedule#"] = '#idSetHeatingSchedule#';
        }

        $obj = $this->getCmd(null, 'setDhwSchedule');
        if (is_object($obj)) {
            $replace["#idSetDhwSchedule#"] = $obj->getId();
        } else {
            $replace["#idSetDhwSchedule#"] = '#idSetDhwSchedule#';
        }

        $this->buildhtml($replace, 'heatingGazConsumptionDay', 'idHeatingGazConsumptionDay',true);
        $this->buildhtml($replace, 'dhwGazConsumptionDay', 'idDhwGazConsumptionDay',true);
        $str1=$this->buildhtml($replace, 'totalGazConsumptionDay', 'idTotalGazConsumptionDay',true);

        $jours = array("Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim");

        $maintenant = time();
        $jour = date("N", $maintenant) - 1;
        $joursSemaine = '';
        $n = substr_count($str1, ",") + 1;
        $joursSemaine = implode(',', array_map(function($j) use ($jours) {
            return "'" . $jours[$j] . "'";
        }, range($jour, $jour - $n + 1, -1)));
        $replace["#joursSemaine#"] = $joursSemaine;

        $this->buildhtml($replace, 'heatingGazConsumptionWeek', 'idHeatingGazConsumptionWeek',true);
        $this->buildhtml($replace, 'dhwGazConsumptionWeek', 'idDhwGazConsumptionWeek',true);
        $str2 = $this->buildhtml($replace, 'totalGazConsumptionWeek', 'idTotalGazConsumptionWeek',true);

        $maintenant = time();
        $semaine = date("W", $maintenant);
        $semaines = '';
        $n = substr_count($str2, ",") + 1;
        $semaines = implode(',', array_map(function($i) use (&$semaine, &$maintenant) {
            $week = "'" . $semaine . "'";
            $maintenant -= 7 * 24 * 60 * 60;
            $semaine = date("W", $maintenant);
            return $week;
        }, range(0, $n - 1)));
        $replace["#semaines#"] = $semaines;

        $this->buildhtml($replace, 'heatingGazConsumptionMonth', 'idHeatingGazConsumptionMonth',true);
        $this->buildhtml($replace, 'dhwGazConsumptionMonth', 'idDhwGazConsumptionMonth',true);
        $str3 = $this->buildhtml($replace, 'totalGazConsumptionMonth', 'idTotalGazConsumptionMonth',true);

        $libMois = ["Janv", "Févr", "Mars", "Avr", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"];

        $maintenant = time();
        $mois = date("m", $maintenant) - 1;
        $n = substr_count($str3, ",") + 1;
        $moisS = implode(',', array_map(function($i) use (&$mois, $libMois) {
            $moisS = "'" . $libMois[$mois] . "'";
            $mois = ($mois - 1 + 12) % 12;
            return $moisS;
        }, range(0, $n - 1)));
        $replace["#moisS#"] = $moisS;

        $this->buildhtml($replace, 'heatingGazConsumptionYear', 'idHeatingGazConsumptionYear',true);
        $this->buildhtml($replace, 'dhwGazConsumptionYear', 'idDhwGazConsumptionYear',true);
        $str4 = $this->buildhtml($replace, 'totalGazConsumptionYear', 'idTotalGazConsumptionYear',true);

        $maintenant = time();
        $annee = date("Y", $maintenant);
        $annees = implode(',', array_map(function($i) use (&$annee) {
            return "'" . $annee-- . "'";
        }, range(0, substr_count($str4, ",") + 1)));
        $replace["#annees#"] = $annees;

        $str5= $this->buildhtml($replace, 'heatingPowerConsumptionDay', 'idHeatingPowerConsumptionDay',true);

        $maintenant = time();
        $jour = date("N", $maintenant) - 1;
        $n = substr_count($str5, ",") + 1;
        $joursSemaine = implode(',', array_map(function($j) use ($jours) {
            return "'" . $jours[$j] . "'";
        }, range($jour, $jour - $n + 1, -1)));
        $replace["#elec_joursSemaine#"] = $joursSemaine;

        $str6 = $this->buildhtml($replace, 'heatingPowerConsumptionWeek', 'idHeatingPowerConsumptionWeek',true);

        $maintenant = time();
        $semaines = implode(',', array_map(function($i) use (&$semaine, &$maintenant) {
            $week = "'" . $semaine . "'";
            $maintenant -= 7 * 24 * 60 * 60;
            $semaine = date("W", $maintenant);
            return $week;
        }, range(0, substr_count($str6, ",") + 1)));
        $replace["#elec_semaines#"] = $semaines;

        $str7 = $this->buildhtml($replace, 'heatingPowerConsumptionMonth', 'idHeatingPowerConsumptionMonth',true);

        $maintenant = time();
        $mois = date("m", $maintenant) - 1;
        $n = substr_count($str7, ",") + 1;
        $moisS = implode(',', array_map(function($i) use (&$mois, $libMois) {
            $moisS = "'" . $libMois[$mois] . "'";
            $mois = ($mois - 1 + 12) % 12;
            return $moisS;
        }, range(0, $n - 1)));
        $replace["#elec_moisS#"] = $moisS;

        $str8 = $this->buildhtml($replace, 'heatingPowerConsumptionYear', 'idHeatingPowerConsumptionYear',true);

        $maintenant = time();
        $annee = date("Y", $maintenant);
        $n = substr_count($str8, ",") + 1;
        $annees = implode(',', array_map(function($i) use (&$annee) {
            return "'" . $annee-- . "'";
        }, range(0, $n - 1)));
        $replace["#elec_annees#"] = $annees;

        $obj = $this->getCmd(null, 'isOneTimeDhwCharge');
        if (is_object($obj)) {
            $replace["#isOneTimeDhwCharge#"] = $obj->execCmd();
            $replace["#idIsOneTimeDhwCharge#"] = $obj->getId();
            $this->buildhtmlId($replace, 'startOneTimeDhwCharge', 'idStartOneTimeDhwCharge');
            $this->buildhtmlId($replace, 'stopOneTimeDhwCharge', 'idStopOneTimeDhwCharge');
        } else {
            $replace["#isOneTimeDhwCharge#"] = -1;
            $replace["#idIsOneTimeDhwCharge#"] = "#idIsOneTimeDhwCharge#";
        }

        $obj = $this->getCmd(null, 'isActivateComfortProgram');
        if (is_object($obj)) {
            $replace["#isActivateComfortProgram#"] = $obj->execCmd();
            $replace["#idIsActivateComfortProgram#"] = $obj->getId();
            $this->buildhtmlId($replace, 'activateComfortProgram', 'idActivateComfortProgram');
            $this->buildhtmlId($replace, 'deActivateComfortProgram', 'idDeActivateComfortProgram');
        } else {
            $replace["#isActivateComfortProgram#"] = -1;
            $replace["#idIsActivateComfortProgram#"] = "#idIsActivateComfortProgram#";
        }

        $obj = $this->getCmd(null, 'isActivateEcoProgram');
        if (is_object($obj)) {
            $replace["#isActivateEcoProgram#"] = $obj->execCmd();
            $replace["#idIsActivateEcoProgram#"] = $obj->getId();
            $this->buildhtmlId($replace, 'activateEcoProgram', 'idActivateEcoProgram');
            $this->buildhtmlId($replace, 'deActivateEcoProgram', 'idDeActivateEcoProgram');
        } else {
            $replace["#isActivateEcoProgram#"] = -1;
            $replace["#idIsActivateEcoProgram#"] = "#idIsActivateEcoProgram#";
        }

        $obj = $this->getCmd(null, 'isActivateLastSchedule');
        if (is_object($obj)) {
            $replace["#isActivateLastSchedule#"] = $obj->execCmd();
            $replace["#idIsActivateLastSchedule#"] = $obj->getId();
            $this->buildhtmlId($replace, 'activateLastSchedule', 'idActivateLastSchedule');
            $this->buildhtmlId($replace, 'deActivateLastSchedule', 'idDeActivateLastSchedule');
        } else {
            $replace["#isActivateLastSchedule#"] = -1;
            $replace["#idIsActivateLastSchedule#"] = "#idIsActivateLastSchedule#";
        }

        $obj = $this->getCmd(null, 'isScheduleHolidayProgram');
        if (is_object($obj)) {
            $replace["#isScheduleHolidayProgram#"] = $obj->execCmd();
            $replace["#idIsScheduleHolidayProgram#"] = $obj->getId();

            $this->buildhtml($replace, 'startHoliday', 'idStartHoliday', false);
            $this->buildhtml($replace, 'endHoliday', 'idEndHoliday', false);
            $this->buildhtmlId($replace, 'startHolidayText', 'idStartHolidayText');
            $this->buildhtmlId($replace, 'endHolidayText', 'idEndHolidayText');
            $this->buildhtmlId($replace, 'scheduleHolidayProgram', 'idScheduleHolidayProgram');
            $this->buildhtmlId($replace, 'unscheduleHolidayProgram', 'idUnscheduleHolidayProgram');
        } else {
            $replace["#isScheduleHolidayProgram#"] = -1;
            $replace["#idIsScheduleHolidayProgram#"] = "#idIsScheduleHolidayProgram#";
        }

        $obj = $this->getCmd(null, 'isScheduleHolidayAtHomeProgram');
        if (is_object($obj)) {
            $replace["#isScheduleHolidayAtHomeProgram#"] = $obj->execCmd();
            $replace["#idIsScheduleHolidayAtHomeProgram#"] = $obj->getId();

            $this->buildhtml($replace, 'startHolidayAtHome', 'idStartHolidayAtHome', false);
            $this->buildhtml($replace, 'endHolidayAtHome', 'idEndHolidayAtHome', false);
            $this->buildhtmlId($replace, 'startHolidayAtHomeText', 'idStartHolidayAtHomeText');
            $this->buildhtmlId($replace, 'endHolidayAtHomeText', 'idEndHolidayAtHomeText');
            $this->buildhtmlId($replace, 'scheduleHolidayAtHomeProgram', 'idScheduleHolidayAtHomeProgram');
            $this->buildhtmlId($replace, 'unscheduleHolidayAtHomeProgram', 'idUnscheduleHolidayAtHomeProgram');
        } else {
            $replace["#isScheduleHolidayAtHomeProgram#"] = -1;
            $replace["#idIsScheduleHolidayAtHomeProgram#"] = "#idIsScheduleHolidayAtHomeProgram#";
        }

        $temp = implode(',', array_map(function ($ot) {
            return "'$ot'";
        }, range(25, -20, -5)));
        $replace["#range_temperature#"] = $temp;

        $this->buildhtml($replace, 'curve', 'idCurve',false);

        $temp = implode(',', array_map(function ($ot) {
            return "'$ot'";
        }, range(25, -20, -5)));
        $replace["#range_temp#"] = $temp;

        $startTime = date("Y-m-d H:i:s", strtotime('-8 days'));
        $endTime = date("Y-m-d H:i:s", time());

        $outsideMinTemperature = $this->getCache('outsideMinTemperature', -1);
        $outsideMaxTemperature = $this->getCache('outsideMaxTemperature', 1);

        $listeMinTemp = array_fill(0, 8, -99);
        $listeMaxTemp = array_fill(0, 8, 99);

        $listeMinTemp[7] = $outsideMinTemperature;
        $listeMaxTemp[7] = $outsideMaxTemperature;

        $cmd = $this->getCmd(null, 'outsideMinTemperature');
        if (is_object($cmd)) {
            $histoGraphe = $cmd->getHistory($startTime, $endTime);
            foreach ($histoGraphe as $row) {
                $value = round($row->getValue(), 1);
                $i = 7 - floor((time() - strtotime($row->getDatetime())) / (24 * 60 * 60));
                $listeMinTemp[$i] = $value;
            }
        }
        $cmd = $this->getCmd(null, 'outsideMaxTemperature');
        if (is_object($cmd)) {
            $histoGraphe = $cmd->getHistory($startTime, $endTime);
            foreach ($histoGraphe as $row) {
                $value = round($row->getValue(), 1);
                $i = 7 - floor((time() - strtotime($row->getDatetime())) / (24 * 60 * 60));
                $listeMaxTemp[$i] = $value;
            }
        }
        $datasMinMax = '';
        for ($i = 0; $i < count($listeMinTemp); $i++) {
            if ($datasMinMax !== '') {
                $datasMinMax = ',' . $datasMinMax;
            }
            $datasMinMax = '[' . $listeMinTemp[$i] . ',' . $listeMaxTemp[$i] . ']' . $datasMinMax;
        }

        $replace["#datasMinMax#"] = $datasMinMax;

        $maintenant = time();
        $jour = date("N", $maintenant) - 1;
        $joursMinMax = implode(',', array_map(function($j) use ($jours) {
            return "'" . $jours[$j] . "'";
        }, range($jour, $jour - 7, -1)));

        $replace["#joursMinMax#"] = $joursMinMax;

        $this->buildhtmlId($replace, 'histoTemperatureCsg', 'idHistoTemperatureCsg');
        $this->buildhtmlId($replace, 'histoTemperatureInt', 'idHistoTemperatureInt');
        $this->buildhtmlId($replace, 'histoTemperatureExt', 'idHistoTemperatureExt');

        $top = $this->getCache('top', '200px');
        $replace["#top#"] = $top;
        $left = $this->getCache('left', '200px');
        $replace["#left#"] = $left;

        return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'viessmannIot_view', 'viessmannIot')));
    }

    private function buildhtml(&$replace, $_logicalId, $_id, $_else=true, $_default='') 
    {
        $_object = $this->getCmd(null, $_logicalId);
        $ret ='';
        if (is_object($_object)) {
            $replace["#$_logicalId#"] = $ret = $_object->execCmd();
            $replace["#$_id#"] = $_object->getId();
        } else 
            if ($_else) {
                $replace["#$_logicalId#"] = $_default;
                $replace["#$_id#"] = "#$_id#";
            } 
        return $ret;
    }
 
    private function buildhtmlId(&$replace, $_logicalId, $_id, $_default='') 
    {
        $_object = $this->getCmd(null, $_logicalId);
        if (is_object($_object)) {
            $replace["#$_id#"] = $_object->getId();
        } else if ($_default != '') 
            {
                $replace["#$_id#"] = $_default;
            } 
        return;
    }


    private function buildFeature($circuitId, $feature)
    {
        return self::HEATING_CIRCUITS . "." . $circuitId . "." . $feature;
    }

    private function buildFeatureBurner($deviceId, $feature)
    {
        if ($feature == '') {
            return self::HEATING_BURNERS . "." . $deviceId;
        }
        return self::HEATING_BURNERS . "." . $deviceId . "." . $feature;
    }
    private function buildFeatureCompressor($deviceId, $feature)
    {
        if ($feature == '') {
            return self::HEATING_COMPRESSORS . "." . $deviceId;
        }
        return self::HEATING_COMPRESSORS . "." . $deviceId . "." . $feature;
    }
    // Lire les températures intérieures
    //
    public function lireTempInt($startDate, $endDate, $dynamique)
    {
        $array = array();

        if (
            ($this->validateDate($startDate, 'Y-m-d') == true) &&
            ($this->validateDate($endDate, 'Y-m-d') == true) &&
            ($dynamique == 'false')
        ) {
            $startTime = $startDate . " 00:00:00";
            $endTime = $endDate . " 00:00:00";
        } else {
            $startTime = date("Y-m-d H:i:s", time() - strtotime('-3 days'));
            $endTime = date("Y-m-d H:i:s", time());
        }

        $cmd = $this->getCmd(null, 'histoTemperatureInt');
        if (is_object($cmd)) {
            $histo = $cmd->getHistory($startTime, $endTime);
            foreach ($histo as $row) {
                $datetime = $row->getDatetime();
                $ts = strtotime($datetime);
                $value = round($row->getValue(), 1);
                $date = date("Y,m,d,H,i,s", $ts);
                $dateParts = explode(",", $date);
                $dateParts[1] -= 1; // Adjust month to zero-based index
                $array[] = array('ts' => implode(",", $dateParts), 'value' => $value);
            }
        }
        return ($array);
    }

    // Lire les températures extérieures
    //
    public function lireTempExt($startDate, $endDate, $dynamique)
    {
        $array = array();

        if (
            ($this->validateDate($startDate, 'Y-m-d') == true) &&
            ($this->validateDate($endDate, 'Y-m-d') == true) &&
            ($dynamique == 'false')
        ) {
            $startTime = $startDate . " 00:00:00";
            $endTime = $endDate . " 00:00:00";
        } else {
            $startTime = date("Y-m-d H:i:s", time() + strtotime('-3 days'));
            $endTime = date("Y-m-d H:i:s", time());
        }

        $cmd = $this->getCmd(null, 'histoTemperatureExt');
        if (is_object($cmd)) {
            $histo = $cmd->getHistory($startTime, $endTime);
            foreach ($histo as $row) {
                $datetime = $row->getDatetime();
                $ts = strtotime($datetime);
                $value = round($row->getValue(), 1);
                $dateParts = explode(",", date("Y,m,d,H,i,s", $ts));
                $dateParts[1] -= 1; // Adjust month to zero-based index
                $array[] = array('ts' => implode(",", $dateParts), 'value' => $value);
            }
        }
        return ($array);
    }

    // Lire les températures de consigne
    //
    public function lireTempCsg($startDate, $endDate, $dynamique)
    {
        $array = array();

        if (
            ($this->validateDate($startDate, 'Y-m-d') == true) &&
            ($this->validateDate($endDate, 'Y-m-d') == true) &&
            ($dynamique == 'false')
        ) {
            $startTime = $startDate . " 00:00:00";
            $endTime = $endDate . " 00:00:00";
        } else {
            $startTime = date("Y-m-d H:i:s", time() + strtotime('-3 days'));
            $endTime = date("Y-m-d H:i:s", time());
        }

        $cmd = $this->getCmd(null, 'histoTemperatureCsg');
        if (is_object($cmd)) {
            $histo = $cmd->getHistory($startTime, $endTime);
            foreach ($histo as $row) {
                $datetime = $row->getDatetime();
                $ts = strtotime($datetime);
                $value = round($row->getValue(), 1);
                $dateParts = explode(",", date("Y,m,d,H,i,s", $ts));
                $dateParts[1] -= 1; // Adjust month to zero-based index
                $array[] = array('ts' => implode(",", $dateParts), 'value' => $value);
            }
        }
        return ($array);
    }
}


class viessmannIotCmd extends cmd
{
    // Exécution d'une commande
    //
    public function execute($_options = array())
    {
        $eqlogic = $this->getEqLogic();
        $logicalId = $this->getLogicalId();

        switch ($logicalId) {
            case 'refresh':
                $viessmannApi = $eqlogic->callViessmannAPI();
                if ($viessmannApi !== null) {
                    $eqlogic->rafraichir($viessmannApi);
                    unset($viessmannApi);
                }
                break;
            case 'startOneTimeDhwCharge':
                $eqlogic->startOneTimeDhwCharge();
                break;
            case 'stopOneTimeDhwCharge':
                $eqlogic->stopOneTimeDhwCharge();
                break;
            case 'activateComfortProgram':
                $eqlogic->activateComfortProgram();
                break;
            case 'deActivateComfortProgram':
                $eqlogic->deActivateComfortProgram();
                break;
            case 'activateEcoProgram':
                $eqlogic->activateEcoProgram();
                break;
            case 'deActivateEcoProgram':
                $eqlogic->deActivateEcoProgram();
                break;
            case 'activateLastSchedule':
                $eqlogic->activateLastSchedule();
                break;
            case 'deActivateLastSchedule':
                $eqlogic->deActivateLastSchedule();
                break;
            case 'modeStandby':
            case 'modeHeating':
            case 'modeCooling':
            case 'modeHeatingCooling':
            case 'modeTestMode':
            case 'modeDhw':
            case 'modeDhwAndHeating':
                $eqlogic->setMode(str_replace('mode', '', $logicalId));
                break;
            case 'modeDhwBalanced':
            case 'modeDhwComfort':
            case 'modeDhwEco':
            case 'modeDhwOff':
                $eqlogic->setDhwMode(str_replace('modeDhw', '', $logicalId));
                break;
            case 'scheduleHolidayProgram':
                $eqlogic->scheduleHolidayProgram();
                break;
            case 'unscheduleHolidayProgram':
                $eqlogic->unscheduleHolidayProgram();
                break;
            case 'scheduleHolidayAtHomeProgram':
                $eqlogic->scheduleHolidayAtHomeProgram();
                break;
            case 'unscheduleHolidayAtHomeProgram':
                $eqlogic->unscheduleHolidayAtHomeProgram();
                break;
            case 'dhwSlider':
            case 'comfortProgramSlider':
            case 'normalProgramSlider':
            case 'reducedProgramSlider':
            case 'shiftSlider':
            case 'slopeSlider':
                if (isset($_options['slider']) && is_numeric($_options['slider'])) {
                    $cmd = str_replace('Slider', 'Temperature', $logicalId);
                    $eqlogic->getCmd(null, $cmd)->event($_options['slider']);
                    $method = 'set' . ucfirst($cmd);
                    $eqlogic->$method($_options['slider']);
                }
                break;
            case 'startHolidayText':
            case 'endHolidayText':
            case 'startHolidayAtHomeText':
            case 'endHolidayAtHomeText':
                if (isset($_options['text']) && $_options['text'] !== '') {
                    $cmd = str_replace('Text', '', $logicalId);
                    $eqlogic->getCmd(null, $cmd)->event($_options['text']);
                }
                break;
            case 'setHeatingSchedule':
            case 'setDhwSchedule':
                if ($this->getSubType() === 'message' && $_options !== null) {
                    $titre = $_options['title'] ?? '';
                    $message = $_options['message'] ?? '';
                    $method = str_replace('set', 'set', $logicalId);
                    $eqlogic->$method($titre, $message);
                }
                break;
        }
    }
}
