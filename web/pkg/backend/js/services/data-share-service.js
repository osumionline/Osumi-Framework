(function(){
  'use strict';

  angular
    .module('BackendApp')
    .factory('DataShareService', DataShareService);
 
  function DataShareService(){
    var service = {};

    service.globals     = {};
    service.SetGlobal   = SetGlobal;
    service.GetGlobal   = GetGlobal;
    
    return service;

    function SetGlobal(key,val){
      service.globals[key] = val;
    }
    function GetGlobal(key){
      return (service.globals[key]) ? service.globals[key] : null;
    }
  }
})();