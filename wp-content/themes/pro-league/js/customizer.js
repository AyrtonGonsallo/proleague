(function ($) {
	$("#navbar .navbar-toggler").on("click", () => {
		$("div#navbar-menu").toggleClass("active");
	});
})(jQuery);