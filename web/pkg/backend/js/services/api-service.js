(function(){
  'use strict';

  angular
    .module('BackendApp')
    .factory('APIService', APIService);

  APIService.$inject = ['$http','DataShareService'];
  function APIService($http,DataShareService){
    var service = {};
 
    service.Login         = Login;
    service.GetModels     = GetModels;
    service.GetRecords    = GetRecords;
    service.GetRefs       = GetRefs;
    service.DeleteRecord  = DeleteRecord;
    service.DeleteRecords = DeleteRecords;
    service.AddEditRecord = AddEditRecord;

    return service;
 
    function Login(user, pass, callback){
      $http.post('/backend/login', {user:user, pass:pass})
        .success(function (response){
          callback && callback(response);
        });
    }

    function GetModels(callback){
      $http.post('/backend/get_models', {token: DataShareService.GetGlobal('token')})
        .success(function (response){
          callback && callback(response);
        });
    }

    function GetRecords(table, num_pag, pag, order_by, order_sent, callback){
      $http.post('/backend/get_records', {token: DataShareService.GetGlobal('token'), table:table, num_pag:num_pag, pag:pag, order_by:order_by, order_sent:order_sent})
        .success(function (response){
          callback && callback(response);
        });
    }
    
    function GetRefs(refs, data, callback){
      $http.post('/backend/get_refs', {token: DataShareService.GetGlobal('token'), refs:refs})
        .success(function (response){
          callback && callback(data,response);
        });
    }

    function DeleteRecord(model, table, field, ind, callback){
      $http.post('/backend/delete_record', {token: DataShareService.GetGlobal('token'), model: model, table: table, field: field})
        .success(function (response){
          callback && callback(ind);
        });
    }

    function DeleteRecords(model, table, list, callback){
      $http.post('/backend/delete_records', {token: DataShareService.GetGlobal('token'), model: model, table: table, list: list})
        .success(function (response){
          callback && callback();
        });
    }
    
    function AddEditRecord(table, record, callback){
      $http.post('/backend/add_edit_record', {token: DataShareService.GetGlobal('token'), table: table, record: record})
        .success(function (response){
          callback && callback();
        });
    }
  }
})();