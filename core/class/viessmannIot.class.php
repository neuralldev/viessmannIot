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
                        $viessmannApi = $viessmannIot->getViessmann();
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
    public function getViessmann()
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
                $installationId = $viessmannApi->getInstallationId($numChaudiere);
                $serial = $viessmannApi->getSerial($numChaudiere);

                $this->setConfiguration('installationId', $installationId);
                $this->setConfiguration('serial', $serial)->save();
            }
            //              $this->deleteAllCommands();
            $this->createCommands($viessmannApi);
        }

        if ($viessmannApi->isNewToken()) {
            $expires_at = time() + $viessmannApi->getExpiresIn() - 300;
            $this->setCache('expires_at', $expires_at);
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
        if ($facteurConversionGaz == 0) {
            $facteurConversionGaz = 1;
        }

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
        $nbrFeatures = count($features["data"]);
        for ($i = 0; $i < $nbrFeatures; $i++) {
            log::add('viessmannIot', 'debug', 'feature  '.$i);
            if ($features["data"][$i]["isEnabled"] == true) {
                log::add('viessmannIot', 'debug', 'feature  '.$features["data"][$i]["feature"]);
                if ($features["data"][$i]["feature"] == self::OUTSIDE_TEMPERATURE) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $outsideTemperature = $val;
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::PUMP_STATUS)) {
                    $val = $features["data"][$i]["properties"]["status"]["value"];
                    $obj = $this->getCmd(null, 'pumpStatus');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATPUMP_SECONDARY) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    log::add('viessmannIot', 'debug', 'heatpump delestage '.$val);
                    $obj = $this->getCmd(null, 'secondaryCircuitTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::HEATPUMP_STATUS)) {
                    log::add('viessmannIot', 'debug', 'heatpump status refresh');
                    $val = $features["data"][$i]["properties"]["status"]["value"];
                    $obj = $this->getCmd(null, 'pumpStatus');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::HOT_WATER_STORAGE_TEMPERATURE) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'hotWaterStorageTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::DHW_TEMPERATURE) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'dhwTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                    $this->getCmd(null, 'dhwTemperature')->event($val);
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::ACTIVE_MODE)) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'activeMode');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::ACTIVE_PROGRAM)) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $activeProgram = $val;
                    $obj = $this->getCmd(null, 'activeProgram');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeatureBurner($deviceId, '')) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isHeatingBurnerActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeatureCompressor($deviceId, '') && $features["data"][$i]["isEnabled"] == true) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isHeatingCompressorActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::STANDBY_MODE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isStandbyModeActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::HEATING_MODE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isHeatingModeActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::DHW_MODE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isDhwModeActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::DHW_AND_HEATING_MODE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isDhwAndHeatingModeActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::COOLING_MODE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isCoolingModeActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::DHW_AND_HEATING_COOLING_MODE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isDhwAndHeatingCoolingModeActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::HEATING_COOLING_MODE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isHeatingCoolingModeActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::NORMAL_STANDBY_MODE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isNormalStandbyModeActive');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::SENSORS_TEMPERATURE_SUPPLY)) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'supplyProgramTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif (($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::COMFORT_PROGRAM) ||
                        $features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::COMFORT_PROGRAM_HEATING))) {
                    $val = $features["data"][$i]["properties"]["temperature"]["value"];
                    $comfortProgramTemperature = $val;
                    $obj = $this->getCmd(null, 'comfortProgramTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isActivateComfortProgram');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif (
                    ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::NORMAL_PROGRAM) ||
                        $features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::NORMAL_PROGRAM_HEATING))

                ) {
                    $val = $features["data"][$i]["properties"]["temperature"]["value"];
                    $normalProgramTemperature = $val;
                    $obj = $this->getCmd(null, 'normalProgramTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif (
                    ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::REDUCED_PROGRAM) ||
                        $features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::REDUCED_PROGRAM_HEATING))

                ) {
                    $val = $features["data"][$i]["properties"]["temperature"]["value"];
                    $reducedProgramTemperature = $val;
                    $obj = $this->getCmd(null, 'reducedProgramTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::ECO_PROGRAM)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isActivateEcoProgram');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::FORCED_LAST_FROM_SCHEDULE)) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isActivateLastSchedule');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::SENSORS_TEMPERATURE_ROOM)) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $roomTemperature = $val;
                } elseif ($features["data"][$i]["feature"] == self::HEATING_DHW_ONETIMECHARGE) {
                    $val = $features["data"][$i]["properties"]["active"]["value"];
                    $obj = $this->getCmd(null, 'isOneTimeDhwCharge');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::HEATING_CURVE)) {
                    $shift = $features["data"][$i]["properties"]["shift"]["value"];
                    $obj = $this->getCmd(null, 'shift');
                    if (is_object($obj)) {
                        $obj->event($shift);
                    }
                    $slope = $features["data"][$i]["properties"]["slope"]["value"];
                    $obj = $this->getCmd(null, 'slope');
                    if (is_object($obj)) {
                        $obj->event($slope);
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_DHW_SCHEDULE) {
                    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                    $dhwSchedule = '';

                    foreach ($days as $day) {
                        $n = count($features["data"][$i]["properties"]['entries']['value'][$day]);
                        for ($j = 0; $j < $n; $j++) {
                            $dhwSchedule .= 'n,';
                            $dhwSchedule .= $features["data"][$i]["properties"]['entries']['value'][$day][$j]['start'] . ',';
                            $dhwSchedule .= $features["data"][$i]["properties"]['entries']['value'][$day][$j]['end'];
                            if ($j < $n - 1) {
                                $dhwSchedule .= ',';
                            }
                        }
                        $dhwSchedule .= ';';
                    }

                    $obj = $this->getCmd(null, 'dhwSchedule');
                    if (is_object($obj)) {
                        $obj->event($dhwSchedule);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::HEATING_SCHEDULE)) {
                    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                    $heatingSchedule = '';

                    foreach ($days as $day) {
                        $n = count($features["data"][$i]["properties"]['entries']['value'][$day]);
                        for ($j = 0; $j < $n; $j++) {
                            $heatingSchedule .= substr($features["data"][$i]["properties"]['entries']['value'][$day][$j]['mode'], 0, 1) . ',';
                            $heatingSchedule .= $features["data"][$i]["properties"]['entries']['value'][$day][$j]['start'] . ',';
                            $heatingSchedule .= $features["data"][$i]["properties"]['entries']['value'][$day][$j]['end'];
                            if ($j < $n - 1) {
                                $heatingSchedule .= ',';
                            }
                        }
                        $heatingSchedule .= ';';
                    }

                    $obj = $this->getCmd(null, 'heatingSchedule');
                    if (is_object($obj)) {
                        $obj->event($heatingSchedule);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, self::HEATING_FROSTPROTECTION)) {
                    $val = $features["data"][$i]["properties"]["status"]["value"];
                    $obj = $this->getCmd(null, 'frostProtection');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_BOILER_SENSORS_TEMPERATURE) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'boilerTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_BOILER_SENSORS_TEMPERATURE_MAIN) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'boilerTemperatureMain');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::PRESSURE_SUPPLY) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'pressureSupply');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::SOLAR_TEMPERATURE) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'solarTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::SOLAR_DHW_TEMPERATURE) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'solarDhwTemperature');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_GAS_CONSUMPTION_TOTAL) {
                    $bConsumptionSeen = true;

                    $periods = ['day', 'week', 'month', 'year'];
                    $heatingGazConsumptions = [];

                    foreach ($periods as $period) {
                        $values = $features["data"][$i]["properties"][$period]['value'];
                        $heatingGazConsumptions[$period] = array_map(function($value) use ($facteurConversionGaz) {
                            return $value * $facteurConversionGaz;
                        }, $values);
                    }

                    $this->getCmd(null, 'totalGazConsumption')->event($heatingGazConsumptions['day'][0]);

                    $conso = $heatingGazConsumptions['day'][0];
                    $oldConso = $this->getCache('oldConsoTotal', -1);
                    if ($oldConso >= $conso) {
                        $dateVeille = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
                        $this->getCmd(null, 'totalGazHistorize')->event($heatingGazConsumptions['day'][1], $dateVeille);
                    }
                    $this->setCache('oldConsoTotal', $conso);

                    foreach ($periods as $period) {
                        $values = implode(',', array_reverse($heatingGazConsumptions[$period]));
                        $obj = $this->getCmd(null, "totalGazConsumption" . ucfirst($period));
                        if (is_object($obj)) {
                            $obj->event($values);
                        }
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_GAS_CONSUMPTION_DHW) {
                    $bConsumptionSeen = true;

                    $periods = ['day', 'week', 'month', 'year'];
                    $dhwGazConsumptions = [];

                    foreach ($periods as $period) {
                        $values = $features["data"][$i]["properties"][$period]['value'];
                        $dhwGazConsumptions[$period] = array_map(function($value) use ($facteurConversionGaz) {
                            return $value * $facteurConversionGaz;
                        }, $values);
                    }

                    $this->getCmd(null, 'dhwGazConsumption')->event($dhwGazConsumptions['day'][0]);

                    $conso = $dhwGazConsumptions['day'][0];
                    $oldConso = $this->getCache('oldConsoDhw', -1);
                    if ($oldConso >= $conso) {
                        $dateVeille = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
                        $this->getCmd(null, 'dhwGazHistorize')->event($dhwGazConsumptions['day'][1], $dateVeille);
                    }
                    $this->setCache('oldConsoDhw', $conso);

                    foreach ($periods as $period) {
                        $values = implode(',', array_reverse($dhwGazConsumptions[$period]));
                        $obj = $this->getCmd(null, "dhwGazConsumption" . ucfirst($period));
                        if (is_object($obj)) {
                            $obj->event($values);
                        }
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_GAS_CONSUMPTION_HEATING) {
                    $bConsumptionSeen = true;

                    $periods = ['day', 'week', 'month', 'year'];
                    $heatingGazConsumptions = [];

                    foreach ($periods as $period) {
                        $values = $features["data"][$i]["properties"][$period]['value'];
                        $heatingGazConsumptions[$period] = array_map(function($value) use ($facteurConversionGaz) {
                            return $value * $facteurConversionGaz;
                        }, $values);
                    }

                    $this->getCmd(null, 'heatingGazConsumption')->event($heatingGazConsumptions['day'][0]);

                    $conso = $heatingGazConsumptions['day'][0];
                    $oldConso = $this->getCache('oldConsoHeating', -1);
                    if ($oldConso >= $conso) {
                        $dateVeille = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
                        $this->getCmd(null, 'heatingGazHistorize')->event($heatingGazConsumptions['day'][1], $dateVeille);
                    }
                    $this->setCache('oldConsoHeating', $conso);

                    foreach ($periods as $period) {
                        $values = implode(',', array_reverse($heatingGazConsumptions[$period]));
                        $obj = $this->getCmd(null, "heatingGazConsumption" . ucfirst($period));
                        if (is_object($obj)) {
                            $obj->event($values);
                        }
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_POWER_CONSUMPTION_TOTAL) {
                    $bConsumptionSeen = true;

                    $periods = ['day', 'week', 'month', 'year'];
                    $totalPowerConsumptions = [];

                    foreach ($periods as $period) {
                        $values = $features["data"][$i]["properties"][$period]['value'];
                        $totalPowerConsumptions[$period] = array_map('floatval', $values);
                    }

                    $this->getCmd(null, 'totalPowerConsumption')->event($totalPowerConsumptions['day'][0]);

                    $conso = $totalPowerConsumptions['day'][0];
                    $oldConso = $this->getCache('oldConsoPowerTotal', -1);
                    if ($oldConso >= $conso) {
                        $dateVeille = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
                        $this->getCmd(null, 'totalPowerHistorize')->event($totalPowerConsumptions['day'][1], $dateVeille);
                    }
                    $this->setCache('oldConsoPowerTotal', $conso);

                    foreach ($periods as $period) {
                        $values = implode(',', array_reverse($totalPowerConsumptions[$period]));
                        $this->getCmd(null, "totalPowerConsumption" . ucfirst($period))->event($values);
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_POWER_CONSUMPTION_DHW) {
                    $bConsumptionSeen = true;

                    $periods = ['day', 'week', 'month', 'year'];
                    $dhwPowerConsumptions = [];

                    foreach ($periods as $period) {
                        $values = $features["data"][$i]["properties"][$period]['value'];
                        $dhwPowerConsumptions[$period] = array_map('floatval', $values);
                    }

                    $this->getCmd(null, 'dhwPowerConsumption')->event($dhwPowerConsumptions['day'][0]);

                    $conso = $dhwPowerConsumptions['day'][0];
                    $oldConso = $this->getCache('oldConsoPowerDhw', -1);
                    if ($oldConso >= $conso) {
                        $dateVeille = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
                        $this->getCmd(null, 'dhwPowerHistorize')->event($dhwPowerConsumptions['day'][1], $dateVeille);
                    }
                    $this->setCache('oldConsoPowerDhw', $conso);

                    foreach ($periods as $period) {
                        $values = implode(',', array_reverse($dhwPowerConsumptions[$period]));
                        $obj = $this->getCmd(null, "dhwPowerConsumption" . ucfirst($period));
                        if (is_object($obj)) {
                            $obj->event($values);
                        }
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_POWER_CONSUMPTION_HEATING) {
                    $bConsumptionSeen = true;

                    $periods = ['day', 'week', 'month', 'year'];
                    $heatingPowerConsumptions = [];

                    foreach ($periods as $period) {
                        $values = $features["data"][$i]["properties"][$period]['value'];
                        $heatingPowerConsumptions[$period] = array_map('floatval', $values);
                    }

                    $this->getCmd(null, 'heatingPowerConsumption')->event($heatingPowerConsumptions['day'][0]);

                    $conso = $heatingPowerConsumptions['day'][0];
                    $oldConso = $this->getCache('oldConsoPowerHeating', -1);
                    if ($oldConso >= $conso) {
                        $dateVeille = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
                        $this->getCmd(null, 'heatingPowerHistorize')->event($heatingPowerConsumptions['day'][1], $dateVeille);
                    }
                    $this->setCache('oldConsoPowerHeating', $conso);

                    foreach ($periods as $period) {
                        $values = implode(',', array_reverse($heatingPowerConsumptions[$period]));
                        $obj = $this->getCmd(null, "heatingPowerConsumption" . ucfirst($period));
                        if (is_object($obj)) {
                            $obj->event($values);
                        }
                    }
                } elseif ($features["data"][$i]["feature"] == self::HEATING_SERVICE_TIMEBASED) {
                    $val = $features["data"][$i]["properties"]["lastService"]["value"];
                    $val = substr($val, 0, 19);
                    $val = str_replace('T', ' ', $val);
                    $obj = $this->getCmd(null, 'lastServiceDate');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                    $val = $features["data"][$i]["properties"]["serviceIntervalMonths"]["value"];
                    $obj = $this->getCmd(null, 'serviceInterval');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                    $val = $features["data"][$i]["properties"]["activeMonthSinceLastService"]["value"];
                    $obj = $this->getCmd(null, 'monthSinceService');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeatureBurner($deviceId, self::STATISTICS)) {
                    $val = $features["data"][$i]["properties"]["hours"]["value"];
                    $heatingBurnerHours = $val;
                    $obj = $this->getCmd(null, 'heatingBurnerHours');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                    $val = $features["data"][$i]["properties"]["starts"]["value"];
                    $heatingBurnerStarts = $val;
                    $obj = $this->getCmd(null, 'heatingBurnerStarts');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeatureCompressor($deviceId, self::STATISTICS) && $features["data"][$i]["isEnabled"] == true) {
                    $val = $features["data"][$i]["properties"]["hours"]["value"];
                    $heatingCompressorHours = $val;
                    $obj = $this->getCmd(null, 'heatingCompressorHours');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                    $val = $features["data"][$i]["properties"]["starts"]["value"];
                    $heatingCompressorStarts = $val;
                    $obj = $this->getCmd(null, 'heatingCompressorStarts');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == $this->buildFeatureBurner($deviceId, self::MODULATION)) {
                    $val = $features["data"][$i]["properties"]["value"]["value"];
                    $obj = $this->getCmd(null, 'heatingBurnerModulation');
                    if (is_object($obj)) {
                        $obj->event($val);
                    }
                } elseif ($features["data"][$i]["feature"] == self::HOLIDAY_PROGRAM) {
                    $active = $features["data"][$i]["properties"]["active"]["value"];
                    $start = $features["data"][$i]["properties"]["start"]["value"];
                    $end = $features["data"][$i]["properties"]["end"]["value"];

                    $start = str_replace('"', '', $start);
                    $end = str_replace('"', '', $end);

                    if ($active == true) {
                        $obj = $this->getCmd(null, 'isScheduleHolidayProgram');
                        if (is_object($obj)) {
                            $obj->event(1);
                        }
                        $obj = $this->getCmd(null, 'startHoliday');
                        if (is_object($obj)) {
                            $obj->event($start);
                        }
                        $obj = $this->getCmd(null, 'endHoliday');
                        if (is_object($obj)) {
                            $obj->event($end);
                        }
                    } else {
                        $obj = $this->getCmd(null, 'isScheduleHolidayProgram');
                        if (is_object($obj)) {
                            $obj->event(0);
                        }
                    }
                } elseif ($features["data"][$i]["feature"] == self::HOLIDAY_AT_HOME_PROGRAM) {
                    $active = $features["data"][$i]["properties"]["active"]["value"];
                    $start = $features["data"][$i]["properties"]["start"]["value"];
                    $end = $features["data"][$i]["properties"]["end"]["value"];

                    $start = str_replace('"', '', $start);
                    $end = str_replace('"', '', $end);

                    if ($active == true) {
                        $obj = $this->getCmd(null, 'isScheduleHolidayAtHomeProgram');
                        if (is_object($obj)) {
                            $obj->event(1);
                        }
                        $obj = $this->getCmd(null, 'startHolidayAtHome');
                        if (is_object($obj)) {
                            $obj->event($start);
                        }
                        $obj = $this->getCmd(null, 'endHolidayAtHome');
                        if (is_object($obj)) {
                            $obj->event($end);
                        }
                    } else {
                        $obj = $this->getCmd(null, 'isScheduleHolidayAtHomeProgram');
                        if (is_object($obj)) {
                            $obj->event(0);
                        }
                    }
                }
            }
        }

        if (!$bConsumptionSeen) {
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
                $dateVeille = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
                $this->getCmd(null, "{$type}GazHistorize")->event($conso, $dateVeille);
            }
            $this->setCache("oldConso{$type}", $conso);

            $conso = $powerSummary["day{$type}"];
            $oldConso = $this->getCache("oldConsoPower{$type}", -1);
            if ($oldConso >= $conso) {
                $dateVeille = date('Y-m-d 00:00:00', time() - 24 * 60 * 60);
                $this->getCmd(null, "{$type}PowerHistorize")->event($conso, $dateVeille);
            }
            $this->setCache("oldConsoPower{$type}", $conso);
            }

            foreach (['Total', 'Dhw', 'Heating'] as $type) {
            $c = $this->getCmd(null, "total{$type}Consumption");
            if (is_object($c)) $c->event($gasSummary["day{$type}"]);
            $c = $this->getCmd(null, "totalPowerConsumption");
            if (is_object($c)) $c->event($powerSummary["day{$type}"]);
            }

            foreach (['day', 'week', 'month', 'year'] as $period) {
            foreach (['Dhw', 'Heating', 'Total'] as $type) {
                $c=$this->getCmd(null, "{$type}GazConsumption{$period}");
                if (is_object($c)) $c->event($gasSummary["{$period}{$type}"]);
                $c=$this->getCmd(null, "{$type}PowerConsumption{$period}");
                if (is_object($c)) $c->event($powerSummary["{$period}{$type}"]);
            }
            }
        }

        $maintenant = time();
        $minute = date("i", $maintenant);
        if ($minute == 0 || $viessmannApi->getLogFeatures() == 'Oui') {
            $viessmannApi->getEvents();
            $events = $viessmannApi->getArrayEvents();
            $nbrEvents = count($events["data"]);
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
                $erreurCourante = $event['body']['active'] ? $errorCode : ($erreurCourante == $errorCode ? '' : $erreurCourante);
                $nbr++;
                }
            }
            }
            $this->getCmd(null, 'errors')->event($erreurs);
            $this->getCmd(null, 'currentError')->event($erreurCourante);
        }

        if ($outsideTemperature == 99) {
            $outsideTemperature = jeedom::evaluateExpression($this->getConfiguration('temperature_exterieure'));
            if (!is_numeric($outsideTemperature)) {
                $outsideTemperature = 99;
            } else {
                $outsideTemperature = round($outsideTemperature, 1);
            }
        }

        if ($outsideTemperature != 99) {
            $obj = $this->getCmd(null, 'outsideTemperature');
            if (is_object($obj)) {
                $obj->event($outsideTemperature);
            }
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
            $obj = $this->getCmd(null, 'roomTemperature');
            if (is_object($obj)) {
                $obj->event($roomTemperature);
            }
        }

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

        if (
            ($consigneTemperature != 99) &&
            ($slope != 99) &&
            ($shift != 99)
        ) {
            $curve = '';
            for ($ot = 25; $ot >= -20; $ot -= 5) {
                $deltaT = $ot - $consigneTemperature;
                $tempDepart = $consigneTemperature + $shift - $slope * $deltaT * (1.4347 + 0.021 * $deltaT + 247.9e-6 * $deltaT * $deltaT);
                if ($curve == '') {
                    $curve = round($tempDepart, 0);
                } else {
                    $curve = $curve . ',' . round($tempDepart, 0);
                }
            }
            $this->checkAndUpdateCmd('curve', $curve);
        }

        $now = time();

        // Historisation temperatures
        //
        $dateCron = time();
        $dateCron = date('Y-m-d H:i:00', $dateCron);
        if (
            ($roomTemperature != 99) &&
            ($consigneTemperature != 99) &&
            ($outsideTemperature != 99)
        ) {
            $obj = $this->getCmd(null, 'histoTemperatureInt');
            if (is_object($obj)) {
                $obj->event($roomTemperature, $dateCron);
            }

            $obj = $this->getCmd(null, 'histoTemperatureCsg');
            if (is_object($obj)) {
                $obj->event($consigneTemperature, $dateCron);
            }

            $obj = $this->getCmd(null, 'histoTemperatureExt');
            if (is_object($obj)) {
                $obj->event($outsideTemperature, $dateCron);
            }
        }
        $outsideMinTemperature = $this->getCache('outsideMinTemperature', 99);
        $outsideMaxTemperature = $this->getCache('outsideMaxTemperature', -99);

        $jour = date("d", $now);
        $oldJour = $this->getCache('oldJour', -1);

        if ($oldJour != $jour) {
            $dateVeille = time() - 24 * 60 * 60;
            $dateVeille = date('Y-m-d 00:00:00', $dateVeille);

            if (($heatingBurnerHours != -1) && ($heatingBurnerStarts != -1)) {
                $oldHours = $this->getCache('oldHours', -1);
                $oldStarts = $this->getCache('oldStarts', -1);
                if ($oldHours != -1) {
                    $obj = $this->getCmd(null, 'heatingBurnerHoursPerDay');
                    if (is_object($obj)) {
                        $obj->event(round($heatingBurnerHours - $oldHours, 1), $dateVeille);
                    }
                }
                if ($oldStarts != -1) {
                    $obj = $this->getCmd(null, 'heatingBurnerStartsPerDay');
                    if (is_object($obj)) {
                        $obj->event($heatingBurnerStarts - $oldStarts, $dateVeille);
                    }
                }
                $this->setCache('oldHours', $heatingBurnerHours);
                $this->setCache('oldStarts', $heatingBurnerStarts);
            }

            if (($heatingCompressorHours != -1) && ($heatingCompressorStarts != -1)) {
                $oldHours = $this->getCache('oldHoursComp', -1);
                $oldStarts = $this->getCache('oldStartsComp', -1);
                if ($oldHours != -1) {
                    $obj = $this->getCmd(null, 'heatingCompressorHoursPerDay');
                    if (is_object($obj)) {
                        $obj->event(round($heatingCompressorHours - $oldHours, 1), $dateVeille);
                    }
                }
                if ($oldStarts != -1) {
                    $obj = $this->getCmd(null, 'heatingCompressorStartsPerDay');
                    if (is_object($obj)) {
                        $obj->event($heatingCompressorStarts - $oldStarts, $dateVeille);
                    }
                }
                $this->setCache('oldHoursComp', $heatingCompressorHours);
                $this->setCache('oldStartsComp', $heatingCompressorStarts);
            }

            if ($outsideMinTemperature != 99) {
                $obj = $this->getCmd(null, 'outsideMinTemperature');
                if (is_object($obj)) {
                    $obj->event($outsideMinTemperature, $dateVeille);
                }
            }

            if ($outsideMaxTemperature != -99) {
                $obj = $this->getCmd(null, 'outsideMaxTemperature');
                if (is_object($obj)) {
                    $obj->event($outsideMaxTemperature, $dateVeille);
                }
            }

            $outsideMinTemperature = 99;
            $this->setCache('outsideMinTemperature', $outsideMinTemperature);
            $outsideMaxTemperature = -99;
            $this->setCache('outsideMaxTemperature', $outsideMaxTemperature);

            $this->setCache('oldJour', $jour);
        }

        if ($outsideTemperature != 99) {
            if ($outsideTemperature < $outsideMinTemperature) {
                $this->setCache('outsideMinTemperature', $outsideTemperature);
            }

            if ($outsideTemperature > $outsideMaxTemperature) {
                $this->setCache('outsideMaxTemperature', $outsideTemperature);
            }
        }

        $date = new DateTime();
        $date = $date->format('d-m-Y H:i:s');
        $this->getCmd(null, 'refreshDate')->event($date);

        return;
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
                        $viessmannApi = $viessmann->getViessmann();
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
                    $viessmannApi = $viessmann->getViessmann();
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

        if (($viessmannApi = $this->getViessmann()) === null) return;

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

        if (($viessmannApi = $this->getViessmann()) === null) return;

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

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{\"mode\":\"" . $mode . "\"}";
        $viessmannApi->setFeature(self::ACTIVE_DHW_MODE, "setMode", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'activeDhwMode')->event($mode);
    }

    // Set Comfort Program Temperature
    //
    public function setComfortProgramTemperature($temperature)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

//        $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $program = $this->getConfiguration('comfortProgram', '');

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{\"targetTemperature\": $temperature}";
        $viessmannApi->setFeature($program, "setTemperature", $data);

        unset($viessmannApi);

        $obj = $this->getCmd(null, 'activeProgram');
        $activeProgram = '';
        if (is_object($obj)) {
            $activeProgram = $obj->execCmd();
        }
        if ($activeProgram === 'comfort') {
            $this->getCmd(null, 'programTemperature')->event($temperature);
        }
    }

    // Set Normal Program Temperature
    //
    public function setNormalProgramTemperature($temperature)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

//        $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $program = $this->getConfiguration('normalProgram', '');

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{\"targetTemperature\": $temperature}";
        $viessmannApi->setFeature($program, "setTemperature", $data);

        unset($viessmannApi);

        $obj = $this->getCmd(null, 'activeProgram');
        $activeProgram = '';
        if (is_object($obj)) {
            $activeProgram = $obj->execCmd();
        }
        if ($activeProgram === 'normal') {
            $this->getCmd(null, 'programTemperature')->event($temperature);
        }
    }

    // Set Reduced Program Temperature
    //
    public function setReducedProgramTemperature($temperature)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

 //       $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $program = $this->getConfiguration('reducedProgram', '');

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{\"targetTemperature\": $temperature}";
        $viessmannApi->setFeature($program, "setTemperature", $data);

        unset($viessmannApi);
        $obj = $this->getCmd(null, 'activeProgram');
        $activeProgram = '';
        if (is_object($obj)) {
            $activeProgram = $obj->execCmd();
        }
        if (($activeProgram !== 'comfort') && ($activeProgram !== 'normal')) {
            $this->getCmd(null, 'programTemperature')->event($temperature);
        }
    }

    // Start One Time Dhw Charge
    //
    public function startOneTimeDhwCharge()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature(self::HEATING_DHW_ONETIMECHARGE, "activate", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isOneTimeDhwCharge')->event(1);
    }

    // Stop One Time Dhw Charge
    //
    public function stopOneTimeDhwCharge()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature(self::HEATING_DHW_ONETIMECHARGE, "deactivate", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isOneTimeDhwCharge')->event(0);
    }

    // Activate Comfort Program
    //
    public function activateComfortProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

  //      $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $program = $this->getConfiguration('comfortProgram', '');

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($program, "activate", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isActivateComfortProgram')->event(1);
    }

    // deActivate Comfort Program
    //
    public function deActivateComfortProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

   //     $circuitId = trim($this->getConfiguration('circuitId', '0'));
        $program = $this->getConfiguration('comfortProgram', '');

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($program, "deactivate", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isActivateComfortProgram')->event(0);
    }

    // Activate Eco Program
    //
    public function activateEcoProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::ECO_PROGRAM), "activate", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isActivateEcoProgram')->event(1);
    }

    // deActivate Eco Program
    //
    public function deActivateEcoProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::ECO_PROGRAM), "deactivate", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isActivateEcoProgram')->event(0);
    }

    // Activate Last Schedule
    //
    public function activateLastSchedule()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::FORCED_LAST_FROM_SCHEDULE), "activate", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isActivateLastSchedule')->event(1);
    }

    // deActivate Last Schedule
    //
    public function deActivateLastSchedule()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature($this->buildFeature($circuitId, self::FORCED_LAST_FROM_SCHEDULE), "deactivate", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isActivateLastSchedule')->event(0);
    }

    // Set Slope
    //
    public function setSlope($slope)
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        $circuitId = trim($this->getConfiguration('circuitId', '0'));

        if (($viessmannApi = $this->getViessmann()) === null) return;

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

        if (($viessmannApi = $this->getViessmann()) === null) return;

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

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{\"start\":\"" . $startHoliday . "\",\"end\":\"" . $endHoliday . "\"}";
        $viessmannApi->setFeature(self::HOLIDAY_PROGRAM, "schedule", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isScheduleHolidayProgram')->event(1);
    }

    // Unschedule Holiday Program
    //
    public function unscheduleHolidayProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature(self::HOLIDAY_PROGRAM, "unschedule", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isScheduleHolidayProgram')->event(0);
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

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{\"start\":\"" . $startHolidayAtHome . "\",\"end\":\"" . $endHolidayAtHome . "\"}";
        $viessmannApi->setFeature(self::HOLIDAY_AT_HOME_PROGRAM, "schedule", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isScheduleHolidayAtHomeProgram')->event(1);
    }

    // Unschedule Holiday At Home Program
    //
    public function unscheduleHolidayAtHomeProgram()
    {
        $this->setCache('tempsRestant', self::REFRESH_TIME);

        if (($viessmannApi = $this->getViessmann()) === null) return;

        $data = "{}";
        $viessmannApi->setFeature(self::HOLIDAY_AT_HOME_PROGRAM, "unschedule", $data);
        unset($viessmannApi);

        $this->getCmd(null, 'isScheduleHolidayAtHomeProgram')->event(0);
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

        if (($viessmannApi = $this->getViessmann()) === null) return;

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

        if (($viessmannApi = $this->getViessmann()) === null) {
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
        }
        $obj = $this->getCmd(null, 'isHeatingBurnerActive');
        if (is_object($obj)) {
            $replace["#isHeatingBurnerActive#"] = $obj->execCmd();
            $replace["#idIsHeatingBurnerActive#"] = $obj->getId();
        } else {
            $replace["#isHeatingBurnerActive#"] = -1;
            $replace["#idIsHeatingBurnerActive#"] = "#idIsHeatingBurnerActive#";
        }

        $obj = $this->getCmd(null, 'isHeatingCompressorActive');
        if (is_object($obj)) {
            $replace["#isHeatingCompressorActive#"] = $obj->execCmd();
            $replace["#idIsHeatingCompressorActive#"] = $obj->getId();
        } else {
            $replace["#isHeatingCompressorActive#"] = -1;
            $replace["#idIsHeatingCompressorActive#"] = "#idIsHeatingCompressorActive#";
        }

        $obj = $this->getCmd(null, 'currentError');
        if (is_object($obj)) {
            $replace["#currentError#"] = $obj->execCmd();
            $replace["#idCurrentError#"] = $obj->getId();
        } else {
            $replace["#currentError#"] = '';
            $replace["#idCurrentError#"] = "#idCurrentError#";
        }

        $obj = $this->getCmd(null, 'outsideTemperature');
        if (is_object($obj)) {
            $replace["#outsideTemperature#"] = $obj->execCmd();
            $replace["#idOutsideTemperature#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'activeProgram');
        if (is_object($obj)) {
            $replace["#activeProgram#"] = $obj->execCmd();
            $replace["#idActiveProgram#"] = $obj->getId();
        } else {
            $replace["#activeProgram#"] = '';
            $replace["#idActiveProgram#"] = "#idActiveProgram#";
        }

        $replace["#circuitName#"] = $circuitName;
        $replace["#displayWater#"] = $displayWater;
        $replace["#displayGas#"] = $displayGas;
        $replace["#displayPower#"] = $displayPower;
        $replace["#uniteGaz#"] = $uniteGaz;

        $obj = $this->getCmd(null, 'roomTemperature');
        if (is_object($obj)) {
            $replace["#roomTemperature#"] = $obj->execCmd();
            $replace["#idRoomTemperature#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'secondaryCircuitTemperature');
        if (is_object($obj)) {
            $replace["#secondaryCircuitTemperature#"] = $obj->execCmd();
            $replace["#idsecondaryCircuit#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'programTemperature');
        if (is_object($obj)) {
            $replace["#programTemperature#"] = $obj->execCmd();
            $replace["#idProgramTemperature#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'hotWaterStorageTemperature');
        if (is_object($obj)) {
            $replace["#hotWaterStorageTemperature#"] = $obj->execCmd();
            $replace["#idHotWaterStorageTemperature#"] = $obj->getId();
        } else {
            $replace["#hotWaterStorageTemperature#"] = 99;
            $replace["#idHotWaterStorageTemperature#"] = "#idHotWaterStorageTemperature#";
        }

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

        $obj = $this->getCmd(null, 'dhwGazConsumption');
        if (is_object($obj)) {
            $replace["#dhwGazConsumption#"] = $obj->execCmd();
            $replace["#idDhwGazConsumption#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'heatingGazConsumption');
        if (is_object($obj)) {
            $replace["#heatingGazConsumption#"] = $obj->execCmd();
            $replace["#idHeatingGazConsumption#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'dhwPowerConsumption');
        if (is_object($obj)) {
            $replace["#dhwPowerConsumption#"] = $obj->execCmd();
            $replace["#idDhwPowerConsumption#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'heatingPowerConsumption');
        if (is_object($obj)) {
            $replace["#heatingPowerConsumption#"] = $obj->execCmd();
            $replace["#idHeatingPowerConsumption#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'refreshDate');
        if (is_object($obj)) {
            $replace["#refreshDate#"] = $obj->execCmd();
            $replace["#idRefreshDate#"] = $obj->getId();
        }
        $obj = $this->getCmd(null, 'heatingBurnerHours');
        if (is_object($obj)) {
            $replace["#heatingBurnerHours#"] = $obj->execCmd();
            $replace["#idHeatingBurnerHours#"] = $obj->getId();
        } else {
            $replace["#heatingBurnerHours#"] = -1;
            $replace["#idHeatingBurnerHours#"] = "#idHeatingBurnerHours#";
        }

        $obj = $this->getCmd(null, 'heatingBurnerHoursPerDay');
        if (is_object($obj)) {
            $replace["#heatingBurnerHoursPerDay#"] = $obj->execCmd();
            $replace["#idHeatingBurnerHoursPerDay#"] = $obj->getId();
        } else {
            $replace["#heatingBurnerHoursPerDay#"] = -1;
            $replace["#idHeatingBurnerHoursPerDay#"] = "#idHeatingBurnerHoursPerDay#";
        }

        $obj = $this->getCmd(null, 'heatingBurnerStarts');
        if (is_object($obj)) {
            $replace["#heatingBurnerStarts#"] = $obj->execCmd();
            $replace["#idHeatingBurnerStarts#"] = $obj->getId();
        } else {
            $replace["#heatingBurnerStarts#"] = -1;
            $replace["#idHeatingBurnerStarts#"] = "#idHeatingBurnerStarts#";
        }

        $obj = $this->getCmd(null, 'heatingBurnerStartsPerDay');
        if (is_object($obj)) {
            $replace["#heatingBurnerStartsPerDay#"] = $obj->execCmd();
            $replace["#idHeatingBurnerStartsPerDay#"] = $obj->getId();
        } else {
            $replace["#heatingBurnerStartsPerDay#"] = -1;
            $replace["#idHeatingBurnerStartsPerDay#"] = "#idHeatingBurnerStartsPerDay#";
        }

        $obj = $this->getCmd(null, 'heatingBurnerModulation');
        if (is_object($obj)) {
            $replace["#heatingBurnerModulation#"] = $obj->execCmd();
            $replace["#idHeatingBurnerModulation#"] = $obj->getId();
        } else {
            $replace["#heatingBurnerModulation#"] = -1;
            $replace["#idHeatingBurnerModulation#"] = "#idHeatingBurnerModulation#";
        }

        $obj = $this->getCmd(null, 'heatingCompressorHours');
        if (is_object($obj)) {
            $replace["#heatingCompressorHours#"] = $obj->execCmd();
            $replace["#idHeatingCompressorHours#"] = $obj->getId();
        } else {
            $replace["#heatingCompressorHours#"] = -1;
            $replace["#idHeatingCompressorHours#"] = "#idHeatingCompressorHours#";
        }

        $obj = $this->getCmd(null, 'heatingCompressorHoursPerDay');
        if (is_object($obj)) {
            $replace["#heatingCompressorHoursPerDay#"] = $obj->execCmd();
            $replace["#idHeatingCompressorHoursPerDay#"] = $obj->getId();
        } else {
            $replace["#heatingCompressorHoursPerDay#"] = -1;
            $replace["#idHeatingCompressorHoursPerDay#"] = "#idHeatingCompressorHoursPerDay#";
        }

        $obj = $this->getCmd(null, 'heatingCompressorStarts');
        if (is_object($obj)) {
            $replace["#heatingCompressorStarts#"] = $obj->execCmd();
            $replace["#idHeatingCompressorStarts#"] = $obj->getId();
        } else {
            $replace["#heatingCompressorStarts#"] = -1;
            $replace["#idHeatingCompressorStarts#"] = "#idHeatingCompressorStarts#";
        }

        $obj = $this->getCmd(null, 'heatingCompressorStartsPerDay');
        if (is_object($obj)) {
            $replace["#heatingCompressorStartsPerDay#"] = $obj->execCmd();
            $replace["#idHeatingCompressorStartsPerDay#"] = $obj->getId();
        } else {
            $replace["#heatingCompressorStartsPerDay#"] = -1;
            $replace["#idHeatingCompressorStartsPerDay#"] = "#idHeatingCompressorStartsPerDay#";
        }

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

        $obj = $this->getCmd(null, 'pressureSupply');
        if (is_object($obj)) {
            $replace["#pressureSupply#"] = $obj->execCmd();
            $replace["#idPressureSupply#"] = $obj->getId();
        } else {
            $replace["#pressureSupply#"] = 99;
            $replace["#idPressureSupply#"] = "#idPressureSupply#";
        }

        $obj = $this->getCmd(null, 'solarTemperature');
        if (is_object($obj)) {
            $replace["#solarTemperature#"] = $obj->execCmd();
            $replace["#idSolarTemperature#"] = $obj->getId();
        } else {
            $replace["#solarTemperature#"] = 99;
            $replace["#idSolarTemperature#"] = "#idSolarTemperature#";
        }

        $obj = $this->getCmd(null, 'solarDhwTemperature');
        if (is_object($obj)) {
            $replace["#solarDhwTemperature#"] = $obj->execCmd();
            $replace["#idSolarDhwTemperature#"] = $obj->getId();
        } else {
            $replace["#solarDhwTemperature#"] = 99;
            $replace["#idSolarDhwTemperature#"] = "#idSolarDhwTemperature#";
        }

        $obj = $this->getCmd(null, 'lastServiceDate');
        if (is_object($obj)) {
            $replace["#lastServiceDate#"] = $obj->execCmd();
            $replace["#idLastServiceDate#"] = $obj->getId();
        } else {
            $replace["#lastServiceDate#"] = '';
            $replace["#idLastServiceDate#"] = "#idLastServiceDate#";
        }

        $obj = $this->getCmd(null, 'serviceInterval');
        if (is_object($obj)) {
            $replace["#serviceInterval#"] = $obj->execCmd();
            $replace["#idServiceInterval#"] = $obj->getId();
        } else {
            $replace["#serviceInterval#"] = 99;
            $replace["#idServiceInterval#"] = "#idServiceInterval#";
        }

        $obj = $this->getCmd(null, 'monthSinceService');
        if (is_object($obj)) {
            $replace["#monthSinceService#"] = $obj->execCmd();
            $replace["#idMonthSinceService#"] = $obj->getId();
        } else {
            $replace["#monthSinceService#"] = 99;
            $replace["#idMonthSinceService#"] = "#idMonthSinceService#";
        }

        $obj = $this->getCmd(null, 'errors');
        if (is_object($obj)) {
            $replace["#errors#"] = $obj->execCmd();
            $replace["#idErrors#"] = $obj->getId();
        } else {
            $replace["#errors#"] = '';
            $replace["#idErrors#"] = "#idErrors#";
        }

        $obj = $this->getCmd(null, 'activeMode');
        if (is_object($obj)) {
            $replace["#activeMode#"] = $obj->execCmd();
            $replace["#idActiveMode#"] = $obj->getId();

            $obj = $this->getCmd(null, 'modeStandby');
            if (is_object($obj)) {
                $replace["#idModeStandby#"] = $obj->getId();
            } else {
                $replace["#idModeStandby#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeAuto');
            if (is_object($obj)) {
                $replace["#idModeAuto#"] = $obj->getId();
            } else {
                $replace["#idModeAuto#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeHeating');
            if (is_object($obj)) {
                $replace["#idModeHeating#"] = $obj->getId();
            } else {
                $replace["#idModeHeating#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeCooling');
            if (is_object($obj)) {
                $replace["#idModeCooling#"] = $obj->getId();
            } else {
                $replace["#idModeCooling#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeHeatingCooling');
            if (is_object($obj)) {
                $replace["#idModeHeatingCooling#"] = $obj->getId();
            } else {
                $replace["#idModeHeatingCooling#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeTestMode');
            if (is_object($obj)) {
                $replace["#idModeTestMode#"] = $obj->getId();
            } else {
                $replace["#idModeTestMode#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeDhw');
            if (is_object($obj)) {
                $replace["#idModeDhw#"] = $obj->getId();
            } else {
                $replace["#idModeDhw#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeDhwAndHeating');
            if (is_object($obj)) {
                $replace["#idModeDhwAndHeating#"] = $obj->getId();
            } else {
                $replace["#idModeDhwAndHeating#"] = '??';
            }
        } else {
            $replace["#activeMode#"] = '??';
            $replace["#idActiveMode#"] = "#idActiveMode#";
        }

        $obj = $this->getCmd(null, 'activeDhwMode');
        if (is_object($obj)) {
            $replace["#activeDhwMode#"] = $obj->execCmd();
            $replace["#idActiveDhwMode#"] = $obj->getId();

            $obj = $this->getCmd(null, 'modeDhwBalanced');
            if (is_object($obj)) {
                $replace["#idModeDhwBalanced#"] = $obj->getId();
            } else {
                $replace["#idModeDhwBalanced#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeDhwComfort');
            if (is_object($obj)) {
                $replace["#idModeDhwComfort#"] = $obj->getId();
            } else {
                $replace["#idModeDhwComfort#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeDhwEco');
            if (is_object($obj)) {
                $replace["#idModeDhwEco#"] = $obj->getId();
            } else {
                $replace["#idModeDhwEco#"] = '??';
            }
            $obj = $this->getCmd(null, 'modeDhwOff');
            if (is_object($obj)) {
                $replace["#idModeDhwOff#"] = $obj->getId();
            } else {
                $replace["#idModeDhwOff#"] = '??';
            }
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

        $obj = $this->getCmd(null, 'supplyProgramTemperature');
        if (is_object($obj)) {
            $replace["#supplyProgramTemperature#"] = $obj->execCmd();
            $replace["#idSupplyProgramTemperature#"] = $obj->getId();
        } else {
            $replace["#supplyProgramTemperature#"] = 99;
            $replace["#idSupplyProgramTemperature#"] = "#idSupplyProgramTemperature#";
        }

        $obj = $this->getCmd(null, 'boilerTemperature');
        if (is_object($obj)) {
            $replace["#boilerTemperature#"] = $obj->execCmd();
            $replace["#idBoilerTemperature#"] = $obj->getId();
        } else {
            $replace["#boilerTemperature#"] = 99;
            $replace["#idBoilerTemperature#"] = "#idBoilerTemperature#";
        }

        $obj = $this->getCmd(null, 'boilerTemperatureMain');
        if (is_object($obj)) {
            $replace["#boilerTemperatureMain#"] = $obj->execCmd();
            $replace["#idBoilerTemperatureMain#"] = $obj->getId();
        } else {
            $replace["#boilerTemperatureMain#"] = 99;
            $replace["#idBoilerTemperatureMain#"] = "#idBoilerTemperatureMain#";
        }

        $obj = $this->getCmd(null, 'frostProtection');
        if (is_object($obj)) {
            $replace["#frostProtection#"] = $obj->execCmd();
            $replace["#idFrostProtection#"] = $obj->getId();
        } else {
            $replace["#frostProtection#"] = '??';
            $replace["#idFrostProtection#"] = "#idFrostProtection#";
        }

        $obj = $this->getCmd(null, 'pumpStatus');
        if (is_object($obj)) {
            $replace["#pumpStatus#"] = $obj->execCmd();
            $replace["#idPumpStatus#"] = $obj->getId();
        } else {
            $replace["#pumpStatus#"] = '??';
            $replace["#idPumpStatus#"] = "#idPumpStatus#";
        }

        $obj = $this->getCmd(null, 'heatingSchedule');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingSchedule#"] = $str;
            $replace["#idHeatingSchedule#"] = $obj->getId();
        } else {
            $replace["#heatingSchedule#"] = "";
            $replace["#idHeatingSchedule#"] = '#idHeatingSchedule#';
        }

        $obj = $this->getCmd(null, 'dhwSchedule');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#dhwSchedule#"] = $str;
            $replace["#idDhwSchedule#"] = $obj->getId();
        } else {
            $replace["#dhwSchedule#"] = "";
            $replace["#idDhwSchedule#"] = '#idDhwSchedule#';
        }

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

        $str = '';
        $obj = $this->getCmd(null, 'heatingGazConsumptionDay');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingGazConsumptionDay#"] = $str;
            $replace["#idHeatingGazConsumptionDay#"] = $obj->getId();
        } else {
            $replace["#heatingGazConsumptionDay#"] = '';
            $replace["#idHeatingGazConsumptionDay#"] = "#idHeatingGazConsumptionDay#";
        }

        $obj = $this->getCmd(null, 'dhwGazConsumptionDay');
        if (is_object($obj)) {
            $replace["#dhwGazConsumptionDay#"] = $obj->execCmd();
            $replace["#idDhwGazConsumptionDay#"] = $obj->getId();
        } else {
            $replace["#dhwGazConsumptionDay#"] = '';
            $replace["#idDhwGazConsumptionDay#"] = "#idDhwGazConsumptionDay#";
        }

        $obj = $this->getCmd(null, 'totalGazConsumptionDay');
        if (is_object($obj)) {
            $replace["#totalGazConsumptionDay#"] = $obj->execCmd();
            $replace["#idTotalGazConsumptionDay#"] = $obj->getId();
        } else {
            $replace["#totalGazConsumptionDay#"] = '';
            $replace["#idTotalGazConsumptionDay#"] = "#idTotalGazConsumptionDay#";
        }

        $jours = array("Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim");

        $maintenant = time();
        $jour = date("N", $maintenant) - 1;
        $joursSemaine = '';
        $n = substr_count($str, ",") + 1;

        for ($i = 0; $i < $n; $i++) {
            if ($joursSemaine !== '') {
                $joursSemaine = ',' . $joursSemaine;
            }
            $joursSemaine = "'" . $jours[$jour] . "'" . $joursSemaine;
            $jour--;
            if ($jour < 0) {
                $jour = 6;
            }
        }
        $replace["#joursSemaine#"] = $joursSemaine;

        $str = '';
        $obj = $this->getCmd(null, 'heatingGazConsumptionWeek');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingGazConsumptionWeek#"] = $str;
            $replace["#idHeatingGazConsumptionWeek#"] = $obj->getId();
        } else {
            $replace["#heatingGazConsumptionWeek#"] = '';
            $replace["#idHeatingGazConsumptionWeek#"] = "#idHeatingGazConsumptionWeek#";
        }

        $obj = $this->getCmd(null, 'dhwGazConsumptionWeek');
        if (is_object($obj)) {
            $replace["#dhwGazConsumptionWeek#"] = $obj->execCmd();
            $replace["#idDhwGazConsumptionWeek#"] = $obj->getId();
        } else {
            $replace["#dhwGazConsumptionWeek#"] = '';
            $replace["#idDhwGazConsumptionWeek#"] = "#idDhwGazConsumptionWeek#";
        }

        $obj = $this->getCmd(null, 'totalGazConsumptionWeek');
        if (is_object($obj)) {
            $replace["#totalGazConsumptionWeek#"] = $obj->execCmd();
            $replace["#idTotalGazConsumptionWeek#"] = $obj->getId();
        } else {
            $replace["#totalGazConsumptionWeek#"] = '';
            $replace["#idTotalGazConsumptionWeek#"] = "#idTotalGazConsumptionWeek#";
        }

        $maintenant = time();
        $semaine = date("W", $maintenant);
        $semaines = '';
        $n = substr_count($str, ",") + 1;

        for ($i = 0; $i < $n; $i++) {
            if ($semaines !== '') {
                $semaines = ',' . $semaines;
            }
            $semaines = "'" . $semaine . "'" . $semaines;
            $maintenant -= 7 * 24 * 60 * 60;
            $semaine = date("W", $maintenant);
        }
        $replace["#semaines#"] = $semaines;

        $str = '';
        $obj = $this->getCmd(null, 'heatingGazConsumptionMonth');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingGazConsumptionMonth#"] = $str;
            $replace["#idHeatingGazConsumptionMonth#"] = $obj->getId();
        } else {
            $replace["#heatingGazConsumptionMonth#"] = '';
            $replace["#idHeatingGazConsumptionMonth#"] = "#idHeatingGazConsumptionMonth#";
        }

        $obj = $this->getCmd(null, 'dhwGazConsumptionMonth');
        if (is_object($obj)) {
            $replace["#dhwGazConsumptionMonth#"] = $obj->execCmd();
            $replace["#idDhwGazConsumptionMonth#"] = $obj->getId();
        } else {
            $replace["#dhwGazConsumptionMonth#"] = '';
            $replace["#idDhwGazConsumptionMonth#"] = "#idDhwGazConsumptionMonth#";
        }

        $obj = $this->getCmd(null, 'totalGazConsumptionMonth');
        if (is_object($obj)) {
            $replace["#totalGazConsumptionMonth#"] = $obj->execCmd();
            $replace["#idTotalGazConsumptionMonth#"] = $obj->getId();
        } else {
            $replace["#totalGazConsumptionMonth#"] = '';
            $replace["#idTotalGazConsumptionMonth#"] = "#idTotalGazConsumptionMonth#";
        }

        $libMois = ["Janv", "Févr", "Mars", "Avr", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"];

        $maintenant = time();
        $mois = date("m", $maintenant) - 1;
        $moisS = '';
        $n = substr_count($str, ",") + 1;

        for ($i = 0; $i < $n; $i++) {
            $moisS = ($moisS !== '' ? ',' : '') . "'" . $libMois[$mois] . "'";
            $mois = ($mois - 1 + 12) % 12;
        }
        $replace["#moisS#"] = $moisS;

        $str = '';
        $obj = $this->getCmd(null, 'heatingGazConsumptionYear');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingGazConsumptionYear#"] = $str;
            $replace["#idHeatingGazConsumptionYear#"] = $obj->getId();
        } else {
            $replace["#heatingGazConsumptionYear#"] = '';
            $replace["#idHeatingGazConsumptionYear#"] = "#idHeatingGazConsumptionYear#";
        }

        $obj = $this->getCmd(null, 'dhwGazConsumptionYear');
        if (is_object($obj)) {
            $replace["#dhwGazConsumptionYear#"] = $obj->execCmd();
            $replace["#idDhwGazConsumptionYear#"] = $obj->getId();
        } else {
            $replace["#dhwGazConsumptionYear#"] = '';
            $replace["#idDhwGazConsumptionYear#"] = "#idDhwGazConsumptionYear#";
        }

        $obj = $this->getCmd(null, 'totalGazConsumptionYear');
        if (is_object($obj)) {
            $replace["#totalGazConsumptionYear#"] = $obj->execCmd();
            $replace["#idTotalGazConsumptionYear#"] = $obj->getId();
        } else {
            $replace["#totalGazConsumptionYear#"] = '';
            $replace["#idTotalGazConsumptionYear#"] = "#idTotalGazConsumptionYear#";
        }

        $maintenant = time();
        $annee = date("Y", $maintenant);
        $annees = '';
        $n = substr_count($str, ",") + 1;

        for ($i = 0; $i < $n; $i++) {
            if ($annees !== '') {
                $annees = ',' . $annees;
            }
            $annees = "'" . $annee . "'" . $annees;
            $annee--;
        }
        $replace["#annees#"] = $annees;

        $str = '';
        $obj = $this->getCmd(null, 'heatingPowerConsumptionDay');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingPowerConsumptionDay#"] = $str;
            $replace["#idHeatingPowerConsumptionDay#"] = $obj->getId();
        } else {
            $replace["#heatingPowerConsumptionDay#"] = '';
            $replace["#idHeatingPowerConsumptionDay#"] = "#idHeatingPowerConsumptionDay#";
        }

        $maintenant = time();
        $jour = date("N", $maintenant) - 1;
        $joursSemaine = '';
        $n = substr_count($str, ",") + 1;

        for ($i = 0; $i < $n; $i++) {
            if ($joursSemaine !== '') {
                $joursSemaine = ',' . $joursSemaine;
            }
            $joursSemaine = "'" . $jours[$jour] . "'" . $joursSemaine;
            $jour--;
            if ($jour < 0) {
                $jour = 6;
            }
        }
        $replace["#elec_joursSemaine#"] = $joursSemaine;

        $str = '';
        $obj = $this->getCmd(null, 'heatingPowerConsumptionWeek');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingPowerConsumptionWeek#"] = $str;
            $replace["#idHeatingPowerConsumptionWeek#"] = $obj->getId();
        } else {
            $replace["#heatingPowerConsumptionWeek#"] = '';
            $replace["#idHeatingPowerConsumptionWeek#"] = "#idHeatingPowerConsumptionWeek#";
        }

        $maintenant = time();
        $semaine = date("W", $maintenant);
        $semaines = '';
        $n = substr_count($str, ",") + 1;

        for ($i = 0; $i < $n; $i++) {
            if ($semaines !== '') {
                $semaines = ',' . $semaines;
            }
            $semaines = "'" . $semaine . "'" . $semaines;
            $maintenant -= 7 * 24 * 60 * 60;
            $semaine = date("W", $maintenant);
        }
        $replace["#elec_semaines#"] = $semaines;

        $str = '';
        $obj = $this->getCmd(null, 'heatingPowerConsumptionMonth');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingPowerConsumptionMonth#"] = $str;
            $replace["#idHeatingPowerConsumptionMonth#"] = $obj->getId();
        } else {
            $replace["#heatingPowerConsumptionMonth#"] = '';
            $replace["#idHeatingPowerConsumptionMonth#"] = "#idHeatingPowerConsumptionMonth#";
        }

        $maintenant = time();
        $mois = date("m", $maintenant) - 1;
        $moisS = '';
        $n = substr_count($str, ",") + 1;

        for ($i = 0; $i < $n; $i++) {
            if ($moisS !== '') {
                $moisS = ',' . $moisS;
            }
            $moisS = "'" . $libMois[$mois] . "'" . $moisS;
            $mois--;
            if ($mois < 0) {
                $mois = 11;
            }
        }
        $replace["#elec_moisS#"] = $moisS;

        $str = '';
        $obj = $this->getCmd(null, 'heatingPowerConsumptionYear');
        if (is_object($obj)) {
            $str = $obj->execCmd();
            $replace["#heatingPowerConsumptionYear#"] = $str;
            $replace["#idHeatingPowerConsumptionYear#"] = $obj->getId();
        } else {
            $replace["#heatingPowerConsumptionYear#"] = '';
            $replace["#idHeatingPowerConsumptionYear#"] = "#idHeatingPowerConsumptionYear#";
        }

        $maintenant = time();
        $annee = date("Y", $maintenant);
        $annees = '';
        $n = substr_count($str, ",") + 1;

        for ($i = 0; $i < $n; $i++) {
            if ($annees !== '') {
                $annees = ',' . $annees;
            }
            $annees = "'" . $annee . "'" . $annees;
            $annee--;
        }
        $replace["#elec_annees#"] = $annees;

        $obj = $this->getCmd(null, 'isOneTimeDhwCharge');
        if (is_object($obj)) {
            $replace["#isOneTimeDhwCharge#"] = $obj->execCmd();
            $replace["#idIsOneTimeDhwCharge#"] = $obj->getId();

            $obj = $this->getCmd(null, 'startOneTimeDhwCharge');
            $replace["#idStartOneTimeDhwCharge#"] = $obj->getId();

            $obj = $this->getCmd(null, 'stopOneTimeDhwCharge');
            $replace["#idStopOneTimeDhwCharge#"] = $obj->getId();
        } else {
            $replace["#isOneTimeDhwCharge#"] = -1;
            $replace["#idIsOneTimeDhwCharge#"] = "#idIsOneTimeDhwCharge#";
        }

        $obj = $this->getCmd(null, 'isActivateComfortProgram');
        if (is_object($obj)) {
            $replace["#isActivateComfortProgram#"] = $obj->execCmd();
            $replace["#idIsActivateComfortProgram#"] = $obj->getId();

            $obj = $this->getCmd(null, 'activateComfortProgram');
            $replace["#idActivateComfortProgram#"] = $obj->getId();

            $obj = $this->getCmd(null, 'deActivateComfortProgram');
            $replace["#idDeActivateComfortProgram#"] = $obj->getId();
        } else {
            $replace["#isActivateComfortProgram#"] = -1;
            $replace["#idIsActivateComfortProgram#"] = "#idIsActivateComfortProgram#";
        }

        $obj = $this->getCmd(null, 'isActivateEcoProgram');
        if (is_object($obj)) {
            $replace["#isActivateEcoProgram#"] = $obj->execCmd();
            $replace["#idIsActivateEcoProgram#"] = $obj->getId();

            $obj = $this->getCmd(null, 'activateEcoProgram');
            $replace["#idActivateEcoProgram#"] = $obj->getId();

            $obj = $this->getCmd(null, 'deActivateEcoProgram');
            $replace["#idDeActivateEcoProgram#"] = $obj->getId();
        } else {
            $replace["#isActivateEcoProgram#"] = -1;
            $replace["#idIsActivateEcoProgram#"] = "#idIsActivateEcoProgram#";
        }

        $obj = $this->getCmd(null, 'isActivateLastSchedule');
        if (is_object($obj)) {
            $replace["#isActivateLastSchedule#"] = $obj->execCmd();
            $replace["#idIsActivateLastSchedule#"] = $obj->getId();

            $obj = $this->getCmd(null, 'activateLastSchedule');
            $replace["#idActivateLastSchedule#"] = $obj->getId();

            $obj = $this->getCmd(null, 'deActivateLastSchedule');
            $replace["#idDeActivateLastSchedule#"] = $obj->getId();
        } else {
            $replace["#isActivateLastSchedule#"] = -1;
            $replace["#idIsActivateLastSchedule#"] = "#idIsActivateLastSchedule#";
        }

        $obj = $this->getCmd(null, 'isScheduleHolidayProgram');
        if (is_object($obj)) {
            $replace["#isScheduleHolidayProgram#"] = $obj->execCmd();
            $replace["#idIsScheduleHolidayProgram#"] = $obj->getId();

            $obj = $this->getCmd(null, 'startHoliday');
            $replace["#startHoliday#"] = $obj->execCmd();
            $replace["#idStartHoliday#"] = $obj->getId();

            $obj = $this->getCmd(null, 'endHoliday');
            $replace["#endHoliday#"] = $obj->execCmd();
            $replace["#idEndHoliday#"] = $obj->getId();

            $obj = $this->getCmd(null, 'startHolidayText');
            $replace["#idStartHolidayText#"] = $obj->getId();

            $obj = $this->getCmd(null, 'endHolidayText');
            $replace["#idEndHolidayText#"] = $obj->getId();

            $obj = $this->getCmd(null, 'scheduleHolidayProgram');
            $replace["#idScheduleHolidayProgram#"] = $obj->getId();

            $obj = $this->getCmd(null, 'unscheduleHolidayProgram');
            $replace["#idUnscheduleHolidayProgram#"] = $obj->getId();
        } else {
            $replace["#isScheduleHolidayProgram#"] = -1;
            $replace["#idIsScheduleHolidayProgram#"] = "#idIsScheduleHolidayProgram#";
        }

        $obj = $this->getCmd(null, 'isScheduleHolidayAtHomeProgram');
        if (is_object($obj)) {
            $replace["#isScheduleHolidayAtHomeProgram#"] = $obj->execCmd();
            $replace["#idIsScheduleHolidayAtHomeProgram#"] = $obj->getId();

            $obj = $this->getCmd(null, 'startHolidayAtHome');
            $replace["#startHolidayAtHome#"] = $obj->execCmd();
            $replace["#idStartHolidayAtHome#"] = $obj->getId();

            $obj = $this->getCmd(null, 'endHolidayAtHome');
            $replace["#endHolidayAtHome#"] = $obj->execCmd();
            $replace["#idEndHolidayAtHome#"] = $obj->getId();

            $obj = $this->getCmd(null, 'startHolidayAtHomeText');
            $replace["#idStartHolidayAtHomeText#"] = $obj->getId();

            $obj = $this->getCmd(null, 'endHolidayAtHomeText');
            $replace["#idEndHolidayAtHomeText#"] = $obj->getId();

            $obj = $this->getCmd(null, 'scheduleHolidayAtHomeProgram');
            $replace["#idScheduleHolidayAtHomeProgram#"] = $obj->getId();

            $obj = $this->getCmd(null, 'unscheduleHolidayAtHomeProgram');
            $replace["#idUnscheduleHolidayAtHomeProgram#"] = $obj->getId();
        } else {
            $replace["#isScheduleHolidayAtHomeProgram#"] = -1;
            $replace["#idIsScheduleHolidayAtHomeProgram#"] = "#idIsScheduleHolidayAtHomeProgram#";
        }

        $temp = implode(',', array_map(function ($ot) {
            return "'$ot'";
        }, range(25, -20, -5)));
        $replace["#range_temperature#"] = $temp;

        $obj = $this->getCmd(null, 'curve');
        if (is_object($obj)) {
            $replace["#curve#"] = $obj->execCmd();
            $replace["#idCurve#"] = $obj->getId();
        }
        $temp = implode(',', array_map(function ($ot) {
            return "'$ot'";
        }, range(25, -20, -5)));
        $replace["#range_temp#"] = $temp;

        $startTime = date("Y-m-d H:i:s", time() - 8 * 24 * 60 * 60);
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

        $obj = $this->getCmd(null, 'histoTemperatureCsg');
        if (is_object($obj)) 
            $replace["#idHistoTemperatureCsg#"] = $obj->getId();

        $obj = $this->getCmd(null, 'histoTemperatureInt');
        if (is_object($obj)) 
            $replace["#idHistoTemperatureInt#"] = $obj->getId();

        $obj = $this->getCmd(null, 'histoTemperatureExt');
        if (is_object($obj)) 
            $replace["#idHistoTemperatureExt#"] = $obj->getId();

        $top = $this->getCache('top', '200px');
        $replace["#top#"] = $top;
        $left = $this->getCache('left', '200px');
        $replace["#left#"] = $left;

        return $this->postToHtml($_version, template_replace($replace, getTemplate('core', $version, 'viessmannIot_view', 'viessmannIot')));
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
            $startTime = date("Y-m-d H:i:s", time() - 3 * 24 * 60 * 60);
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
            $startTime = date("Y-m-d H:i:s", time() - 3 * 24 * 60 * 60);
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
            $startTime = date("Y-m-d H:i:s", time() - 3 * 24 * 60 * 60);
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
                $viessmannApi = $eqlogic->getViessmann();
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
