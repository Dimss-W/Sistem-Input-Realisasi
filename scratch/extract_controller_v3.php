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

    // Check all content fields, tool call response contents, etc.
    $contents = [];
    if (isset($data['content'])) {
        $contents[] = $data['content'];
    }
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $tc) {
            if (isset($tc['content'])) {
                $contents[] = $tc['content'];
            }
        }
    }

    foreach ($contents as $content) {
        $contentLines = explode("\n", $content);
        foreach ($contentLines as $cl) {
            if (preg_match('/^(\d+):\s?(.*)$/', $cl, $matches)) {
                $lineNum = (int)$matches[1];
                $lineText = $matches[2];
                // Always keep the longest version of the line or latest
                $lines[$lineNum] = $lineText;
            }
        }
    }
}
fclose($handle);

ksort($lines);

echo "Reconstructed " . count($lines) . " lines.\n";

$reconstructedContent = "";
$maxLine = count($lines) > 0 ? max(array_keys($lines)) : 0;
for ($i = 1; $i <= $maxLine; $i++) {
    if (isset($lines[$i])) {
        $text = html_entity_decode($lines[$i]);
        $text = rtrim($text, "\r");
        $reconstructedContent .= $text . "\n";
    } else {
        $reconstructedContent .= "// MISSING LINE $i\n";
    }
}

file_put_contents("C:/laragon/www/Input_Realisasi/scratch/reconstructed_controller_v3.php", $reconstructedContent);
echo "Written to reconstructed_controller_v3.php\n";
