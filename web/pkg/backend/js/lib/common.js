/*
 * Función para renderizar plantillas
 */
function template(id,data){
  var obj = document.getElementById(id).innerHTML;
  var temp = '';

  for (var ind in data){
    temp = '{{'+ind+'}}';
    obj = obj.replace(new RegExp(temp,"g") ,data[ind]);
  }

  return obj;
}

/*
 * Función para crear el slug de un texto
 */
function slugify(str){
  return str.toString().toLowerCase()
    .replace(/ñ/,'n')
    .replace(/\s+/g, '-')           // Replace spaces with -
    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
    .replace(/^-+/, '')             // Trim - from start of text
    .replace(/-+$/, '');            // Trim - from end of text
}

/*
 * Función equivalente al urlencode de php
 */
function urlencode(str){
  if (!str){ return ''; }
  return encodeURIComponent( str ).replace( /\%20/g, '+' ).replace( /!/g, '%21' ).replace( /'/g, '%27' ).replace( /\(/g, '%28' ).replace( /\)/g, '%29' ).replace( /\*/g, '%2A' ).replace( /\~/g, '%7E' );
}

/*
 * Función equivalente al urldecode de php
 */
function urldecode(str){
  if (!str){ return ''; }
  return decodeURIComponent( str.replace( /\+/g, '%20' ).replace( /\%21/g, '!' ).replace( /\%27/g, "'" ).replace( /\%28/g, '(' ).replace( /\%29/g, ')' ).replace( /\%2A/g, '*' ).replace( /\%7E/g, '~' ) );
}

/*
 * Función equivalente al ucfirst de php
 */
function ucfirst(str){
  return str.charAt(0).toUpperCase() + str.slice(1);
}

/*
 * Función para guardar en localstorage
 */
function setLocalStorageData(key,data){
  localStorage.setItem(key, JSON.stringify(data));
}

/*
 * Función para leer de localstorage y callback de error si no existe
 */
function getLocalStorageData(key,callback){
  var chk = localStorage.getItem(key);
  
  if (chk){
    return JSON.parse(chk);
  }
  else{
    return callback();
  }
}