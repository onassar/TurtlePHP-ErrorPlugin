
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
     * __allowRememberMe
     * 
     * @access  private
     * @var     Boolean (default: false)
     */
    var __allowRememberMe = false;

    /**
     * Methods
     * 
     */

    /**
     * __copyToClipboard
     * 
     * @access  private
     * @param   String content
     * @return  void
     */
    function __copyToClipboard(content) {
        var $element = jQuery('<input type="text" />'),
            element = $element[0];
        $(document.body).append($element);
        $element.val(content);
        element.select();
        document.execCommand('copy');
        element.blur();
        $element.remove();
    }

    /**
     * _getBlockFlagElement
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  HTMLElement
     */
    function _getBlockFlagElement($block) {
        var $flag = $block.querySelector('div.flag');
        return $flag;
    }

    /**
     * _getBlockHighlightIndex
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  Number
     */
    function _getBlockHighlightIndex($block) {
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
        var highlightIndex = _getBlockHighlightIndex($block),
            selector = 'li:nth-child(' + (highlightIndex + 1) + ')',
            $listItem = $block.querySelector(selector);
        return $listItem;
    }

    /**
     * __positionBlockFlag
     * 
     * @access  private
     * @param   HTMLElement $block
     * @return  void
     */
    function __positionBlockFlag($block) {
        var $flag = _getBlockFlagElement($block),
            $listItem = __getBlockListItemHighlighted($block)
            listItemPosition = $listItem.getBoundingClientRect(),
            bodyPosition = document.body.getBoundingClientRect(),
            blockPosition = $block.getBoundingClientRect();
        $flag.style.top = (listItemPosition.y - bodyPosition.y - blockPosition.y - 6) + 'px';
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
    __prettyPrint();
    __positionBlockFlags();
    return true;
})();

            // var blocks = $('.block');
            // jQuery.each(blocks, function(index, block) {
            //     var focusingLineNumber = $(block).attr('data-line-number'),
            //         first = $(block).find('li[value]'),
            //         lines = first.nextAll(),
            //         previous = first.val();

            //     first.attr('data-line', first.val());
            //     jQuery.each(lines, function(index, line) {
            //         $(line).attr('data-line', previous + 1);
            //         ++previous;
            //     });

            //     // selected line
            //     var selected = $(block).find('li[data-line="' + (focusingLineNumber) + '"]');
            //     selected.addClass('focus');

            //     // create a hovered-focus element for the errored-line
            //     var hovered = $('<div class="hovered"></div>'),
            //         position = selected.position();
            //     hovered.css({
            //         left: position.left - 75,
            //         top: position.top + 40
            //     });
            //     $(block).append(hovered);
            // });
