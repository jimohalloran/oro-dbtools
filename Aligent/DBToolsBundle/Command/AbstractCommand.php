<?php
namespace Aligent\DBToolsBundle\Command;

use Aligent\DBToolsBundle\Helper\Compressor\Compressor;
use Aligent\DBToolsBundle\Helper\Compressor\Gzip;
use Aligent\DBToolsBundle\Helper\Compressor\Uncompressed;
use Aligent\DBToolsBundle\Helper\DatabaseHelper;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract Command Class - All Commands are children of this.
 *
 * @category  Aligent
 * @package   DBToolsBundle
 * @author    Adam Hall <adam.hall@aligent.com.au>
 * @copyright 2017 Aligent Consulting.
 * @license   https://opensource.org/licenses/mit MIT License
 * @link      http://www.aligent.com.au/
 **/

abstract class AbstractCommand extends Command
{
    /** @var  DatabaseHelper */
    protected $database;

    protected Uncompressed $uncompressedCompressor;
    protected Gzip $gzipCompressor;

    public function processCommand($command)
    {
        $descriptorSpec = array(
            0 => STDIN,
            1 => STDOUT,
            2 => STDERR,
        );

        $pipes = array();
        $process = proc_open($command, $descriptorSpec, $pipes);

        if (is_resource($process)) {
            proc_close($process);
        }
    }

    public function setDatabaseHelper($database)
    {
      $this->database = $database;
    }

    public function setUncompressedCompressor(Uncompressed $uncompressedCompressor) {
        $this->uncompressedCompressor = $uncompressedCompressor;
    }

    public function setGzipCompressor(Gzip $gzip)
    {
        $this->gzipCompressor = $gzip;
    }

    /**
     * @param string $type
     * @return Compressor
     * @throws InvalidArgumentException
     */
    protected function getCompressor($type)
    {
        switch ($type) {
            case null:
                return $this->uncompressedCompressor;
            case 'gz':
            case 'gzip':
                return $this->gzipCompressor;
            default:
                throw new InvalidArgumentException(
                    "Compression type '{$type}' is not supported. Known values are: gz, gzip"
                );
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $text
     * @param string $style
     */
    protected function writeSection(OutputInterface $output, $text, $style = 'bg=blue;fg=white')
    {
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock($text, $style, true),
            '',
        ));
    }
}
