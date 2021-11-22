<?php


class JsonToPhpArrayWriter
{
    protected const INDENT_SPACES = 4;

    public function convert(string $filepath, string $ns)
    {
        $jsonString = file_get_contents($filepath);
        if (!$jsonString) {
            throw new Horde_Exception("Could not find $filepath");
        }
        $data = json_decode($jsonString, true);
        if (!$data) {
            throw new Horde_Exception("$filepath does not contain valid json");
        }

        $depth = 0;
        $s = $this->getHeader($ns);
        $s .= $this->getArrayStr($data);
        $s .= $this->getFooter();
        echo $s;
    }

    protected function getArrayStr($data)
    {
        $s = '';
        $spaces = self::INDENT_SPACES;
        $rec = function ($arr, $depth=3) use (&$s, $spaces, &$rec) {
            if ($arr) {
                foreach ($arr as $key => $value) {
                    if (is_array($value)) {
                        $s .= str_repeat(' ', $depth*$spaces) . "'$key' => [\n";
                        $rec($value, $depth+1);
                    } else {
                        $s .= str_repeat(' ', $depth*$spaces) . "'$key' => _('$value'),\n";
                    }
                }
                $s .= str_repeat(' ', $depth*$spaces) . "],\n";
            }
        };
        $rec($data);
        return $s;
    }

    protected function getHeader(string $ns, string $clsName = 'GetTranslation')
    {
        return <<<ENDHEADER
        <?php
        declare(strict_types=1);
        namespace $ns;

        use Horde\Core\Translation\Middleware\Api\GetTranslationBase;

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
