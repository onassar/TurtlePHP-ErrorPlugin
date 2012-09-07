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
         * @param  Integer $type
         * @param  String $message
         * @param  String $path
         * @param  Integer $line
         * @param  Array $context
         * @return void
         */
        protected static function _addBlock($type, $message, $path, $line, $context)
        {
            // setup the block
            $block = array(
                'type' => $type,
                'message' => $message,
                'path' => $path,
                'line' => $line + 1,
                'context' => $context,
                'output' => '',
                'start' => 1
            );

            // grab data contents
            $data = file_get_contents($path);
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
         * _log
         *
         * @access protected
         * @static
         * @param  Array $error
         * @return void
         */
        protected static function _log(array $error)
        {
            error_log(
                $error[1] . ' in ' .
                $error[2] . ': ' .
                $error[3]
            );
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
            // retrieve params
            $type = $error[0];
            $message = $error[1];
            $path = $error[2];
            $line = $error[3];
            $context = $error[4];

            // add a block for the passed-in error
            self::_addBlock(
                $type,
                $message,
                $path,
                ((int) $line - 1),
                $context
            );

            // log it
            self::_log($error);

            // render the error view
            $response = self::_render();
            echo $response;
            exit(0);
        }
    }
