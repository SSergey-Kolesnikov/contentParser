<?php
class App {
    /** @var array $config */
    public $config = [
        'new_domain' => null,
        'old_domain' => null,
        'schema' => 'http',
        'cms' => '',
        'folders' => [
            'cache' => 'cache',
            'files' => 'files'
        ],
    ];

    public $new_site = null;

    public $old_site = null;

    /**
     * App constructor
     *
     * @param array $config
     */
    function __construct(array $config) {
        $this->config = (object) array_merge($this->config, $config);

        $this->new_site = $this->config->schema . '://' . $this->config->new_domain;
        $this->old_site = $this->config->schema . '://' . $this->config->old_domain;
        $this->config->folder_cache = str_replace(basename(__DIR__), '', __DIR__) . $this->config->folders['cache'] . DIRECTORY_SEPARATOR;
        $this->config->folder_files = str_replace(basename(__DIR__), '', __DIR__) . $this->config->folders['files'] . DIRECTORY_SEPARATOR;

        (is_dir($this->config->folder_cache)) ?: mkdir($this->config->folder_cache);
        (is_dir($this->config->folder_files)) ?: mkdir($this->config->folder_files);
    }

    /**
     * @param $url
     * @param boolean $only_header
     * @param boolean $user_agent
     * @param array $options
     *
     * @return object
     */
    function getHttp($url, $only_header = false, $user_agent = false, array $options = []) {
        $curl = curl_init();

        $parameters = [
            CURLOPT_URL => $url,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_NOBODY => $only_header,
            CURLOPT_USERAGENT => $user_agent ? $_SERVER['HTTP_USER_AGENT'] : "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/530.5 (KHTML, like Gecko) Chrome/2.0.172.39 Safari/530.5"
        ];

        if (!empty($options)) {
            $parameters = array_merge($parameters, $options);
        }

        curl_setopt_array($curl, $parameters);

        $output = curl_exec($curl);
        $error_code = curl_errno($curl);
        $error_message = curl_error($curl);
        $header = curl_getinfo($curl);

        curl_close($curl);

        return (object) array_merge($header, [
            'output' => $output,
            'error_code' => $error_code,
            'error_message' => $error_message,
        ]);
    }

    /**
     * @param $route
     * @param boolean $required
     * @param boolean $cache
     * @param string $charset
     *
     * @return boolean|null|string
     * @throws Exception
     */
    function getHtml($route, $required = true, $cache = true, $charset = 'utf-8') {
        $output = null;

        if ($cache) {
            $response = $this->getCache($route, $required);
        } else {
            $response = $this->getFile($route, $required);
        }

        (is_null($response)) ?: $output = $response;

        return $output;
    }

    /**
     * @param $route
     * @param boolean $array
     * @param boolean $required
     * @param boolean $cache
     *
     * @return array|object
     * @throws Exception
     */
    function getXml($route, $array = true, $required = true, $cache = true) {
        $output = null;

        if ($cache) {
            $response = $this->getCache($route, $required);
        } else {
            $response = $this->getFile($route, $required);
        }

        if (!is_null($response)) {
            if (($output = @simplexml_load_string($response)) === false) {
                throw new Exception("Error parsing xml");
            }
        }

        return $array ? (array) json_decode(json_encode($output), true) : (object) $output;
    }

    /**
     * @param $route
     * @param boolean $remove_domain
     * @param boolean $cache
     *
     * @return object
     * @throws Exception
     */
    function getSiteMap($route, $remove_domain = false, $cache = true) {
        $xml = $this->getXml($route, true, true, $cache);
        $xml = end($xml);
        $output = [];

        foreach ($xml as $i) {
            if ($remove_domain) {
                $loc = parse_url($i['loc']);

                unset($loc['scheme'], $loc['host']);

                array_push($output, $this->cleanText(implode('', $loc)));
            } else {
                array_push($output, $this->cleanText($i['loc']));
            }
        }

        return (object) $output;
    }

    /**
     * @param $route
     * @param boolean $required
     *
     * @return boolean|null|string
     * @throws Exception
     */
    function getCache($route, $required = true) {
        $file = null;

        switch (true) {
            case $route instanceof Closure:
                break;
            case is_file($route):
                $file = $route;
                break;
            case $this->is_url($route):
                $file = $this->getFileName($route, 'cache');
                if (!$this->checkFile($file)) {
                    $this->setFile($file, $route, $required, 'url');
                }
                break;
        }

        return $this->getFile($file, $required);
    }

    /**
     * @param string $value
     * @param string $type
     * @param boolean|string $prefix
     *
     * @return string
     */
    function getFileName($value, $type = 'json', $prefix = false) {
        switch ($type) {
            case 'json':
                return $this->config->folder_files . $prefix . $value . '.json';
                break;
            case 'cache':
                return $this->config->folder_cache . $prefix . sha1($value) . '.cache';
                break;
            case 'crypt.json':
                return $this->config->folder_files . $prefix . sha1($value) . '.json';
                break;
            default:
                return '';
        }
    }

    /**
     * @param $route
     * @param boolean $required
     *
     * @return boolean|null|string
     * @throws Exception
     */
    function getFile($route, $required = true) {
        $output = null;

        switch (true) {
            case is_file($route) && is_readable($route):
                $output = file_get_contents($route);
                break;
            case $this->is_url($route):
                $response = $this->getHttp($route);
                if ($response->http_code != 200) {
                    if ($required) {
                        throw new Exception("Invalid page header, code: '$response->http_code', request: '$route'");
                    }
                } else {
                    $output = $response->output;
                }
                break;
        }

        if (!$output && $required) {
            throw new Exception("Empty response, request: '$route'");
        }

        return $output;
    }

    /**
     * @param $file
     * @param $value
     * @param boolean $required
     * @param string $type
     *
     * @return boolean|integer|null
     * @throws Exception
     */
    function setFile($file, $value, $required = true, $type = 'file') {
        $set = null;

        switch ($type) {
            case 'url':
                if ($this->is_url($value)) {
                    if ($this->checkFile($file)) {
                        return true;
                    }
                    $set = $this->getFile($value, $required);
                }
                break;
            case 'file':
                if (is_file($value)) {
                    $set = file_get_contents($value);
                }
                break;
            case 'json':
                $set = json_encode($value);
                break;
        }

        $file = preg_replace('#(\\|\/)#', DIRECTORY_SEPARATOR, $file);

        if (strripos($file, $_SERVER['DOCUMENT_ROOT']) === false) {
            $file = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
        }

        $info = (object) pathinfo($file);
        $explode = ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], null, $info->dirname), DIRECTORY_SEPARATOR);
        $dirname = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;

        foreach (explode(DIRECTORY_SEPARATOR, $explode) as $folder) {
            $dirname .= $folder . DIRECTORY_SEPARATOR;

            if (!is_dir($dirname)) {
                mkdir($dirname);
            }
        }

        return !is_null($set) ? file_put_contents($file, $set) : null;
    }

    /**
     * @param $link
     *
     * @return boolean
     */
    function is_url($link) {
        return isset(parse_url((string)$link)['host']) ? true : false;
    }

    /**
     * @param $link
     *
     * @return boolean
     */
    function is_file_link($link) {
        $info = (object) pathinfo($link);
        return isset($info->extension) ? true : false;
    }

    /**
     * @param $file
     * @param boolean $required
     *
     * @return boolean
     * @throws Exception
     */
    function checkFile($file, $required = false) {
        if (is_file($file) && is_readable($file)) {
            return true;
        } else {
            if ($required) {
                throw new Exception ("'$file', the file does not exist");
            }

            return false;
        }
    }

    /**
     * @param string $text
     *
     * @return string
     */
    function cleanText($text) {
        $text = (string) $text;
        $text = htmlentities($text);
        $text = htmlspecialchars_decode($text);

        $text = preg_replace('%([\n]+)%', ' ', $text);
        $text = preg_replace('%([\t]+)%', null, $text);
        $text = preg_replace('%([\r]+)%', null, $text);
        $text = preg_replace('%^[\s]+%', null, $text);
        $text = preg_replace('%[\s]+$%', null, $text);
        $text = preg_replace('%[\s]{1,}%', ' ', $text);
        $text = preg_replace('%>[\s]{1,}%', '>', $text);
        $text = preg_replace('%[\s]{1,}<%', '<', $text);

        return (string) $text;
    }

    /**
     * Транслитерация текста с переводом в нижний или верхний регистр
     *
     * @param string $text - Текст для транслитерации
     * @param string $separator - Разделитель между словами
     * @param boolean $uppercase - Перевести в верхний регистр
     *
     * @return string
     */
    function transliterationText($text, $separator = ' ', $uppercase = false) {
        $text = mb_strtolower($text, 'utf-8');
        $text = preg_replace('#&\w{2,6};#', ' ', $text);

        $text = $this->replacementCharacters($text);

        $text = preg_replace('#[^a-z0-9]#', $separator, $text);
        $text = trim(preg_replace('#-+#', $separator, $text), $separator);

        if ($uppercase)
            $text = mb_strtoupper($text, 'utf-8');

        return (string) $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    function replacementCharacters($text) {
        $text = str_replace(
            array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я'),
            array('a','b','v','g','d','e','yo','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','sch','','y','','e','yu','ya'),
            $text
        );

        return (string) $text;
    }

    /**
     * @param $url
     * @param null $domain
     *
     * @return null|string
     */
    function parsing_url($url, $domain = null) {
        $parse = (object) parse_url((string)$url);

        if (empty($parse)) {
            return null;
        }

        $path = null;

        if (empty($parse->host)) {
            $parse->scheme = $this->config->schema;

            if (!empty($domain)) {
                $parse->host = $domain;
            } else {
                $parse->host = $this->config->old_domain;
            }
        }

        if (!empty($parse->path)) {
            $path = trim($parse->path, '/');
            $path = str_replace([
                '%2F',
                '%3A',
                '%40',
                '%2B',
                '%3F',
                '%26',
                '+'
            ], [
                '/',
                ':',
                '@',
                '+',
                '?',
                '&',
                '%20'
            ], urlencode($path));
        }

        return $parse->scheme . '://' . $parse->host . '/' . ltrim($path, '/');
    }

    function arraySearch($array, $key, $text) {
        $i = 0;
        do {
            if ($array[$i][$key] == $text)
                return $i;
        } while (++$i < count($array));
    }

    /**
     * @param object $html
     * @param string $folder
     *
     * @return object
     * @throws Exception
     */
    function substitutionImages($html, $folder = 'images') {
        if (!($html instanceof simple_html_dom_node)) {
            throw new Exception('Argument $html is not an object of class simple_html_dom_node');
        }
        foreach ($html->find('img') as $image) {
            $src = $this->parsing_url($image->src);
            $info = (object) pathinfo($src);
            $file = '/' . trim($folder, '/') . '/' . sha1($src) . '.' . strtolower($info->extension);

            if ($this->setFile($file, $src, false, 'url')) {
                $image->src = $file;
            }
        }

        return $html;
    }

    /**
     * @param object $html
     * @param string $folder
     * @param array $image_types
     *
     * @return object
     * @throws Exception
     */
    function substitutionReferencesImages($html, $folder = 'images', $image_types = array('jpg', 'jpeg', 'gif', 'png')) {
        if (!($html instanceof simple_html_dom_node)) {
            throw new Exception('Argument $html is not an object of class simple_html_dom_node');
        }

        foreach ($html->find('a') as $link) {
            $src = $this->parsing_url($link->href);
            $info = (object) pathinfo($src);
            if ($this->is_file_link($src) && in_array(strtolower($info->extension), $image_types)) {
                $file = '/' . trim($folder, '/') . '/' . sha1($src) . '.' . strtolower($info->extension);
                if ($this->setFile($file, $src, false, 'url')) {
                    $link->href = $file;
                }
            }
        }

        return (object) $html;
    }

    /**
     * @param object $html
     *
     * @return object
     * @throws Exception
     */
    function substitutionLinks($html) {
        if (!($html instanceof simple_html_dom_node)) {
            throw new Exception('Argument $html is not an object of class simple_html_dom_node');
        }

        foreach ($html->find('a') as $link) {
            if (!$this->is_file_link($link->href)) {
                $link->href = str_replace($this->config->old_site, '', $link->href);
            }
        }

        return (object) $html;
    }

    /**
     * Генерация пароля
     *
     * @param integer $length - Количество симоволов создаваемого пароля
     *
     * @return string
     */
    function generatePass($length = 10) {
        $allowable_characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $ps_len = strlen($allowable_characters);
        mt_srand((double)microtime()*1000000);
        $pass = '';
        for($i = 0; $i < $length; $i++) {
            $pass .= $allowable_characters[mt_rand(0, $ps_len-1)];
        }

        return (string) $pass;
    }

    /**
     * Получение основных мета тегов
     *
     * @param simple_html_dom $html
     * @param array $index
     * @param boolean $clean_text
     *
     * @return object
     * @throws Exception
     */
    function getMETA($html, array $index = [], $clean_text = true) {
        if (!($html instanceof simple_html_dom)) {
            throw new Exception('Argument $html is not an object of class simple_html_dom');
        }

        $choose = [
            'h1' => [
                'choose' => 'h1',
                'index' => 0,
                'output' => 'plaintext'
            ],
            'title' => [
                'choose' => 'title',
                'index' => 0,
                'output' => 'plaintext'
            ],
            'description' => [
                'choose' => 'meta[name="description"]',
                'index' => 0,
                'output' => 'content'
            ],
            'keywords' => [
                'choose' => 'meta[name="keywords"]',
                'index' => 0,
                'output' => 'content'
            ]
        ];

        $data = [];

        foreach (array_merge($choose, $index) as $key => $param) {
            $data[$key] = (string)$html->find($param['choose'], $param['index'])->{$param['output']};
        }

        if ($clean_text) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->cleanText($value);
            }
        }

        return (object) $data;
    }

    /**
     * @param null $arguments
     */
    function message($arguments = null) {
        echo '<div>' . $arguments . '</div>';
    }

    /**
     * @param null $arguments
     */
    function view($arguments = null) {
        echo '<pre>' . print_r($arguments, true);
    }

    /**
     * @param null $arguments
     */
    function viewEnd($arguments = null) {
        echo '<pre>' . print_r($arguments, true);
        exit;
    }
}