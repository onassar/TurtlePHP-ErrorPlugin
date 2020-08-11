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
         * @param   array $traceFrame
         * @param   null|string $traceFunctionName
         * @return  void
         */
        protected static function _addBlock(array $traceFrame, ?string $traceFunctionName): void
        {
            $args = array($traceFrame, $traceFunctionName);
            $block = static::_getBlockProperties(... $args);
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
         * _getBlockOutput
         * 
         * @access  protected
         * @static
         * @param   array $traceFrame
         * @return  string
         */
        protected static function _getBlockOutput(array $traceFrame): string
        {
            // Get content lines
            $args = array($traceFrame);
            $slicedContentLines = static::_getBlockOutputSlicedContentLines(... $args);

            // Encode lines and include leading newline
            $encodedLines = static::_encode($slicedContentLines);
            $output = "\n" . implode("\n", $encodedLines);

            // Append a newline if the output reaches the end of the file
            if (preg_match('/(\n){1}$/', $output) === 1) {
                $output .= "\n";
            }
            return $output;
        }

        /**
         * _getBlockOutputContentLines
         * 
         * @access  protected
         * @static
         * @param   array $traceFrame
         * @return  array
         */
        protected static function _getBlockOutputContentLines(array $traceFrame): array
        {
            $path = $traceFrame['file'];
            $content = file_get_contents($path);
            $contentLines = explode("\n", $content);
            return $contentLines;
        }

        /**
         * _getBlockOutputSlicedContentLines
         * 
         * @access  protected
         * @static
         * @param   array $traceFrame
         * @return  array
         */
        protected static function _getBlockOutputSlicedContentLines(array $traceFrame): array
        {
            $contentLines = static::_getBlockOutputContentLines($traceFrame);
            $line = (int) $traceFrame['line'];
            $configData = static::_getConfigData();
            $maxNumberOfLines = $configData['maxNumberOfLines'];
            $start = max(0, $line - $maxNumberOfLines - 1);
            $end = min($line + $maxNumberOfLines, ($maxNumberOfLines * 2) + 1);
            $slicedContentLines = array_slice($contentLines, $start, $end);
            return $slicedContentLines;
        }

        /**
         * _getBlockProperties
         * 
         * @access  protected
         * @static
         * @param   array $traceFrame
         * @param   null|string $traceFunctionName
         * @return  array
         */
        protected static function _getBlockProperties(array $traceFrame, ?string $traceFunctionName): array
        {
            $path = $traceFrame['file'];
            $line = (int) $traceFrame['line'];
            $functionName = $traceFunctionName;
            $output = static::_getBlockOutput($traceFrame);
            $start = static::_getBlockStart($traceFrame);
            $args = array('path', 'line', 'functionName', 'output', 'start');
            $properties = compact(... $args);
            return $properties;
        }

        /**
         * _getBlockStart
         * 
         * Returns the line that the trace frame should start on (based on the
         * config setting related to how many lines to show).
         * 
         * @access  protected
         * @static
         * @param   array $traceFrame
         * @return  int
         */
        protected static function _getBlockStart(array $traceFrame): int
        {
            $line = (int) $traceFrame['line'];
            $configData = static::_getConfigData();
            $maxNumberOfLines = $configData['maxNumberOfLines'];
            $start = max(1, $line - $maxNumberOfLines);
            return $start;
        }

        /**
         * _getFileBasedTraceFrames
         * 
         * @access  protected
         * @static
         * @param   array $trace
         * @return  array
         */
        protected static function _getFileBasedTraceFrames(array $trace): array
        {
            $traceFrames = array();
            foreach ($trace as $traceFrame) {
                $traceFrameFile = $traceFrame['file'] ?? null;
                if ($traceFrameFile === null) {
                    continue;
                }
                array_push($traceFrames, $traceFrame);
            }
            return $traceFrames;
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
         * _getTraceFunctionNames
         * 
         * Returns function names from the trace that are useful (eg. ignores
         * certain closures).
         * 
         * @access  protected
         * @static
         * @param   array $trace
         * @return  array
         */
        protected static function _getTraceFunctionNames(array $trace): array
        {
            $traceFunctionNames = array();
            array_shift($trace);
            foreach ($trace as $traceFrame) {
                $traceFunctionName = $traceFrame['function'] ?? null;
                if ($traceFunctionName === null) {
                    continue;
                }
                if ($traceFunctionName === 'call_user_func_array') {
                    continue;
                }
                array_push($traceFunctionNames, $traceFunctionName);
            }
            return $traceFunctionNames;
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
            $traceFunctionNames = static::_getTraceFunctionNames($trace);
            $traceFrames = static::_getFileBasedTraceFrames($trace);
            foreach ($traceFrames as $traceFrame) {
                $traceFunctionName = array_shift($traceFunctionNames) ?? null;
                static::_addBlock($traceFrame, $traceFunctionName);
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
