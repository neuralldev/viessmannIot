<?php

/* This file is part of Jeedom.
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

  require_once __DIR__  . '/../../../../core/php/core.inc.php';
  include __DIR__ . '/../php/viessmannApi.php';

  class viessmannIot extends eqLogic
  {
      
      // Supprimer les commandes
      //
      public function deleteAllCommands()
      {
          $cmds = $this->getCmd();
          foreach ($cmds as $cmd) {
              if ($cmd->getLogicalId() != 'refresh') {
                  $cmd->remove();
              }
          }
      }

      // Créer les commandes
      //
      public function createCommands($viessmannApi)
      {
          $circuitId = trim($this->getConfiguration('circuitId', '0'));

          $features = $viessmannApi->getArrayFeatures();
          $n = count($features["data"]);
          for ($i=0; $i<$n; $i++) {
              if ($features["data"][$i]["feature"] == "heating.sensors.temperature.outside") {
                  $obj = $this->getCmd(null, 'outsideTemperature');
                  if (!is_object($obj)) {
                      $obj = new viessmannIotCmd();
                      $obj->setName(__('Température extérieure', __FILE__));
                      $obj->setUnite('°C');
                      $obj->setIsVisible(1);
                      $obj->setIsHistorized(0);
                  }
                  $obj->setEqLogic_id($this->getId());
                  $obj->setType('info');
                  $obj->setSubType('numeric');
                  $obj->setLogicalId('outsideTemperature');
                  $obj->save();
              } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, "circulation.pump")) {
                  $obj = $this->getCmd(null, 'pumpStatus');
                  if (!is_object($obj)) {
                      $obj = new viessmannIotCmd();
                      $obj->setName(__('Statut circulateur', __FILE__));
                      $obj->setIsVisible(1);
                      $obj->setIsHistorized(0);
                  }
                  $obj->setEqLogic_id($this->getId());
                  $obj->setType('info');
                  $obj->setSubType('string');
                  $obj->setLogicalId('pumpStatus');
                  $obj->save();
              } elseif ($features["data"][$i]["feature"] == "heating.dhw.sensors.temperature.hotWaterStorage") {
                  $obj = $this->getCmd(null, 'hotWaterStorageTemperature');
                  if (!is_object($obj)) {
                      $obj = new viessmannIotCmd();
                      $obj->setName(__('Température eau chaude', __FILE__));
                      $obj->setIsVisible(1);
                      $obj->setIsHistorized(0);
                  }
                  $obj->setEqLogic_id($this->getId());
                  $obj->setType('info');
                  $obj->setSubType('numeric');
                  $obj->setLogicalId('hotWaterStorageTemperature');
                  $obj->save();
              } elseif ($features["data"][$i]["feature"] == "heating.dhw.temperature.main") {
                  $objDhw = $this->getCmd(null, 'dhwTemperature');
                  if (!is_object($objDhw)) {
                      $objDhw = new viessmannIotCmd();
                      $objDhw->setName(__('Consigne eau chaude', __FILE__));
                      $objDhw->setIsVisible(1);
                      $objDhw->setIsHistorized(0);
                  }
                  $objDhw->setEqLogic_id($this->getId());
                  $objDhw->setType('info');
                  $objDhw->setSubType('numeric');
                  $objDhw->setLogicalId('dhwTemperature');
                  $objDhw->setConfiguration('minValue', 10);
                  $objDhw->setConfiguration('maxValue', 60);
                  $objDhw->save();

                  $obj = $this->getCmd(null, 'dhwSlider');
                  if (!is_object($obj)) {
                      $obj = new viessmannIotCmd();
                      $obj->setUnite('°C');
                      $obj->setName(__('Slider consigne eau chaude ', __FILE__));
                      $obj->setIsVisible(1);
                      $obj->setIsHistorized(0);
                  }
                  $obj->setEqLogic_id($this->getId());
                  $obj->setType('action');
                  $obj->setSubType('slider');
                  $obj->setLogicalId('dhwSlider');
                  $obj->setValue($objDhw->getId());
                  $obj->setConfiguration('minValue', 10);
                  $obj->setConfiguration('maxValue', 60);
                  $obj->save();
              }
          }
      }

      // Accès au serveur Viessmann
      //
      public function getViessmann()
      {
          $clientId = trim($this->getConfiguration('clientId', ''));
          $codeChallenge = trim($this->getConfiguration('codeChallenge', ''));

          $userName = trim($this->getConfiguration('userName', ''));
          $password = trim($this->getConfiguration('password', ''));

          $installationId = trim($this->getConfiguration('installationId', ''));
          $serial = trim($this->getConfiguration('serial', ''));

          $deviceId = trim($this->getConfiguration('deviceId', '0'));
          $circuitId = trim($this->getConfiguration('circuitId', '0'));

          $expires_at = $this->getCache('expires_at', 0);
          $token = $this->getCache('token', '');

          if (($userName === '') || ($password === '') || ($clientId === '') || ($codeChallenge === '')) {
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
          "token" => $token
        ];

          try {
              $viessmannApi = new ViessmannApi($params);
          } catch (Throwable $t) {
              log::add('viessmannIot', 'error', $t->getMessage());
              return null;
          } catch (Exception $e) {
              log::add('viessmannIot', 'error', $e->getMessage());
              return null;
          }
                        
          if ((empty($installationId)) || (empty($serial))) {
              $installationId = $viessmannApi->getInstallationId();
              $serial = $viessmannApi->getSerial();
              $this->setConfiguration('installationId', $installationId);
              $this->setConfiguration('serial', $serial)->save();
              log::add('viessmannIot', 'debug', 'Récupération id installation ' . $installationId);
              log::add('viessmannIot', 'debug', 'Récupération serial ' . $serial);
              log::add('viessmannIot', 'debug', 'Récupération login id ' . $viessmannApi->getLoginId());
              log::add('viessmannIot', 'debug', 'Récupération outside temperature ' . $viessmannApi->getOutsideTemperature());

              $this->deleteAllCommands();
              $this->createCommands($viessmannApi);
          }

          if ($viessmannApi->isNewToken()) {
              $expires_at = time() + $viessmannApi->getExpiresIn() - 300;
              $this->setCache('expires_at', $expires_at);
              $this->setCache('token', $viessmannApi->getNewToken());
              log::add('viessmannIot', 'debug', 'Token expires at ' . date('d-m-Y H:i:s', $expires_at));
          }

          return $viessmannApi;
      }

      public function rafraichir($viessmannApi)
      {
          $circuitId = trim($this->getConfiguration('circuitId', '0'));

          $features = $viessmannApi->getArrayFeatures();
          $n = count($features["data"]);
          for ($i=0; $i<$n; $i++) {
              if ($features["data"][$i]["feature"] == "heating.sensors.temperature.outside") {
                  $val = $features["data"][$i]["properties"]["value"]["value"];
                  $this->getCmd(null, 'outsideTemperature')->event($val);
              } elseif ($features["data"][$i]["feature"] == $this->buildFeature($circuitId, "circulation.pump")) {
                  $val = $features["data"][$i]["properties"]["status"]["value"];
                  $this->getCmd(null, 'pumpStatus')->event($val);
              } elseif ($features["data"][$i]["feature"] == "heating.dhw.sensors.temperature.hotWaterStorage") {
                  $val = $features["data"][$i]["properties"]["value"]["value"];
                  $this->getCmd(null, 'hotWaterStorageTemperature')->event($val);
              } elseif ($features["data"][$i]["feature"] == "heating.dhw.temperature.main") {
                  $val = $features["data"][$i]["properties"]["value"]["value"];
                  $this->getCmd(null, 'dhwTemperature')->event($val);
              }
          }
          return;
      }

      public static function cron10()
      {
      }

      // Fonction exécutée automatiquement avant la création de l'équipement
      //
      public function preInsert()
      {
      }

      // Fonction exécutée automatiquement après la création de l'équipement
      //
      public function postInsert()
      {
      }

      // Fonction exécutée automatiquement avant la mise à jour de l'équipement
      //
      public function preUpdate()
      {
      }

      // Fonction exécutée automatiquement après la mise à jour de l'équipement
      //
      public function postUpdate()
      {
      }

      // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
      //
      public function preSave()
      {
      }

      // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
      //
      public function postSave()
      {
          $obj = $this->getCmd(null, 'refresh');
          if (!is_object($obj)) {
              $obj = new viessmannIotCmd();
              $obj->setName(__('Rafraichir', __FILE__));
          }
          $obj->setEqLogic_id($this->getId());
          $obj->setLogicalId('refresh');
          $obj->setType('action');
          $obj->setSubType('other');
          $obj->save();
      }

      // Fonction exécutée automatiquement avant la suppression de l'équipement
      //
      public function preRemove()
      {
      }

      // Fonction exécutée automatiquement après la suppression de l'équipement
      //
      public function postRemove()
      {
      }

      private function buildFeature($circuitId, $feature)
      {
          return "heating.circuits" . "." . $circuitId . "." . $feature;
      }
  }
  
  class viessmannIotCmd extends cmd
  {
      // Exécution d'une commande
      //
      public function execute($_options = array())
      {
          $eqlogic = $this->getEqLogic();
          switch ($this->getLogicalId()) {
            case 'refresh':
                $viessmannApi = $eqlogic->getViessmann();
                if ($viessmannApi !== null) {
                    $eqlogic->rafraichir($viessmannApi);
                    unset($viessmannApi);
                }
        }
      }
  }
