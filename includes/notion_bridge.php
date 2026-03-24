<?php
/**
 * Notion Bridge - Professional Version
 * Handles Markdown to Block conversion for consistent syncing.
 */

class NotionBridge {
    private $token;
    private $baseUrl = 'https://api.notion.com/v1/';

    public function __construct($token) {
        $this->token = $token;
    }

    /**
     * Create a page with an icon and markdown content
     */
    public function createPage($parentId, $title, $markdownContent = '', $icon = '📄') {
        $url = $this->baseUrl . 'pages';
        
        $children = [];
        if (!empty($markdownContent)) {
            $children = $this->parseMarkdownToBlocks($markdownContent);
        }

        $data = [
            'parent' => ['page_id' => $parentId],
            'icon' => ['type' => 'emoji', 'emoji' => $icon],
            'properties' => [
                'title' => [
                    'title' => [['text' => ['content' => $title]]]
                ]
            ],
            'children' => array_slice($children, 0, 100) // Notion limit
        ];

        return $this->sendRequest('POST', $url, $data);
    }

    /**
     * Simple Markdown to Notion Blocks parser
     */
    private function parseMarkdownToBlocks($markdown) {
        $lines = explode("\n", $markdown);
        $blocks = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $type = 'paragraph';
            $content = $line;
            
            if (strpos($line, '# ') === 0) {
                $type = 'heading_1'; $content = substr($line, 2);
            } elseif (strpos($line, '## ') === 0) {
                $type = 'heading_2'; $content = substr($line, 3);
            } elseif (strpos($line, '### ') === 0) {
                $type = 'heading_3'; $content = substr($line, 4);
            } elseif (preg_match('/^[\*\-]\s/', $line)) {
                $type = 'bulleted_list_item'; $content = substr($line, 2);
            }

            $blocks[] = [
                'object' => 'block',
                'type' => $type,
                $type => [
                    'rich_text' => [['type' => 'text', 'text' => ['content' => substr($content, 0, 2000)]]]
                ]
            ];
        }
        return $blocks;
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
?>
