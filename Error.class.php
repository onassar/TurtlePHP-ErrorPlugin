<?php

    // namespace
    namespace Plugin;

    /**
     * Error
     * 
     * Error plugin for TurtlePHP.
     *
     * @author  Oliver Nassar <onassar@gmail.com>
     * @abstract
     * @extends Base
     */
    abstract class Error extends Base
    {
        /**
         * _blocks
         * 
         * @access  protected
         * @var     array
         * @static
         */
        protected static $_blocks = array();

        /**
         * _configPath
         * 
         * @access  protected
         * @var     string
         * @static
         */
        protected static $_configPath = 'config.default.inc.php';

        /**
         * _errorMessage
         * 
         * @access  protected
         * @var     null|string (default: null)
         * @static
         */
        protected static $_errorMessage = null;

        /**
         * _initiated
         * 
         * @access  protected
         * @var     bool
         * @static
         */
        protected static $_initiated = false;

        /**
         * _addBlock
         * 
         * @access  protected
         * @static
         * @param   string $path
         * @param   int $line
         * @param   string $functionName
         * @return  void
         */
        protected static function _addBlock(string $path, int $line, string $functionName): void
        {
            // setup the block
            $block = array(
                'path' => $path,
                'line' => $line + 1,
                'functionName' => $functionName,
                'output' => '',
                'start' => 1
            );

            // grab data contents
            $data = file_get_contents($path);
            $data = explode("\n", $data);

            // slice it up to get at most <static::$_maxNumberOfLines> lines before and after error-line
            $configData = static::_getConfigData();
            $maxNumberOfLines = $configData['maxNumberOfLines'];
            $start = max(0, $line - $maxNumberOfLines);
            $end = min($line + 1 + $maxNumberOfLines, ($maxNumberOfLines * 2) +1);
            $lines = array_slice($data, $start, $end);

            // encode and include starting-line
            $encoded = static::_encode($lines);
            $block['output'] = "\n" . implode("\n", $encoded);
            if (preg_match('/(\n){1}$/', $block['output'])) {
                $block['output'] .= "\n";
            }

            // set which line the output is starting on, relative to the file
            $block['start'] = max(1, $line - $maxNumberOfLines + 1);

            // push to local storage
            array_push(static::$_blocks, $block);
        }

        /**
         * _addErrorDrawHook
         * 
         * @access  protected
         * @static
         * @return  void
         */
        protected static function _addErrorDrawHook(): void
        {
            $hook = 'error';
            $callback = array('\Plugin\Error', 'draw');
            \Turtle\Application::addHook($hook, $callback);
        }

        /**
         * _addErrorHooks
         * 
         * @note    Order matters
         * @access  protected
         * @static
         * @return  void
         */
        protected static function _addErrorHooks(): void
        {
            static::_addErrorLogHook();
            static::_addErrorDrawHook();
        }

        /**
         * _addErrorLogHook
         * 
         * @access  protected
         * @static
         * @return  void
         */
        protected static function _addErrorLogHook(): void
        {
            $hook = 'error';
            $callback = array('\Plugin\Error', 'log');
            \Turtle\Application::addHook($hook, $callback);
        }

        /**
         * _checkDependencies
         * 
         * @access  protected
         * @static
         * @return  void
         */
        protected static function _checkDependencies(): void
        {
            static::_checkConfigPluginDependency();
        }

        /**
         * _clearErrorHook
         * 
         * @access  protected
         * @static
         * @return  void
         */
        protected static function _clearErrorHook(): void
        {
            $hook = 'error';
            \Turtle\Application::clearHooks($hook);
        }

        /**
         * _getLoggingLines
         * 
         * @access  protected
         * @static
         * @param   array $metadata
         * @return  array
         */
        protected static function _getLoggingLines(array $metadata): array
        {
            $padLength = 11;
            $lines = array();
            $leading = str_pad('Message:', $padLength, ' ');
            $value = $metadata[1];
            array_push($lines, ($leading) . ($value));
            $leading = str_pad('File:', $padLength, ' ');
            $value = $metadata[2];
            array_push($lines, ($leading) . ($value));
            $leading = str_pad('Line:', $padLength, ' ');
            $value = $metadata[3];
            array_push($lines, ($leading) . ($value));
            $leading = str_pad('Client:', $padLength, ' ');
            $value = IP;
            array_push($lines, ($leading) . ($value));
            $leading = str_pad('URI:', $padLength, ' ');
            $value = static::_getLoggingURI();
            array_push($lines, ($leading) . ($value));
            return $lines;
        }

        /**
         * _getLoggingURI
         * 
         * @access  protected
         * @static
         * @return  null|string
         */
        protected static function _getLoggingURI(): ?string
        {
            $uri = $_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? null;
            return $uri;
        }

        /**
         * _render
         * 
         * @access  protected
         * @static
         * @return  string
         */
        protected static function _render(): string
        {
            // buffer handling
            ob_start();
            $blocks = static::$_blocks;
            $errorMessage = static::$_errorMessage;

            // render view
            include 'render.inc.php';
            $_response = ob_get_contents();
            ob_end_clean();

            // return captured response
            return $_response;
        }

        /**
         * draw
         * 
         * @access  public
         * @static
         * @param   \Turtle\Request $request
         * @param   array $metadata
         * @param   array $trace
         * @return  void
         */
        public static function draw(\Turtle\Request $request, array $metadata, array $trace): void
        {
            // error message (for view)
            static::$_errorMessage = $metadata[1];

            // filter calls to clean out function calls that aren't useful
            $functionCalls = $trace;
            $functionNames = array();
            array_shift($functionCalls);
            foreach ($functionCalls as $x => $call) {
                if (isset($call['function'])) {
                    if ($call['function'] === 'call_user_func_array') {
                        continue;
                    }
                    array_push($functionNames, $call['function']);
                }
            }

            // define blocks for output
            foreach ($trace as $x => $marker) {
                if (isset($marker['file'])) {
                    $functionName = false;
                    if (count($functionNames) > 0) {
                        $functionName = array_shift($functionNames);
                    }
                    static::_addBlock(
                        $marker['file'],
                        ((int) $marker['line'] - 1),
                        $functionName
                    );
                }
            }

            // render the error view
            $response = static::_render();
            $request->setResponse($response);
            // echo $response;
            exit(0);
        }

        /**
         * init
         * 
         * @access  public
         * @static
         * @return  bool
         */
        public static function init(): bool
        {
            if (static::$_initiated === true) {
                return false;
            }
            parent::init();
            static::_clearErrorHook();
            static::_addErrorHooks();
            return true;
        }

        /**
         * log
         * 
         * @access  public
         * @static
         * @param   \Turtle\Request $request
         * @param   array $metadata
         * @param   array $trace
         * @return  void
         */
        public static function log(\Turtle\Request $request, array $metadata, array $trace)
        {
            $lines = static::_getLoggingLines($metadata);
            $message = implode("\n", $lines);
            $message = "\n" . ($message);
            error_log($message);
        }
    }

    // Config path loading
    $info = pathinfo(__DIR__);
    $parent = ($info['dirname']) . '/' . ($info['basename']);
    $configPath = ($parent) . '/config.inc.php';
    \Plugin\Error::setConfigPath($configPath);
