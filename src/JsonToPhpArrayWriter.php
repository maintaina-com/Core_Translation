<?php

namespace Horde\Core\Translation;

class JsonToPhpArrayWriter
{
    protected const INDENT_SPACES = 4;

    public function convert(string $phpNs, string $jsonPath)
    {
        $jsonPath = rtrim($jsonPath, '/');
        if (!is_dir($jsonPath)) {
            throw new \Exception("Could not find $jsonPath");
        }
        chdir($jsonPath);
        $files = glob('*.json');
        $data = [];
        foreach ($files as $filename) {
            $ns = substr($filename, 0, strlen($filename) - 5);
            $content =  file_get_contents($filename);
            $nsData = json_decode($content, true);
            if (is_null($nsData)) {
                throw new \Exception("$filename does not contain valid json");
            }
            $data[$ns] = $nsData;
        }

        $s = $this->getHeader($phpNs);
        $s .= $this->getArrayStr($data);
        $s .= $this->getFooter();
        echo $s;
    }

    protected function getArrayStr($data)
    {
        $s = '';
        $spaces = self::INDENT_SPACES;
        $initialDepth = 3;
        $rec = function ($arr, $depth) use (&$s, $spaces, &$rec, $initialDepth) {
            if ($arr) {
                foreach ($arr as $key => $value) {
                    if (is_array($value)) {
                        $s .= str_repeat(' ', $depth * $spaces) . "'$key' => [\n";
                        $rec($value, $depth + 1);
                    } else {
                        $value = str_replace("'", "\\'", $value);
                        $s .= str_repeat(' ', $depth * $spaces) . "'$key' => _('$value'),\n";
                    }
                }
                if ($depth > $initialDepth) {
                    $s .= str_repeat(' ', ($depth - 1) * $spaces) . "],\n";
                }
            }
        };
        $rec($data, $initialDepth);
        // $s = substr($s, 0, -3);
        return $s;
    }

    protected function getHeader(string $ns, string $clsName = 'GetTranslation')
    {
        return <<<ENDHEADER
        <?php

        declare(strict_types=1);
        
        namespace $ns;

        use Horde\Core\Translation\GetTranslationBase;

        /**
         * Returns locale json file for a specific language and namespace.
         */
        class $clsName extends GetTranslationBase
        {
            protected function getData(): array
            {
                return  [
        
        ENDHEADER;
    }

    protected function getFooter(string $clsName = 'GetTranslation')
    {
        return <<<'ENDFOOTER'
                ];
            }
        }

        ENDFOOTER;
    }
}
