<?php

    // namespace
    namespace Plugin;

    /**
     * Error
     *
     * @author   Oliver Nassar <onassar@gmail.com>
     * @abstract
     */
    abstract class Error
    {
        /**
         * _blocks
         *
         * @var    array
         * @access protected
         * @static
         */
        protected static $_blocks = array();

        /**
         * _errorMessage
         *
         * @var    string
         * @access protected
         * @static
         */
        protected static $_errorMessage;

        /**
         * _maxNumberOfLines
         * 
         * Maximum number of lines to show before and after a specific line
         * number (eg. where an error or stacktrace-call occured).
         *
         * @var    integer
         * @access protected
         * @static
         */
        protected static $_maxNumberOfLines = 10;

        /**
         * init
         *
         * @access public
         * @static
         * @return void
         */
        public static function init()
        {
            // clear previous hooks; add them
            \Turtle\Application::clearHooks('error');
            \Turtle\Application::addHook(
                'error',
                array('\Plugin\Error', 'log')
            );
            \Turtle\Application::addHook(
                'error',
                array('\Plugin\Error', 'draw')
            );
        }

        /**
         * _addBlock
         * 
         * @access protected
         * @static
         * @param  string $path
         * @param  integer $line
         * @param  string $functionName
         * @return void
         */
        protected static function _addBlock($path, $line, $functionName)
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

            // slice it up to get at most <self::$_maxNumberOfLines> lines before and after error-line
            $start = max(0, $line - self::$_maxNumberOfLines);
            $end = min($line + 1 + self::$_maxNumberOfLines, (self::$_maxNumberOfLines * 2) +1);
            $lines = array_slice($data, $start, $end);

            // encode and include starting-line
            $encoded = self::_encode($lines);
            $block['output'] = "\n" . implode("\n", $encoded);
            if (preg_match('/(\n){1}$/', $block['output'])) {
                $block['output'] .= "\n";
            }

            // set which line the output is starting on, relative to the file
            $block['start'] = max(1, $line - self::$_maxNumberOfLines + 1);

            // push to local storage
            array_push(self::$_blocks, $block);
        }

        /**
         * _encode
         * 
         * @access protected
         * @static
         * @param  mixed $mixed
         * @return array
         */
        protected static function _encode($mixed)
        {
            if (is_array($mixed)) {
                foreach ($mixed as $key => $value) {
                    $mixed[$key] = self::_encode($value);
                }
                return $mixed;
            }
            return htmlentities($mixed, ENT_QUOTES, 'UTF-8');
        }

        /**
         * _render
         *
         * @access protected
         * @static
         * @return String
         */
        protected static function _render()
        {
            // buffer handling
            ob_start();
            $blocks = self::$_blocks;
            $errorMessage = self::$_errorMessage;

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
         * @access public
         * @static
         * @param  Request $request
         * @param  array $error
         * @param  array $trace
         * @return void
         */
        public static function draw(
            \Turtle\Request $request,
            array $error,
            array $backtrace
        ) {
            // error message (for view)
            self::$_errorMessage = $error[1];

            // filter calls to clean out function calls that aren't useful
            $functionCalls = $backtrace;
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
            foreach ($backtrace as $x => $marker) {
                if (isset($marker['file'])) {
                    $functionName = false;
                    if (count($functionNames) > 0) {
                        $functionName = array_shift($functionNames);
                    }
                    self::_addBlock(
                        $marker['file'],
                        ((int) $marker['line'] - 1),
                        $functionName
                    );
                }
            }

            // render the error view
            $response = self::_render();
            echo $response;
            exit(0);
        }

        /**
         * log
         *
         * @access public
         * @static
         * @param  Request $request
         * @param  array $error
         * @param  array $trace
         * @return void
         */
        public static function log(
            \Turtle\Request $request,
            array $error,
            array $trace
        ) {
            error_log(
                $error[1] . ' in ' .
                $error[2] . ': ' .
                $error[3]
            );
        }
    }
