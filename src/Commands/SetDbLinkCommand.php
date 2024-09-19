<?php

namespace Rishadblack\OracleTableLinker\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SetDbLinkCommand extends Command
{
    protected $signature = 'model:dblink {model}';
    protected $description = 'Add the HasDbLink trait to a specified model';

    public function handle(Filesystem $filesystem)
    {
        // Get the model name from the command argument
        $model = $this->argument('model');

        // Determine the full path to the model file
        $modelPath = app_path("Models/{$model}.php");

        // Check if the model file exists
        if (!$filesystem->exists($modelPath)) {
            $this->error("Model file {$model}.php not found.");
            return;
        }

        // Read the contents of the model file
        $fileContents = $filesystem->get($modelPath);

        // Check if the fully qualified trait is already present
        $traitImport = 'use Rishadblack\\OracleTableLinker\\Traits\\HasDbLink;';
        if (strpos($fileContents, $traitImport) !== false) {
            $this->info("The HasDbLink trait is already added to the {$model} model.");
            return;
        }

        // Extract namespace from the file contents
        preg_match('/^namespace\s+([^\s;]+);/m', $fileContents, $matches);
        $namespace = $matches[1] ?? 'App\\Models';

        // Add the fully qualified namespace import at the top if missing
        $namespacePattern = '/^namespace\s+[^\s]+;/m';
        $namespaceReplacement = "namespace $namespace;\n\nuse Rishadblack\\OracleTableLinker\\Traits\\HasDbLink;";
        $updatedContents = preg_replace($namespacePattern, $namespaceReplacement, $fileContents);

        // If the namespace was not found and added, ensure to include it
        if ($updatedContents === $fileContents) {
            $updatedContents = "namespace $namespace;\n\n" . $updatedContents;
        }

        // Add the trait use statement after the class declaration
        $classPattern = '/class\s+' . preg_quote($this->getClassName($model), '/') . '\s+extends\s+Model\s*{/';
        $traitUsage = "class {$this->getClassName($model)} extends Model {\n    use HasDbLink;";
        $updatedContents = preg_replace($classPattern, $traitUsage, $updatedContents);

        // Write the updated contents back to the file
        $filesystem->put($modelPath, $updatedContents);

        // Inform the user that the trait has been added
        $this->info("HasDbLink trait has been successfully added to the {$model} model.");
    }

    /**
     * Get the class name from the model path.
     *
     * @param string $model
     * @return string
     */
    protected function getClassName(string $model): string
    {
        $parts = explode('/', $model);
        return end($parts);
    }
}
