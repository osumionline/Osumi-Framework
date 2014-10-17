function level(){
  this.id = '';
  this.id_design = '';
  this.name = '';
  this.height = '';
  this.data = '';
  this.list = '';
  this.loaded = false;

  this.setId = function(i){
    this.id = i;
  }

  this.getId = function(){
    return this.id;
  }

  this.setIdDesign = function(id){
    this.id_design = id;
  }

  this.getIdDesign = function(){
    return this.id_design;
  }

  this.setName = function(n){
    this.name = n;
  }

  this.getName = function(){
    return this.name;
  }

  this.setHeight = function(h){
    this.height = h;
  }

  this.getHeight = function(){
    return this.height;
  }

  this.setData = function(d){
    this.data = d;
  }

  this.getData = function(){
    return this.data;
  }

  this.setList = function(l){
    this.list = l;
  }

  this.getList = function(){
    if (this.list == ''){
      var data = this.getData();
      var ret = [];
      var temp = [];
      var item_data;
      var color = 0;

      if (data.indexOf('-') == -1){
        return ret;
      }

      if (data.indexOf(',') != -1){
        temp = data.split(',');
      }
      else{
        temp[0] = data;
      }

      for(i=0;i<temp.length;i++){
        item_data = temp[i].split('-');
        color = 0;

        if (ret[item_data[0]] == undefined){
          ret[item_data[0]] = [];
        }

        if (item_data[2]){ color = item_data[2]; }

        ret[item_data[0]][item_data[1]] = color;
      }

      this.list = ret;
    }

    return this.list;
  }

  this.setLoaded = function(l){
    this.loaded = l;
  }

  this.getLoaded = function(){
    return this.loaded;
  }
}