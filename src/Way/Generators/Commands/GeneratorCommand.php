<?php namespace Way\Generators\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;
use Way\Generators\Filesystem\FileAlreadyExists;
use Way\Generators\Generator;

abstract class GeneratorCommand extends Command
{
    /**
     * The Generator instance.
     *
     * @var Generator
     */
    protected $generator;

    /**
     * Create a new GeneratorCommand instance.
     *
     * @param  Generator  $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    /**
     * Fetch the template data.
     *
     * @return array
     */
    abstract protected function getTemplateData();

    /**
     * The path to where the file will be created.
     *
     * @return mixed
     */
    abstract protected function getFileGenerationPath();

    /**
     * Get the path to the generator template.
     *
     * @return mixed
     */
    abstract protected function getTemplatePath();

    /**
     * Compile and generate the file.
     * @throws \Way\Generators\Filesystem\FileNotFound
     */
    public function fire()
    {
        $filePathToGenerate = $this->getFileGenerationPath();

        try {
            $this->generator->make(
                $this->getTemplatePath(),
                $this->getTemplateData(),
                $filePathToGenerate
            );

            $this->info("Created: {$filePathToGenerate}");
        } catch (FileAlreadyExists $e) {
            $this->error("The file, {$filePathToGenerate}, already exists! I don't want to overwrite it.");
        }
    }

    /**
     * Get a directory path through a command option, or from the configuration.
     *
     * @param $option
     * @param $configName
     * @return array|bool|mixed|string|null
     */
    protected function getPathByOptionOrConfig($option, $configName)
    {
        if ($path = $this->option($option)) {
            return $path;
        }

        return Config::get("generators.config.{$configName}");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_REQUIRED, 'Where should the file be created?'],
            ['templatePath', null, InputOption::VALUE_REQUIRED, 'The location of the template for this generator']
        ];
    }
}
