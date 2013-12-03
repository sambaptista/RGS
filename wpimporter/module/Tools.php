<?php


class Tools
{

    /** Nettoie un nom de fichier afin qu'il soit utilisable dans une url
     *
     * @param filename Nom du fichier complet avec extension s'il y en a une.
     * @param addAfter texte à mettre à la fin du nom, avant l'extension
     */
    public static function cleanFilename($filename, $addAfter = null)
    {
        $nameArray = explode('.', $filename);
        $ext = array_pop($nameArray);
        $name = implode('-', $nameArray);

        $name = strtolower($name);
        $name = preg_replace('~[^\\pL0-9_]+~u', '-', $name);
        $name = trim($name, "-");
        $name = iconv("utf-8", "us-ascii//TRANSLIT", $name);
        $name = strtolower($name);
        $name = preg_replace('~[^-a-z0-9_]+~', '', $name);

        if ($addAfter) {
            $name .= '-' . $addAfter;
        }
        $name .= '.' . $ext;

        return $name;
    }

    /** Imprime le contenu d'un tableau en enlevant les texte afin que la structure soit visible directement.
     */
    public static function printStructure($array)
    {
        echo '<pre>';
        print_r(Tools::getStructure($array));
        echo '</pre>';
    }

    public static function getStructure($array)
    {
        foreach ($array as $key => $cell) {
            if (is_string($cell) && !is_numeric($cell)) {
                if (strlen($cell) > 50) {
                    $array[$key] = ''; //substr($cell, 0, 50).'</a></strong></span></p></div>...';
                } else {
                    $array[$key] = $cell;
                }
                //$array[$key] = '';
            } else if (is_array($cell)) {
                $array[$key] = Tools::getStructure($cell);
            }
        }

        return $array;
    }

    public static function getUrl($string)
    {
        $xmlDoc = new DOMDocument();
        @$xmlDoc->loadHTML($string);

        return $xmlDoc->getElementsByTagName('a')->item(0)->getAttribute('href');

    }

    public static function getLinkText($string)
    {

        $xmlDoc = new DOMDocument();
        @$xmlDoc->loadHTML($string);

        return $xmlDoc->getElementsByTagName('a')->item(0)->nodeValue;
    }

    public static function arrayToString($array, $deep = 0)
    {
        $string = '';
        $tab = "";
        for ($i = 0; $i <= $deep; $i++) {
            $tab .= "\t";
        }
        foreach ($array as $key => $arr) {
            if (is_array($arr)) {
                $string .= $tab . $key . " => \n" . Tools::arrayToString($arr, $deep + 1);
            } else {
                $string .= $tab . $key . ' => ' . $arr . ",\n";
            }
        }

        return $string;
    }
}

