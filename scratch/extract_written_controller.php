<?php

$logPath = "C:/Users/Dimas Wijanarko/.gemini/antigravity-ide/brain/09ab9fd5-415e-43c8-8fd1-1545d62bc95c/.system_generated/logs/transcript_full.jsonl";

if (!file_exists($logPath)) {
    die("Log file not found: $logPath\n");
}

$handle = fopen($logPath, "r");

while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;

    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $tc) {
            $name = $tc['name'] ?? '';
            $args = $tc['args'] ?? [];
            if ($name === 'write_to_file') {
                $target = $args['TargetFile'] ?? '';
                if (stripos($target, 'RealisasiController.php') !== false) {
                    echo "--- FOUND WRITE_TO_FILE in step {$data['step_index']} ---\n";
                    file_put_contents("C:/laragon/www/Input_Realisasi/scratch/written_controller_step_" . $data['step_index'] . ".php", $args['CodeContent']);
                    echo "Written to scratch/written_controller_step_" . $data['step_index'] . ".php\n";
                }
            }
        }
    }
}
fclose($handle);
