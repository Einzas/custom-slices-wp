// assets/block.js
( function( blocks, element, components, blockEditor, serverSideRender ) {
  const el = element.createElement;
  const { InspectorControls } = blockEditor;
  const { PanelBody, RangeControl, TextControl } = components;

  blocks.registerBlockType( "cps/slider", {
    title: "News Slides Slider",
    icon: "slides",
    category: "widgets",
    attributes: {
      numPosts: {
        type: "number",
        default: 5,
      },
      year: {
        type: "string",
        default: "2024",
      },
    },
    edit: function( props ) {
      const { attributes: { numPosts, year }, setAttributes } = props;
      return [
        el(
            InspectorControls,
            { key: "inspector" },
            el(
                PanelBody,
                { title: "Configuración del Slider", initialOpen: true },
                el( RangeControl, {
                  label: "Número de posts",
                  value: numPosts,
                  onChange: ( value ) => setAttributes({ numPosts: value }),
                  min: 1,
                  max: 25,
                } ),
                el( TextControl, {
                  label: "Año",
                  value: year,
                  onChange: ( value ) => setAttributes({ year: value }),
                  placeholder: "Ejemplo: 2022",
                } )
            )
        ),
        el( serverSideRender, {
          block: "cps/slider",
          attributes: props.attributes,
        } ),
      ];
    },
    save: function() {
      return null;
    },
  } );
} )( window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor || window.wp.editor, window.wp.serverSideRender );
