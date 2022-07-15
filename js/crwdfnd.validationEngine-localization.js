(function ($) {
    $(document).ready(function () {
        $.extend(true, $.validationEngineLanguage.allRules, crwdfnd_validationEngine_localization);
        $(".crwdfnd-validate-form").validationEngine('attach');
    });
})(jQuery);