jQuery(document).ready(function ($) {
    $(document).tooltip({
        selector: '[title]',
        track: true,
        position: {
            my: 'center top',
            at: 'center bottom+10'
        }
    });
});