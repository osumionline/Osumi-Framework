$(document).ready(function(){
  // Enlace para añadir nuevo diseño
  $('#add_design').click(function(e){
    e.preventDefault();
    showNewDesign();
  });

  // Enlace para cerrar cuadro de nuevo diseño
  $('#add_design_close').click(function(e){
    e.preventDefault();
    showNewDesign();
  });

  // Enlace para guardar nuevo diseño
  $('#new_design_go').click(function(e){
    e.preventDefault();
    checkNewDesign();
  });

  // Enlace para guardar diseño
  $('#opt_save').click(function(e){
    e.preventDefault();
    design.save();
  });

  // Enlace para cancelar edición
  $('#opt_cancel').click(function(e){
    e.preventDefault();
    design.cancel();
  });

  // Enlace para añadir nuevo nivel al diseño
  $('#opt_level_add').click(function(e){
    e.preventDefault();
    design.addNewLevel();
  });

  // Enlace para activar las marcas
  $('#marcas_on').click(function(e){
    e.preventDefault();
    design.showRulers('on');
  });

  // Enlace para desactivar las marcas
  $('#marcas_off').click(function(e){
    e.preventDefault();
    design.showRulers('off');
  });

  // Botón para cambiar el zoom
  $('#btn_zoom').click(function(){
    design.changeZoom();
  });

  // Enlace para dibujar una linea
  $('#btn_line').click(function(e){
    e.preventDefault();
    design.startLine();
  });

  // Enlace para activar los bordes
  $('#bordes_on').click(function(e){
    e.preventDefault();
    design.showBorders('on');
  });

  // Enlace para desactivar los bordes
  $('#bordes_off').click(function(e){
    e.preventDefault();
    design.showBorders('off');
  });

  // Desplegable con la lista de colores
  $('#color_sel').change(function(){
    var color = $(this).val();
    design.setCurrentColor(color);
  });

  // Enlace para mostrar/ocultar la lista de diseños
  $('#btn_design_list').click(function(e){
    e.preventDefault();
    showDesignList();
  });
});