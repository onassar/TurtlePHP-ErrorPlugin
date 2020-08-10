
/**
 * default.js
 * 
 */
(function() {

    /**
     * Properties
     * 
     */

    /**
     * __animatingClassName
     * 
     * @access  private
     * @var     String (default: 'flip')
     */
    var __animatingClassName = 'flip';

    /**
     * Methods
     * 
     */

    /**
     * __addCopyEventListener
     * 
     * @access  private
     * @return  void
     */
    function __addCopyEventListener() {
        var $anchors = document.querySelectorAll('a.copy');
        $anchors.forEach(function($anchor) {
            var eventName = 'click',
                callback = __handleCopyClickEvent;
            $anchor.addEventListener(eventName, callback, false);
        })
    }

    /**
     * __addKeyPressEventListener
     * 
     * @access  private
     * @return  void
     */
    function __addKeyPressEventListener() {
        var eventName = 'keypress',
            callback = __handleKeyPressEvent;
        document.body.addEventListener(eventName, callback, false);
    }

    /**
     * __animateCSS
     * 
     * @link    https://animate.style/
     * @see     https://caniuse.com/#search=classList
     * @see     https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals
     * @access  private
     * @param   HTMLElement $element
     * @param   String animation
     * @param   String prefix
     * @return  Promise
     */
    function __animateCSS($element, animation, prefix) {
        var promise = new Promise(
            function(resolve, reject) {
                var animationName = (prefix) + (animation);
                $element.classList.add((prefix) + 'animated');
                $element.classList.add(animationName);
                function handleAnimationEnd() {
                    $element.classList.remove((prefix) + 'animated');
                    $element.classList.remove(animationName);
                    $element.removeEventListener(
                        'animationend',
                        handleAnimationEnd
                    );
                    resolve('Animation ended');
                }
                $element.addEventListener(
                    'animationend',
                    handleAnimationEnd
                );
            }
        );
        return promise;
    }

    /**
     * __animateElement
     * 
     * @access  private
     * @param   HTMLElement $element
     * @return  void
     */
    function __animateElement($element) {
        var animatingClassName = __animatingClassName,
            prefix = 'animate__';
        __animateCSS($element, animatingClassName, prefix);
    }

    /**
     * __copyToClipboard
     * 
     * @access  private
     * @param   String content
     * @return  void
     */
    function __copyToClipboard(content) {
        var $input = document.createElement('input');
        $input.type = 'text';
        $input.value = content;
        document.body.appendChild($input);
        $input.select();
        document.execCommand('Copy');
        document.body.removeChild($input);
    }

    /**
     * __getBlockFlagElement
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  HTMLElement
     */
    function __getBlockFlagElement($block) {
        var $flag = $block.querySelector('div.flag');
        return $flag;
    }

    /**
     * __getBlockHighlightIndex
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  Number
     */
    function __getBlockHighlightIndex($block) {
        var highlightIndex = $block.getAttribute('data-highlight-index');
        highlightIndex = parseInt(highlightIndex, 10);
        return highlightIndex;
    }

    /**
     * __getBlockListItemHighlighted
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  HTMLElement
     */
    function __getBlockListItemHighlighted($block) {
        var highlightIndex = __getBlockHighlightIndex($block),
            selector = 'li:nth-child(' + (highlightIndex + 1) + ')',
            $listItem = $block.querySelector(selector);
        return $listItem;
    }

    /**
     * __getFlagOffsetTopValue
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  Number
     */
    function __getFlagOffsetTopValue($block) {
        var $listItem = __getBlockListItemHighlighted($block),
            offsetTop = $listItem.offsetTop;
        return offsetTop;
    }

    /**
     * __getFlagTopPosition
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  String
     */
    function __getFlagTopPosition($block) {
        var offsetTop = __getFlagOffsetTopValue($block),
            $listItem = __getBlockListItemHighlighted($block),
            listItemHeight = $listItem.offsetHeight,
            $flag = __getBlockFlagElement($block),
            flagHeight = $flag.offsetHeight,
            topPosition = (offsetTop + (listItemHeight - flagHeight) / 2) + 'px';
        return topPosition;
    }

    /**
     * __getViewportElements
     * 
     * @access  private
     * @param   String selector
     * @return  NodeList
     */
    function __getViewportElements(selector) {
        var $elements = document.querySelectorAll(selector),
            $filtered = Array.from($elements).filter(function($element) {
                return __inViewport($element);
            });
        return $filtered;
    }

    /**
     * __handleCopyClickEvent
     * 
     * @access  private
     * @param   Object event
     * @return  void
     */
    function __handleCopyClickEvent(event) {
        event.preventDefault();
        var copy = this.getAttribute('data-copy-value');
        __copyToClipboard(copy);
        __animateElement(this);
    }

    /**
     * __handleKeyPressEvent
     * 
     * @access  private
     * @param   Object event
     * @return  void
     */
    function __handleKeyPressEvent(event) {
        if (event.key === 'c') {
            var selector = 'a.copy',
                $anchors = __getViewportElements(selector);
            if ($anchors.length > 0) {
                var $anchor = $anchors[0],
                    copy = $anchor.getAttribute('data-copy-value');
                __copyToClipboard(copy);
                __animateElement($anchor);
            }
        }
    }

    /**
     * __inViewport
     * 
     * @link    https://vanillajstoolkit.com/helpers/isinviewport/
     * @access  private
     * @param   HTMLElement $element
     * @return  Boolean
     */
    function __inViewport($element) {
        var distance = $element.getBoundingClientRect();
        return (
            distance.top >= 0 &&
            distance.left >= 0 &&
            distance.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            distance.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    };

    /**
     * __positionBlockFlag
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  void
     */
    function __positionBlockFlag($block) {
        var $flag = __getBlockFlagElement($block),
            $listItem = __getBlockListItemHighlighted($block),
            top = __getFlagTopPosition($block);
        $flag.style.top = top;
    }

    /**
     * __positionBlockFlags
     * 
     * @access  private
     * @return  void
     */
    function __positionBlockFlags() {
        var $blocks = document.querySelectorAll('div.block');
        $blocks.forEach(function($block, index) {
            __positionBlockFlag($block);
        });
    }

    /**
     * __prettyPrint
     * 
     * @access  private
     * @return  void
     */
    function __prettyPrint() {
        PR.prettyPrint();
    }

    // Construct
    __addCopyEventListener();
    __addKeyPressEventListener();
    __prettyPrint();
    __positionBlockFlags();
    return true;
})();
