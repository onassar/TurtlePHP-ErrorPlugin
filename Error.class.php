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
         * @var     array (default: array())
         * @static
         */
        protected static $_blocks = array();

        /**
         * _configPath
         * 
         * @access  protected
         * @var     string (default: 'config.default.inc.php')
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
         * @var     bool (defualt: false)
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
            $hookKey = 'error';
            $callback = array('\Plugin\Error', 'draw');
            \Turtle\Application::addHook($hookKey, $callback);
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
            $hookKey = 'error';
            $callback = array('\Plugin\Error', 'log');
            \Turtle\Application::addHook($hookKey, $callback);
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
            $hookKey = 'error';
            \Turtle\Application::clearHooks($hookKey);
        }

        /**
         * _getLoggingLines
         * 
         * @access  protected
         * @static
         * @param   \Throwable $throwable
         * @return  array
         */
        protected static function _getLoggingLines(\Throwable $throwable): array
        {
            $padLength = 11;
            $lines = array();
            $leading = str_pad('Message:', $padLength, ' ');
            $value = $throwable->getMessage();
            array_push($lines, ($leading) . ($value));
            $leading = str_pad('File:', $padLength, ' ');
            $value = $throwable->getFile();
            array_push($lines, ($leading) . ($value));
            $leading = str_pad('Line:', $padLength, ' ');
            $value = $throwable->getLine();
            array_push($lines, ($leading) . ($value));
            $leading = str_pad('IP:', $padLength, ' ');
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
         * _renderView
         * 
         * @access  protected
         * @static
         * @return  string
         */
        protected static function _renderView(): string
        {
            $configData = static::_getConfigData();
            $template = $configData['template'];
            $path = (__DIR__) . '/views/' . ($template) . '/render.inc.php';
            $skin = $configData['skin'];
            $blocks = static::$_blocks;
            $errorMessage = static::$_errorMessage;
            $vars = compact('blocks', 'errorMessage', 'skin');
            $response = static::_renderPath($path, $vars);
            return $response;
        }

        /**
         * _setBlocks
         * 
         * @access  protected
         * @static
         * @param   array $trace
         * @return  void
         */
        protected static function _setBlocks(array $trace): void
        {
            // filter calls to clean out function calls that aren't useful
            $functionCalls = $trace;
el(pr($trace, true));
            $functionNames = array();
            array_shift($functionCalls);
            foreach ($functionCalls as $call) {
                if (isset($call['function'])) {
                    if ($call['function'] === 'call_user_func_array') {
                        continue;
                    }
                    array_push($functionNames, $call['function']);
                }
            }

            // define blocks for output
            foreach ($trace as $traceFrame) {
                if (isset($traceFrame['file'])) {
                    $functionName = false;
                    if (count($functionNames) > 0) {
                        $functionName = array_shift($functionNames);
                    }
                    static::_addBlock(
                        $traceFrame['file'],
                        ((int) $traceFrame['line'] - 1),
                        $functionName
                    );
                }
            }
        }

        /**
         * _setErrorMessage
         * 
         * @access  protected
         * @static
         * @param   \Throwable $throwable
         * @return  void
         */
        protected static function _setErrorMessage(\Throwable $throwable): void
        {
            $message = $throwable->getMessage();
            static::$_errorMessage = $message;
        }

        /**
         * draw
         * 
         * @access  public
         * @static
         * @param   \Turtle\Request $request
         * @param   \Throwable $throwable
         * @param   array $trace
         * @return  void
         */
        public static function draw(\Turtle\Request $request, \Throwable $throwable, array $trace): void
        {
            static::_setBlocks($trace);
            static::_setErrorMessage($throwable);
            $response = static::_renderView();
            $request->setResponse($response);
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
         * @param   \Throwable $throwable
         * @param   array $trace
         * @return  void
         */
        public static function log(\Turtle\Request $request, \Throwable $throwable, array $trace): void
        {
            $lines = static::_getLoggingLines($throwable);
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
