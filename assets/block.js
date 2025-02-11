(function (blocks, element) {
  const el = element.createElement;

  blocks.registerBlockType("cps/slider", {
    title: "News Slides Slider",
    icon: "slides",
    category: "widgets",
    edit: function () {
      return el(
        "div",
        { className: "cps-slider-block" },
        "News Slides Slider - Vista previa en frontend"
      );
    },
    save: function () {
      // Bloque dinámico
      return null;
    },
  });
})(window.wp.blocks, window.wp.element);
