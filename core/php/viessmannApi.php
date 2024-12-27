<?php

// Classe gérant les exceptions
//
class ViessmannApiException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

// Classe gérant l'accès au serveur Viessmann
//
class ViessmannApi
{
    const AUTHORIZE_URL = "https://iam.viessmann.com/idp/v3/authorize";
    const CALLBACK_URI = "http://localhost:4200/";
    
    const TOKEN_URL = "https://iam.viessmann.com/idp/v3/token";

    const IDENTITY_URL = "https://api.viessmann.com/users/v1/users/me?sections=identity";
    
    const GATEWAY_URL = "https://api.viessmann.com/iot/v1/equipment/gateways";

    const FEATURES_URL = "https://api.viessmann.com/iot/v1/equipment";
 
    const EVENTS_URL_1 = "https://api.viessmann.com/iot/v2/events-history/installations/";
    const EVENTS_URL_2 = "/events";

    // Les paramètres d'accès au serveur
    //
    //   Obligatoires
    //
    private $clientId;
    private $codeChallenge;
    private $user;
    private $pwd;
    //
    // Facultatifs
    //
    private $installationId;
    private $serial;
    private $deviceId;
    private $circuitId;
    //
    // Gestion du token
    //
    private $accessToken;
    private $refreshToken;
    private $expires_at;
    private $expires_in;
    private $if_new_token;

    //
    // Données récupérées du serveur Viessmann
    //
    private $identity;
    private $gateway;
    private $features;
    private $events;

    private $logFeatures;
 
    // Constructeur
    //
    public function __construct($params)
    {

        // Contrôle des paramètres et mémorisation dans la classe
        //
        $requiredParams = ['clientId', 'codeChallenge', 'user', 'pwd'];
        foreach ($requiredParams as $param) {
            if (!array_key_exists($param, $params) || empty($params[$param])) {
            throw new ViessmannApiException(ucfirst($param) . ' obligatoire', 2);
            }
            $this->$param = $params[$param];
        }

        $this->installationId = array_key_exists('installationId', $params) ? trim($params['installationId']) : '';
        $this->serial = array_key_exists('serial', $params) ? trim($params['serial']) : '';
        $this->deviceId = array_key_exists('deviceId', $params) ? trim($params['deviceId']) : 0;
        $this->circuitId = array_key_exists('circuitId', $params) ? trim($params['circuitId']) : 0;
        $this->accessToken = array_key_exists('access_token', $params) ? trim($params['access_token']) : '';
        $this->refreshToken = array_key_exists('refresh_token', $params) ? trim($params['refresh_token']) : '';
        $this->expires_at = array_key_exists('expires_at', $params) ? intval($params['expires_at']) : 0;
        $this->logFeatures = array_key_exists('logFeatures', $params) ? $params['logFeatures'] : '';
            
        $this->identity = array();
        $this->gateway = array();
        $this->features = array();
        $this->events = array();
        
        // Si c'est possible on réutilise l'ancien token
        //
        $this->if_new_token = false;

        if ((time() <= $this->expires_at) && !empty($this->accessToken) && !empty($this->installationId) && !empty($this->serial)) {
            return;
        }

        if (!$this->refreshToken()) {
            if (!($code = $this->getCode())) {
            throw new ViessmannApiException("Erreur acquisition code sur le serveur Viessmann", 2);
            }
        
            if (!$this->getToken($code)) {
            throw new ViessmannApiException("Erreur acquisition token sur le serveur Viessmann", 2);
            }
        }   

        $this->if_new_token = true;

        if (empty($this->installationId) || empty($this->serial)) {
            $this->getGateway();
            $this->getIdentity();
            $this->installationId = $this->getInstallationId(0);
            $this->serial = $this->getSerial(0);
        }
    }

    // Lire le code d'accès au serveur Viessmann
    //
    private function getCode()
    {
        // Paramètres code
        //
        $url = self::AUTHORIZE_URL . "?client_id=" . $this->clientId . "&code_challenge=" . $this->codeChallenge . "&scope=IoT%20User%20offline_access&redirect_uri=" .
        self::CALLBACK_URI . "&response_type=code";
        
        $header = array("Content-Type: application/x-www-form-urlencoded");

        $curloptions = array(
           CURLOPT_URL => $url,
           CURLOPT_HTTPHEADER => $header,
           CURLOPT_SSL_VERIFYPEER => false,
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_USERPWD => $this->user.':'.$this->pwd,
           CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
           CURLOPT_POST => true,
        );

        // Appel Curl Code
        //
        $curl = curl_init();
        curl_setopt_array($curl, $curloptions);
        $response = curl_exec($curl);
        curl_close($curl);

        // Extraction Code
        //
        if (preg_match('/code=([^&"]+)/', $response, $matches)) {
            return $matches[1];
        } else {
            return false;
        }
    }

    // Lire le token d'accès au serveur Viessmann
    //
    private function getToken($code)
    {
        // Paramètres Token
        $url = self::TOKEN_URL . "?grant_type=authorization_code&code_verifier=" . $this->codeChallenge . "&client_id=" .
        $this->clientId . "&redirect_uri=" . self::CALLBACK_URI . "&code=" . $code;
        
        $header = ["Content-Type: application/x-www-form-urlencoded"];

        $curloptions = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_POST => true,
        ];

        // Appel Curl Token
        $curl = curl_init();
        curl_setopt_array($curl, $curloptions);
        $response = curl_exec($curl);
        curl_close($curl);

        // Extraction Token
        $json = json_decode($response, true);
        if (isset($json['error']) || !isset($json['access_token'], $json['expires_in'])) {
            return false;
        }

        $this->accessToken = $json['access_token'];
        $this->refreshToken = $json['refresh_token'] ?? '';
        $this->expires_in = $json['expires_in'];

        if (empty($this->refreshToken)) {
            log::add('viessmannIot', 'debug', 'No Refresh token');
        }

        return true;
    }

    // Rafraichir le token d'accès au serveur Viessmann 
    //
    private function refreshToken()
    {
        if (empty($this->refreshToken)) {
            return false;
        }

        // Paramètres Token
        $url = self::TOKEN_URL . "?grant_type=refresh_token&refresh_token=" . $this->refreshToken . "&client_id=" . $this->clientId;
        $header = ["Content-Type: application/x-www-form-urlencoded"];

        $curloptions = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_POST => true,
        ];

        // Appel Curl Token
        $curl = curl_init();
        curl_setopt_array($curl, $curloptions);
        $response = curl_exec($curl);
        curl_close($curl);

        // Extraction Token
        $json = json_decode($response, true);
        if (isset($json['error']) || !isset($json['access_token'], $json['expires_in'])) {
            log::add('viessmannIot', 'debug', 'Refresh token error');
            return false;
        }

        $this->accessToken = $json['access_token'];
        $this->refreshToken = $json['refresh_token'] ?? '';
        $this->expires_in = $json['expires_in'];

        if (empty($this->refreshToken)) {
            log::add('viessmannIot', 'debug', 'No Refresh token');
        }

        return true;
    }

    // Lire les données d'identité
    //
    public function getIdentity()
    {
        // Lire les données utilisateur
        //
        $url = self::IDENTITY_URL;
        $header = array("Authorization: Bearer " . $this->accessToken);

        $curloptions = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        );

        // Appel Curl Données
        //
        $curl = curl_init();
        curl_setopt_array($curl, $curloptions);
        $response = curl_exec($curl);
        curl_close($curl);

        $this->identity = json_decode($response, true);

        $json_file = __DIR__ . '/../../data/identity.json';
        $response = str_replace($this->installationId, 'XXXXXX', $response);
        $response = str_replace($this->serial, 'XXXXXXXXXXXXXXXX', $response);
        file_put_contents($json_file, $response);
    }

    // Lire les données du gateway
    //
    public function getGateway()
    {

        // Lire les données du gateway
        //
        $url = self::GATEWAY_URL;
        $header = array("Authorization: Bearer " . $this->accessToken);

        $curloptions = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        );

        // Appel Curl Données
        //
        $curl = curl_init();
        curl_setopt_array($curl, $curloptions);
        $response = curl_exec($curl);
        curl_close($curl);

        $this->gateway = json_decode($response, true);
        $json_file = __DIR__ . '/../../data/gateway.json';
        $response = str_replace($this->installationId, 'XXXXXX', $response);
        $response = str_replace($this->serial, 'XXXXXXXXXXXXXXXX', $response);
        file_put_contents($json_file, $response);

        if (array_key_exists('statusCode', $this->gateway)) {
            $json_file = __DIR__ . '/../../data/erreur.json';
            $response = str_replace($this->installationId, 'XXXXXX', $response);
            $response = str_replace($this->serial, 'XXXXXXXXXXXXXXXX', $response);
            file_put_contents($json_file, $response);

            return $this->gateway["message"];

        }

        return true;
        
    }

    // Lire les features
    //
    public function getFeatures()
    {
        // Lire les données features
        $url = self::FEATURES_URL . "/installations/" . $this->installationId . "/gateways/" . $this->serial . "/devices/" . $this->deviceId . "/features";
        $header = ["Authorization: Bearer " . $this->accessToken];

        $curloptions = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        ];

        // Appel Curl Données
        $curl = curl_init();
        curl_setopt_array($curl, $curloptions);
        $response = curl_exec($curl);
        curl_close($curl);

        $this->features = json_decode($response, true);

        if ($this->logFeatures === 'Oui') {
            $json_file = __DIR__ . '/../../data/features.json';
            $response = str_replace([$this->installationId, $this->serial], ['XXXXXX', 'XXXXXXXXXXXXXXXX'], $response);
            file_put_contents($json_file, $response);
        }

        if (isset($this->features['statusCode'])) {
            $json_file = __DIR__ . '/../../data/erreur.json';
            $response = str_replace([$this->installationId, $this->serial], ['XXXXXX', 'XXXXXXXXXXXXXXXX'], $response);
            file_put_contents($json_file, $response);

            $message = $this->features["message"];
            if (isset($this->features['extendedPayload']['details'])) {
                $message .= ' ( ' . $this->features['extendedPayload']['details'] . ' ) ';
            }

            return $message;
        }

        return true;
    }

    // Lire log features
    //
    public function getLogFeatures()
    {
        return $this->logFeatures;
    }

    // Lire les events
    //
    public function getEvents()
    {
        // Lire les données events
        //
        $url = self::EVENTS_URL_1 . $this->installationId . self::EVENTS_URL_2 . "?gatewaySerial=" . $this->serial . "&limit=1000";
        $header = array("Authorization: Bearer " . $this->accessToken);

        $curloptions = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        );

        // Appel Curl Données
        //
        $curl = curl_init();
        curl_setopt_array($curl, $curloptions);
        $response = curl_exec($curl);
        curl_close($curl);
/*
        $response = '{
            "data": [        
                
        {
            "eventType": "device-error",
            "gatewaySerial": "XXXXXXXXXXXXXXXX",
            "body": {
                "errorCode": "80",
                "deviceId": "0",
                "modelId": "VScotHO1_200_11",
                "active": false,
                "equipmentType": "Boiler",
                "errorEventType": "Error",
                "errorDescription": "No flame formation – gas pressure absent/low"
            },
            "createdAt": "2021-08-23T21:39:26.498Z",
            "eventTimestamp": "2021-08-23T21:38:56.000Z",
            "editedBy": "system",
            "origin": "system"
        },
        
        {
            "eventType": "device-error",
            "gatewaySerial": "XXXXXXXXXXXXXXXX",
            "body": {
                "errorCode": "80",
                "deviceId": "0",
                "modelId": "VScotHO1_200_11",
                "active": true,
                "equipmentType": "Boiler",
                "errorEventType": "Error",
                "errorDescription": "No flame formation – gas pressure absent/low"
            },
            "createdAt": "2021-08-23T21:38:47.061Z",
            "eventTimestamp": "2021-08-23T21:28:45.000Z",
            "editedBy": "system",
            "origin": "system"
        }
    ],
    "cursor": {
        "next": ""
    }
}';
*/
        $this->events = json_decode($response, true);

        if ($this->logFeatures == 'Oui') {
            $json_file = __DIR__ . '/../../data/events.json';
            $response = str_replace($this->installationId, 'XXXXXX', $response);
            $response = str_replace($this->serial, 'XXXXXXXXXXXXXXXX', $response);
            file_put_contents($json_file, $response);
        }
        
        if (array_key_exists('statusCode', $this->features)) {
            throw new ViessmannApiException($this->features["message"], 2);
        }
    }

    // Ecrire une feature
    //
    public function setFeature($feature, $action, $data)
    {

        // Lire les données du gateway
        //
        $url = self::FEATURES_URL . "/installations/" . $this->installationId . "/gateways/" . $this->serial . "/devices/" . $this->deviceId . "/features/" . $feature . "/commands/" . $action;

        $header = array(
            "Content-Type: application/json",
            "Accept : application/vnd.siren+json",
            "Authorization: Bearer " . $this->accessToken);
 
        $curloptions = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        );

        // Appel Curl Données
        //
        $curl = curl_init();
        curl_setopt_array($curl, $curloptions);
        $response = curl_exec($curl);
        curl_close($curl);

        $features = json_decode($response, true);

        if (array_key_exists('statusCode', $features)) {
            throw new ViessmannApiException($features["message"], 2);
        }
    }

    // Lire Installation Id
    //
    public function getInstallationId($numChaudiere)
    {
        return $this->gateway["data"][$numChaudiere]["installationId"];
    }

    // Lire Login Id
    //
    public function getSerial($numChaudiere)
    {
        return $this->gateway["data"][$numChaudiere]["serial"];
    }

    // Si nouveau token
    //
    public function isNewToken()
    {
        return $this->if_new_token;
    }

    // Get Access Token
    //
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    // Get Refresh Token
    //
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    // Expires In
    //
    public function getExpiresIn()
    {
        return $this->expires_in;
    }

    // Get Array Identity
    //
    public function getArrayIdentity()
    {
        return $this->identity;
    }

    // Get Array Gateway
    //
    public function getArrayGateway()
    {
        return $this->gateway;
    }

    // Get Array Features
    //
    public function getArrayFeatures()
    {
        return $this->features;
    }

    // Get Array Events
    //
    public function getArrayEvents()
    {
        return $this->events;
    }
}
