jQuery(document).ready(function ($) {
    $('#bob-api-key-toggle').on('click', function () {
        let apiKeyInput = $('input[name="bob-openai-api-key"]');
        let apiKeyType = apiKeyInput.attr('type');

        if (apiKeyType === 'password') {
            apiKeyInput.attr('type', 'text');
            $(this).text('Hide');
        } else {
            apiKeyInput.attr('type', 'password');
            $(this).text('Show');
        }
    });

    // Handle tab navigation
    var $tabs = $('.nav-tab-wrapper a');
    var $content = $('.bob-settings-content > div');

    function setActiveTab(target) {
        // Set the active tab
        $tabs.removeClass('nav-tab-active');
        $('a[href="#' + target + '"]').addClass('nav-tab-active');

        // Show the corresponding content
        $content.hide();
        $('#' + target).show();
    }

    $tabs.on('click', function(e) {
        e.preventDefault();

        var target = $(this).attr('href').replace('#', '');
        setActiveTab(target);
        localStorage.setItem('bob_active_tab', target);
    });

    // Show the stored tab or the first tab by default
    var activeTab = localStorage.getItem('bob_active_tab') || $tabs.first().attr('href').replace('#', '');
    setActiveTab(activeTab);
});