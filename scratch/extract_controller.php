<?php

$logPath = "C:/Users/Dimas Wijanarko/.gemini/antigravity-ide/brain/09ab9fd5-415e-43c8-8fd1-1545d62bc95c/.system_generated/logs/transcript_full.jsonl";

if (!file_exists($logPath)) {
    die("Log file not found: $logPath\n");
}

$handle = fopen($logPath, "r");
$lines = [];

while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;

    $content = $data['content'] ?? '';
    if (empty($content)) continue;

    // Check if the content is a file view of RealisasiController.php
    if (stripos($content, 'RealisasiController.php') !== false) {
        // Find line patterns: e.g. "449:     public function dashboard("
        preg_match_all('/^(\d+):\s*(.*)$/m', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $lineNum = (int)$m[1];
            $lineText = $m[2];
            $lines[$lineNum] = html_entity_decode($lineText);
        }
    }
}
fclose($handle);

ksort($lines);

echo "Reconstructed " . count($lines) . " lines.\n";

$reconstructedContent = "";
for ($i = 1; $i <= max(array_keys($lines)); $i++) {
    if (isset($lines[$i])) {
        $reconstructedContent .= $lines[$i] . "\n";
    } else {
        $reconstructedContent .= "// MISSING LINE $i\n";
    }
}

file_put_contents("C:/laragon/www/Input_Realisasi/scratch/reconstructed_controller.php", $reconstructedContent);
echo "Written to reconstructed_controller.php\n";
