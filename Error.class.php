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
         * @var    Array
         * @access protected
         * @static
         */
        protected static $_blocks = array();

        /**
         * _max
         *
         * @var    Integer
         * @access protected
         * @static
         */
        protected static $_max = 10;

        /**
         * _message
         *
         * @var    String
         * @access protected
         * @static
         */
        protected static $_message;

        /**
         * _trace
         *
         * @var    String
         * @access protected
         * @static
         */
        protected static $_trace;

        /**
         * init
         *
         * @access public
         * @static
         * @return void
         */
        public static function init()
        {
            // add ths error-hook
            \Turtle\Application::addHook(
                'error',
                array('\Plugin\Error', 'callback')
            );
        }

        /**
         * _addBlock
         * 
         * @access protected
         * @static
         * @param  String $file
         * @param  Integer $line
         * @return void
         */
        protected static function _addBlock($file, $line)
        {
            // setup the block
            $block = array(
                'path' => $file,
                'line' => $line + 1,
                'output' => ''
            );

            // grab data contents
            $data = file_get_contents($file);
            $data = explode("\n", $data);

            // slice it up to get at most <self::$_max> lines before and after error-line
            $start = max(0, $line - self::$_max);
            $end = min($line + 1 + self::$_max, (self::$_max * 2) +1);
            $lines = array_slice($data, $start, $end);

            // encode and include starting-line
            $encoded = self::_encode($lines);
            $block['output'] = "\n" . implode("\n", $encoded);
            if (preg_match('/(\n){1}$/', $block['output'])) {
                $block['output'] .= "\n";
            }

            // set which line the output is starting on, relative to the file
            $block['start'] = max(1, $line - self::$_max + 1);

            // push to local storage
            array_push(self::$_blocks, $block);
        }

        /**
         * _encode
         * 
         * @access protected
         * @param  mixed $mixed
         * @return array
         */
        protected function _encode($mixed)
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
        public static function _render()
        {
            // set the data for the view
            $data = array(
                'message' => self::$_message,
                'blocks' => self::$_blocks
            );

            // buffer handling
            ob_start();

            // bring variables forward
            foreach ($data as $__name => $__value) {
                $$__name = $__value;
            }

            // render view
            include 'render.inc.php';
            $_response = ob_get_contents();
            ob_end_clean();

            // return captured response
            return $_response;
        }

        /**
         * callback
         *
         * @access public
         * @static
         * @param  Request $request
         * @param  Array $error
         * @return void
         */
        public static function callback(\Turtle\Request $request, array $error)
        {
            // parse generated error message
            $pieces = explode('Stack trace:', $error['message']);
            $full = trim($pieces[0]);
            self::$_trace = trim($pieces[1]);

            // get error message
            $pieces = explode(' in ', $full);
            self::$_message = trim($pieces[0]);

            // add a block for error-trakcing
            self::_addBlock($error['file'], ((int) $error['line'] - 1));

            // render the error view
            $response = self::_render();
            exit($response);
        }
    }
