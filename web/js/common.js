/*
 * Función para renderizar plantillas
 */
function template(id,data){
  var obj = $('#'+id).html();
  var temp = '';

  for (var ind in data){
    temp = '{{'+ind+'}}';
    obj = obj.replace(new RegExp(temp,"g") ,data[ind]);
  }

  return obj;
}

/*
 * Función equivalente al urlencode de php
 */
function urlencode(s){
  return encodeURIComponent( s ).replace( /\%20/g, '+' ).replace( /!/g, '%21' ).replace( /'/g, '%27' ).replace( /\(/g, '%28' ).replace( /\)/g, '%29' ).replace( /\*/g, '%2A' ).replace( /\~/g, '%7E' );
}

/*
 * Función equivalente al urldecode de php
 */
function urldecode(s){
  return decodeURIComponent( s.replace( /\+/g, '%20' ).replace( /\%21/g, '!' ).replace( /\%27/g, "'" ).replace( /\%28/g, '(' ).replace( /\%29/g, ')' ).replace( /\%2A/g, '*' ).replace( /\%7E/g, '~' ) );
}

/*
 * Función para obtener parametros de la url
 */
function getURLParameter(name){
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}

/*
 * Función para obtener el slug de un texto
 */
function slugify(text){
	text = text.replace(/[^-a-zA-Z0-9,&\s]+/ig, '');
	text = text.replace(/-/gi, "_");
	text = text.replace(/\s/gi, "-");
	return text;
}

/*
 * Funciones equivalentes a trim de php
 */
function trim(str, chars){
  return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars){
  chars = chars || "\\s";
  return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars){
  chars = chars || "\\s";
  return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}