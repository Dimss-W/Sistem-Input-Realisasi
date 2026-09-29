<?php

$v2Path = "C:/laragon/www/Input_Realisasi/scratch/reconstructed_controller_v2.php";
$step62Path = "C:/laragon/www/Input_Realisasi/scratch/written_controller_step_62.php";

if (!file_exists($v2Path) || !file_exists($step62Path)) {
    die("Source files not found!\n");
}

$v2Lines = file($v2Path);
$finalLines = [];

// Copy lines 1 to 200 (1-indexed is indices 0 to 199)
for ($i = 0; $i < 200; $i++) {
    $finalLines[] = $v2Lines[$i];
}

// Add the missing lines 201 to 219 (indices 200 to 218)
$missingCode = <<<CODE
        RealisasiLog::record(
            $realisasi->id, 'UPDATE',
            $oldData, $realisasi->toArray(),
            null, $request->ip()
        );

        return redirect()->route('realisasi.index')
            ->with('success', "Data realisasi #{\$realisasi->id} berhasil diperbarui.");
    }

    public function destroy(Request $request, string $id)
    {
        \$realisasi = Realisasi::findOrFail(\$id);
        \$oldData   = \$realisasi->toArray();
        \$realisasi->delete();

CODE;

$missingLines = explode("\n", $missingCode);
foreach ($missingLines as $ml) {
    $finalLines[] = $ml . "\n";
}

// Copy lines 220 to 625 (indices 219 to 624)
for ($i = 219; $i < 625; $i++) {
    $finalLines[] = $v2Lines[$i];
}

// Add remaining validateRealisasi and normalizeInput from step 62 (starting from project_id rule)
$step62Lines = file($step62Path);
// Project_id validation starts around line 523 (index 522) of step 62 file
for ($i = 522; $i < count($step62Lines); $i++) {
    $finalLines[] = $step62Lines[$i];
}

$controllerContent = implode("", $finalLines);
file_put_contents("C:/laragon/www/Input_Realisasi/app/Http/Controllers/RealisasiController.php", $controllerContent);
echo "Merged controller written successfully to app/Http/Controllers/RealisasiController.php\n";
