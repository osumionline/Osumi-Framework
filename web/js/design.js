var design = {
  id               : '',
  ind              : '',
  name             : '',
  size_x           : '',
  size_y           : '',
  levels           : [],
  current_level    : -1,
  levels_loaded    : false,
  selected_tiles   : [],
  modified         : false,
  rulers           : false,
  zoom             : 1,
  linea_start      : false,
  linea_start_tile : [],
  linea_end_tile   : [],
  linea_points     : [],
  border_color     : 1,
  current_color    : 0,
  colores          : ['Negro','Azul','Verde','Rojo','Amarillo','Marrón'],

  setId : function(i){
    this.id = i;
  },

  getId : function(){
    return this.id;
  },

  setInd : function(i){
    this.ind = i;
  },

  getInd : function(){
    return this.ind;
  },

  setName : function(n){
    this.name = n;
  },

  getName : function(){
    return this.name;
  },

  setSizeX : function(sx){
    this.size_x = sx;
  },

  getSizeX : function(){
    return this.size_x;
  },

  setSizeY : function(sy){
    this.size_y = sy;
  },

  getSizeY : function(){
    return this.size_y;
  },

  setLevels : function(l){
    this.levels = l;
  },

  getLevels : function(){
    return this.levels;
  },

  setCurrentLevel : function(cl){
    this.current_level = cl;
  },

  getCurrentLevel : function(){
    return this.current_level;
  },

  setCurrentTiles : function(ct){
    var list = this.getLevels();
    list[this.getCurrentLevel()].setList(ct);
  },

  getCurrentTiles : function(){
    var list = this.getLevels();
    if (list[this.getCurrentLevel()]){
      return list[this.getCurrentLevel()].getList();
    }
    else{
      return [];
    }
  },

  setLevelsLoaded : function(ll){
    this.levels_loaded = ll;
  },

  getLevelsLoaded : function(){
    return this.levels_loaded;
  },

  setSelectedTiles : function(st){
    this.selected_tiles = st;
  },

  getSelectedTiles : function(){
    return this.selected_tiles;
  },

  setModified : function(m){
    this.modified = m;
  },

  getModified : function(){
    return this.modified;
  },

  setRulers : function(r){
    this.rulers = r;
  },

  getRulers : function(){
    return this.rulers;
  },

  setZoom : function(z){
    this.zoom = z;
  },

  getZoom : function(){
    return this.zoom;
  },

  setLineaStart : function(ls){
    this.linea_start = ls;
  },

  getLineaStart : function(){
    return this.linea_start;
  },

  setLineaStartTile : function(lst){
    this.linea_start_tile = lst;
  },

  getLineaStartTile : function(){
    return this.linea_start_tile;
  },

  setLineaEndTile : function(l){
    this.linea_end_tile = l;
  },

  getLineaEndTile : function(){
    return this.linea_end_tile;
  },

  setLineaPoints : function(lp){
    this.linea_points = lp;
  },

  getLineaPoints : function(){
    return this.linea_points;
  },

  setBorderColor : function(bc){
    this.border_color = bc;
  },

  getBorderColor : function(){
    return this.border_color;
  },

  setCurrentColor : function(c){
    if (c===undefined){
      c = $('#color_sel').val();
    }
    this.current_color = c;
  },

  getCurrentColor : function(){
    return this.current_color;
  },

  setColores : function(c){
    this.colores = c;
  },

  getColores : function(){
    return this.colores;
  },

  getLinea : function(start,end){
    // Buscar la linea
    var sent_x = 1;
    var sent_y = 1;
    var ind_x = start[0];
    var ind_y = start[1];
    var linea_points = [start];
    var start_line = false;

    if (start[0]>end[0]){
      sent_x = -1;
    }
    if (start[1]>end[1]){
      sent_y = -1;
    }

    while (ind_x != end[0] || ind_y != end[1]){
      ind_x = ind_x + sent_x;
      ind_y = ind_y + sent_y;

      if (sent_x>0){
        if (ind_x>end[0]){
          ind_x = end[0];
        }
      }
      else{
        if (ind_x<end[0]){
          ind_x = end[0];
        }
      }
      if (sent_y>0){
        if (ind_y>end[1]){
          ind_y = end[1];
        }
      }
      else{
        if (ind_y<end[1]){
          ind_y = end[1];
        }
      }

      linea_points.push([ind_x,ind_y]);
    }
    linea_points.pop();
    linea_points.reverse();

    return linea_points;
  },

  selectPixel : function(x,y){
    var obj = $('#pixel_'+x+'_'+y);
    var start_line = false;

    // Compruebo si estoy pintando una linea
    if (this.getLineaStart()){
      if (this.getLineaStartTile().length == 0){
        this.setLineaStartTile([x,y]);
        $('#btn_line').html('Pincha en el final');
      }
      else{
        this.setLineaEndTile([x,y]);
      }

      if (this.getLineaStartTile().length != 0 && this.getLineaEndTile().length != 0){
        // Busco la linea
        var linea_points = this.getLinea(this.getLineaStartTile(),this.getLineaEndTile());
        start_line = true;
      }

      obj.removeClass();
      obj.addClass('pixel_yellow');

      if (start_line){
        this.setLineaStart(false);
        this.setLineaStartTile([]);
        this.setLineaEndTile([]);
        this.setLineaPoints(linea_points);
      }
      else{
        return false;
      }
    }

    if (obj.hasClass('pixel') || obj.hasClass('pixel_yellow')){
      if (obj.hasClass('pixel')){
        obj.removeClass('pixel');
      }
      if (obj.hasClass('pixel_yellow')){
        obj.removeClass('pixel_yellow');
      }
      obj.addClass('pixel_selected');
      obj.addClass('color-'+this.getCurrentColor());

      this.selectTile(x,y,this.getCurrentColor());
      $('#opt_save').show();
      $('#opt_cancel').show();
    }
    else{
      obj.removeClass (function (index, css) {
        return (css.match (/\bcolor-\S+/g) || []).join(' ');
      });
      obj.removeClass('pixel_selected');
      if (this.getRulers() && ((x % 5 == 0) || (y % 5 == 0))){
        obj.addClass('pixel_yellow');
      }
      else{
        obj.addClass('pixel');
      }

      this.unSelectTile(x,y);
    }

    if (this.getLineaPoints().length > 0){
      var tiles = this.getLineaPoints();
      var tile = tiles.shift();
      this.setLineaPoints(tiles);

      if (tiles.length == 0){
        $('#btn_line').html('Dibujar linea');
      }

      $('#pixel_'+tile[0]+'_'+tile[1]).trigger('click');
    }
  },

  selectTile : function(x,y,c){
    var st = this.getSelectedTiles();

    if (st[x] == undefined){
      st[x] = [];
    }

    st[x][y] = c;
    this.setModified(true);
    this.setSelectedTiles(st);
    $('#save_box').show();
  },

  unSelectTile : function(x,y){
    var st = this.getSelectedTiles();

    st[x][y] = undefined;

    this.setModified(true);
    this.setSelectedTiles(st);
    $('#save_box').show();
  },

  render : function(){
    var tpl_tabla = '';
    var tpl_linea = '';
    var tpl_pixel = '';
    var tpl_data_tabla = {};
    var tpl_data_linea = {};
    var tpl_data_pixel = {};
    var lineas = [];
    var pixels = [];

    var clase = '';
    var width = (((this.getZoom()*10)+3)*this.getSizeY()) +10;

    for (x=0;x<this.getSizeX();x++){
      pixels = [];

      for (y=0;y<this.getSizeY();y++){
        clase = 'pixel';
        if (this.getRulers() && ((x % 5 == 0) || (y % 5 == 0))){
          clase = 'pixel_yellow';
        }
        tpl_data_pixel = {
          x: x,
          y: y,
          clase: clase,
          height: (this.getZoom()*11),
          width: (this.getZoom()*11),
          border: this.getBorderColor()
        };
        tpl_pixel = template('pixel_box',tpl_data_pixel);
        pixels.push(tpl_pixel);
      }

      tpl_data_linea = {
        pixels: pixels.join('')
      };
      tpl_linea = template('table_line',tpl_data_linea);
      lineas.push(tpl_linea);
    }
    tpl_data_tabla = {
      width: width,
      lines: lineas.join('')
    };
    tpl_tabla = template('drawing_table',tpl_data_tabla);

    return tpl_tabla;
  },

  save : function(){
    if (!this.getModified()){
      return false;
    }

    var cad = '';
    var corta = false;
    var st = this.getSelectedTiles();

    for (x=0;x<st.length;x++){
      if (st[x] != undefined){
        for (y=0;y<st[x].length;y++){
          if (st[x][y] != undefined){
            cad += x + '-' + y + '-' + st[x][y] + ',';
            corta = true;
          }
        }
      }
    }
    if (corta){
      cad = cad.substring(0,cad.length -1);
    }

    var url_data = {
                     design: this.getId(),
                     level: this.getCurrentLevel(),
                     data: cad
                   };
    var obj = this;
    $.post(
      api_url + 'saveDesign',
      url_data,
      function(data){
        if (data.status=='ok'){
          obj.setModified(false);
          obj.setCurrentTiles(st);
          $('#opt_save').hide();
          $('#opt_cancel').hide();
        }
      }
    );
  },

  cancel : function(){
    var ct = this.getCurrentTiles();

    this.setSelectedTiles(ct);
    $('#drawing_canvas').html(this.render());
    this.pintaSelected();
    $('#opt_save').hide();
    $('#opt_cancel').hide();
  },

  pintaSelected : function(){
    var st = this.getSelectedTiles();
    var obj = null;

    for (x=0;x<st.length;x++){
      if (st[x] != undefined){
        for (y=0;y<st[x].length;y++){
          if (st[x][y] != undefined){
            obj = $('#pixel_'+x+'_'+y);
            obj.removeClass('pixel');
            obj.addClass('pixel_selected color-'+st[x][y]);
          }
        }
      }
    }
  },

  pintaListaLevels : function(){
    var levels = this.getLevels();
    var tpl_data_level = {};
    var tpl_level = '';
    var level_list = [];

    for (i=0;i<levels.length;i++){
      tpl_data_level = {
        height: levels[i].getHeight(),
        name: levels[i].getName()
      };

      if (i!=this.getCurrentLevel()){
        level_list.push( template('level_list_item',tpl_data_level) );
      }
      else{
        level_list.push( template('level_list_item_current',tpl_data_level) );
      }
    }
    var opt_level_list = $('#opt_level_list');
    opt_level_list.html(level_list.join(''));
    opt_level_list.show();

    var opt_level_add = $('#opt_level_add');
    opt_level_add.show();

    var opt_marcas = $('#opt_marcas');
    var marcas_on = $('#marcas_on');
    var marcas_off = $('#marcas_off');
    if (this.getRulers()){
      marcas_on.attr('class','opt_on');
      marcas_on.prop('disabled',false);
      marcas_off.attr('class','opt_off');
      marcas_off.prop('disabled',true);
    }
    else{
      marcas_on.attr('class','opt_off');
      marcas_on.prop('disabled',true);
      marcas_off.attr('class','opt_on');
      marcas_off.prop('disabled',false);
    }
    opt_marcas.show();

    var obj_zoom = $('#zoom');
    obj_zoom.val( this.getZoom() * 100 );
    var btn_zoom = $('#btn_zoom');
    btn_zoom.click(function(e){
      e.preventDefault();
      design.changeZoom();
    });
    var opt_zoom = $('#opt_zoom');
    opt_zoom.show();

    var btn_line = $('#btn_line');
    btn_line.click(function(e){
      e.preventDefault();
      design.startLine();
    });
    var opt_line = $('#opt_line');
    opt_line.show();

    var opt_bordes = $('#opt_bordes');
    var bordes_on = $('#bordes_on');
    var bordes_off = $('#bordes_off');
    if (this.getRulers()){
      bordes_on.attr('class','opt_on');
      bordes_on.prop('disabled',false);
      bordes_off.attr('class','opt_off');
      bordes_off.prop('disabled',true);
    }
    else{
      bordes_on.attr('class','opt_off');
      bordes_on.prop('disabled',true);
      bordes_off.attr('class','opt_on');
      bordes_off.prop('disabled',false);
    }
    opt_marcas.show();

    var colores = this.getColores();
    var color_sel = $('#color_sel');
    color_sel.change(function(){
      design.setCurrentColor();
    });
    var opt_color = $('#opt_color');
    var colores_list = [];
    var tpl_color = '';
    var tpl_data_color = {};

    for (i=0;i<colores.length;i++){
      tpl_data_color = {
        i: i,
        color: colores[i]
      };
      tpl_color = template('color_list_item',tpl_data_color);
      colores_list.push(tpl_color);
    }
    color_sel.html(colores_list.join(''));
    opt_color.show();

    var opt_design_list = $('#opt_design_list');
    opt_design_list.show();

    $('#design_options').show();
  },

  addNewLevel : function(){
    var url_data = {
                     design: this.getId()
                   };

    $.post(
      api_url + 'newLevel',
      url_data,
      function(data){
        if (data.status=='ok'){
          var temp = design.getLevels();

          var item = new level();
          item.setId(data.level.id);
          item.setIdDesign(data.level.id_design);
          item.setName(urldecode(data.level.name));
          item.setHeight(data.level.height);
          item.setData(urldecode(data.level.data));

          temp.push(item);

          design.setLevels(temp);
          design.setCurrentLevel(data.level.height);
          design.setLevelsLoaded(true);

          var st = design.getCurrentTiles();
          design.setSelectedTiles(st);
          design.render();
          design.pintaSelected();

          design.pintaListaLevels();
        }
      }
    );
  },

  changeLevel : function(l){
    if (this.getModified()){
      alert('¡No puedes cambiar de nivel sin guardar!');
      return false;
    }

    this.setCurrentLevel(l);
    var st = this.getCurrentTiles();
    this.setSelectedTiles(st);
    $('#drawing_canvas').html(this.render());
    this.pintaSelected();

    this.pintaListaLevels();
  },

  clone : function(id){
    var url_data = {
                     design: this.getId(),
                     level: id
                   };

    $.post(
      api_url + 'cloneLevel',
      url_data,
      function(data){
        if (data.status=='ok'){
          var temp = design.getLevels();

          var item = new level();
          item.setId(data.level.id);
          item.setIdDesign(data.level.id_design);
          item.setName(urldecode(data.level.name));
          item.setHeight(data.level.height);
          item.setData(urldecode(data.level.data));

          temp.push(item);

          design.setLevels(temp);
          design.setCurrentLevel(data.level.height);
          design.setLevelsLoaded(true);

          var st = design.getCurrentTiles();
          design.setSelectedTiles(st);
          design.render();
          design.pintaSelected();

          design.pintaListaLevels();
        }
      }
    );
  },

  showRulers : function(mode){
    for (x=0;x<this.getSizeX();x++){
      for (y=0;y<this.getSizeY();y++){
        if ((x % 5 == 0) || (y % 5 == 0)){
          var obj = $('#pixel_'+x+'_'+y);

          if (mode == 'on'){
            if (obj.hasClass('pixel')){
              obj.removeClass('pixel');
              obj.addClass('pixel_yellow');
            }
          }

          if (mode == 'off'){
            if (obj.hasClass('pixel_yellow')){
              obj.removeClass('pixel_yellow');
              obj.addClass('pixel');
            }
          }
        }
      }
    }

    var marcas_on = $('#marcas_on');
    var marcas_off = $('#marcas_off');
    if (mode == 'on'){
      marcas_on.attr('class','opt_on');
      marcas_on.prop('disabled',true);
      marcas_off.attr('class','opt_off');
      marcas_off.prop('disabled',false);
      this.setRulers(true);
    }
    else{
      marcas_on.attr('class','opt_off');
      marcas_on.prop('disabled',false);
      marcas_off.attr('class','opt_on');
      marcas_off.prop('disabled',true);
      this.setRulers(false);
    }
  },

  changeZoom : function(){
    var obj = $('#zoom');
    if (obj.val() == ''){
      alert('No puedes dejar el zoom en blanco!');
      obj.focus();
      return false;
    }

    if (isNaN(obj.val())){
      alert('Valor de zoom incorrecto!');
      obj.select();
      return false;
    }

    var zoom = parseInt(obj.val()) / 100;
    this.setZoom(zoom);
    var st = this.getCurrentTiles();
    this.setSelectedTiles(st);
    $('#drawing_canvas').html(this.render());
    this.pintaSelected();
  },

  startLine : function(){
    this.setLineaStart(true);
    this.setLineaStartTile([]);
    this.setLineaEndTile([]);

    $('#btn_line').html('Pincha en el principio');
  },

  showBorders : function(mode){
    var bordes_on = $('#bordes_on');
    var bordes_off = $('#bordes_off');
    if (mode == 'on'){
      bordes_on.attr('class','opt_on');
      bordes_on.prop('disabled',true);
      bordes_off.attr('class','opt_off');
      bordes_off.prop('disabled',false);
      this.setBorderColor('1');
    }
    else{
      bordes_on.attr('class','opt_off');
      bordes_on.prop('disabled',false);
      bordes_off.attr('class','opt_on');
      bordes_off.prop('disabled',true);
      this.setBorderColor('0');
    }

    var st = this.getCurrentTiles();
    this.setSelectedTiles(st);
    $('#drawing_canvas').html(this.render());
    this.pintaSelected();
  }
}

function showNewDesign(){
  var obj_back = $('#dark_bg');
  var obj_box = $('#add_design_box');

  if (!obj_box.is(':visible')){
    var obj_name = $('#new_design_name');
    var obj_size_x = $('#new_design_size_x');
    var obj_size_y = $('#new_design_size_y');

    obj_name.val('');
    obj_size_x.val('75');
    obj_size_y.val('75');

    obj_back.show();
    obj_box.show();
    obj_name.focus();
  }
  else{
    obj_box.hide();
    obj_back.hide();
  }
}

function checkNewDesign(){
  var obj_name = $('#new_design_name');
  if (obj_name.val() == ''){
    alert('¡No puedes dejar el nombre del nuevo diseño en blanco!');
    obj_name.focus();
    return false;
  }

  var obj_size_x = $('#new_design_size_x');
  if (obj_size_x.val() == ''){
    alert('¡No puedes dejar el tamaño horizontal del lienzo en blanco!');
    obj_size_x.focus();
    return false;
  }

  if (isNaN(obj_size_x.val())){
    alert('¡La anchura introducida no es un número válido!');
    obj_size_x.select();
    return false;
  }

  var obj_size_y = $('#new_design_size_y');
  if (obj_size_y.val() == ''){
    alert('¡No puedes dejar el tamaño vertical del lienzo en blanco!');
    obj_size_y.focus();
    return false;
  }

  if (isNaN(obj_size_y.val())){
    alert('¡La altura introducida no es un número válido!');
    obj_size_y.select();
    return false;
  }

  var url_data = {
                   name: urlencode(obj_name.val()),
                   size_x: obj_size_x.val(),
                   size_y: obj_size_y.val()
                 };
  $.post(
    api_url + 'newDesign',
    url_data,
    function(data){
      if (data.status=='ok'){
        design.setId(data.id_design);
        design.setName(urldecode(data.name));
        design.setSizeX(data.size_x);
        design.setSizeY(data.size_y);

        var tpl_data_design = {
          id: design.getId(),
          name: design.getName(),
          x: design.getSizeX(),
          y: design.getSizeY()
        };
        var tpl_design = template('design_list_item',tpl_data_design);
        $('#design_list').append(tpl_design);

        alert('¡Nuevo diseño añadido!');

        showNewDesign();
      }
    }
  );
}

function openDesign(id){
  var url_data = {design:id};
  $.post(
      api_url + 'loadDesign',
      url_data,
      function(data){
        if (data.status=='ok'){
          var temp = [];
          var item;

          design.setId(data.id_design);
          design.setSizeX(data.size_x);
          design.setSizeY(data.size_y);
          $('#drawing_canvas').html(design.render());

          for(i=0;i<data.list.length;i++){
            item = new level();
            item.setId(data.list[i].id);
            item.setIdDesign(data.list[i].id_design);
            item.setName(urldecode(data.list[i].name));
            item.setHeight(data.list[i].height);
            item.setData(urldecode(data.list[i].data));

            temp[data.list[i].height] = item;
          }

          design.setLevels(temp);
          design.setCurrentLevel(0);
          design.setLevelsLoaded(true);

          var st = design.getCurrentTiles();
          design.setSelectedTiles(st);
          design.pintaSelected();

          design.pintaListaLevels();

          $('#design_list').hide();
          $('#drawing_canvas').show();
        }
      });
}

function showDesignList(){
  var obj = $('#design_list');
  if (!obj.is(':visible')){
    obj.slideDown();
  }
  else{
    obj.slideUp();
  }
}