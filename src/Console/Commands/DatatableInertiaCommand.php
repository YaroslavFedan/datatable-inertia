<?php

namespace Dongrim\DatatableInertia\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatatableInertiaCommand extends Command
{
    protected $signature = 'datatable:make {DatatableName?}';

    protected $description = 'Create a new Datatable service class.';

    /**
     * Name Datatable class
     *
     * @var string
     */
    protected $datatableName;

    /**
     * Path to directory containing the new Datatable class file
     *
     * @var string
     */
    protected $pathToDirectory;

    /**
     * Path to file the Datatable stub
     *
     * @var string
     */
    protected $stub;

    /**
     * @var array
     */
    protected $replaces;


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $this->pathToDirectory = config('datatables.basePath', 'App/Datatables');
        $this->datatableName = $this->argument('DatatableName') ?? 'ExampleDatatable';
        $this->stub = __DIR__ . '/../../../stubs/DatatableInertia.stub';

        if (blank($this->argument('DatatableName'))) {
            $this->interview();
        }

        $this->replaces = [
            'NAMESPASE' => $this->getNamespaceClass(),
            'CLASS' => $this->getClassName()
        ];

        $this->createDatatableFile();

        return 0;
    }

    protected function interview()
    {
        $changePathTo = $this->confirm("Do you want to change the default Datatable path?");

        if ($changePathTo) {
            $pathToDirectory = $this->ask("Set path to Datatable directory");
            if (!blank($pathToDirectory)) {
                $this->pathToDirectory = $pathToDirectory;
            }
        }

        $changeDatatableName = $this->confirm("Do you want to set the Datatable class name?");

        if ($changeDatatableName) {
            $datatableName = $this->ask("Set Datatable class name");
            if (!blank($datatableName)) {
                $this->datatableName = $datatableName;
            }
        }
    }

    protected function createDatatableFile()
    {
        $path = base_path($this->pathToDirectory);
        $filename = $this->getClassName() . '.php';
        $filePath = $this->pathToDirectory . DIRECTORY_SEPARATOR . $filename;

        if (!File::exists($path)) {
            $createDirecory = $this->confirm("Do you want to create Datatable class a new directory: " . $filePath);
            if (!$createDirecory) {
                $this->info("Exit.");
                return;
            }
        }

        if (!File::isDirectory($path)) {
            File::ensureDirectoryExists($path);
        }

        $this->saveTo($path, $filename);
        $this->info('Class ' . $this->getClassName() . ' created successfully.');
    }


    protected function getNamespaceClass()
    {
        return Str::replace('/', '\\', $this->pathToDirectory);
    }

    protected function getClassName()
    {
        return Str::studly($this->datatableName);
    }

    /**
     * Save stub to specific path.
     *
     * @param string $path
     * @param string $filename
     *
     * @return bool
     */
    public function saveTo($path, $filename)
    {
        return file_put_contents($path . '/' . $filename, $this->getContents());
    }

    /**
     * Get stub contents.
     *
     * @return mixed|string
     */
    protected function getContents()
    {
        $contents = file_get_contents($this->stub);

        foreach ($this->replaces as $search => $replace) {
            $contents = str_replace('$' . strtoupper($search) . '$', $replace, $contents);
        }

        return $contents;
    }
}
