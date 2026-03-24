<?php
/**
 * Notion Birthday Database Sync
 */
require_once 'c:\TGPN\consultora\includes\db.php';
require_once 'c:\TGPN\consultora\includes\notion_bridge.php';

class NotionBirthdaySync {
    private $token;
    private $baseUrl = 'https://api.notion.com/v1/';

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * Create a Database in Notion
     */
    public function createBirthdayDatabase($parentPageId) {
        $url = $this->baseUrl . 'databases';
        $data = [
            'parent' => ['type' => 'page_id', 'page_id' => $parentPageId],
            'icon' => ['type' => 'emoji', 'emoji' => '🎂'],
            'title' => [['type' => 'text', 'text' => ['content' => '🎁 Calendario de Cumpleaños']]],
            'properties' => [
                'Cliente' => ['title' => []],
                'Cumpleaños' => ['date' => []],
                'Teléfono' => ['phone_number' => []],
                'ID Sistema' => ['number' => []]
            ]
        ];

        return $this->sendRequest('POST', $url, $data);
    }

    /**
     * Add a client to the Notion Database
     */
    public function addClientToDatabase($databaseId, $nombre, $apellido, $fechaNac, $telefono, $idLocal) {
        $url = $this->baseUrl . 'pages';
        
        // Formatear fecha para Notion (YYYY-MM-DD)
        // En la DB local está como 2000-MM-DD si solo se guardó mes/dia
        $notionDate = $fechaNac; 

        $data = [
            'parent' => ['database_id' => $databaseId],
            'properties' => [
                'Cliente' => [
                    'title' => [['text' => ['content' => "$nombre $apellido"]]]
                ],
                'Cumpleaños' => [
                    'date' => ['start' => $notionDate]
                ],
                'Teléfono' => [
                    'phone_number' => $telefono
                ],
                'ID Sistema' => [
                    'number' => intval($idLocal)
                ]
            ]
        ];

        return $this->sendRequest('POST', $url, $data);
    }

    private function sendRequest($method, $url, $data = null) {
        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json',
            'Notion-Version: 2022-06-28'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['status' => $httpCode, 'body' => json_decode($response, true)];
    }
}

$token = getenv('NOTION_API_KEY');
$rootPageId = '324e0e274c7b809498c0f481b84f6a46';

$sync = new NotionBirthdaySync($token);

echo "1. Creando Base de Datos de Cumpleaños en Notion...\n";
$resDb = $sync->createBirthdayDatabase($rootPageId);

if ($resDb['status'] == 200) {
    $notionDbId = $resDb['body']['id'];
    echo "DB Creada con éxito: $notionDbId\n";

    echo "2. Obteniendo clientes de la base local...\n";
    $stmt = $pdo->query("SELECT * FROM Clientes WHERE FechaNac IS NOT NULL AND Estado = 1");
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "3. Sincronizando " . count($clientes) . " clientes...\n";
    foreach ($clientes as $c) {
        echo "Sincronizando: " . $c['Nombre'] . " " . $c['Apellido'] . "\n";
        $sync->addClientToDatabase(
            $notionDbId, 
            $c['Nombre'], 
            $c['Apellido'], 
            $c['FechaNac'], 
            $c['Telefono'], 
            $c['IdCliente']
        );
        usleep(333333); // Evitar rate limiting de Notion
    }
    echo "\n✅ ¡Base de Datos de Cumpleaños sincronizada en Notion!";
} else {
    echo "Error creando la DB: " . json_encode($resDb);
}
?>
