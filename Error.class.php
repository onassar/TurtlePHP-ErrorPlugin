<?php

    // namespace
    namespace TurtlePHP\Plugin;

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
         * @return  bool
         */
        protected static function _addErrorDrawHook(): bool
        {
            $renderView = static::_getConfigData('renderView');
            if ($renderView === false) {
                return false;
            }
            $hookKey = 'error/draw';
            \TurtlePHP\Application::clearHooks($hookKey);
            $callback = array('\TurtlePHP\Plugin\Error', 'draw');
            \TurtlePHP\Application::addHook($hookKey, $callback);
            return true;
        }

        /**
         * _addErrorHooks
         * 
         * @access  protected
         * @static
         * @return  void
         */
        protected static function _addErrorHooks(): void
        {
            static::_addErrorDrawHook();
            static::_addErrorLogHook();
        }

        /**
         * _addErrorLogHook
         * 
         * @access  protected
         * @static
         * @return  bool
         */
        protected static function _addErrorLogHook(): bool
        {
            $logErrors = static::_getConfigData('logErrors');
            if ($logErrors === false) {
                return false;
            }
            $hookKey = 'error/log';
            \TurtlePHP\Application::clearHooks($hookKey);
            $callback = array('\TurtlePHP\Plugin\Error', 'log');
            \TurtlePHP\Application::addHook($hookKey, $callback);
            return true;
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
            $maxNumberOfLines = static::_getConfigData('maxNumberOfLines');
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
            $maxNumberOfLines = static::_getConfigData('maxNumberOfLines');
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
         * _getLoggingMessage
         * 
         * @access  protected
         * @static
         * @param   \Throwable $throwable
         * @return  string
         */
        protected static function _getLoggingMessage(\Throwable $throwable): string
        {
            $template = static::_getConfigData('template');
            $path = (__DIR__) . '/templates/' . ($template) . '/log.inc.php';
            $vars = compact('throwable');
            $msg = \TurtlePHP\Application::renderPath($path, $vars);
            $msg = (PHP_EOL) . ($msg);
            return $msg;
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
         * _setBlocks
         * 
         * @access  protected
         * @static
         * @param   array $trace
         * @return  void
         */
        protected static function _setBlocks(array $trace): void
        {
            static::$_blocks = array();
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
            $msg = $throwable->getMessage();
            static::$_errorMessage = $msg;
        }

        /**
         * addCustomHook
         * 
         * @access  public
         * @static
         * @param   callable $hook
         * @return  void
         */
        public static function addCustomHook(callable $hook): void
        {
            $hookKey = 'error/custom';
            \TurtlePHP\Application::addHook($hookKey, $hook);
        }

        /**
         * draw
         * 
         * @access  public
         * @static
         * @param   \TurtlePHP\Request $request
         * @param   \Throwable $throwable
         * @param   array $trace
         * @return  void
         */
        public static function draw(\TurtlePHP\Request $request, \Throwable $throwable, array $trace): void
        {
            $response = static::renderView($request, $throwable, $trace);
            $request->setResponse($response);
            $request->setServiceUnavailableHeaders();
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
            static::_addErrorHooks();
            return true;
        }

        /**
         * log
         * 
         * @access  public
         * @static
         * @param   \TurtlePHP\Request $request
         * @param   \Throwable $throwable
         * @param   array $trace
         * @return  void
         */
        public static function log(\TurtlePHP\Request $request, \Throwable $throwable, array $trace): void
        {
            $msg = static::_getLoggingMessage($throwable);
            error_log($msg);
        }

        /**
         * renderView
         * 
         * @access  public
         * @static
         * @param   \TurtlePHP\Request $request
         * @param   \Throwable $throwable
         * @param   array $trace
         * @return  string
         */
        public static function renderView(\TurtlePHP\Request $request, \Throwable $throwable, array $trace): string
        {
            static::_setBlocks($trace);
            static::_setErrorMessage($throwable);
            $template = static::_getConfigData('template');
            $path = (__DIR__) . '/templates/' . ($template) . '/view.inc.php';
            $skin = static::_getConfigData('skin');
            $blocks = static::$_blocks;
            $errorMessage = static::$_errorMessage;
            $vars = compact('blocks', 'errorMessage', 'skin');
            $response = static::_renderPath($path, $vars);
            return $response;
        }
    }

    // Config path loading
    $info = pathinfo(__DIR__);
    $parent = ($info['dirname']) . '/' . ($info['basename']);
    $configPath = ($parent) . '/config.inc.php';
    \TurtlePHP\Plugin\Error::setConfigPath($configPath);
