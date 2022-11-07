jQuery(document).ready(function(){
    jQuery('.ewd-ulb-meta-menu-item').on('click', function() {
        var ID = jQuery(this).attr('id');
        var ID_Base = ID.substring(5);

        jQuery(".ewd-ulb-meta-body").each(function() {
            jQuery(this).addClass("ewd-ulb-hidden");
        });
        jQuery('#Body_'+ID_Base).removeClass("ewd-ulb-hidden");
    
        jQuery(".ewd-ulb-meta-menu-item").each(function() {
            jQuery(this).removeClass("meta-menu-tab-active");
        });
        jQuery(this).addClass("meta-menu-tab-active");
    });
});

