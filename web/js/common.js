(function(){
  window.onload = startDate;

  function startDate(){
    setInterval(loadDate, 5000);
    loadDate();
  }

  function urldecode(str){
    if (!str){ return ''; }
    return decodeURIComponent( str.replace( /\+/g, '%20' ).replace( /\%21/g, '!' ).replace( /\%27/g, "'" ).replace( /\%28/g, '(' ).replace( /\%29/g, ')' ).replace( /\%2A/g, '*' ).replace( /\%7E/g, '~' ) );
  }

  function loadDate(){
    fetch('/api/getDate')
      .then((response) => response.json())
      .then((obj) => {
        document.querySelector('#date-box').innerHTML = urldecode(obj.date);
      });
  }
})();
